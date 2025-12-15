<?php
/**
 * Admin Theme Settings Form
 *
 * @author  Movve - https://movve.nl
 *
 * @since   1.0.0
 */
if (! defined('NOVA_INIT')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

$gtm_id = $gtm_id ?? '';
$youtube = $youtube ?? '';
$linkedin = $linkedin ?? '';
$facebook = $facebook ?? '';
$instagram = $instagram ?? '';
$header_logo = $header_logo ?? '';
$footer_logo = $footer_logo ?? '';
$header_logo_size = $header_logo_size ?? '72';
$footer_logo_size = $footer_logo_size ?? '76';
$cookiebot_script = $cookiebot_script ?? ''; ?>

<form id="nova-theme-settings-form-left" class="nova-theme-settings-form" enctype="multipart/form-data">

    <!-- Nonce -->
	<?php wp_nonce_field('save_theme_settings_nonce', 'theme_settings_nonce'); ?>

    <!-- Header Logo -->
	<?php echo nova_get_admin_template('inputs/upload-image', [
	    'id' => 'nova_theme_header_logo',
	    'name' => 'nova_theme_header_logo',
	    'label' => __('Header Logo', 'nova'),
	    'value' => $header_logo,
	]); ?>

    <!-- Header Logo Size -->
	<?php echo nova_get_admin_template('inputs/regular-range', [
	    'id' => 'nova_theme_header_logo_size',
	    'name' => 'nova_theme_header_logo_size',
	    'label' => __('Header Logo Grootte', 'nova'),
	    'value' => $header_logo_size,
	    'description' => __('Kies de grootte van het header logo', 'nova'),
	    'min' => 20,
	    'max' => 120,
	    'step' => 4,
	    'unit' => 'px',
	]); ?>

    <!-- Footer Logo -->
	<?php echo nova_get_admin_template('inputs/upload-image', [
	    'id' => 'nova_theme_footer_logo',
	    'name' => 'nova_theme_footer_logo',
	    'label' => __('Footer Logo', 'nova'),
	    'value' => $footer_logo,
	]); ?>

    <!-- Footer Logo Size -->
	<?php echo nova_get_admin_template('inputs/regular-range', [
	    'id' => 'nova_theme_footer_logo_size',
	    'name' => 'nova_theme_footer_logo_size',
	    'label' => __('Footer Logo Grootte', 'nova'),
	    'value' => $footer_logo_size,
	    'description' => __('Kies de grootte van het footer logo', 'nova'),
	    'min' => 20,
	    'max' => 120,
	    'step' => 4,
	    'unit' => 'px',
	]); ?>

    <!-- Spacer -->
	<?php echo nova_get_admin_template('inputs/spacer'); ?>

    <!-- Facebook -->
	<?php echo nova_get_admin_template('inputs/regular-text', [
	    'id' => 'nova_theme_facebook',
	    'name' => 'nova_theme_facebook',
	    'label' => __('Facebook', 'nova'),
	    'value' => $facebook,
	]); ?>

    <!-- Instagram -->
    <?php echo nova_get_admin_template('inputs/regular-text', [
        'id' => 'nova_theme_instagram',
        'name' => 'nova_theme_instagram',
        'label' => __('Instagram', 'nova'),
        'value' => $instagram,
    ]); ?>

    <!-- LinkedIn -->
    <?php echo nova_get_admin_template('inputs/regular-text', [
        'id' => 'nova_theme_linkedin',
        'name' => 'nova_theme_linkedin',
        'label' => __('LinkedIn', 'nova'),
        'value' => $linkedin,
    ]); ?>

    <!-- YouTube -->
    <?php echo nova_get_admin_template('inputs/regular-text', [
        'id' => 'nova_theme_youtube',
        'name' => 'nova_theme_youtube',
        'label' => __('YouTube', 'nova'),
        'value' => $youtube,
    ]); ?>

    <!-- Spacer -->
	<?php echo nova_get_admin_template('inputs/spacer'); ?>

    <!-- Google Tag Manager -->
	<?php echo nova_get_admin_template('inputs/regular-text', [
	    'id' => 'nova_theme_gtm_id',
	    'name' => 'nova_theme_gtm_id',
	    'label' => __('Google Tag Manager Id', 'nova'),
	    'value' => $gtm_id,
	]); ?>

    <!-- Cookiebot Script -->
	<?php echo nova_get_admin_template('inputs/regular-text', [
	    'id' => 'nova_theme_cookiebot_script',
	    'name' => 'nova_theme_cookiebot_script',
	    'label' => __('CookieBot Script Id', 'nova'),
	    'value' => $cookiebot_script,
	]); ?>

    <!-- Spacer -->
	<?php echo nova_get_admin_template('inputs/spacer'); ?>

    <!-- Save Button -->
	<?php submit_button(__('Instellingen Opslaan', 'nova')); ?>
</form>
