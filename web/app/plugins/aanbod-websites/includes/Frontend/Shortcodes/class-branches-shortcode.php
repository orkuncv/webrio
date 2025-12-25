<?php
/**
 * Branches Shortcode Class
 * Handles the [website_branches] shortcode functionality
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if ( ! defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Branches_Shortcode
{

    /**
     * Register the shortcode
     */
    public static function register()
    {
        add_shortcode('website_branches', [__CLASS__, 'render']);
    }

    /**
     * Render the branches shortcode
     *
     * @param array $atts Shortcode attributes
     *
     * @return string HTML output
     */
    public static function render($atts)
    {
        // Disable wpautop for this shortcode
        $wpautop_enabled = has_filter('the_content', 'wpautop');
        if ($wpautop_enabled !== false) {
            remove_filter('the_content', 'wpautop');
        }

        $atts = shortcode_atts([
                'orderby' => 'name',
                'order' => 'ASC',
        ], $atts);

        // Get all branch terms
        $terms = get_terms([
                'taxonomy' => 'website_categorie',
                'hide_empty' => false,
                'orderby' => $atts['orderby'],
                'order' => $atts['order'],
        ]);

        // Check if we have terms
        if (empty($terms) || is_wp_error($terms)) {
            return '<p>' . __('Geen branches beschikbaar.', 'aanbod-websites') . '</p>';
        }

        ob_start();
        ?>
        <div class="website-branches-container alignwide">
        <style>
            .website-branches-container {
                /*width: 100%;*/
                /*max-width: 100%;*/
                /*padding: 30px 0;*/
            }
            .website-branches-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 30px;
                padding: 15px 0;
            }
            .branch-card {
                border-radius: 10px;
                display: block;
                height: 350px;
                overflow: hidden;
                position: relative;
                transform: perspective(1px) translateZ(0);
                transition-duration: .3s;
                transition-property: transform;
                transition-timing-function: ease-in-out;
                width: 100%;
            }
            .branch-card .overlay_link {
                height: 100%;
                left: 0;
                position: absolute;
                top: 0;
                width: 100%;
            }
            .branch-card:hover {
                box-shadow: 0 3px 20px rgba(0, 0, 0, .40);
            }
            .branch-card .gridItemImg {
                height: 100%;
                position: relative;
                width: 100%;
            }
            .branch-card .gridItemImg img{
                height: 100%;
                -o-object-fit: cover;
                object-fit: cover;
                width: 100%;
            }
            .branch-card .gridItemContent {
                background: linear-gradient(180deg, transparent 20%, #000 100%);
                background-color: transparent;
                bottom: 0;
                color: #fff;
                cursor: pointer;
                height: 100%;
                left: 0;
                position: absolute;
                transition: all .1s;
                width: 100%;
            }
            .branch-card .gridItemContent .gridItemContentWrapper {
                align-items: flex-start;
                display: -webkit-box;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
                flex-direction: column;
                height: 100%;
                justify-content: flex-end;
                position: relative;
                top: 0;
                width: 100%;
            }
            .branch-card .gridItemContent .gridItemContentWrapper p {
                margin: 0;
                padding: 0 0 0 30px;
            }
            .branch-card .gridItemContent .gridItemContentWrapper h4 {
                margin: 0 0 15px 0;
                padding: 0 0 0 30px;
            }
            .branch-card .gridItemContent .gridItemContentWrapper .branch-cta {
                background: #fff;
                border: 2px solid #fff;
                border-radius: 5px;
                color: #414B5A;
                display: inline-block;
                font-size: 14px;
                font-weight: 600;
                margin: 0 0 30px 30px;
                padding: 10px 20px;
                text-decoration: none;
                transition: all .3s ease;
                position: relative;
                z-index: 10;
            }
            .branch-card .gridItemContent .gridItemContentWrapper .branch-cta:hover {
                background: #414B5A;
                color: #fff;
                transform: translateY(-2px);
            }
        </style>
        <div class="website-branches-grid"><?php
            $fallback_image_url = 'https://dummyimage.com/380x350/000/fff';

            foreach ($terms as $term):
                // Get term meta
                $image_id = get_term_meta($term->term_id, 'branch_image', true);
                $price = get_term_meta($term->term_id, 'branch_price', true);
                $usp_1 = get_term_meta($term->term_id, 'branch_usp_1', true) ?: 'Binnen enkele dagen online';
                $usp_2 = get_term_meta($term->term_id, 'branch_usp_2', true) ?: 'Alles voor één vast maandbedrag';
                $usp_3 = get_term_meta($term->term_id, 'branch_usp_3', true) ?: '100% mobielvriendelijk ontwerp';

                // Get and truncate description
                $description = $term->description;
                if (strlen($description) > 120) {
                    $description = substr($description, 0, 120) . '...';
                }

                $price = $price ? $price : '49';
                $demo_url = get_term_link($term);
                ?>
                <?php if ($demo_url && ! is_wp_error($demo_url)): ?>
                <div class="branch-card">
                    <div class="gridItemImg">
                        <?php if ($image_id): ?>
                            <?php echo wp_get_attachment_image($image_id, 'medium', false, ['class' => 'branch-image']); ?>
                        <?php else: ?>
                            <img src="<?php echo esc_url($fallback_image_url); ?>" alt="<?php echo esc_attr($term->name); ?>" class="branch-image"/>
                        <?php endif; ?><a href="<?php echo esc_url($demo_url); ?>" class="overlay_link"></a>
                    </div>
                    <div class="gridItemContent">
                        <div class="gridItemContentWrapper">
                            <h4><?php echo esc_html($term->name); ?></h4>
                            <a href="<?php echo esc_url($demo_url); ?>" class="branch-cta">Bekijk websites</a>
                            <a href="<?php echo esc_url($demo_url); ?>" class="overlay_link"></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php endforeach; ?></div>
        </div><?php
        $output = ob_get_clean();

        // Re-enable wpautop if it was enabled
        if ($wpautop_enabled !== false) {
            add_filter('the_content', 'wpautop');
        }

        return trim($output);
    }
}
