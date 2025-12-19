<?php
/**
 * Packages Shortcode Class
 * Handles the [website_packages] shortcode functionality
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if ( ! defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Packages_Shortcode
{

    /**
     * Register the shortcode
     */
    public static function register()
    {
        add_shortcode('website_packages', [__CLASS__, 'render']);
    }

    /**
     * Render the packages shortcode
     *
     * @param array $atts Shortcode attributes
     *
     * @return string HTML output
     */
    public static function render($atts)
    {
        global $post;

        $atts = shortcode_atts([
                'website_id' => 0,
                'checkout_page' => 'checkout',
                'button_text' => __('Kies dit pakket', 'aanbod-websites'),
        ], $atts);

        // Get website ID from attribute, current post, or get_the_ID()
        $website_id = $atts['website_id'] ? intval($atts['website_id']) : ($post->ID ?? get_the_ID());

        // Only show packages on website post types
        if ( ! $website_id || get_post_type($website_id) !== 'website') {
            return '<p>' . __('Deze shortcode werkt alleen op website paginas.', 'aanbod-websites') . '</p>';
        }

        $packages = get_option('website_packages', []);

        if (empty($packages)) {
            return '<p>' . __('Geen pakketten beschikbaar.', 'aanbod-websites') . '</p>';
        }

        $checkout_url = home_url('/' . $atts['checkout_page'] . '/');

        ob_start();
        ?>
        <div class="website-packages-container alignfull">
            <!-- Pricing Toggle -->
            <div class="pricing-toggle-container">
                <div class="pricing-toggle">
                    <button class="toggle-btn active" data-period="monthly">Maandelijks</button>
                    <button class="toggle-btn" data-period="lifetime">
                        Eenmalig
                        <small class="save-badge">Bespaar 20%</small>
                    </button>
                </div>
            </div>

            <!-- Packages Grid -->
            <div class="website-packages-wrapper" data-website-id="<?php echo esc_attr($website_id); ?>" data-checkout-url="<?php echo esc_url($checkout_url); ?>">
            <style>
                .pricing-toggle-container {
                    display: flex;
                    justify-content: center;
                    margin-bottom: 12px;
                }

                .pricing-toggle {
                    display: inline-flex;
                    background: #F3F4F6;
                    border-radius: var(--wp--custom--border-radius--large);
                    padding: 4px;
                    gap: 4px;
                }

                .toggle-btn {
                    padding: 12px 24px;
                    border: none;
                    background: transparent;
                    border-radius: var(--wp--custom--border-radius--medium);
                    cursor: pointer;
                    font-family: var(--wp--custom--font-family--button);
                    font-size: 14px;
                    font-weight: 600;
                    color: #6B7280;
                    transition: all 0.3s ease;
                    position: relative;
                }

                .toggle-btn.active {
                    background: white;
                    color: #0B1F3B;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                }

                .save-badge {
                    display: inline-block;
                    background: #10B981;
                    color: white;
                    font-size: 11px;
                    padding: 2px 8px;
                    border-radius: var(--wp--custom--border-radius--full);
                    margin-left: 8px;
                    font-weight: 700;
                }

                .website-packages-wrapper {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 30px;
                    padding: 15px 0;
                }

                .package-card {
                    background: #fff;
                    border: 1px solid #E4E7EC;
                    border-radius: var(--wp--custom--border-radius--large);
                    padding: 24px;
                    transition: all 0.3s ease;
                    display: flex;
                    flex-direction: column;
                }

                .package-card:hover {
                    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                }

                .package-card h3 {
                    margin: 0 0 17px 0;
                    font-size: 24px;
                }

                .package-price {
                    font-size: 36px;
                    margin: 15px 0 0 0;
                }

                .package-type {
                    color: #6F7988;
                    margin: 0 0 15px 0;
                }

                .package-description {
                    margin: 15px 0 0 0;
                    font-size: 14px;
                }

                .package-features {
                    list-style: none;
                    padding: 0;
                    margin: 0 0 32px 0;
                    flex: 1;
                }

                .package-features li {
                    padding: 8px 0;
                    color: #6F7988;
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
                    padding: 10px 30px;
                    background: #ffffff;
                    color: #000;
                    text-align: center;
                    border-radius: var(--wp--custom--border-radius--large);
                    font-size: 16px;
                    transition: background 0.3s ease;
                    border: 1px solid #CAD0D9;
                    cursor: pointer;
                    font-family: var(--wp--custom--font-family--button);
                }

                .package-cta-button:hover {
                    background: #CAD0D9;
                }
            </style>
            <?php foreach ($packages as $index => $package):
                $price_monthly = $package['price_monthly'] ?? $package['price'] ?? 0;
                $price_yearly = $package['price_yearly'] ?? 0;
            ?>
                <div class="package-card"
                     data-package-index="<?php echo $index; ?>"
                     data-price-monthly="<?php echo esc_attr($price_monthly); ?>"
                     data-price-yearly="<?php echo esc_attr($price_yearly); ?>">
                    <h3><?php echo esc_html($package['name']); ?></h3>
                    <div class="package-price">
                        €<span class="price-amount"><?php echo number_format((float) $price_monthly, 0, ',', '.'); ?></span>
                    </div>
                    <p class="package-type">
                        <span class="period-text">Per maand + €299 Opstartkosten</span>
                    </p>
                    <button
                            class="package-cta-button"
                            data-package-index="<?php echo $index; ?>"
                            data-website-id="<?php echo esc_attr($website_id); ?>"
                            data-checkout-url="<?php echo esc_url($checkout_url); ?>">
                        <?php echo esc_html($atts['button_text']); ?>
                    </button>
                    <?php if ( ! empty($package['features'])):
                        $features = array_filter(explode("\n", $package['features']));
                        if ( ! empty($features)):
                            ?>
                            <?php if ( ! empty($package['description'])): ?>
                            <div class="package-description">
                                <?php echo wp_kses_post(nl2br($package['description'])); ?>
                            </div>
                        <?php endif; ?>
                            <ul class="package-features">
                                <?php foreach ($features as $feature): ?>
                                    <li><?php echo esc_html(trim($feature)); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; endif; ?>
                </div>
            <?php endforeach; ?>
            </div><!-- .website-packages-wrapper -->

            <script>
            (function() {
                const toggleBtns = document.querySelectorAll('.toggle-btn');
                const packageCards = document.querySelectorAll('.package-card');

                // Toggle between monthly/yearly pricing
                toggleBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const period = this.dataset.period;

                        // Update active state
                        toggleBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');

                        // Update prices and period text
                        packageCards.forEach(card => {
                            const priceMonthly = parseFloat(card.dataset.priceMonthly);
                            const priceYearly = parseFloat(card.dataset.priceYearly);
                            const priceAmount = card.querySelector('.price-amount');
                            const periodText = card.querySelector('.period-text');

                            if (period === 'monthly') {
                                priceAmount.textContent = priceMonthly.toLocaleString('nl-NL', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                                periodText.textContent = 'Per maand + €299 Opstartkosten';
                            } else {
                                priceAmount.textContent = priceYearly.toLocaleString('nl-NL', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                                periodText.textContent = 'Eenmalig + €299 Opstartkosten';
                            }
                        });
                    });
                });

                // Handle package selection
                const packageButtons = document.querySelectorAll('.package-cta-button');
                packageButtons.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();

                        const packageIndex = this.dataset.packageIndex;
                        const websiteId = this.dataset.websiteId;
                        const checkoutUrl = this.dataset.checkoutUrl;

                        // Disable button and show loading state
                        this.disabled = true;
                        this.textContent = 'Bezig...';

                        // Make AJAX call to save selection
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                action: 'set_selected_website_and_package',
                                website_id: websiteId,
                                package_index: packageIndex
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = checkoutUrl;
                            } else {
                                alert(data.data.message || 'Er is iets misgegaan');
                                this.disabled = false;
                                this.textContent = '<?php echo esc_js($atts['button_text']); ?>';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Er is een fout opgetreden');
                            this.disabled = false;
                            this.textContent = '<?php echo esc_js($atts['button_text']); ?>';
                        });
                    });
                });
            })();
            </script>
        </div><!-- .website-packages-container -->
        <?php
        return ob_get_clean();
    }
}
