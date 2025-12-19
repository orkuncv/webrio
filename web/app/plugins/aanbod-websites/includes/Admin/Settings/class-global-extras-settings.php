<?php
/**
 * Global Extras Settings Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Global_Extras_Settings
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
            __('Globale Extra Opties', 'aanbod-websites'),
            __('Globale Extra Opties', 'aanbod-websites'),
            'manage_options',
            'website-global-extras',
            [__CLASS__, 'render_page']
        );
    }

    /**
     * Render settings page
     */
    public static function render_page()
    {
        if (isset($_POST['save_global_extras']) && check_admin_referer('website_global_extras_nonce')) {
            $extras = isset($_POST['global_extras']) ? $_POST['global_extras'] : [];
            $sanitized_extras = [];

            foreach ($extras as $extra) {
                if (!empty($extra['name'])) {
                    $sanitized_extras[] = [
                        'name' => sanitize_text_field($extra['name']),
                        'price' => sanitize_text_field($extra['price']),
                    ];
                }
            }

            update_option('website_global_extras', $sanitized_extras);
            echo '<div class="notice notice-success"><p>' . __('Globale extra opties opgeslagen!', 'aanbod-websites') . '</p></div>';
        }

        $global_extras = get_option('website_global_extras', []);
        ?>
        <div class="wrap">
            <h1><?php _e('Globale Extra Opties', 'aanbod-websites'); ?></h1>
            <p><?php _e('Beheer extra opties die beschikbaar zijn voor alle websites. Deze worden altijd getoond op de checkout pagina, naast eventuele website-specifieke opties.', 'aanbod-websites'); ?></p>

            <style>
                .global-extras-table { width: 100%; max-width: 800px; margin-top: 20px; }
                .global-extras-table th { text-align: left; padding: 10px; background: #f5f5f5; }
                .global-extras-table td { padding: 10px; border-bottom: 1px solid #ddd; }
                .global-extras-table input[type="text"] { width: 100%; }
                .global-extras-table input[type="number"] { width: 100%; }
            </style>

            <form method="post" action="">
                <?php wp_nonce_field('website_global_extras_nonce'); ?>

                <table class="global-extras-table">
                    <thead>
                        <tr>
                            <th style="width: 50%;"><?php _e('Naam Extra Optie', 'aanbod-websites'); ?></th>
                            <th style="width: 30%;"><?php _e('Prijs (€)', 'aanbod-websites'); ?></th>
                            <th style="width: 20%;"><?php _e('Acties', 'aanbod-websites'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="global-extras-list">
                        <?php
                        if (!empty($global_extras)) {
                            foreach ($global_extras as $index => $extra) {
                                self::render_global_extra_row($index, $extra);
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <p>
                    <button type="button" class="button" id="add-global-extra"><?php _e('Extra Optie Toevoegen', 'aanbod-websites'); ?></button>
                    <button type="submit" name="save_global_extras" class="button button-primary"><?php _e('Opties Opslaan', 'aanbod-websites'); ?></button>
                </p>
            </form>

            <script>
            jQuery(document).ready(function($) {
                var extraIndex = <?php echo count($global_extras); ?>;

                $('#add-global-extra').on('click', function() {
                    var html = `
                        <tr>
                            <td>
                                <input type="text" name="global_extras[${extraIndex}][name]" placeholder="Bijv. SEO Optimalisatie" />
                            </td>
                            <td>
                                <input type="number" name="global_extras[${extraIndex}][price]" placeholder="0.00" step="0.01" min="0" />
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="button button-remove-global-extra">Verwijder</button>
                            </td>
                        </tr>
                    `;
                    $('#global-extras-list').append(html);
                    extraIndex++;
                });

                $(document).on('click', '.button-remove-global-extra', function() {
                    $(this).closest('tr').remove();
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * Render global extra row
     */
    private static function render_global_extra_row($index, $extra)
    {
        ?>
        <tr>
            <td>
                <input type="text" name="global_extras[<?php echo $index; ?>][name]" value="<?php echo esc_attr($extra['name']); ?>" />
            </td>
            <td>
                <input type="number" name="global_extras[<?php echo $index; ?>][price]" value="<?php echo esc_attr($extra['price']); ?>" step="0.01" min="0" />
            </td>
            <td style="text-align: center;">
                <button type="button" class="button button-remove-global-extra">Verwijder</button>
            </td>
        </tr>
        <?php
    }
}
