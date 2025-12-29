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

remove_filter( 'the_content', 'wpautop' );

/**
 * Replace term name placeholder in term descriptions
 *
 * @since 1.0.0
 */
function nova_child_replace_term_placeholders( string $description, $term_id = null ): string {
	// Get term object
	if ( $term_id ) {
		$term = get_term( $term_id );
	} else {
		$term = get_queried_object();
	}

	// If no term found, return original description
	if ( ! $term || ! isset( $term->name ) ) {
		return $description;
	}

	// Replace placeholders
	$replacements = [
		'{term_name}' => $term->name,
		'{term_slug}' => $term->slug ?? '',
		'{term_count}' => $term->count ?? 0,
	];

	return str_replace( array_keys( $replacements ), array_values( $replacements ), $description );
}
add_filter( 'get_the_archive_description', 'nova_child_replace_term_placeholders', 10, 1 );
add_filter( 'term_description', 'nova_child_replace_term_placeholders', 10, 2 );
add_filter( 'the_content', 'nova_child_replace_term_placeholders', 10, 1 );
add_filter( 'the_title', 'nova_child_replace_term_placeholders', 10, 1 );
add_filter( 'render_block', 'nova_child_replace_term_placeholders', 10, 1 );

/**
 * Add taxonomy term image to cover block on taxonomy archives
 *
 * @since 1.0.0
 */
function nova_child_add_taxonomy_image_to_cover() {
	// Only run on taxonomy archives
	if ( ! is_tax( 'website_categorie' ) ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term ) {
		return;
	}

	// Get branch image from term meta
	$image_id = get_term_meta( $term->term_id, 'branch_image', true );
	if ( ! $image_id ) {
		return;
	}

	$image_url = wp_get_attachment_image_url( $image_id, 'full' );
	if ( ! $image_url ) {
		return;
	}

	// Add inline CSS to set the cover background
	?>
	<style>
		.nova-hero-bg .wp-block-cover__image-background,
		.nova-hero-bg::before {
			background-image: url('<?php echo esc_url( $image_url ); ?>') !important;
			background-size: cover !important;
			background-position: center !important;
		}
		.nova-hero-bg {
			background-image: url('<?php echo esc_url( $image_url ); ?>');
			background-size: cover;
			background-position: center;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'nova_child_add_taxonomy_image_to_cover' );
