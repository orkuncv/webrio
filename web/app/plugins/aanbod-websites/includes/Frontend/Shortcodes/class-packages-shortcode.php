<?php
/**
 * Packages Shortcode Class
 *
 * Handles the [website_packages] shortcode functionality
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Packages_Shortcode {

    /**
     * Register the shortcode
     */
    public static function register() {
        add_shortcode('website_packages', [__CLASS__, 'render']);
    }

    /**
     * Render the packages shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public static function render($atts) {
        global $post;

        $atts = shortcode_atts([
            'website_id' => 0,
            'checkout_page' => 'checkout',
            'button_text' => __('Kies dit pakket', 'aanbod-websites'),
        ], $atts);

        // Get website ID from attribute, current post, or get_the_ID()
        $website_id = $atts['website_id'] ? intval($atts['website_id']) : ($post->ID ?? get_the_ID());

        // Only show packages on website post types
        if (!$website_id || get_post_type($website_id) !== 'website') {
            return '<p>' . __('Deze shortcode werkt alleen op website paginas.', 'aanbod-websites') . '</p>';
        }

        $packages = get_option('website_packages', []);

        if (empty($packages)) {
            return '<p>' . __('Geen pakketten beschikbaar.', 'aanbod-websites') . '</p>';
        }

        $checkout_url = home_url('/' . $atts['checkout_page'] . '/');

        ob_start();
        ?>
        <div class="website-packages-wrapper" data-website-id="<?php echo esc_attr($website_id); ?>" data-checkout-url="<?php echo esc_url($checkout_url); ?>">
            <style>
                .website-packages-wrapper {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 30px;
                    margin: 40px 0;
                }
                .package-card {
                    background: #fff;
                    border: 2px solid #e5e5e5;
                    border-radius: 8px;
                    padding: 30px;
                    transition: all 0.3s ease;
                    display: flex;
                    flex-direction: column;
                }
                .package-card:hover {
                    border-color: #2271b1;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                }
                .package-card h3 {
                    margin: 0 0 15px 0;
                    color: #2271b1;
                    font-size: 24px;
                }
                .package-price {
                    font-size: 32px;
                    font-weight: bold;
                    color: #333;
                    margin: 15px 0;
                }
                .package-price small {
                    font-size: 16px;
                    font-weight: normal;
                    color: #666;
                }
                .package-description {
                    color: #666;
                    margin: 15px 0;
                    line-height: 1.6;
                }
                .package-features {
                    list-style: none;
                    padding: 0;
                    margin: 20px 0;
                    flex: 1;
                }
                .package-features li {
                    padding: 8px 0;
                    border-bottom: 1px solid #f0f0f0;
                    color: #444;
                }
                .package-features li:before {
                    content: "✓";
                    color: #2271b1;
                    font-weight: bold;
                    margin-right: 10px;
                }
                .package-cta-button {
                    display: inline-block;
                    width: 100%;
                    padding: 15px 30px;
                    background: #2271b1;
                    color: #fff;
                    text-align: center;
                    text-decoration: none;
                    border-radius: 4px;
                    font-weight: 600;
                    font-size: 16px;
                    transition: background 0.3s ease;
                    border: none;
                    cursor: pointer;
                    margin-top: 20px;
                }
                .package-cta-button:hover {
                    background: #135e96;
                    color: #fff;
                }
            </style>
            <?php foreach ($packages as $index => $package): ?>
            <div class="package-card" data-package-index="<?php echo $index; ?>">
                <h3><?php echo esc_html($package['name']); ?></h3>
                <div class="package-price">
                    €<?php echo number_format((float)$package['price'], 2, ',', '.'); ?>
                    <small>/eenmalig</small>
                </div>
                <?php if (!empty($package['description'])): ?>
                <div class="package-description">
                    <?php echo wp_kses_post(nl2br($package['description'])); ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($package['features'])):
                    $features = array_filter(explode("\n", $package['features']));
                    if (!empty($features)):
                ?>
                <ul class="package-features">
                    <?php foreach ($features as $feature): ?>
                    <li><?php echo esc_html(trim($feature)); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; endif; ?>
                <button
                    class="package-cta-button"
                    data-package-index="<?php echo $index; ?>"
                    data-website-id="<?php echo esc_attr($website_id); ?>"
                    data-checkout-url="<?php echo esc_url($checkout_url); ?>">
                    <?php echo esc_html($atts['button_text']); ?>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
