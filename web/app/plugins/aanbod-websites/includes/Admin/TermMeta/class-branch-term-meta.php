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
            <label><?php _e('Branch Features', 'aanbod-websites'); ?></label>
            <div id="branch-features-repeater">
                <div class="branch-features-empty" style="padding: 20px; background: #f0f0f1; border-radius: 4px;">
                    <p><?php _e('Voeg features toe om ze hier te zien.', 'aanbod-websites'); ?></p>
                </div>
            </div>
            <button type="button" class="button button-primary" id="add-branch-feature" style="margin-top: 10px;">
                <?php _e('+ Voeg Feature Toe', 'aanbod-websites'); ?>
            </button>
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
        $features = get_term_meta($term->term_id, 'branch_features', true);
        if (!is_array($features)) {
            $features = [];
        }
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
                <label><?php _e('Branch Features', 'aanbod-websites'); ?></label>
            </th>
            <td>
                <div id="branch-features-repeater" style="margin-bottom: 10px;">
                    <?php if (empty($features)): ?>
                        <div class="branch-features-empty" style="padding: 20px; background: #f0f0f1; border-radius: 4px; margin-bottom: 10px;">
                            <p><?php _e('Geen features toegevoegd. Klik op "Voeg Feature Toe" om te beginnen.', 'aanbod-websites'); ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($features as $index => $feature): ?>
                            <div class="branch-feature-item" data-index="<?php echo $index; ?>" style="background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 4px; border: 1px solid #ddd;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <strong><?php _e('Feature', 'aanbod-websites'); ?> #<?php echo ($index + 1); ?></strong>
                                    <button type="button" class="button button-small remove-branch-feature"><?php _e('Verwijder', 'aanbod-websites'); ?></button>
                                </div>

                                <p>
                                    <label><?php _e('Titel', 'aanbod-websites'); ?></label><br>
                                    <input type="text" name="branch_features[<?php echo $index; ?>][title]"
                                           value="<?php echo esc_attr($feature['title'] ?? ''); ?>"
                                           class="regular-text" placeholder="Bijvoorbeeld: Responsive Design" />
                                </p>

                                <p>
                                    <label><?php _e('Afbeelding', 'aanbod-websites'); ?></label><br>
                                    <input type="hidden" name="branch_features[<?php echo $index; ?>][image]"
                                           value="<?php echo esc_attr($feature['image'] ?? ''); ?>"
                                           class="branch-feature-image-id" />
                                    <button type="button" class="button branch-feature-upload-image"><?php _e('Upload afbeelding', 'aanbod-websites'); ?></button>
                                    <button type="button" class="button branch-feature-remove-image" style="<?php echo !empty($feature['image']) ? '' : 'display:none;'; ?>"><?php _e('Verwijder afbeelding', 'aanbod-websites'); ?></button>
                                    <div class="branch-feature-image-preview" style="margin-top: 10px;">
                                        <?php if (!empty($feature['image'])): ?>
                                            <?php echo wp_get_attachment_image($feature['image'], 'thumbnail'); ?>
                                        <?php endif; ?>
                                    </div>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" class="button button-primary" id="add-branch-feature">
                    <?php _e('+ Voeg Feature Toe', 'aanbod-websites'); ?>
                </button>
                <p class="description"><?php _e('Voeg features met afbeeldingen toe die specifiek zijn voor deze branche.', 'aanbod-websites'); ?></p>
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

        // Save repeater features
        if (isset($_POST['branch_features']) && is_array($_POST['branch_features'])) {
            $features = [];
            foreach ($_POST['branch_features'] as $feature) {
                $features[] = [
                    'title' => sanitize_text_field($feature['title'] ?? ''),
                    'image' => absint($feature['image'] ?? 0),
                ];
            }
            update_term_meta($term_id, 'branch_features', $features);
        } else {
            // If no features, save empty array
            update_term_meta($term_id, 'branch_features', []);
        }
    }
}
