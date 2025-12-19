<?php
/**
 * Taxonomies Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Taxonomies
{
    /**
     * Register all taxonomies
     */
    public static function register()
    {
        self::register_category_taxonomy();
        self::register_tag_taxonomy();
    }

    /**
     * Register Category taxonomy (Branches)
     */
    private static function register_category_taxonomy()
    {
        $category_labels = [
            'name'              => _x('Branches', 'taxonomy general name', 'aanbod-websites'),
            'singular_name'     => _x('Branche', 'taxonomy singular name', 'aanbod-websites'),
            'search_items'      => __('Branches Zoeken', 'aanbod-websites'),
            'all_items'         => __('Alle Branches', 'aanbod-websites'),
            'parent_item'       => __('Parent Branche', 'aanbod-websites'),
            'parent_item_colon' => __('Parent Branche:', 'aanbod-websites'),
            'edit_item'         => __('Branche Bewerken', 'aanbod-websites'),
            'update_item'       => __('Branche Updaten', 'aanbod-websites'),
            'add_new_item'      => __('Nieuwe Branche Toevoegen', 'aanbod-websites'),
            'new_item_name'     => __('Nieuwe Branche Naam', 'aanbod-websites'),
            'menu_name'         => __('Branches', 'aanbod-websites'),
        ];

        $category_args = [
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'branche'],
        ];

        register_taxonomy('website_categorie', ['website'], $category_args);
    }

    /**
     * Register Tag taxonomy (Branche Tags)
     */
    private static function register_tag_taxonomy()
    {
        $tag_labels = [
            'name'              => _x('Branche Tags', 'taxonomy general name', 'aanbod-websites'),
            'singular_name'     => _x('Branche Tag', 'taxonomy singular name', 'aanbod-websites'),
            'search_items'      => __('Branche Tags Zoeken', 'aanbod-websites'),
            'all_items'         => __('Alle Branche Tags', 'aanbod-websites'),
            'edit_item'         => __('Branche Tag Bewerken', 'aanbod-websites'),
            'update_item'       => __('Branche Tag Updaten', 'aanbod-websites'),
            'add_new_item'      => __('Nieuwe Branche Tag Toevoegen', 'aanbod-websites'),
            'new_item_name'     => __('Nieuwe Branche Tag Naam', 'aanbod-websites'),
            'menu_name'         => __('Branche Tags', 'aanbod-websites'),
        ];

        $tag_args = [
            'hierarchical'      => false,
            'labels'            => $tag_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'branche-tag'],
        ];

        register_taxonomy('website_tag', ['website'], $tag_args);
    }
}
