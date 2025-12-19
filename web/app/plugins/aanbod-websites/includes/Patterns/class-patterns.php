<?php
/**
 * Patterns Class
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Patterns
{
    /**
     * Register patterns and pattern category
     */
    public static function register()
    {
        add_action('init', [__CLASS__, 'register_pattern_category']);
        add_action('init', [__CLASS__, 'register_patterns']);
    }

    /**
     * Register pattern category
     */
    public static function register_pattern_category()
    {
        register_block_pattern_category(
            'aanbod-websites',
            [
                'label' => __('Websites', 'aanbod-websites'),
            ]
        );
    }

    /**
     * Register all patterns
     */
    public static function register_patterns()
    {
        $patterns_dir = plugin_dir_path(dirname(dirname(__FILE__))) . 'patterns/';

        if (!is_dir($patterns_dir)) {
            return;
        }

        $patterns = glob($patterns_dir . '*.php');

        foreach ($patterns as $pattern_file) {
            $pattern_data = get_file_data($pattern_file, [
                'title'       => 'Title',
                'slug'        => 'Slug',
                'description' => 'Description',
                'categories'  => 'Categories',
                'keywords'    => 'Keywords',
            ]);

            if (empty($pattern_data['slug'])) {
                continue;
            }

            ob_start();
            include $pattern_file;
            $pattern_content = ob_get_clean();

            $categories = !empty($pattern_data['categories'])
                ? array_map('trim', explode(',', $pattern_data['categories']))
                : ['aanbod-websites'];

            $keywords = !empty($pattern_data['keywords'])
                ? array_map('trim', explode(',', $pattern_data['keywords']))
                : [];

            register_block_pattern(
                'aanbod-websites/' . $pattern_data['slug'],
                [
                    'title'       => $pattern_data['title'],
                    'description' => $pattern_data['description'],
                    'content'     => $pattern_content,
                    'categories'  => $categories,
                    'keywords'    => $keywords,
                ]
            );
        }
    }
}
