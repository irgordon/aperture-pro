<?php

namespace AperturePro\Services;

use AperturePro\Http\Rest\ProjectStatusController;
use AperturePro\Http\Rest\ProofingController;
use AperturePro\Http\Rest\DeliveryController;
use AperturePro\Http\Rest\TokenController;
use AperturePro\Http\Rest\JobsController;
use AperturePro\Http\Rest\WizardController;
use AperturePro\Http\Rest\BioController;

class RestApi implements ServiceInterface
{
    public function register(): void
    {
        add_action('rest_api_init', function () {
            (new ProjectStatusController())->register_routes();
            (new ProofingController())->register_routes();
            (new DeliveryController())->register_routes();
            (new TokenController())->register_routes();
            (new JobsController())->register_routes();
            (new WizardController())->register_routes();
            (new BioController())->register_routes();
        });
    }
}
