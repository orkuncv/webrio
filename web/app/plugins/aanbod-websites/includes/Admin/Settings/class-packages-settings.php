<?php
/**
 * Packages Settings Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Packages_Settings
{
    /**
     * Register settings page
     */
    public static function register()
    {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
    }

    /**
     * Add menu page
     */
    public static function add_menu()
    {
        add_submenu_page(
            'edit.php?post_type=website',
            __('Pakketten', 'aanbod-websites'),
            __('Pakketten', 'aanbod-websites'),
            'manage_options',
            'website-packages',
            [__CLASS__, 'render_page']
        );
    }

    /**
     * Render settings page
     */
    public static function render_page()
    {
        if (isset($_POST['save_packages']) && check_admin_referer('website_packages_nonce')) {
            $packages = isset($_POST['packages']) ? $_POST['packages'] : [];
            $sanitized_packages = [];

            foreach ($packages as $package) {
                if (!empty($package['name'])) {
                    $sanitized_packages[] = [
                        'name' => sanitize_text_field($package['name']),
                        'description' => sanitize_textarea_field($package['description']),
                        'price_monthly' => sanitize_text_field($package['price_monthly']),
                        'price_yearly' => sanitize_text_field($package['price_yearly']),
                        'features' => sanitize_textarea_field($package['features']),
                    ];
                }
            }

            update_option('website_packages', $sanitized_packages);
            echo '<div class="notice notice-success"><p>' . __('Pakketten opgeslagen!', 'aanbod-websites') . '</p></div>';
        }

        $packages = get_option('website_packages', []);
        ?>
        <div class="wrap">
            <h1><?php _e('Pakketten Beheer', 'aanbod-websites'); ?></h1>
            <p><?php _e('Beheer de pakketten die klanten kunnen kiezen bij een website. Pakketten bepalen de prijs en features.', 'aanbod-websites'); ?></p>
            <p><strong><?php _e('Shortcode:', 'aanbod-websites'); ?></strong> <code>[website_packages]</code> - <?php _e('Gebruik deze shortcode om alle pakketten te tonen op een pagina.', 'aanbod-websites'); ?></p>

            <style>
                .packages-table { width: 100%; max-width: 1200px; margin-top: 20px; border-collapse: collapse; }
                .packages-table th { text-align: left; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; }
                .packages-table td { padding: 10px; border: 1px solid #ddd; vertical-align: top; }
                .packages-table input[type="text"], .packages-table textarea { width: 100%; }
                .packages-table textarea { min-height: 80px; resize: vertical; }
                .package-row { background: #fff; }
                .package-row:nth-child(even) { background: #f9f9f9; }
            </style>

            <form method="post" action="">
                <?php wp_nonce_field('website_packages_nonce'); ?>

                <table class="packages-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;"><?php _e('Pakket Naam', 'aanbod-websites'); ?></th>
                            <th style="width: 25%;"><?php _e('Beschrijving', 'aanbod-websites'); ?></th>
                            <th style="width: 10%;"><?php _e('Maand Prijs (€)', 'aanbod-websites'); ?></th>
                            <th style="width: 10%;"><?php _e('Jaar Prijs (€)', 'aanbod-websites'); ?></th>
                            <th style="width: 30%;"><?php _e('Features', 'aanbod-websites'); ?></th>
                            <th style="width: 10%;"><?php _e('Acties', 'aanbod-websites'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="packages-list">
                        <?php
                        if (!empty($packages)) {
                            foreach ($packages as $index => $package) {
                                self::render_package_row($index, $package);
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <p>
                    <button type="button" class="button" id="add-package"><?php _e('Pakket Toevoegen', 'aanbod-websites'); ?></button>
                    <button type="submit" name="save_packages" class="button button-primary"><?php _e('Pakketten Opslaan', 'aanbod-websites'); ?></button>
                </p>
            </form>

            <script>
            jQuery(document).ready(function($) {
                var packageIndex = <?php echo count($packages); ?>;

                $('#add-package').on('click', function() {
                    var html = `
                        <tr class="package-row">
                            <td>
                                <input type="text" name="packages[${packageIndex}][name]" placeholder="Bijv. Basis Pakket" />
                            </td>
                            <td>
                                <textarea name="packages[${packageIndex}][description]" placeholder="Korte beschrijving van het pakket"></textarea>
                            </td>
                            <td>
                                <input type="number" name="packages[${packageIndex}][price_monthly]" placeholder="Per maand" step="0.01" min="0" />
                            </td>
                            <td>
                                <input type="number" name="packages[${packageIndex}][price_yearly]" placeholder="Per jaar" step="0.01" min="0" />
                            </td>
                            <td>
                                <textarea name="packages[${packageIndex}][features]" placeholder="Eén feature per regel"></textarea>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="button button-remove-package">Verwijder</button>
                            </td>
                        </tr>
                    `;
                    $('#packages-list').append(html);
                    packageIndex++;
                });

                $(document).on('click', '.button-remove-package', function() {
                    $(this).closest('tr').remove();
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * Render package row
     */
    private static function render_package_row($index, $package)
    {
        ?>
        <tr class="package-row">
            <td>
                <input type="text" name="packages[<?php echo $index; ?>][name]" value="<?php echo esc_attr($package['name']); ?>" />
            </td>
            <td>
                <textarea name="packages[<?php echo $index; ?>][description]"><?php echo esc_textarea($package['description']); ?></textarea>
            </td>
            <td>
                <input type="number" name="packages[<?php echo $index; ?>][price_monthly]" value="<?php echo esc_attr($package['price_monthly'] ?? $package['price'] ?? ''); ?>" step="0.01" min="0" placeholder="Per maand" />
            </td>
            <td>
                <input type="number" name="packages[<?php echo $index; ?>][price_yearly]" value="<?php echo esc_attr($package['price_yearly'] ?? ''); ?>" step="0.01" min="0" placeholder="Per jaar" />
            </td>
            <td>
                <textarea name="packages[<?php echo $index; ?>][features]"><?php echo esc_textarea($package['features']); ?></textarea>
            </td>
            <td style="text-align: center;">
                <button type="button" class="button button-remove-package">Verwijder</button>
            </td>
        </tr>
        <?php
    }
}
