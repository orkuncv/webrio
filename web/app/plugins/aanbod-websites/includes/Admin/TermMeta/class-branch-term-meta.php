<?php
/**
 * Branch Term Meta Class
 * Handles custom fields for website_categorie taxonomy
 *
 * @package Aanbod_Websites
 * @since 1.0.0
 */

if ( ! defined('ABSPATH')) {
    exit;
}

class Aanbod_Websites_Branch_Term_Meta
{
    /**
     * Register hooks for term meta fields
     */
    public static function register()
    {
        add_action('website_categorie_add_form_fields', [__CLASS__, 'add_term_fields']);
        add_action('website_categorie_edit_form_fields', [__CLASS__, 'edit_term_fields'], 10, 2);
        add_action('created_website_categorie', [__CLASS__, 'save_term_meta']);
        add_action('edited_website_categorie', [__CLASS__, 'save_term_meta']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_media_scripts']);
    }

    /**
     * Enqueue media uploader scripts
     */
    public static function enqueue_media_scripts($hook)
    {
        if ('edit-tags.php' !== $hook && 'term.php' !== $hook) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_script('branch-term-meta', plugin_dir_url(__FILE__) . '../../../assets/branch-term-meta.js', ['jquery'], '1.0.0', true);
    }

    /**
     * Add custom fields to term add form
     *
     * @param string $taxonomy Current taxonomy slug
     */
    public static function add_term_fields($taxonomy)
    {
        ?>
        <div class="form-field">
            <label for="branch_image"><?php _e('Branch afbeelding', 'aanbod-websites'); ?></label>
            <input type="hidden" name="branch_image" id="branch_image" value="" />
            <button type="button" class="button branch-upload-image"><?php _e('Upload afbeelding', 'aanbod-websites'); ?></button>
            <button type="button" class="button branch-remove-image" style="display:none;"><?php _e('Verwijder afbeelding', 'aanbod-websites'); ?></button>
            <div class="branch-image-preview" style="margin-top: 10px;"></div>
        </div>

        <div class="form-field">
            <label for="branch_price"><?php _e('Startprijs per maand (€)', 'aanbod-websites'); ?></label>
            <input type="text" name="branch_price" id="branch_price" placeholder="99" />
            <p class="description"><?php _e('Bijvoorbeeld: 99', 'aanbod-websites'); ?></p>
        </div>

        <div class="form-field">
            <label for="branch_usp_1"><?php _e('USP 1', 'aanbod-websites'); ?></label>
            <input type="text" name="branch_usp_1" id="branch_usp_1" placeholder="Bijvoorbeeld: Moderne designs" />
        </div>

        <div class="form-field">
            <label for="branch_usp_2"><?php _e('USP 2', 'aanbod-websites'); ?></label>
            <input type="text" name="branch_usp_2" id="branch_usp_2" placeholder="Bijvoorbeeld: SEO geoptimaliseerd" />
        </div>

        <div class="form-field">
            <label for="branch_usp_3"><?php _e('USP 3', 'aanbod-websites'); ?></label>
            <input type="text" name="branch_usp_3" id="branch_usp_3" placeholder="Bijvoorbeeld: 24/7 support" />
        </div>
        <?php
    }

    /**
     * Add custom fields to term edit form
     *
     * @param WP_Term $term Current term object
     * @param string $taxonomy Current taxonomy slug
     */
    public static function edit_term_fields($term, $taxonomy)
    {
        $image_id = get_term_meta($term->term_id, 'branch_image', true);
        $price = get_term_meta($term->term_id, 'branch_price', true);
        $usp_1 = get_term_meta($term->term_id, 'branch_usp_1', true);
        $usp_2 = get_term_meta($term->term_id, 'branch_usp_2', true);
        $usp_3 = get_term_meta($term->term_id, 'branch_usp_3', true);
        ?>
        <tr class="form-field">
            <th scope="row">
                <label for="branch_image"><?php _e('Branch afbeelding', 'aanbod-websites'); ?></label>
            </th>
            <td>
                <input type="hidden" name="branch_image" id="branch_image" value="<?php echo esc_attr($image_id); ?>" />
                <button type="button" class="button branch-upload-image"><?php _e('Upload afbeelding', 'aanbod-websites'); ?></button>
                <button type="button" class="button branch-remove-image" style="<?php echo $image_id ? '' : 'display:none;'; ?>"><?php _e('Verwijder afbeelding', 'aanbod-websites'); ?></button>
                <div class="branch-image-preview" style="margin-top: 10px;">
                    <?php if ($image_id): ?>
                        <?php echo wp_get_attachment_image($image_id, 'medium'); ?>
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="branch_price"><?php _e('Startprijs per maand (€)', 'aanbod-websites'); ?></label>
            </th>
            <td>
                <input type="text" name="branch_price" id="branch_price"
                       value="<?php echo esc_attr($price); ?>" placeholder="99" />
                <p class="description"><?php _e('Bijvoorbeeld: 99', 'aanbod-websites'); ?></p>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="branch_usp_1"><?php _e('USP 1', 'aanbod-websites'); ?></label>
            </th>
            <td>
                <input type="text" name="branch_usp_1" id="branch_usp_1"
                       value="<?php echo esc_attr($usp_1); ?>"
                       placeholder="Bijvoorbeeld: Moderne designs" class="regular-text" />
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="branch_usp_2"><?php _e('USP 2', 'aanbod-websites'); ?></label>
            </th>
            <td>
                <input type="text" name="branch_usp_2" id="branch_usp_2"
                       value="<?php echo esc_attr($usp_2); ?>"
                       placeholder="Bijvoorbeeld: SEO geoptimaliseerd" class="regular-text" />
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row">
                <label for="branch_usp_3"><?php _e('USP 3', 'aanbod-websites'); ?></label>
            </th>
            <td>
                <input type="text" name="branch_usp_3" id="branch_usp_3"
                       value="<?php echo esc_attr($usp_3); ?>"
                       placeholder="Bijvoorbeeld: 24/7 support" class="regular-text" />
            </td>
        </tr>
        <?php
    }

    /**
     * Save term meta fields
     *
     * @param int $term_id Term ID
     */
    public static function save_term_meta($term_id)
    {
        if ( ! current_user_can('edit_term', $term_id)) {
            return;
        }

        if (isset($_POST['branch_image'])) {
            update_term_meta($term_id, 'branch_image', absint($_POST['branch_image']));
        }

        if (isset($_POST['branch_price'])) {
            update_term_meta($term_id, 'branch_price', sanitize_text_field($_POST['branch_price']));
        }

        if (isset($_POST['branch_usp_1'])) {
            update_term_meta($term_id, 'branch_usp_1', sanitize_text_field($_POST['branch_usp_1']));
        }

        if (isset($_POST['branch_usp_2'])) {
            update_term_meta($term_id, 'branch_usp_2', sanitize_text_field($_POST['branch_usp_2']));
        }

        if (isset($_POST['branch_usp_3'])) {
            update_term_meta($term_id, 'branch_usp_3', sanitize_text_field($_POST['branch_usp_3']));
        }
    }
}
