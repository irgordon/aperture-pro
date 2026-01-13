<?php
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

// Mock WordPress functions
function sanitize_text_field($str) { return trim($str); }
function wp_unslash($str) { return $str; }
function wp_generate_password($len) { return bin2hex(random_bytes($len/2)); }
// gmdate is internal, don't redefine
function sanitize_textarea_field($str) { return $str; }
define('WEEK_IN_SECONDS', 604800);

// Mock DB
class MockDB {
    public $prefix = 'wp_';
    public $queries = [];

    public function prepare($query, ...$args) {
        // Simple placeholder replacement for testing
        foreach($args as $arg) {
            $query = preg_replace('/%[ds]/', is_numeric($arg) ? $arg : "'$arg'", $query, 1);
        }
        return $query;
    }

    public function replace($table, $data, $format) {
        $this->queries[] = "REPLACE INTO $table " . json_encode($data);
    }

    public function query($query) {
        $this->queries[] = $query;
    }

    public function get_results($query) {
        // Return dummy data for counts
        if (strpos($query, 'COUNT(*)') !== false) {
             $obj1 = new stdClass(); $obj1->status = 'approved'; $obj1->count = 5;
             $obj2 = new stdClass(); $obj2->status = 'rejected'; $obj2->count = 2;
             return [$obj1, $obj2];
        }
        return [];
    }
}
$wpdb = new MockDB();
function current_time($type) { return date('Y-m-d H:i:s'); }
function get_post_meta($id, $key, $single) { return ''; } // default
function ap_get_project_images($id) {
    // Return 10 dummy images
    $imgs = [];
    for($i=0; $i<10; $i++) {
        $o = new stdClass(); $o->ID = $i;
        $imgs[] = $o;
    }
    return $imgs;
}

// Load classes
require_once __DIR__ . '/../src/Domain/Tokens/TokenService.php';
require_once __DIR__ . '/../src/Domain/Tokens/TokenRepository.php';
require_once __DIR__ . '/../src/Domain/Tokens/TokenTypes.php';
require_once __DIR__ . '/../src/Domain/Tokens/TokenValidator.php';
require_once __DIR__ . '/../src/Domain/Logs/Logger.php';
require_once __DIR__ . '/../src/Domain/Proofing/ProofingRepository.php';
require_once __DIR__ . '/../src/Domain/Proofing/ProofingState.php';

// Test Token Extraction
$_GET['token'] = 'test_token_123';
$extracted = \AperturePro\Domain\Tokens\TokenService::extractToken();
if ($extracted !== 'test_token_123') {
    echo "FAIL: Token extraction from GET failed.\n";
    exit(1);
}

unset($_GET['token']);
$_SERVER['HTTP_X_APERTURE_TOKEN'] = 'header_token_456';
$extractedHeader = \AperturePro\Domain\Tokens\TokenService::extractToken();
if ($extractedHeader !== 'header_token_456') {
    echo "FAIL: Token extraction from Header failed.\n";
    exit(1);
}

echo "PASS: Token extraction works.\n";

// Test Proofing Repository SQL generation (via Mock DB)
\AperturePro\Domain\Proofing\ProofingRepository::updateImageStatus(1, 101, 'approved', 'Nice photo');
$lastQuery = end($wpdb->queries);
// It should use INSERT INTO ... ON DUPLICATE KEY UPDATE
if (strpos($lastQuery, 'INSERT INTO wp_ap_proofing') === false || strpos($lastQuery, 'ON DUPLICATE KEY UPDATE') === false) {
    echo "FAIL: ProofingRepository did not use proper INSERT ... ON DUPLICATE KEY UPDATE.\n";
    echo "Query: $lastQuery\n";
    exit(1);
}
echo "PASS: ProofingRepository uses custom table.\n";

// Test Proofing Counts
$counts = \AperturePro\Domain\Proofing\ProofingRepository::getCounts(1);
// We mocked 5 approved, 2 rejected. Total images 10.
// So pending should be 10 - (5+2) = 3.
if ($counts['approved'] === 5 && $counts['rejected'] === 2 && $counts['pending'] === 3) {
    echo "PASS: Proofing counts calculation is correct.\n";
} else {
    echo "FAIL: Proofing counts calculation failed.\n";
    print_r($counts);
    exit(1);
}

echo "ALL CHECKS PASSED\n";
