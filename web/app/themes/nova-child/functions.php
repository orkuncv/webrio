<?php
/**
 * Nova Child Theme Functions
 *
 * @author  Movve - https://movve.nl
 * @package Nova
 * @since   1.0.0
 */

declare( strict_types=1 );

/**
 * Load the parent theme autoloader
 *
 * @since 1.0.0
 */
require_once get_template_directory() . '/inc/autoloader.php';

/**
 * Load the child theme bootstrap
 *
 * @since 1.0.0
 */
require_once get_stylesheet_directory() . '/inc/bootstrap.php';

/**
 * Enqueue Google Fonts
 *
 * @since 1.0.0
 */
function nova_child_enqueue_google_fonts(): void {
	wp_enqueue_style(
		'nova-child-google-fonts',
		'https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&display=swap',
		[],
		null
	);
}
add_action( 'wp_enqueue_scripts', 'nova_child_enqueue_google_fonts' );
