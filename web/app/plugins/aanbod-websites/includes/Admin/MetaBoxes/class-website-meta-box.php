<?php
/**
 * Website Meta Box Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Website_Meta_Box
{
    /**
     * Register meta box
     */
    public static function register()
    {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_box']);
        add_action('save_post_website', [__CLASS__, 'save_meta'], 10, 2);
    }

    /**
     * Add meta box
     */
    public static function add_meta_box()
    {
        add_meta_box(
            'website_pricing',
            __('Prijzen', 'aanbod-websites'),
            [__CLASS__, 'render'],
            'website',
            'normal',
            'high'
        );
    }

    /**
     * Render meta box
     */
    public static function render($post)
    {
        wp_nonce_field('website_pricing_nonce', 'website_pricing_nonce');

        $base_price = get_post_meta($post->ID, '_website_base_price', true);
        $extras = get_post_meta($post->ID, '_website_extras', true);

        if (!is_array($extras)) {
            $extras = [];
        }
        ?>
        <div class="website-pricing-fields">
            <style>
                .website-pricing-fields { padding: 10px 0; }
                .pricing-field { margin-bottom: 20px; }
                .pricing-field label { display: block; margin-bottom: 5px; font-weight: 600; }
                .pricing-field input[type="number"] { width: 200px; }
                .extras-list { margin-top: 10px; }
                .extra-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; padding: 10px; background: #f5f5f5; border-radius: 4px; }
                .extra-item input[type="text"] { flex: 1; }
                .extra-item input[type="number"] { width: 150px; }
                .button-remove-extra { color: #b32d2e; }
                .button-remove-extra:hover { color: #dc3232; }
            </style>

            <div class="pricing-field">
                <label for="website_base_price"><?php _e('Basisprijs (€)', 'aanbod-websites'); ?></label>
                <input
                    type="number"
                    id="website_base_price"
                    name="website_base_price"
                    value="<?php echo esc_attr($base_price); ?>"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                />
            </div>

            <div class="pricing-field">
                <label><?php _e('Website-specifieke Extra Opties', 'aanbod-websites'); ?></label>
                <p style="color: #666; font-size: 13px; margin: 5px 0 10px 0;">
                    <?php _e('Deze opties zijn alleen voor deze website. ', 'aanbod-websites'); ?>
                    <a href="<?php echo admin_url('edit.php?post_type=website&page=website-global-extras'); ?>" target="_blank">
                        <?php _e('Beheer globale opties →', 'aanbod-websites'); ?>
                    </a>
                </p>
                <div class="extras-list" id="extras-list">
                    <?php
                    if (!empty($extras)) {
                        foreach ($extras as $index => $extra) {
                            self::render_extra_field($index, $extra);
                        }
                    }
                    ?>
                </div>
                <button type="button" class="button" id="add-extra"><?php _e('Website-specifieke Optie Toevoegen', 'aanbod-websites'); ?></button>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var extraIndex = <?php echo count($extras); ?>;

            $('#add-extra').on('click', function() {
                var html = `
                    <div class="extra-item">
                        <input
                            type="text"
                            name="website_extras[${extraIndex}][name]"
                            placeholder="<?php _e('Naam van extra optie', 'aanbod-websites'); ?>"
                        />
                        <input
                            type="number"
                            name="website_extras[${extraIndex}][price]"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                        />
                        <span>€</span>
                        <button type="button" class="button button-remove-extra">Verwijder</button>
                    </div>
                `;
                $('#extras-list').append(html);
                extraIndex++;
            });

            $(document).on('click', '.button-remove-extra', function() {
                $(this).closest('.extra-item').remove();
            });
        });
        </script>
        <?php
    }

    /**
     * Render extra field
     */
    private static function render_extra_field($index, $extra)
    {
        ?>
        <div class="extra-item">
            <input
                type="text"
                name="website_extras[<?php echo $index; ?>][name]"
                value="<?php echo esc_attr($extra['name'] ?? ''); ?>"
                placeholder="<?php _e('Naam van extra optie', 'aanbod-websites'); ?>"
            />
            <input
                type="number"
                name="website_extras[<?php echo $index; ?>][price]"
                value="<?php echo esc_attr($extra['price'] ?? ''); ?>"
                placeholder="0.00"
                step="0.01"
                min="0"
            />
            <span>€</span>
            <button type="button" class="button button-remove-extra">Verwijder</button>
        </div>
        <?php
    }

    /**
     * Save meta box data
     */
    public static function save_meta($post_id, $post)
    {
        if (!isset($_POST['website_pricing_nonce']) || !wp_verify_nonce($_POST['website_pricing_nonce'], 'website_pricing_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save base price
        if (isset($_POST['website_base_price'])) {
            update_post_meta($post_id, '_website_base_price', sanitize_text_field($_POST['website_base_price']));
        }

        // Save extras
        if (isset($_POST['website_extras']) && is_array($_POST['website_extras'])) {
            $extras = [];
            foreach ($_POST['website_extras'] as $extra) {
                if (!empty($extra['name'])) {
                    $extras[] = [
                        'name' => sanitize_text_field($extra['name']),
                        'price' => sanitize_text_field($extra['price'] ?? '0'),
                    ];
                }
            }
            update_post_meta($post_id, '_website_extras', $extras);
        } else {
            delete_post_meta($post_id, '_website_extras');
        }
    }
}
