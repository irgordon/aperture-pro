<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="ap-shell">
    <header class="ap-header sticky">
        <div class="ap-logo">
            <h1>
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    Ian Gordon | Portrait & Street
                </a>
            </h1>
        </div>

        <nav class="ap-nav">
            <a href="#portfolio">Portfolio</a>
            <a href="<?php echo esc_url(home_url('/client-portal')); ?>" class="ap-login-link">Client Portal</a>
            <a href="#contact" class="ap-cta-header">Book Session</a>
        </nav>

        <button class="ap-hamburger" aria-label="Open Menu">
            <span></span><span></span><span></span>
        </button>
    </header>
    <main class="ap-main">
