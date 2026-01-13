<?php

namespace AperturePro\Http\Rest;

use WP_REST_Request;
use AperturePro\Http\Middleware\Input;
use AperturePro\Http\Middleware\Permissions;
use AperturePro\Http\Middleware\StateValidation;
use AperturePro\Domain\Proofing\ProofingService;
use AperturePro\Domain\Proofing\ProofingState;
use AperturePro\Domain\Proofing\ProofingRepository;

class ProofingController
{
    public function register_routes(): void
    {
        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/proofing/update', [
            'methods'             => 'POST',
            'callback'            => [$this, 'updateImage'],
            'permission_callback' => [Permissions::class, 'client_or_admin_can_proof'],
        ]);

        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/proofing/submit', [
            'methods'             => 'POST',
            'callback'            => [$this, 'submit'],
            'permission_callback' => [Permissions::class, 'client_or_admin_can_proof'],
        ]);

        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/proofing/unlock', [
            'methods'             => 'POST',
            'callback'            => [$this, 'unlock'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);

        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/proofing/complete', [
            'methods'             => 'POST',
            'callback'            => [$this, 'complete'],
            'permission_callback' => [Permissions::class, 'admin_only'],
        ]);

        register_rest_route('aperture-pro/v1', '/projects/(?P<id>\d+)/proofing/summary', [
            'methods'             => 'GET',
            'callback'            => [$this, 'summary'],
            'permission_callback' => [Permissions::class, 'client_or_admin_can_view_project'],
        ]);
    }

    public function updateImage(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];
        $image_id   = Input::int($req, 'image_id');
        $status     = Input::enum($req, 'status', ['approved', 'rejected', 'revision']);
        $note       = $req->get_param('note');

        StateValidation::assertStage($project_id, ['proofing']);
        StateValidation::assertProofingState($project_id, [ProofingState::OPEN, ProofingState::REOPENED]);

        ProofingService::updateImage($project_id, $image_id, $status, $note);

        return [
            'success' => true,
        ];
    }

    public function submit(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];
        $message    = sanitize_textarea_field($req->get_param('message'));

        StateValidation::assertStage($project_id, ['proofing']);
        StateValidation::assertProofingState($project_id, [ProofingState::OPEN, ProofingState::REOPENED]);

        ProofingService::submitSelections($project_id, $message);

        return [
            'success' => true,
            'state'   => ProofingState::SUBMITTED,
        ];
    }

    public function unlock(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];

        StateValidation::assertProofingState($project_id, [ProofingState::SUBMITTED]);

        ProofingService::reopen($project_id);

        return [
            'success' => true,
            'state'   => ProofingState::REOPENED,
        ];
    }

    public function complete(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];

        StateValidation::assertProofingState($project_id, [ProofingState::SUBMITTED]);

        ProofingService::complete($project_id);

        return [
            'success' => true,
            'stage'   => 'editing',
        ];
    }

    public function summary(WP_REST_Request $req)
    {
        $project_id = (int) $req['id'];

        return [
            'round'  => ProofingRepository::getRound($project_id),
            'counts' => ProofingRepository::getCounts($project_id),
            'state'  => ProofingRepository::getState($project_id),
            'message'=> get_post_meta($project_id, 'ap_proofing_message', true),
        ];
    }
}
