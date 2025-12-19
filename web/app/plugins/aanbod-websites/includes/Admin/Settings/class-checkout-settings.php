<?php
/**
 * Checkout Settings Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Checkout_Settings
{
    /**
     * Register settings page
     */
    public static function register()
    {
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);
    }

    /**
     * Add menu page
     */
    public static function add_menu()
    {
        add_submenu_page(
            'edit.php?post_type=website',
            __('Checkout Instellingen', 'aanbod-websites'),
            __('Checkout Instellingen', 'aanbod-websites'),
            'manage_options',
            'website-checkout-settings',
            [__CLASS__, 'render_page']
        );
    }

    /**
     * Register settings
     */
    public static function register_settings()
    {
        register_setting('website_checkout_settings', 'website_checkout_fields');
        register_setting('website_checkout_settings', 'website_startup_cost');
    }

    /**
     * Render settings page
     */
    public static function render_page()
    {
        if (isset($_POST['save_checkout_fields']) && check_admin_referer('website_checkout_fields_nonce')) {
            $fields = isset($_POST['checkout_fields']) ? $_POST['checkout_fields'] : [];
            $sanitized_fields = [];

            foreach ($fields as $field) {
                if (!empty($field['label'])) {
                    $sanitized_fields[] = [
                        'label' => sanitize_text_field($field['label']),
                        'type' => sanitize_text_field($field['type']),
                        'required' => isset($field['required']) ? 1 : 0,
                        'placeholder' => sanitize_text_field($field['placeholder'] ?? ''),
                        'width' => sanitize_text_field($field['width'] ?? '100'),
                    ];
                }
            }

            update_option('website_checkout_fields', $sanitized_fields);

            // Save startup cost
            $startup_cost = isset($_POST['startup_cost']) ? floatval($_POST['startup_cost']) : 0;
            update_option('website_startup_cost', $startup_cost);

            echo '<div class="notice notice-success"><p>' . __('Instellingen opgeslagen!', 'aanbod-websites') . '</p></div>';
        }

        $fields = get_option('website_checkout_fields', []);
        $startup_cost = get_option('website_startup_cost', 0);
        ?>
        <div class="wrap">
            <h1><?php _e('Checkout Formulier Instellingen', 'aanbod-websites'); ?></h1>
            <p><?php _e('Beheer de velden die worden getoond op de checkout pagina.', 'aanbod-websites'); ?></p>

            <style>
                .checkout-fields-table { width: 100%; max-width: 1000px; margin-top: 20px; }
                .checkout-fields-table th { text-align: left; padding: 10px; background: #f5f5f5; }
                .checkout-fields-table td { padding: 10px; border-bottom: 1px solid #ddd; }
                .checkout-fields-table input[type="text"] { width: 100%; }
                .checkout-fields-table select { width: 100%; }
                .field-actions { text-align: center; }
            </style>

            <form method="post" action="">
                <?php wp_nonce_field('website_checkout_fields_nonce'); ?>

                <h2><?php _e('Algemene Instellingen', 'aanbod-websites'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="startup_cost"><?php _e('Opstartkosten', 'aanbod-websites'); ?></label>
                        </th>
                        <td>
                            <input type="number"
                                   id="startup_cost"
                                   name="startup_cost"
                                   value="<?php echo esc_attr($startup_cost); ?>"
                                   step="0.01"
                                   min="0"
                                   class="regular-text" />
                            <p class="description"><?php _e('Eenmalige opstartkosten die worden toegevoegd aan elke bestelling (bijv. 299)', 'aanbod-websites'); ?></p>
                        </td>
                    </tr>
                </table>

                <h2><?php _e('Checkout Formulier Velden', 'aanbod-websites'); ?></h2>
                <table class="checkout-fields-table">
                    <thead>
                        <tr>
                            <th style="width: 20%;"><?php _e('Label', 'aanbod-websites'); ?></th>
                            <th style="width: 15%;"><?php _e('Type', 'aanbod-websites'); ?></th>
                            <th style="width: 20%;"><?php _e('Placeholder', 'aanbod-websites'); ?></th>
                            <th style="width: 15%;"><?php _e('Breedte', 'aanbod-websites'); ?></th>
                            <th style="width: 15%;"><?php _e('Verplicht', 'aanbod-websites'); ?></th>
                            <th style="width: 15%;"><?php _e('Acties', 'aanbod-websites'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="checkout-fields-list">
                        <?php
                        if (!empty($fields)) {
                            foreach ($fields as $index => $field) {
                                self::render_field_row($index, $field);
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <p>
                    <button type="button" class="button" id="add-checkout-field"><?php _e('Veld Toevoegen', 'aanbod-websites'); ?></button>
                    <button type="submit" name="save_checkout_fields" class="button button-primary"><?php _e('Instellingen Opslaan', 'aanbod-websites'); ?></button>
                </p>
            </form>

            <script>
            jQuery(document).ready(function($) {
                var fieldIndex = <?php echo count($fields); ?>;

                $('#add-checkout-field').on('click', function() {
                    var html = `
                        <tr>
                            <td>
                                <input type="text" name="checkout_fields[${fieldIndex}][label]" placeholder="Bijv. Naam" />
                            </td>
                            <td>
                                <select name="checkout_fields[${fieldIndex}][type]">
                                    <option value="text">Tekst</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Telefoon</option>
                                    <option value="textarea">Tekstgebied</option>
                                    <option value="number">Nummer</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="checkout_fields[${fieldIndex}][placeholder]" placeholder="Placeholder tekst" />
                            </td>
                            <td>
                                <select name="checkout_fields[${fieldIndex}][width]">
                                    <option value="50">50%</option>
                                    <option value="100" selected>100%</option>
                                </select>
                            </td>
                            <td style="text-align: center;">
                                <input type="checkbox" name="checkout_fields[${fieldIndex}][required]" value="1" />
                            </td>
                            <td class="field-actions">
                                <button type="button" class="button button-remove-field">Verwijder</button>
                            </td>
                        </tr>
                    `;
                    $('#checkout-fields-list').append(html);
                    fieldIndex++;
                });

                $(document).on('click', '.button-remove-field', function() {
                    $(this).closest('tr').remove();
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * Render field row
     */
    private static function render_field_row($index, $field)
    {
        $width = isset($field['width']) ? $field['width'] : '100';
        ?>
        <tr>
            <td>
                <input type="text" name="checkout_fields[<?php echo $index; ?>][label]" value="<?php echo esc_attr($field['label']); ?>" />
            </td>
            <td>
                <select name="checkout_fields[<?php echo $index; ?>][type]">
                    <option value="text" <?php selected($field['type'], 'text'); ?>>Tekst</option>
                    <option value="email" <?php selected($field['type'], 'email'); ?>>Email</option>
                    <option value="tel" <?php selected($field['type'], 'tel'); ?>>Telefoon</option>
                    <option value="textarea" <?php selected($field['type'], 'textarea'); ?>>Tekstgebied</option>
                    <option value="number" <?php selected($field['type'], 'number'); ?>>Nummer</option>
                </select>
            </td>
            <td>
                <input type="text" name="checkout_fields[<?php echo $index; ?>][placeholder]" value="<?php echo esc_attr($field['placeholder'] ?? ''); ?>" />
            </td>
            <td>
                <select name="checkout_fields[<?php echo $index; ?>][width]">
                    <option value="50" <?php selected($width, '50'); ?>>50%</option>
                    <option value="100" <?php selected($width, '100'); ?>>100%</option>
                </select>
            </td>
            <td style="text-align: center;">
                <input type="checkbox" name="checkout_fields[<?php echo $index; ?>][required]" value="1" <?php checked($field['required'], 1); ?> />
            </td>
            <td class="field-actions">
                <button type="button" class="button button-remove-field">Verwijder</button>
            </td>
        </tr>
        <?php
    }
}
