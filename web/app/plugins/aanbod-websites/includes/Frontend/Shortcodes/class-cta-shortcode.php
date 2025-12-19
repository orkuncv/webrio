<?php
/**
 * CTA Shortcode Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_CTA_Shortcode
{
    /**
     * Register the shortcode
     */
    public static function register()
    {
        add_shortcode('website_cta', [__CLASS__, 'render']);
    }

    /**
     * Render the CTA shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public static function render($atts)
    {
        global $post;

        $atts = shortcode_atts([
            'text' => __('Website Bestellen', 'aanbod-websites'),
            'checkout_page' => 'checkout',
            'website_id' => 0,
        ], $atts);

        // Get post ID from attribute, current post, or get_the_ID()
        $post_id = $atts['website_id'] ? intval($atts['website_id']) : ($post->ID ?? get_the_ID());

        if (!$post_id || get_post_type($post_id) !== 'website') {
            return '';
        }

        $checkout_url = home_url('/' . $atts['checkout_page'] . '/');

        return sprintf(
            '<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)"><div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button website-cta-button" href="%s" data-website-id="%s" data-checkout-url="%s">%s</a></div></div>',
            esc_url($checkout_url),
            esc_attr($post_id),
            esc_url($checkout_url),
            esc_html($atts['text'])
        );
    }
}
