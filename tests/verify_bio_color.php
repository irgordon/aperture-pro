<?php

// Bootstrap WordPress
require_once getenv('WP_LOAD_PATH') ?? '../../../wp-load.php';

use AperturePro\Core\BioSettings;
use AperturePro\Client\BioPage;

// Mock the query var for the Bio Page
set_query_var('ap_bio', true);

// Update the primary color to a test value
$test_color = '#ff0000';
BioSettings::updateSettings(['primaryColor' => $test_color]);

// Capture the enqueued styles
ob_start();
wp_head();
$wp_head_output = ob_get_clean();

// Check if the inline style is present
$expected_style = ":root { --ap-bio-primary: {$test_color}; }";
if (strpos($wp_head_output, $expected_style) !== false) {
    echo "SUCCESS: Bio primary color is correctly set in inline styles.\n";
} else {
    echo "FAILURE: Bio primary color is not set correctly.\n";
    echo "Expected: {$expected_style}\n";
    echo "Got: {$wp_head_output}\n";
    exit(1);
}

// Restore the original color
BioSettings::updateSettings(['primaryColor' => '#0073aa']);

exit(0);
