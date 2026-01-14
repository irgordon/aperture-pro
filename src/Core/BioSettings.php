<?php

namespace AperturePro\Core;

class BioSettings
{
    const OPTION_BIO_LINKS = 'ap_bio_links';
    const OPTION_BIO_DONATION_ENABLED = 'ap_bio_donation_enabled';
    const OPTION_BIO_DONATION_LINK = 'ap_bio_donation_link';
    const OPTION_BIO_SHOP_ENABLED = 'ap_bio_shop_enabled';
    const OPTION_BIO_PRINTFUL_KEY = 'ap_bio_printful_key';
    const OPTION_BIO_SOCIALS = 'ap_bio_socials';
    const OPTION_BIO_PROFILE_IMAGE = 'ap_bio_profile_image';
    const OPTION_BIO_NAME = 'ap_bio_name';
    const OPTION_BIO_DESCRIPTION = 'ap_bio_description';

    public static function getSettings(): array
    {
        return [
            'profileImage' => get_option(self::OPTION_BIO_PROFILE_IMAGE, ''),
            'name' => get_option(self::OPTION_BIO_NAME, ''),
            'description' => get_option(self::OPTION_BIO_DESCRIPTION, ''),
            'links' => get_option(self::OPTION_BIO_LINKS, []),
            'donationEnabled' => (bool) get_option(self::OPTION_BIO_DONATION_ENABLED, false),
            'donationLink' => get_option(self::OPTION_BIO_DONATION_LINK, ''),
            'shopEnabled' => (bool) get_option(self::OPTION_BIO_SHOP_ENABLED, false),
            'printfulKey' => get_option(self::OPTION_BIO_PRINTFUL_KEY, ''),
            'socials' => get_option(self::OPTION_BIO_SOCIALS, [
                'facebook' => '',
                'instagram' => '',
                'youtube' => '',
                '500px' => ''
            ]),
        ];
    }

    public static function updateSettings(array $settings): void
    {
        if (isset($settings['profileImage'])) {
            update_option(self::OPTION_BIO_PROFILE_IMAGE, esc_url_raw($settings['profileImage']));
        }
        if (isset($settings['name'])) {
            update_option(self::OPTION_BIO_NAME, sanitize_text_field($settings['name']));
        }
        if (isset($settings['description'])) {
            update_option(self::OPTION_BIO_DESCRIPTION, sanitize_textarea_field($settings['description']));
        }
        if (isset($settings['links']) && is_array($settings['links'])) {
            $cleanLinks = array_map(function ($link) {
                return [
                    'label' => sanitize_text_field($link['label'] ?? ''),
                    'url' => esc_url_raw($link['url'] ?? ''),
                    'icon' => sanitize_text_field($link['icon'] ?? ''), // generic icon class or url
                    'thumbnail' => esc_url_raw($link['thumbnail'] ?? ''),
                ];
            }, $settings['links']);
            update_option(self::OPTION_BIO_LINKS, $cleanLinks);
        }

        if (isset($settings['donationEnabled'])) {
            update_option(self::OPTION_BIO_DONATION_ENABLED, (bool) $settings['donationEnabled']);
        }
        if (isset($settings['donationLink'])) {
            update_option(self::OPTION_BIO_DONATION_LINK, esc_url_raw($settings['donationLink']));
        }

        if (isset($settings['shopEnabled'])) {
            update_option(self::OPTION_BIO_SHOP_ENABLED, (bool) $settings['shopEnabled']);
        }
        if (isset($settings['printfulKey'])) {
            update_option(self::OPTION_BIO_PRINTFUL_KEY, sanitize_text_field($settings['printfulKey']));
        }

        if (isset($settings['socials']) && is_array($settings['socials'])) {
            $cleanSocials = [
                'facebook' => esc_url_raw($settings['socials']['facebook'] ?? ''),
                'instagram' => esc_url_raw($settings['socials']['instagram'] ?? ''),
                'youtube' => esc_url_raw($settings['socials']['youtube'] ?? ''),
                '500px' => esc_url_raw($settings['socials']['500px'] ?? ''),
            ];
            update_option(self::OPTION_BIO_SOCIALS, $cleanSocials);
        }
    }
}
