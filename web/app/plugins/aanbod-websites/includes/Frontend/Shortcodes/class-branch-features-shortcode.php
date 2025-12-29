<?php
/**
 * Branch Features Shortcode Class
 * Handles the [branch_features] shortcode functionality
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if ( ! defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Branch_Features_Shortcode
{
    /**
     * Register the shortcode
     */
    public static function register()
    {
        add_shortcode('branch_features', [__CLASS__, 'render']);
    }

    /**
     * Render the branch features shortcode
     *
     * @param array $atts Shortcode attributes
     *
     * @return string HTML output
     */
    public static function render($atts)
    {
        // Only show on taxonomy archives
        if ( ! is_tax('website_categorie')) {
            return '';
        }

        $term = get_queried_object();
        if ( ! $term) {
            return '';
        }

        // Get features from term meta
        $features = get_term_meta($term->term_id, 'branch_features', true);

        if (empty($features) || ! is_array($features)) {
            return '';
        }

        ob_start();
        ?>
        <div class="branch-features-grid alignwide">
            <?php foreach ($features as $feature): ?>
                <?php
                $title = $feature['title'] ?? '';
                $image_id = $feature['image'] ?? 0;

                if (empty($title)) {
                    continue;
                }
                ?>
                <div class="branch-feature-item">
                    <?php if ($image_id): ?>
                        <div class="branch-feature-image">
                            <?php echo wp_get_attachment_image($image_id, 'large', false, ['alt' => esc_attr($title)]); ?>
                        </div>
                    <?php endif; ?>
                    <div class="branch-feature-label"><p><?php echo esc_html($title); ?></p></div></div>
            <?php endforeach; ?>
        </div>

        <style>
            .branch-features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin: 20px 0 0 0;
            }
            .branch-feature-item {
                display: flex;
                flex-direction: column;
            }
            .branch-feature-image {
                width: 100%;
                overflow: hidden;
            }
            .branch-feature-image img {
                width: 100%;
                height: 200px;
                object-fit: cover;
                display: block;
            }
            .branch-feature-label {
                background: var(--wp--preset--color--nova-100, #414B5A);
                padding: 12px 20px;
                text-align: center;
            }
            .branch-feature-label p {
                margin: 0;
                color: #fff;
                font-size: 16px;
                font-weight: 500;
            }
        </style>
        <?php
        return ob_get_clean();
    }
}
