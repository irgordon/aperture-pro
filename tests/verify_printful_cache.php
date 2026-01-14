<?php
// tests/verify_printful_cache.php

// Bootstrap WordPress environment
// This is a simplified bootstrap. A real test suite would use a more robust solution.
if (file_exists(__DIR__ . '/../../../../wp-load.php')) {
    require_once __DIR__ . '/../../../../wp-load.php';
} else {
    // Fallback for different environments if needed
    // require_once 'path/to/wp-load.php';
    die("Couldn't load WordPress environment");
}

// Ensure the class is loaded
require_once __DIR__ . '/../src/Support/Cache.php';
require_once __DIR__ . '/../src/Core/BioSettings.php';
require_once __DIR__ . '/../src/Domain/Shop/PrintfulService.php';

// The test
echo "Running Printful Service Cache Verification...\n";

$service = new \AperturePro\Domain\Shop\PrintfulService();

// Clear the cache for a clean test
delete_transient('ap_printful_products');
echo "Cache cleared.\n";

echo "First call to getProducts() - should fetch from API.\n";
// Set a dummy key for the test
\AperturePro\Core\BioSettings::saveSettings(['printfulKey' => 'test-key']);
$products1 = $service->getProducts();

echo "Second call to getProducts() - should fetch from cache.\n";
$products2 = $service->getProducts();

echo "Third call to getProducts() - should also fetch from cache.\n";
$products3 = $service->getProducts();

echo "Verification complete. Check your debug.log for messages.\n";
echo "Expected: 1 'Fetching from API' message, followed by 2 'Fetching from cache' messages.\n";
