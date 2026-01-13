<?php

namespace AperturePro\Core;

use AperturePro\Http\Rest\ProjectStatusController;
use AperturePro\Http\Rest\ProofingController;
use AperturePro\Http\Rest\EditingController;
use AperturePro\Http\Rest\DeliveryController;
use AperturePro\Http\Rest\ClientController;
use AperturePro\Domain\Jobs\JobRunner;

class Plugin
{
    public function boot(): void
    {
        Hooks::register();

        add_action('rest_api_init', function () {
            (new ProjectStatusController())->register_routes();
            (new ProofingController())->register_routes();
            (new EditingController())->register_routes();
            (new DeliveryController())->register_routes();
            (new ClientController())->register_routes();
        });

        add_action('ap_run_job', [JobRunner::class, 'handle'], 10, 1);
    }
}
