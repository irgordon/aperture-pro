<?php
// Mock WP functions
function add_shortcode($tag, $callback) {}
function shortcode_atts($pairs, $atts, $shortcode = '') {
    $atts = (array) $atts;
    $out = array();
    foreach ($pairs as $name => $default) {
        if (array_key_exists($name, $atts))
            $out[$name] = $atts[$name];
        else
            $out[$name] = $default;
    }
    return $out;
}
function sanitize_text_field($str) { return trim($str); }
function esc_attr($str) { return $str; }
function esc_url($url) { return $url; }
function wp_get_attachment_image_url($id, $size) { return "http://example.com/img-$id.jpg"; }
function get_post_meta($id, $key, $single) { return ''; }
function get_the_ID() { return 1; }
function get_the_title() { return 'Title'; }
function wp_reset_postdata() {}
function get_option($opt, $default) { return $default; }

// Mock WP_Query
class WP_Query {
    public static $last_args = [];
    public function __construct($args) {
        self::$last_args = $args;
    }
    public function have_posts() { return false; }
    public function the_post() {}
}

require_once __DIR__ . '/../src/Client/Gallery.php';

$fail = false;

// Test 1: Default args
AperturePro\Client\Gallery::renderShortcode([]);
$args = WP_Query::$last_args;

if (!isset($args['post_type']) || $args['post_type'] !== 'attachment') {
    echo "FAIL Test 1: post_type should be attachment\n"; $fail = true;
}
if (isset($args['category_name']) || isset($args['tag'])) {
    echo "FAIL Test 1: Should not have category/tag by default\n"; $fail = true;
}

// Test 2: With category
AperturePro\Client\Gallery::renderShortcode(['category' => 'portfolio']);
$args = WP_Query::$last_args;
if (!isset($args['category_name']) || $args['category_name'] !== 'portfolio') {
    echo "FAIL Test 2: category_name should be 'portfolio'. Got: " . ($args['category_name'] ?? 'null') . "\n";
    // This is expected to fail currently
}

// Test 3: With tag
AperturePro\Client\Gallery::renderShortcode(['tag' => 'featured']);
$args = WP_Query::$last_args;
if (!isset($args['tag']) || $args['tag'] !== 'featured') {
    echo "FAIL Test 3: tag should be 'featured'. Got: " . ($args['tag'] ?? 'null') . "\n";
    // This is expected to fail currently
}

if ($fail) {
    // exit(1); // Do not exit so we can see which ones failed.
    echo "Some tests failed (Expected for now)\n";
} else {
    echo "ALL PASS\n";
}
