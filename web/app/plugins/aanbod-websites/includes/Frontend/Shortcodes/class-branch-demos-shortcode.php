<?php
/**
 * Branch Demos Shortcode Class
 * Displays website posts in a modern card grid
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if ( ! defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Branch_Demos_Shortcode
{
    /**
     * Register the shortcode
     */
    public static function register()
    {
        add_shortcode('branch_demos', [__CLASS__, 'render']);
    }

    /**
     * Render the branch demos shortcode
     *
     * @param array $atts Shortcode attributes
     *
     * @return string HTML output
     */
    public static function render($atts)
    {
        // Parse attributes
        $atts = shortcode_atts([
            'posts_per_page' => 9,
            'orderby' => 'date',
            'order' => 'DESC',
        ], $atts);

        // Get current term if on taxonomy archive
        $current_term = null;
        if (is_tax('website_categorie')) {
            $current_term = get_queried_object();
        }

        // Query args
        $query_args = [
            'post_type' => 'aanbod_website',
            'posts_per_page' => intval($atts['posts_per_page']),
            'orderby' => sanitize_text_field($atts['orderby']),
            'order' => sanitize_text_field($atts['order']),
            'post_status' => 'publish',
        ];

        // Filter by current taxonomy term if available
        if ($current_term && isset($current_term->term_id)) {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'website_categorie',
                    'field' => 'term_id',
                    'terms' => $current_term->term_id,
                ],
            ];
        }

        $query = new WP_Query($query_args);

        if (!$query->have_posts()) {
            return '<p class="no-demos-found">Geen websites gevonden.</p>';
        }

        ob_start();
        ?>
        <div class="branch-demos-container alignwide">
            <div class="branch-demos-grid">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <article class="branch-demo-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="demo-card-image">
                                <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                                    <?php the_post_thumbnail('large', ['class' => 'demo-thumbnail']); ?>
                                    <div class="demo-image-overlay">
                                        <span class="demo-view-label">Bekijk demo</span>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="demo-card-content">
                            <h3 class="demo-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <?php if (has_excerpt()) : ?>
                                <div class="demo-card-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </div>
                            <?php endif; ?>

                            <div class="demo-card-footer">
                                <a href="<?php the_permalink(); ?>" class="demo-card-link">
                                    Lees meer
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 12L10 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>

        <style>
            .branch-demos-container {
                margin: 40px auto;
            }

            .branch-demos-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 32px;
                margin: 0;
            }

            @media (max-width: 768px) {
                .branch-demos-grid {
                    grid-template-columns: 1fr;
                    gap: 24px;
                }
            }

            .branch-demo-card {
                background: #fff;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .branch-demo-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            }

            .demo-card-image {
                position: relative;
                overflow: hidden;
                aspect-ratio: 4 / 3;
                background: var(--wp--preset--color--nova-100, #414B5A);
            }

            .demo-card-image a {
                display: block;
                height: 100%;
                text-decoration: none;
            }

            .demo-thumbnail {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .branch-demo-card:hover .demo-thumbnail {
                transform: scale(1.05);
            }

            .demo-image-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(65, 75, 90, 0.85);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .branch-demo-card:hover .demo-image-overlay {
                opacity: 1;
            }

            .demo-view-label {
                color: var(--wp--preset--color--success-green, #BBFFB0);
                font-size: 18px;
                font-weight: 600;
                letter-spacing: 0.02em;
            }

            .demo-card-content {
                padding: 24px;
                display: flex;
                flex-direction: column;
                flex-grow: 1;
            }

            .demo-card-title {
                margin: 0 0 12px 0;
                font-size: 22px;
                font-weight: 600;
                line-height: 1.3;
            }

            .demo-card-title a {
                color: var(--wp--preset--color--nova-100, #414B5A);
                text-decoration: none;
                transition: color 0.2s ease;
            }

            .demo-card-title a:hover {
                color: var(--wp--preset--color--success-green, #BBFFB0);
            }

            .demo-card-excerpt {
                color: #666;
                font-size: 15px;
                line-height: 1.6;
                margin: 0 0 20px 0;
                flex-grow: 1;
            }

            .demo-card-footer {
                margin-top: auto;
                padding-top: 16px;
                border-top: 1px solid #f0f0f0;
            }

            .demo-card-link {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                color: var(--wp--preset--color--nova-100, #414B5A);
                font-size: 15px;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .demo-card-link:hover {
                color: var(--wp--preset--color--success-green, #BBFFB0);
                gap: 10px;
            }

            .demo-card-link svg {
                transition: transform 0.2s ease;
            }

            .demo-card-link:hover svg {
                transform: translateX(2px);
            }

            .no-demos-found {
                text-align: center;
                padding: 60px 20px;
                color: #666;
                font-size: 18px;
            }

            /* Stagger animation on load */
            .branch-demo-card {
                animation: fadeInUp 0.6s ease forwards;
                opacity: 0;
            }

            .branch-demo-card:nth-child(1) { animation-delay: 0.1s; }
            .branch-demo-card:nth-child(2) { animation-delay: 0.2s; }
            .branch-demo-card:nth-child(3) { animation-delay: 0.3s; }
            .branch-demo-card:nth-child(4) { animation-delay: 0.4s; }
            .branch-demo-card:nth-child(5) { animation-delay: 0.5s; }
            .branch-demo-card:nth-child(6) { animation-delay: 0.6s; }
            .branch-demo-card:nth-child(7) { animation-delay: 0.7s; }
            .branch-demo-card:nth-child(8) { animation-delay: 0.8s; }
            .branch-demo-card:nth-child(9) { animation-delay: 0.9s; }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
