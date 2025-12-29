<?php
/**
 * Plugin Name: Aanbod Websites
 * Plugin URI: https://biotap.nl
 * Description: Custom post type voor websites aanbod
 * Version: 1.0.0
 * Author: BioTap
 * Author URI: https://biotap.nl
 * Text Domain: aanbod-websites
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // Start session
        add_action('init', [$this, 'start_session']);

        // Load all organized classes
        $this->load_classes();
    }

    /**
     * Start session for storing selected website/package
     */
    public function start_session()
    {
        if (!session_id() && !headers_sent()) {
            session_start();
        }
    }

    /**
     * Load and register all plugin classes
     */
    private function load_classes()
    {
        $base_path = plugin_dir_path(__FILE__) . 'includes/';

        // Load Post Types
        require_once $base_path . 'PostTypes/class-website-post-type.php';
        require_once $base_path . 'PostTypes/class-order-post-type.php';
        add_action('init', ['Aanbod_Websites_Website_Post_Type', 'register']);
        add_action('init', ['Aanbod_Websites_Order_Post_Type', 'register']);

        // Load Taxonomies
        require_once $base_path . 'Taxonomies/class-taxonomies.php';
        add_action('init', ['Aanbod_Websites_Taxonomies', 'register']);

        // Load Admin MetaBoxes
        require_once $base_path . 'Admin/MetaBoxes/class-website-meta-box.php';
        require_once $base_path . 'Admin/MetaBoxes/class-order-meta-box.php';
        Aanbod_Websites_Website_Meta_Box::register();
        Aanbod_Websites_Order_Meta_Box::register();

        // Load Admin Settings
        require_once $base_path . 'Admin/Settings/class-packages-settings.php';
        require_once $base_path . 'Admin/Settings/class-checkout-settings.php';
        require_once $base_path . 'Admin/Settings/class-global-extras-settings.php';
        Aanbod_Websites_Packages_Settings::register();
        Aanbod_Websites_Checkout_Settings::register();
        Aanbod_Websites_Global_Extras_Settings::register();

        // Load Admin TermMeta
        require_once $base_path . 'Admin/TermMeta/class-branch-term-meta.php';
        Aanbod_Websites_Branch_Term_Meta::register();

        // Load Frontend Shortcodes
        require_once $base_path . 'Frontend/Shortcodes/class-packages-shortcode.php';
        require_once $base_path . 'Frontend/Shortcodes/class-cta-shortcode.php';
        require_once $base_path . 'Frontend/Shortcodes/class-checkout-shortcode.php';
        require_once $base_path . 'Frontend/Shortcodes/class-branches-shortcode.php';
        require_once $base_path . 'Frontend/Shortcodes/class-branch-features-shortcode.php';
        require_once $base_path . 'Frontend/Shortcodes/class-branch-demos-shortcode.php';
        Aanbod_Websites_Packages_Shortcode::register();
        Aanbod_Websites_CTA_Shortcode::register();
        Aanbod_Websites_Checkout_Shortcode::register();
        Aanbod_Websites_Branches_Shortcode::register();
        Aanbod_Websites_Branch_Features_Shortcode::register();
        Aanbod_Websites_Branch_Demos_Shortcode::register();

        // Load Frontend Assets
        require_once $base_path . 'Frontend/class-assets.php';
        Aanbod_Websites_Assets::register();

        // Load Ajax Handlers
        require_once $base_path . 'Ajax/class-ajax-handlers.php';
        Aanbod_Websites_Ajax_Handlers::register();

        // Load Patterns
        require_once $base_path . 'Patterns/class-patterns.php';
        Aanbod_Websites_Patterns::register();
    }
}

// Initialize plugin
Aanbod_Websites::get_instance();
