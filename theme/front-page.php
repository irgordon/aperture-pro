<?php
if (!defined('ABSPATH')) {
    exit;
}
get_header();
?>
<div id="aperture-pro-studio-app">
    <section class="ap-hero">
        <div>
            <h1 class="ap-hero-title">
                Cinematic photography<br>for modern brands & couples.
            </h1>
            <p class="ap-hero-subtitle">
                From intimate weddings to bold commercial campaigns, we craft images that feel like memories from the future.
            </p>
            <div class="ap-hero-cta">
                <a href="#contact" class="ap-btn-primary">Book a consultation</a>
                <a href="#portfolio" class="ap-btn-secondary">View portfolio</a>
            </div>
        </div>
        <div class="ap-hero-media">
            <div class="ap-hero-media-inner">
                Powered by Aperture Pro — seamless proofing & delivery for every shoot.
            </div>
        </div>
    </section>

    <section id="services" class="ap-section">
        <div class="ap-section-header">
            <h2 class="ap-section-title">Services</h2>
            <p class="ap-section-subtitle">Tailored coverage for every story.</p>
        </div>
        <div class="ap-projects-grid">
            <div class="ap-project-card">
                <div class="ap-project-body">
                    <div class="ap-project-title">Weddings & Elopements</div>
                    <div class="ap-project-meta">Full‑day coverage, multi‑day events, destination stories.</div>
                </div>
            </div>
            <div class="ap-project-card">
                <div class="ap-project-body">
                    <div class="ap-project-title">Brand & Editorial</div>
                    <div class="ap-project-meta">Campaigns, lookbooks, founder portraits, product stories.</div>
                </div>
            </div>
            <div class="ap-project-card">
                <div class="ap-project-body">
                    <div class="ap-project-title">Families & Milestones</div>
                    <div class="ap-project-meta">Lifestyle sessions, newborn, anniversaries, legacy albums.</div>
                </div>
            </div>
        </div>
    </section>

    <section id="portfolio" class="ap-section">
        <div class="ap-section-header">
            <h2 class="ap-section-title">Featured Projects</h2>
            <p class="ap-section-subtitle">Curated sessions pulled live from our Aperture Pro backend.</p>
        </div>
        <div id="ap-studio-featured-projects" class="ap-projects-grid">
            <!-- studio-app.js will hydrate this via REST -->
        </div>
    </section>

    <section id="contact" class="ap-section">
        <div class="ap-section-header">
            <h2 class="ap-section-title">Let’s make something unforgettable.</h2>
            <p class="ap-section-subtitle">Share a few details and we’ll follow up within one business day.</p>
        </div>
        <div class="ap-project-card">
            <div class="ap-project-body">
                <?php
                // You can swap this for Gravity Forms, WPForms, etc.
                echo do_shortcode('[contact-form-7 id="123" title="Studio Contact"]');
                ?>
            </div>
        </div>
    </section>
</div>
<?php
get_footer();
