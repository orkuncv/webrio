<?php
/**
 * Assets Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Assets
{
    /**
     * Register assets
     */
    public static function register()
    {
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_frontend_assets']);
    }

    /**
     * Enqueue frontend assets
     */
    public static function enqueue_frontend_assets()
    {
        $plugin_url = plugin_dir_url(dirname(dirname(__FILE__)));

        // Enqueue CTA assets on all pages (since shortcode can be in patterns)
        wp_enqueue_style('aanbod-websites-cta', $plugin_url . 'assets/cta.css', [], '1.0.0');
        wp_enqueue_script('aanbod-websites-cta', $plugin_url . 'assets/cta.js', ['jquery'], '1.0.0', true);
        wp_localize_script('aanbod-websites-cta', 'aanbodWebsitesCTA', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);

        // Enqueue packages assets on all pages (since shortcode can be anywhere)
        wp_enqueue_script('aanbod-websites-packages', $plugin_url . 'assets/packages.js', ['jquery'], '1.0.0', true);
        wp_localize_script('aanbod-websites-packages', 'aanbodWebsitesPackages', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
        ]);

        // Enqueue checkout assets only on checkout page
        if (has_shortcode(get_post()->post_content ?? '', 'website_checkout')) {
            wp_enqueue_style('aanbod-websites-checkout', $plugin_url . 'assets/checkout.css', [], '1.0.0');
            wp_enqueue_script('aanbod-websites-checkout', $plugin_url . 'assets/checkout.js', ['jquery'], '1.0.0', true);

            $selected_package_index = isset($_SESSION['selected_package_index']) ? intval($_SESSION['selected_package_index']) : -1;

            wp_localize_script('aanbod-websites-checkout', 'aanbodWebsites', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('website_order_nonce'),
                'preselectedPackage' => $selected_package_index,
            ]);
        }
    }
}
