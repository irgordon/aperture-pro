<?php

namespace AperturePro\Core;

class Settings
{
    // Option Keys
    const OPTION_BRAND_NAME = 'ap_brand_name';
    const OPTION_BRAND_LOGO = 'ap_brand_logo';
    const OPTION_SEO_TITLE = 'ap_seo_title_template';
    const OPTION_SEO_DESC = 'ap_seo_desc_template';
    const OPTION_IMG_QUALITY = 'ap_img_quality';
    const OPTION_IMG_MAX_WIDTH = 'ap_img_max_width';
    const OPTION_WIZARD_COMPLETED = 'ap_wizard_completed';

    // ImageKit Keys
    const OPTION_IK_PUBLIC_KEY = 'ap_ik_public_key';
    const OPTION_IK_PRIVATE_KEY = 'ap_ik_private_key';
    const OPTION_IK_URL_ENDPOINT = 'ap_ik_url_endpoint';

    public static function getBrandName(): string
    {
        return get_option(self::OPTION_BRAND_NAME, 'Aperture Pro');
    }

    public static function getBrandLogo(): string
    {
        return get_option(self::OPTION_BRAND_LOGO, '');
    }

    public static function getSeoTitleTemplate(): string
    {
        return get_option(self::OPTION_SEO_TITLE, '%project% | %brand%');
    }

    public static function getSeoDescTemplate(): string
    {
        return get_option(self::OPTION_SEO_DESC, 'View the proofing gallery for %project%.');
    }

    public static function getImgQuality(): int
    {
        return (int) get_option(self::OPTION_IMG_QUALITY, 85);
    }

    public static function getImgMaxWidth(): int
    {
        return (int) get_option(self::OPTION_IMG_MAX_WIDTH, 1920);
    }

    public static function getImageKitConfig(): array
    {
        return [
            'publicKey' => get_option(self::OPTION_IK_PUBLIC_KEY, ''),
            'privateKey' => get_option(self::OPTION_IK_PRIVATE_KEY, ''),
            'urlEndpoint' => get_option(self::OPTION_IK_URL_ENDPOINT, ''),
        ];
    }

    public static function isWizardCompleted(): bool
    {
        return (bool) get_option(self::OPTION_WIZARD_COMPLETED, false);
    }

    public static function markWizardCompleted(): void
    {
        update_option(self::OPTION_WIZARD_COMPLETED, true);
    }

    public static function updateSettings(array $settings): void
    {
        if (isset($settings['brandName'])) {
            update_option(self::OPTION_BRAND_NAME, sanitize_text_field($settings['brandName']));
        }
        if (isset($settings['brandLogo'])) {
            update_option(self::OPTION_BRAND_LOGO, esc_url_raw($settings['brandLogo']));
        }
        if (isset($settings['seoTitle'])) {
            update_option(self::OPTION_SEO_TITLE, sanitize_text_field($settings['seoTitle']));
        }
        if (isset($settings['seoDesc'])) {
            update_option(self::OPTION_SEO_DESC, sanitize_text_field($settings['seoDesc']));
        }
        if (isset($settings['imgQuality'])) {
            update_option(self::OPTION_IMG_QUALITY, (int) $settings['imgQuality']);
        }
        if (isset($settings['imgMaxWidth'])) {
            update_option(self::OPTION_IMG_MAX_WIDTH, (int) $settings['imgMaxWidth']);
        }

        // Storage is handled separately or we can do it here if passed
        if (isset($settings['storageAdapter'])) {
            \AperturePro\Storage\StorageSettings::setAdapterKey($settings['storageAdapter']);
        }

        // ImageKit
        if (isset($settings['ikPublicKey'])) {
            update_option(self::OPTION_IK_PUBLIC_KEY, sanitize_text_field($settings['ikPublicKey']));
        }
        if (isset($settings['ikPrivateKey'])) {
            update_option(self::OPTION_IK_PRIVATE_KEY, sanitize_text_field($settings['ikPrivateKey']));
        }
        if (isset($settings['ikUrlEndpoint'])) {
            update_option(self::OPTION_IK_URL_ENDPOINT, esc_url_raw($settings['ikUrlEndpoint']));
        }
    }
}
