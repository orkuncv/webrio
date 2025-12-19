<?php
/**
 * Order Post Type Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Order_Post_Type
{
    /**
     * Register the Order custom post type
     */
    public static function register()
    {
        $labels = [
            'name'                  => _x('Bestellingen', 'Post Type General Name', 'aanbod-websites'),
            'singular_name'         => _x('Bestelling', 'Post Type Singular Name', 'aanbod-websites'),
            'menu_name'             => __('Bestellingen', 'aanbod-websites'),
            'name_admin_bar'        => __('Bestelling', 'aanbod-websites'),
            'all_items'             => __('Alle Bestellingen', 'aanbod-websites'),
            'add_new_item'          => __('Nieuwe Bestelling', 'aanbod-websites'),
            'edit_item'             => __('Bestelling Bewerken', 'aanbod-websites'),
            'view_item'             => __('Bestelling Bekijken', 'aanbod-websites'),
            'search_items'          => __('Bestellingen Zoeken', 'aanbod-websites'),
            'not_found'             => __('Geen Bestellingen Gevonden', 'aanbod-websites'),
        ];

        $args = [
            'label'                 => __('Bestelling', 'aanbod-websites'),
            'description'           => __('Website bestellingen', 'aanbod-websites'),
            'labels'                => $labels,
            'supports'              => ['title'],
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => 'edit.php?post_type=website',
            'menu_position'         => 5,
            'show_in_admin_bar'     => false,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
            'show_in_rest'          => false,
            'capabilities'          => [
                'create_posts' => 'do_not_allow',
            ],
            'map_meta_cap'          => true,
        ];

        register_post_type('website_order', $args);
    }
}
