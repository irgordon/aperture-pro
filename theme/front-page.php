<?php
/**
 * Front Page Template for Aperture Pro Studio Theme
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<div id="aperture-pro-studio-app">

    <!-- ===========================
         Hero Masonry Gallery
    ============================ -->
    <section class="ap-hero-gallery" aria-labelledby="gallery-heading">
        <h2 id="gallery-heading" class="ap-hero-title">
            Capturing the Soul of the Street & Studio
        </h2>

        <div class="ap-masonry-wrapper">
            <?php echo do_shortcode('[ap_masonry_gallery limit="12" columns="3"]'); ?>
        </div>

        <button class="ap-btn-primary ap-view-portfolio" data-open-portfolio>
            View Full Portfolio
        </button>
    </section>


    <!-- ===========================
         Fullscreen Portfolio Modal
    ============================ -->
    <div id="ap-portfolio-modal"
         class="ap-modal"
         aria-hidden="true"
         role="dialog"
         aria-labelledby="portfolio-title">

        <div class="ap-modal-content">
            <button class="ap-modal-close" aria-label="Close Portfolio">&times;</button>

            <h2 id="portfolio-title">Portfolio</h2>

            <div class="ap-portfolio-grid">
                <?php echo do_shortcode('[ap_masonry_gallery limit="20" columns="5" ratio="4x5"]'); ?>
            </div>
        </div>
    </div>


    <!-- ===========================
         Wide CTA Section
    ============================ -->
    <section class="ap-wide-cta" aria-labelledby="cta-heading">
        <h2 id="cta-heading">Ready to Frame Your Story?</h2>

        <a href="#contact" class="ap-btn-primary ap-cta-large">
            Book Now
        </a>
    </section>


    <!-- ===========================
         Contact Section (Optional)
    ============================ -->
    <section id="contact" class="ap-section">
        <h2 class="ap-section-title">Let’s Create Art Together</h2>
        <p class="ap-section-subtitle">
            Share a few details and we’ll follow up within one business day.
        </p>

        <div class="ap-contact-card">
            <?php
            // Replace with your preferred form plugin
            echo do_shortcode('[contact-form-7 id="123" title="Studio Contact"]');
            ?>
        </div>
    </section>


    <!-- ===========================
         Footer
    ============================ -->
    <footer class="ap-footer">
        <div class="ap-social-icons">

            <a href="https://instagram.com/yourstudio" aria-label="Instagram">
                <?php include get_template_directory() . '/assets/icons/instagram.svg'; ?>
            </a>

            <a href="https://facebook.com/yourstudio" aria-label="Facebook">
                <?php include get_template_directory() . '/assets/icons/facebook.svg'; ?>
            </a>

            <a href="https://tiktok.com/@yourstudio" aria-label="TikTok">
                <?php include get_template_directory() . '/assets/icons/tiktok.svg'; ?>
            </a>

        </div>

        <button class="ap-terms-link" data-open-terms>
            Terms & Conditions
        </button>
    </footer>


    <!-- ===========================
         Terms & Conditions Modal
    ============================ -->
    <div id="ap-terms-modal"
         class="ap-modal"
         aria-hidden="true"
         role="dialog"
         aria-labelledby="terms-title">

        <div class="ap-modal-content">
            <button class="ap-modal-close" aria-label="Close Terms">&times;</button>

            <h2 id="terms-title">Terms & Conditions</h2>

            <div class="ap-terms-body">
                <?php echo wpautop(get_option('ap_terms_content')); ?>
            </div>
        </div>
    </div>

</div><!-- #aperture-pro-studio-app -->

<?php
get_footer();
