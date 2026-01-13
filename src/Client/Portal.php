<?php

namespace AperturePro\Client;

use AperturePro\Domain\Tokens\TokenService;
use AperturePro\Domain\Project\ProjectStage;
use AperturePro\Domain\Proofing\ProofingRepository;
use AperturePro\Domain\Delivery\DeliveryService;

class Portal
{
    public static function boot(): void
    {
        add_shortcode('aperture_pro_portal', [self::class, 'renderShortcode']);
        add_action('wp_enqueue_scripts', [self::class, 'enqueueAssets']);
    }

    public static function enqueueAssets(): void
    {
        if (!is_singular()) {
            return;
        }

        global $post;
        if (!has_shortcode($post->post_content, 'aperture_pro_portal')) {
            return;
        }

        wp_enqueue_style(
            'aperture-pro-client',
            plugins_url('assets/client.css', APERTURE_PRO_FILE),
            [],
            APERTURE_PRO_VERSION
        );

        wp_enqueue_script(
            'aperture-pro-client',
            plugins_url('assets/client.js', APERTURE_PRO_FILE),
            ['wp-api-fetch'],
            APERTURE_PRO_VERSION,
            true
        );

        wp_localize_script('aperture-pro-client', 'ApertureProClient', [
            'restUrl' => esc_url_raw(rest_url('aperture-pro/v1/')),
        ]);
    }

    public static function renderShortcode($atts = []): string
    {
        $token = TokenService::extractToken();
        if (!$token) {
            return self::renderTemplate('portal-error.php', [
                'message' => 'This link is missing a token.',
            ]);
        }

        $record = \AperturePro\Domain\Tokens\TokenRepository::find($token);
        if (!$record || !\AperturePro\Domain\Tokens\TokenValidator::validateTokenObject($record)) {
            return self::renderTemplate('portal-error.php', [
                'message' => 'This link is no longer valid.',
            ]);
        }

        $project_id = (int) $record->project_id;
        $project    = get_post($project_id);

        if (!$project || $project->post_type !== 'ap_project') {
            return self::renderTemplate('portal-error.php', [
                'message' => 'We could not find this project.',
            ]);
        }

        $stage         = ProjectStage::get($project_id);
        $proof_state   = ProofingRepository::getState($project_id);
        $delivery_state= DeliveryService::getState($project_id);

        ob_start();
        include APERTURE_PRO_PATH . '/templates/client/portal-shell.php';
        return ob_get_clean();
    }

    public static function renderSection(int $project_id, string $stage, string $proof_state, string $delivery_state): void
    {
        switch ($stage) {
            case ProjectStage::PROOFING:
                include APERTURE_PRO_PATH . '/templates/client/portal-proofing.php';
                break;

            case ProjectStage::DELIVERY:
                include APERTURE_PRO_PATH . '/templates/client/portal-delivery.php';
                break;

            default:
                include APERTURE_PRO_PATH . '/templates/client/portal-locked.php';
                break;
        }
    }

    protected static function renderTemplate(string $file, array $vars = []): string
    {
        extract($vars, EXTR_SKIP);
        ob_start();
        include APERTURE_PRO_PATH . '/templates/client/' . $file;
        return ob_get_clean();
    }
}
