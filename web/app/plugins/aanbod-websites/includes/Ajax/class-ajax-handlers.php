<?php
/**
 * Ajax Handlers Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Ajax_Handlers
{
    /**
     * Register all ajax handlers
     */
    public static function register()
    {
        add_action('wp_ajax_submit_website_order', [__CLASS__, 'handle_order_submission']);
        add_action('wp_ajax_nopriv_submit_website_order', [__CLASS__, 'handle_order_submission']);
        add_action('wp_ajax_set_selected_website', [__CLASS__, 'handle_set_selected_website']);
        add_action('wp_ajax_nopriv_set_selected_website', [__CLASS__, 'handle_set_selected_website']);
        add_action('wp_ajax_set_selected_website_and_package', [__CLASS__, 'handle_set_selected_website_and_package']);
        add_action('wp_ajax_nopriv_set_selected_website_and_package', [__CLASS__, 'handle_set_selected_website_and_package']);
        add_action('wp_ajax_clear_selected_website', [__CLASS__, 'handle_clear_selected_website']);
        add_action('wp_ajax_nopriv_clear_selected_website', [__CLASS__, 'handle_clear_selected_website']);
        add_action('wp_ajax_get_website_id_by_slug', [__CLASS__, 'handle_get_website_id_by_slug']);
        add_action('wp_ajax_nopriv_get_website_id_by_slug', [__CLASS__, 'handle_get_website_id_by_slug']);
    }

    /**
     * Handle order submission
     */
    public static function handle_order_submission()
    {
        check_ajax_referer('website_order_nonce', 'nonce');

        $website_id = isset($_POST['website_id']) ? intval($_POST['website_id']) : 0;
        $selected_package_index = isset($_POST['selected_package']) ? intval($_POST['selected_package']) : -1;
        $form_fields = isset($_POST['form_fields']) ? $_POST['form_fields'] : [];
        $selected_extras_indexes = isset($_POST['selected_extras']) ? $_POST['selected_extras'] : [];

        if (!$website_id || get_post_type($website_id) !== 'website') {
            wp_send_json_error(['message' => __('Ongeldige website.', 'aanbod-websites')]);
        }

        // Get packages and validate selection
        $packages = get_option('website_packages', []);
        if ($selected_package_index < 0 || !isset($packages[$selected_package_index])) {
            wp_send_json_error(['message' => __('Selecteer een pakket om door te gaan.', 'aanbod-websites')]);
        }

        $selected_package = $packages[$selected_package_index];

        // Get website data
        $website = get_post($website_id);
        $global_extras = get_option('website_global_extras', []);
        $custom_extras = get_post_meta($website_id, '_website_extras', true);

        // Calculate total starting with package price
        $total_price = (float) $selected_package['price'];
        $selected_extras = [];

        if (!empty($selected_extras_indexes) && is_array($selected_extras_indexes)) {
            foreach ($selected_extras_indexes as $extra_key) {
                // Check if it's a global or custom extra
                if (strpos($extra_key, 'global_') === 0) {
                    $index = intval(str_replace('global_', '', $extra_key));
                    if (isset($global_extras[$index])) {
                        $extra = $global_extras[$index];
                        $selected_extras[] = $extra;
                        $total_price += (float) $extra['price'];
                    }
                } elseif (strpos($extra_key, 'custom_') === 0) {
                    $index = intval(str_replace('custom_', '', $extra_key));
                    if (is_array($custom_extras) && isset($custom_extras[$index])) {
                        $extra = $custom_extras[$index];
                        $selected_extras[] = $extra;
                        $total_price += (float) $extra['price'];
                    }
                }
            }
        }

        // Sanitize form data
        $sanitized_form_data = [];
        foreach ($form_fields as $key => $value) {
            $sanitized_form_data[sanitize_key($key)] = sanitize_text_field($value);
        }

        // Create order
        $order_title = sprintf(
            __('Bestelling - %s - %s', 'aanbod-websites'),
            $website->post_title,
            date('d-m-Y H:i')
        );

        $order_id = wp_insert_post([
            'post_title' => $order_title,
            'post_type' => 'website_order',
            'post_status' => 'publish',
        ]);

        if ($order_id) {
            update_post_meta($order_id, '_order_website_id', $website_id);
            update_post_meta($order_id, '_order_package', $selected_package);
            update_post_meta($order_id, '_order_form_data', $sanitized_form_data);
            update_post_meta($order_id, '_order_selected_extras', $selected_extras);
            update_post_meta($order_id, '_order_total_price', $total_price);

            // Send email notification
            self::send_order_notification($order_id, $website, $selected_package, $sanitized_form_data, $selected_extras, $total_price);

            wp_send_json_success([
                'message' => __('Bestelling succesvol geplaatst! We nemen zo snel mogelijk contact met u op.', 'aanbod-websites'),
            ]);
        } else {
            wp_send_json_error(['message' => __('Er is een fout opgetreden bij het plaatsen van de bestelling.', 'aanbod-websites')]);
        }
    }

    /**
     * Send order notification email
     */
    private static function send_order_notification($order_id, $website, $package, $form_data, $selected_extras, $total_price)
    {
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('Nieuwe Website Bestelling - %s', 'aanbod-websites'), $website->post_title);

        $message = sprintf(__('Er is een nieuwe bestelling geplaatst voor: %s', 'aanbod-websites'), $website->post_title) . "\n\n";

        $message .= "======================\n";
        $message .= __('Gekozen Pakket', 'aanbod-websites') . "\n";
        $message .= "======================\n\n";
        $message .= $package['name'] . ": €" . number_format((float)$package['price'], 2, ',', '.') . "\n\n";

        $message .= "======================\n";
        $message .= __('Klantgegevens', 'aanbod-websites') . "\n";
        $message .= "======================\n\n";

        foreach ($form_data as $key => $value) {
            $message .= ucfirst(str_replace('_', ' ', $key)) . ": " . $value . "\n";
        }

        if (!empty($selected_extras)) {
            $message .= "\n======================\n";
            $message .= __('Geselecteerde Extra Opties', 'aanbod-websites') . "\n";
            $message .= "======================\n\n";

            foreach ($selected_extras as $extra) {
                $message .= $extra['name'] . ": €" . number_format((float)$extra['price'], 2, ',', '.') . "\n";
            }
        }

        $message .= "\n======================\n";
        $message .= __('Totaalprijs', 'aanbod-websites') . ": €" . number_format($total_price, 2, ',', '.') . "\n";
        $message .= "======================\n\n";

        $message .= sprintf(__('Bekijk de bestelling in WordPress: %s', 'aanbod-websites'), admin_url('post.php?post=' . $order_id . '&action=edit'));

        wp_mail($admin_email, $subject, $message);

        // Send confirmation to customer if email field exists
        if (isset($form_data['email']) || isset($form_data['e_mail'])) {
            $customer_email = $form_data['email'] ?? $form_data['e_mail'] ?? '';
            if (is_email($customer_email)) {
                $customer_subject = __('Bedankt voor uw bestelling', 'aanbod-websites');
                $customer_message = __('Bedankt voor uw interesse in onze website. We hebben uw bestelling ontvangen en nemen zo snel mogelijk contact met u op.', 'aanbod-websites') . "\n\n";
                $customer_message .= sprintf(__('Website: %s', 'aanbod-websites'), $website->post_title) . "\n";
                $customer_message .= sprintf(__('Totaalprijs: €%s', 'aanbod-websites'), number_format($total_price, 2, ',', '.'));

                wp_mail($customer_email, $customer_subject, $customer_message);
            }
        }
    }

    /**
     * Handle set selected website
     */
    public static function handle_set_selected_website()
    {
        $website_id = isset($_POST['website_id']) ? intval($_POST['website_id']) : 0;

        if (!$website_id || get_post_type($website_id) !== 'website') {
            wp_send_json_error(['message' => __('Ongeldige website.', 'aanbod-websites')]);
        }

        $_SESSION['selected_website_id'] = $website_id;

        $checkout_url = home_url('/checkout/');
        wp_send_json_success(['checkout_url' => $checkout_url]);
    }

    /**
     * Handle set selected website and package
     */
    public static function handle_set_selected_website_and_package()
    {
        $website_id = isset($_POST['website_id']) ? intval($_POST['website_id']) : 0;
        $package_index = isset($_POST['package_index']) ? intval($_POST['package_index']) : -1;

        if (!$website_id || get_post_type($website_id) !== 'website') {
            wp_send_json_error(['message' => __('Ongeldige website.', 'aanbod-websites')]);
        }

        // Validate package
        $packages = get_option('website_packages', []);
        if ($package_index < 0 || !isset($packages[$package_index])) {
            wp_send_json_error(['message' => __('Ongeldig pakket.', 'aanbod-websites')]);
        }

        $_SESSION['selected_website_id'] = $website_id;
        $_SESSION['selected_package_index'] = $package_index;

        $checkout_url = home_url('/checkout/');
        wp_send_json_success(['checkout_url' => $checkout_url]);
    }

    /**
     * Handle clear selected website
     */
    public static function handle_clear_selected_website()
    {
        unset($_SESSION['selected_website_id']);
        unset($_SESSION['selected_package_index']);
        wp_send_json_success(['message' => __('Website verwijderd.', 'aanbod-websites')]);
    }

    /**
     * Handle get website ID by slug
     */
    public static function handle_get_website_id_by_slug()
    {
        $slug = isset($_POST['slug']) ? sanitize_title($_POST['slug']) : '';

        if (!$slug) {
            wp_send_json_error(['message' => __('Geen slug opgegeven.', 'aanbod-websites')]);
        }

        $post = get_page_by_path($slug, OBJECT, 'website');

        if ($post && $post->post_type === 'website') {
            wp_send_json_success(['id' => $post->ID]);
        } else {
            wp_send_json_error(['message' => __('Website niet gevonden.', 'aanbod-websites')]);
        }
    }
}
