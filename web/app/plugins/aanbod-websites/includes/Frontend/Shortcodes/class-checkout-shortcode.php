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
        // Get website ID and package index from session
        $website_id = isset($_SESSION['selected_website_id']) ? intval($_SESSION['selected_website_id']) : 0;
        $package_index = isset($_SESSION['selected_package_index']) ? intval($_SESSION['selected_package_index']) : -1;

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
        $startup_cost = get_option('website_startup_cost', 0);

        // Get selected package
        $selected_package = null;
        if ($package_index >= 0 && isset($packages[$package_index])) {
            $selected_package = $packages[$package_index];
        }

        // Combine global and custom extras
        $has_extras = (!empty($global_extras) && is_array($global_extras)) || (!empty($custom_extras) && is_array($custom_extras));

        ob_start();
        ?>
        <div class="website-checkout-wrapper">
            <div class="checkout-container">

                <!-- RIGHT COLUMN: Checkout Form -->
                <div class="checkout-right">
                    <h2><?php _e('Uw Gegevens', 'aanbod-websites'); ?></h2>
                    <p class="subtitle"><?php echo esc_html($website->post_title); ?> - Vul je gegevens in</p>

                    <?php if (!empty($checkout_fields)): ?>
                        <div class="checkout-form-section">
                            <div id="form-messages"></div>
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
                            </form>
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
                </div>
                <!-- END RIGHT COLUMN -->

                <!-- LEFT COLUMN: Product Showcase -->
                <div class="checkout-left">
                    <h2><?php _e('Gekozen Pakket', 'aanbod-websites'); ?>
                        <button type="button" class="change-package-btn" onclick="window.location.href='<?php echo get_permalink($website_id); ?>'">
                            <?php _e('Wijzig pakket', 'aanbod-websites'); ?>
                        </button>
                    </h2>

                    <div class="product-showcase">
                        <p><?php echo esc_html($website->post_title); ?></p>
                    </div>

                    <?php if ($selected_package):
                        $price_monthly = $selected_package['price_monthly'] ?? $selected_package['price'] ?? 0;
                        ?>
                        <div class="product-showcase">
                            <p><?php echo esc_html($selected_package['name']); ?></p>
                            <div class="package-price-display">
                                <span class="price" id="package-price" data-price="0">
                                    €<?php echo number_format((float) $price_monthly, 0, ',', '.'); ?>
                                </span>
                                <span class="price-period">p/m</span>
                            </div>
                            <input type="hidden" name="selected_package" value="<?php echo $package_index; ?>" data-price="<?php echo esc_attr($price_monthly); ?>" data-name="<?php echo esc_attr($selected_package['name']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="no-package-selected">
                            <p><?php _e('Geen pakket geselecteerd. Ga terug en kies een pakket.', 'aanbod-websites'); ?></p>
                            <a href="<?php echo get_permalink($website_id); ?>" class="button-back"><?php _e('Terug naar pakketten', 'aanbod-websites'); ?></a>
                        </div>
                    <?php endif; ?>

                    <?php if ($startup_cost > 0): ?>
                        <div class="product-showcase">
                            <p><?php _e('Opstartkosten', 'aanbod-websites'); ?></p>
                            <div class="package-price-display">
                                <span class="price" id="startup-cost" data-price="<?php echo esc_attr($startup_cost); ?>">
                                    €<?php echo number_format((float) $startup_cost, 2, ',', '.'); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div id="selected-extras-summary"></div>

                    <div class="summary-row summary-total">
                            <span>
                                <?php _e('Vandaag te betalen', 'aanbod-websites'); ?>
                            </span>
                        <span class="price total-price" id="total-price">
                                €<?php echo number_format(0, 2, ',', '.'); ?>
                            </span>
                    </div>

                    <button type="submit" class="button-submit"><?php _e('Bestelling Plaatsen', 'aanbod-websites'); ?></button>
                </div>
                <!-- END LEFT COLUMN -->
            </div>
        </div>

        <script>
        (function() {
            function formatPrice(price) {
                return '€' + parseFloat(price).toLocaleString('nl-NL', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function updateOrderSummary() {
                let total = 0;

                // Get selected package (now a hidden input)
                const selectedPackage = document.querySelector('input[name="selected_package"]');
                const packageRow = document.getElementById('package-price-row');
                const packageNameEl = document.getElementById('package-name');
                const packagePriceEl = document.getElementById('package-price');

                // Update package display
                if (selectedPackage && selectedPackage.dataset.price) {
                    const packagePrice = parseFloat(selectedPackage.dataset.price) || 0;
                    const packageName = selectedPackage.dataset.name || 'Pakket';

                    packageNameEl.textContent = packageName;
                    packagePriceEl.textContent = formatPrice(packagePrice);
                    packagePriceEl.dataset.price = packagePrice;
                    packageRow.style.display = 'flex';

                    total += packagePrice;
                } else {
                    packageRow.style.display = 'none';
                }

                // Get selected extras
                const selectedExtras = document.querySelectorAll('input[name="extras[]"]:checked');
                const extrasSummary = document.getElementById('selected-extras-summary');
                extrasSummary.innerHTML = '';

                selectedExtras.forEach(extra => {
                    const extraPrice = parseFloat(extra.dataset.price) || 0;
                    const extraName = extra.dataset.name || 'Extra';

                    const extraRow = document.createElement('div');
                    extraRow.className = 'summary-row';
                    extraRow.innerHTML = `
                        <span>${extraName}</span>
                        <span class="price">${formatPrice(extraPrice)}</span>
                    `;
                    extrasSummary.appendChild(extraRow);

                    total += extraPrice;
                });

                // Add startup costs - ALWAYS check for this
                const startupCostEl = document.getElementById('startup-cost');
                if (startupCostEl) {
                    const startupCostValue = startupCostEl.getAttribute('data-price');
                    const startupCost = parseFloat(startupCostValue) || 0;
                    total += startupCost;
                }

                // Update total
                const totalPriceEl = document.getElementById('total-price');
                if (totalPriceEl) {
                    totalPriceEl.textContent = formatPrice(total);
                }
            }

            // Wait for DOM to be ready
            function init() {
                // Listen for extras selection changes
                document.querySelectorAll('input[name="extras[]"]').forEach(checkbox => {
                    checkbox.addEventListener('change', updateOrderSummary);
                });

                // Initial update
                updateOrderSummary();
            }

            // Execute when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                // DOM already loaded
                setTimeout(init, 100);
            }
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}
