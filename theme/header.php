<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ian Gordon Photography: Premier freelance photographer in Arlington, VA and DC. Specializing in authentic street, portrait, boudoir, and headshot photography.">
    <meta name="keywords" content="Arlington Photographer, DC Headshots, National Landing Street Photography, Personal Branding DMV, Ian Gordon, Boudoir DC">
    <meta name="author" content="Ian Gordon Photography LLC">

    <meta property="og:title" content="Ian Gordon Photography | Arlington, VA & DC Metro">
    <meta property="og:description" content="Capturing the soul of the DMV. Book your portrait or street session today.">
    <meta property="og:image" content="https://images.unsplash.com/photo-1554048612-387768052bf7?auto=format&fit=crop&w=1200&q=80">
    <meta property="og:url" content="https://iangordon.pro/">

    <link rel="canonical" href="https://iangordon.pro/" />

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Ian Gordon Photography",
      "image": "https://images.unsplash.com/photo-1554048612-387768052bf7?auto=format&fit=crop&w=1200&q=80",
      "description": "Premier freelance photographer in Arlington, VA and DC specializing in authentic street, portrait, and boudoir photography.",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Arlington",
        "addressRegion": "VA",
        "addressCountry": "US"
      },
      "priceRange": "$$",
      "telephone": "+1-555-555-5555",
      "url": "https://iangordon.pro"
    }
    </script>

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
            <a href="#contact" onclick="closeModal()" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 1rem;">Book Session</a>
        </div>
    </header>

    <main id="main-content">
