<?php
/**
 * Checkout Shortcode Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Checkout_Shortcode
{
    /**
     * Register the shortcode
     */
    public static function register()
    {
        add_shortcode('website_checkout', [__CLASS__, 'render']);
    }

    /**
     * Render the checkout shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public static function render($atts)
    {
        // Get website ID from session
        $website_id = isset($_SESSION['selected_website_id']) ? intval($_SESSION['selected_website_id']) : 0;

        if (!$website_id || get_post_type($website_id) !== 'website') {
            return '<div class="no-website-selected">
                <p>' . __('Geen website geselecteerd. Kies eerst een website om te bestellen.', 'aanbod-websites') . '</p>
            </div>';
        }

        $website = get_post($website_id);
        $packages = get_option('website_packages', []);
        $global_extras = get_option('website_global_extras', []);
        $custom_extras = get_post_meta($website_id, '_website_extras', true);
        $checkout_fields = get_option('website_checkout_fields', []);

        // Combine global and custom extras
        $has_extras = (!empty($global_extras) && is_array($global_extras)) || (!empty($custom_extras) && is_array($custom_extras));

        ob_start();
        ?>
        <div class="website-checkout-wrapper">
            <div class="checkout-container">
                <!-- LEFT COLUMN: Product Showcase -->
                <div class="checkout-left">
                    <h2><?php _e('Je Keuze', 'aanbod-websites'); ?></h2>

                    <div class="product-showcase">
                        <?php if (has_post_thumbnail($website_id)): ?>
                            <div class="website-image">
                                <?php echo get_the_post_thumbnail($website_id, 'medium'); ?>
                            </div>
                        <?php endif; ?>

                        <h3><?php echo esc_html($website->post_title); ?></h3>
                        <?php if ($website->post_excerpt): ?>
                            <div class="website-excerpt">
                                <?php echo wp_kses_post($website->post_excerpt); ?>
                            </div>
                        <?php endif; ?>

                        <button type="button" class="button-change-website" id="change-website-btn">
                            <?php _e('Website Wijzigen', 'aanbod-websites'); ?>
                        </button>
                    </div>

                    <script>
                    jQuery(document).ready(function($) {
                        $('#change-website-btn').on('click', function() {
                            if (confirm('<?php _e('Weet je zeker dat je de geselecteerde website wilt wijzigen? Je selecties worden gewist.', 'aanbod-websites'); ?>')) {
                                $.ajax({
                                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                    type: 'POST',
                                    data: {
                                        action: 'clear_selected_website'
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            window.location.href = '<?php echo home_url('/aanbod/'); ?>';
                                        }
                                    }
                                });
                            }
                        });
                    });
                    </script>
                </div>
                <!-- END LEFT COLUMN -->

                <!-- RIGHT COLUMN: Checkout Form -->
                <div class="checkout-right">
                    <h2><?php _e('Start nu je bestelling', 'aanbod-websites'); ?></h2>
                    <p class="subtitle"><?php echo esc_html($website->post_title); ?> - Vul je gegevens in</p>

                    <?php if (!empty($packages)): ?>
                    <div class="package-selection-section">
                        <p class="section-title"><?php _e('Kies een Pakket', 'aanbod-websites'); ?> <span class="required">*</span></p>
                        <p class="section-description"><?php _e('Selecteer het pakket dat bij je past', 'aanbod-websites'); ?></p>

                        <div class="packages-grid">
                            <?php foreach ($packages as $index => $package): ?>
                            <label class="package-option" data-package-index="<?php echo $index; ?>">
                                <input
                                    type="radio"
                                    name="selected_package"
                                    value="<?php echo $index; ?>"
                                    data-price="<?php echo esc_attr($package['price']); ?>"
                                    data-name="<?php echo esc_attr($package['name']); ?>"
                                    required
                                />
                                <div class="package-option-content">
                                    <h4><?php echo esc_html($package['name']); ?></h4>
                                    <div class="package-option-price">€</div>
                                    <?php if (!empty($package['description'])): ?>
                                    <p class="package-option-desc"><?php echo esc_html(wp_trim_words($package['description'], 15)); ?></p>
                                    <?php endif; ?>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($has_extras): ?>
                    <div class="extras-selection">
                        <p><?php _e('Extra Opties', 'aanbod-websites'); ?></p>

                        <?php if (!empty($global_extras) && is_array($global_extras)): ?>
                            <?php foreach ($global_extras as $index => $extra): ?>
                            <label class="extra-option">
                                <input
                                    type="checkbox"
                                    name="extras[]"
                                    value="global_<?php echo $index; ?>"
                                    data-price="<?php echo esc_attr($extra['price']); ?>"
                                    data-name="<?php echo esc_attr($extra['name']); ?>"
                                    data-type="global"
                                />
                                <span class="extra-name"><?php echo esc_html($extra['name']); ?></span>
                                <span class="extra-price">€<?php echo number_format((float)$extra['price'], 2, ',', '.'); ?></span>
                            </label>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!empty($custom_extras) && is_array($custom_extras)): ?>
                            <?php if (!empty($global_extras)): ?>
                                <div style="margin: 15px 0; padding-top: 15px; border-top: 1px solid #ddd;">
                                    <strong style="display: block; margin-bottom: 10px; color: #666; font-size: 14px;">
                                        <?php _e('Website-specifieke opties:', 'aanbod-websites'); ?>
                                    </strong>
                                </div>
                            <?php endif; ?>
                            <?php foreach ($custom_extras as $index => $extra): ?>
                            <label class="extra-option">
                                <input
                                    type="checkbox"
                                    name="extras[]"
                                    value="custom_<?php echo $index; ?>"
                                    data-price="<?php echo esc_attr($extra['price']); ?>"
                                    data-name="<?php echo esc_attr($extra['name']); ?>"
                                    data-type="custom"
                                />
                                <span class="extra-name"><?php echo esc_html($extra['name']); ?></span>
                                <span class="extra-price">€<?php echo number_format((float)$extra['price'], 2, ',', '.'); ?></span>
                            </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($checkout_fields)): ?>
                    <div class="checkout-form-section">
                        <p><?php _e('Uw Gegevens', 'aanbod-websites'); ?></p>
                        <form id="website-checkout-form">
                            <input type="hidden" name="website_id" value="<?php echo $website_id; ?>">
                            <input type="hidden" name="action" value="submit_website_order">
                            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('website_order_nonce'); ?>">

                            <div class="form-fields-grid">
                                <?php foreach ($checkout_fields as $index => $field):
                                    $width = isset($field['width']) ? $field['width'] : '100';
                                    $span = $width == '50' ? '6' : '12';
                                ?>
                                <div class="form-field col-span-<?php echo $span; ?>">
                                    <label for="field_<?php echo $index; ?>">
                                        <?php echo esc_html($field['label']); ?>
                                        <?php if ($field['required']): ?>
                                            <span class="required">*</span>
                                        <?php endif; ?>
                                    </label>

                                    <?php if ($field['type'] === 'textarea'): ?>
                                        <textarea
                                            id="field_<?php echo $index; ?>"
                                            name="form_fields[<?php echo sanitize_key($field['label']); ?>]"
                                            placeholder="<?php echo esc_attr($field['placeholder']); ?>"
                                            <?php echo $field['required'] ? 'required' : ''; ?>
                                        ></textarea>
                                    <?php else: ?>
                                        <input
                                            type="<?php echo esc_attr($field['type']); ?>"
                                            id="field_<?php echo $index; ?>"
                                            name="form_fields[<?php echo sanitize_key($field['label']); ?>]"
                                            placeholder="<?php echo esc_attr($field['placeholder']); ?>"
                                            <?php echo $field['required'] ? 'required' : ''; ?>
                                        />
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="button-submit"><?php _e('Bestelling Plaatsen', 'aanbod-websites'); ?></button>
                        </form>
                        <div id="form-messages"></div>
                    </div>
                    <?php endif; ?>

                    <!-- Order Summary (inside right column) -->
                    <div class="order-summary">
                        <h3><?php _e('Besteloverzicht', 'aanbod-websites'); ?></h3>

                        <div class="summary-row" id="package-price-row" style="display: none;">
                            <span id="package-name"><?php _e('Pakket', 'aanbod-websites'); ?></span>
                            <span class="price" id="package-price" data-price="0">€0,00</span>
                        </div>

                        <div id="selected-extras-summary"></div>

                        <div class="summary-divider"></div>

                        <div class="summary-row summary-total">
                            <span><?php _e('Vandaag te betalen', 'aanbod-websites'); ?></span>
                            <span class="price total-price" id="total-price">
                                €<?php echo number_format(0, 2, ',', '.'); ?>
                            </span>
                        </div>

                        <p class="vat-notice">0% btw</p>
                    </div>

                </div>
                <!-- END RIGHT COLUMN -->
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
