<?php

namespace AperturePro\Admin;

class WizardScreen
{
    public static function render(): void
    {
        ?>
        <div id="ap-wizard-app" class="ap-wizard-wrap">
            <div class="ap-wizard-header">
                <h1>Welcome to Aperture Pro</h1>
                <p>Let's get your studio set up in a few simple steps.</p>
            </div>

            <div id="ap-wizard-stepper">
                <!-- Stepper JS will inject here -->
            </div>

            <div id="ap-wizard-content" class="ap-wizard-card">
                <!-- Step content will inject here -->
            </div>
        </div>
        <?php
    }
}
