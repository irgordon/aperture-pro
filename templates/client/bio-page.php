<?php
/**
 * Bio Page Template
 */

use AperturePro\Core\BioSettings;
use AperturePro\Domain\Shop\PrintfulService;

$settings = BioSettings::getSettings();
$products = [];
if ($settings['shopEnabled']) {
    $products = (new PrintfulService())->getProducts();
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($settings['name'] ?: 'Link In Bio'); ?></title>
    <?php wp_head(); ?>
    <style>
        /* Inline critical CSS or reset if needed, but bio.css should handle it */
        body { margin: 0; background: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
    </style>
</head>
<body class="ap-bio-page">

<div class="ap-bio-container">

    <!-- Profile -->
    <header class="ap-bio-header">
        <?php if ($settings['profileImage']): ?>
            <img src="<?php echo esc_url($settings['profileImage']); ?>" alt="Profile" class="ap-bio-avatar">
        <?php else: ?>
            <div class="ap-bio-avatar-placeholder"></div>
        <?php endif; ?>

        <?php if ($settings['name']): ?>
            <h1 class="ap-bio-name"><?php echo esc_html($settings['name']); ?></h1>
        <?php endif; ?>

        <?php if ($settings['description']): ?>
            <p class="ap-bio-desc"><?php echo nl2br(esc_html($settings['description'])); ?></p>
        <?php endif; ?>
    </header>

    <!-- Links -->
    <?php if (!empty($settings['links'])): ?>
    <div class="ap-bio-links">
        <?php foreach ($settings['links'] as $link): ?>
            <a href="<?php echo esc_url($link['url']); ?>" class="ap-bio-link-card" target="_blank" rel="noopener">
                <?php if (!empty($link['thumbnail'])): ?>
                    <img src="<?php echo esc_url($link['thumbnail']); ?>" class="ap-bio-link-thumb" alt="">
                <?php elseif (!empty($link['icon'])): ?>
                    <i class="<?php echo esc_attr($link['icon']); ?> ap-bio-link-icon"></i>
                <?php endif; ?>
                <span class="ap-bio-link-text"><?php echo esc_html($link['label']); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Donation -->
    <?php if ($settings['donationEnabled']): ?>
    <div class="ap-bio-module ap-bio-donation">
        <h2>Support My Work</h2>
        <div class="ap-bio-donation-box">
             <p>If you love what I do, consider supporting me!</p>
             <form class="ap-donation-form">
                 <div class="ap-donation-presets">
                     <button type="button" class="ap-donate-btn" data-amount="5">$5</button>
                     <button type="button" class="ap-donate-btn" data-amount="10">$10</button>
                     <button type="button" class="ap-donate-btn" data-amount="20">$20</button>
                 </div>
                 <?php if ($settings['donationLink']): ?>
                    <a href="<?php echo esc_url($settings['donationLink']); ?>" class="ap-bio-main-btn" target="_blank">Donate</a>
                 <?php else: ?>
                    <button type="button" class="ap-bio-main-btn">Donate</button>
                 <?php endif; ?>
             </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Shop -->
    <?php if ($settings['shopEnabled'] && !empty($products)): ?>
    <div class="ap-bio-module ap-bio-shop">
        <h2>Shop</h2>
        <div class="ap-bio-shop-grid">
            <?php foreach ($products as $product): ?>
                <a href="<?php echo esc_url($product['url']); ?>" class="ap-bio-product-card" target="_blank">
                    <div class="ap-bio-product-img" style="background-image: url('<?php echo esc_url($product['image']); ?>');"></div>
                    <div class="ap-bio-product-info">
                        <span class="ap-bio-product-name"><?php echo esc_html($product['name']); ?></span>
                        <span class="ap-bio-product-price"><?php echo esc_html($product['price']); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Socials -->
    <footer class="ap-bio-footer">
        <div class="ap-bio-socials">
            <?php if (!empty($settings['socials']['facebook'])): ?>
                <a href="<?php echo esc_url($settings['socials']['facebook']); ?>" target="_blank"><img src="https://cdn.simpleicons.org/facebook/333" alt="Facebook"></a>
            <?php endif; ?>
            <?php if (!empty($settings['socials']['instagram'])): ?>
                <a href="<?php echo esc_url($settings['socials']['instagram']); ?>" target="_blank"><img src="https://cdn.simpleicons.org/instagram/333" alt="Instagram"></a>
            <?php endif; ?>
            <?php if (!empty($settings['socials']['youtube'])): ?>
                <a href="<?php echo esc_url($settings['socials']['youtube']); ?>" target="_blank"><img src="https://cdn.simpleicons.org/youtube/333" alt="YouTube"></a>
            <?php endif; ?>
            <?php if (!empty($settings['socials']['500px'])): ?>
                <a href="<?php echo esc_url($settings['socials']['500px']); ?>" target="_blank"><img src="https://cdn.simpleicons.org/500px/333" alt="500px"></a>
            <?php endif; ?>
        </div>
        <div class="ap-bio-credit">
            Powered by Aperture Pro
        </div>
    </footer>

</div>

<?php wp_footer(); ?>
</body>
</html>
