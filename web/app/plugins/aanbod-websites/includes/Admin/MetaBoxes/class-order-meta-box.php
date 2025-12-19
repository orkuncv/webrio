<?php
/**
 * Order Meta Box Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Order_Meta_Box
{
    /**
     * Register meta box
     */
    public static function register()
    {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_box']);
    }

    /**
     * Add meta box
     */
    public static function add_meta_box()
    {
        add_meta_box(
            'order_details',
            __('Bestelling Details', 'aanbod-websites'),
            [__CLASS__, 'render'],
            'website_order',
            'normal',
            'high'
        );
    }

    /**
     * Render meta box
     */
    public static function render($post)
    {
        $website_id = get_post_meta($post->ID, '_order_website_id', true);
        $package = get_post_meta($post->ID, '_order_package', true);
        $form_data = get_post_meta($post->ID, '_order_form_data', true);
        $selected_extras = get_post_meta($post->ID, '_order_selected_extras', true);
        $total_price = get_post_meta($post->ID, '_order_total_price', true);

        $website = $website_id ? get_post($website_id) : null;
        ?>
        <style>
            .order-details { padding: 10px 0; }
            .order-section { margin-bottom: 25px; padding: 15px; background: #f5f5f5; border-radius: 4px; }
            .order-section h3 { margin-top: 0; }
            .order-field { margin-bottom: 10px; }
            .order-field strong { display: inline-block; width: 150px; }
            .order-price { font-size: 1.5em; color: #2271b1; font-weight: bold; }
        </style>
        <div class="order-details">
            <?php if ($website): ?>
            <div class="order-section">
                <h3><?php _e('Gekozen Website', 'aanbod-websites'); ?></h3>
                <div class="order-field">
                    <strong><?php _e('Website:', 'aanbod-websites'); ?></strong>
                    <a href="<?php echo get_edit_post_link($website_id); ?>" target="_blank">
                        <?php echo esc_html($website->post_title); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($package) && is_array($package)): ?>
            <div class="order-section">
                <h3><?php _e('Gekozen Pakket', 'aanbod-websites'); ?></h3>
                <div class="order-field">
                    <strong><?php _e('Pakket:', 'aanbod-websites'); ?></strong>
                    <?php echo esc_html($package['name']); ?>
                </div>
                <div class="order-field">
                    <strong><?php _e('Prijs:', 'aanbod-websites'); ?></strong>
                    €<?php echo number_format((float)$package['price'], 2, ',', '.'); ?>
                </div>
                <?php if (!empty($package['description'])): ?>
                <div class="order-field">
                    <strong><?php _e('Beschrijving:', 'aanbod-websites'); ?></strong>
                    <?php echo esc_html($package['description']); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($selected_extras) && is_array($selected_extras)): ?>
            <div class="order-section">
                <h3><?php _e('Geselecteerde Extra Opties', 'aanbod-websites'); ?></h3>
                <?php foreach ($selected_extras as $extra): ?>
                <div class="order-field">
                    <strong><?php echo esc_html($extra['name']); ?>:</strong>
                    €<?php echo number_format((float)$extra['price'], 2, ',', '.'); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($total_price): ?>
            <div class="order-section">
                <h3><?php _e('Totaalprijs', 'aanbod-websites'); ?></h3>
                <div class="order-price">€<?php echo number_format((float)$total_price, 2, ',', '.'); ?></div>
            </div>
            <?php endif; ?>

            <?php if (!empty($form_data) && is_array($form_data)): ?>
            <div class="order-section">
                <h3><?php _e('Klantgegevens', 'aanbod-websites'); ?></h3>
                <?php foreach ($form_data as $key => $value): ?>
                <div class="order-field">
                    <strong><?php echo esc_html(ucfirst(str_replace('_', ' ', $key))); ?>:</strong>
                    <?php echo esc_html($value); ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
