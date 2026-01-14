<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

    <header>
        <div class="container nav-flex">
            <a href="<?php echo esc_url(home_url('/')); ?>" onclick="event.preventDefault(); closeModal(); window.scrollTo(0,0);" aria-label="Home" class="logo-block">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                    <circle cx="12" cy="13" r="4"/>
                </svg>
                <div class="logo-text">
                    <span class="logo-title">Ian Gordon</span>
                    <span class="logo-subtitle">Photography</span>
                </div>
            </a>
            <button class="ap-hamburger" aria-label="Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="nav-actions">
                <a href="<?php echo esc_url(home_url('/portal')); ?>" class="btn btn-secondary btn-header mr-1">Client Login</a>
                <a href="#contact" onclick="closeModal()" class="btn btn-primary btn-header">Book Session</a>
            </div>
        </div>
    </header>

    <main id="main-content">
