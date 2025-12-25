<?php
/**
 * Theme Setup Class
 *
 * @author  Movve - https://movve.nl
 * @package Nova
 * @since   1.0.0
 */

declare( strict_types=1 );

if ( ! class_exists( 'Nova_Theme_Setup' ) ) :

	class Nova_Theme_Setup {

		/**
		 * Constructor to hook into WordPress actions
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
			add_action( 'init', [ $this, 'register_block_styles' ] );
			add_action( 'init', [ $this, 'register_pattern_categories' ], 9 );
			add_action( 'init', [ $this, 'register_patterns_from_subdirs' ], 11 );
			add_filter( 'body_class', [ $this, 'add_logo_size_body_classes' ] );
		}

		/**
		 * Theme setup functions.
		 *
		 * @since 1.0.0
		 */
		public function setup_theme(): void {
			// Remove core block patterns
			remove_theme_support( 'core-block-patterns' );
			add_filter( 'should_load_remote_block_patterns', '__return_false' );

			// Register button block styles
			register_block_style('core/button', [
				'name'  => 'rounded',
				'label' => __( 'Rounded', 'nova' ),
			]);

			register_block_style('core/button', [
				'name'  => 'rounded-outline',
				'label' => __( 'Rounded Outline', 'nova' ),
			]);
		}

		/**
		 * Enqueue stylesheets for front-end.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_styles(): void {
			$theme_version = wp_get_theme()->get( 'Version' );

			wp_enqueue_style(
				'nova-style',
				get_stylesheet_uri(),
				[],
				$theme_version
			);
            wp_enqueue_style(
                'nova-blocks-style',
                get_template_directory_uri() . '/assets/css/blocks.css',
                array(),
                $theme_version);
		}

		/**
		 * Register custom block styles.
		 *
		 * @since 1.0.0
		 */
		public function register_block_styles(): void {
			register_block_style(
				'core/list',
				[
					'name'         => 'checkmark-list',
					'label'        => __( 'Checkmark', 'nova' ),
					'inline_style' => '
						ul.is-style-checkmark-list {
							list-style-type: "\2713";
						}
						ul.is-style-checkmark-list li {
							padding-inline-start: 1ch;
						}',
				]
			);
		}

		/**
		 * Register custom pattern categories.
		 *
		 * @since 1.0.0
		 */
		public function register_pattern_categories(): void {
			$categories = [
				'heros' => [
					'label'       => __( 'Heros', 'nova' ),
					'description' => __( 'A collection of hero patterns.', 'nova' ),
				],
				'features' => [
					'label'       => __( 'Features', 'nova' ),
					'description' => __( 'A collection of feature-related patterns.', 'nova' ),
				],
				'content' => [
					'label'       => __( 'Content', 'nova' ),
					'description' => __( 'A collection of content-related patterns.', 'nova' ),
				],
				'description' => [
					'label'       => __( 'Description', 'nova' ),
					'description' => __( 'A collection of description patterns.', 'nova' ),
				],
				'push' => [
					'label'       => __( 'Push', 'nova' ),
					'description' => __( 'A collection of push patterns.', 'nova' ),
				],
				'contact' => [
					'label'       => __( 'Contact', 'nova' ),
					'description' => __( 'A collection of contact-related patterns.', 'nova' ),
				],
				'social-proof' => [
					'label'       => __( 'Social Proof', 'nova' ),
					'description' => __( 'A collection of social proof patterns.', 'nova' ),
				],
				'header' => [
					'label'       => __( 'Header', 'nova' ),
					'description' => __( 'A collection of header patterns.', 'nova' ),
				],
				'footer' => [
					'label'       => __( 'Footer', 'nova' ),
					'description' => __( 'A collection of footer patterns.', 'nova' ),
				],
			];

			foreach ( $categories as $slug => $args ) {
				register_block_pattern_category( $slug, $args );
			}
		}

		/**
		 * Register patterns from subdirectories.
		 *
		 * @since 1.0.0
		 */
		public function register_patterns_from_subdirs(): void {
			$patterns_dir = get_template_directory() . '/patterns';

			if ( ! is_dir( $patterns_dir ) ) {
				return;
			}

			// Get all subdirectories in patterns folder
			$subdirs = glob( $patterns_dir . '/*', GLOB_ONLYDIR );

			foreach ( $subdirs as $subdir ) {
				// Get all PHP files in this subdirectory
				$pattern_files = glob( $subdir . '/*.php' );

				foreach ( $pattern_files as $pattern_file ) {
					$this->register_pattern_from_file( $pattern_file );
				}
			}
		}

		/**
		 * Register a single pattern from file.
		 *
		 * @param string $file_path Path to pattern file.
		 *
		 * @since 1.0.0
		 */
		private function register_pattern_from_file( string $file_path ): void {
			if ( ! file_exists( $file_path ) ) {
				return;
			}

			$file_data = get_file_data(
				$file_path,
				[
					'title'       => 'Title',
					'slug'        => 'Slug',
					'description' => 'Description',
					'categories'  => 'Categories',
				]
			);

			// Only register if we have required data
			if ( empty( $file_data['slug'] ) || empty( $file_data['title'] ) ) {
				return;
			}

			// Get pattern content
			ob_start();
			include $file_path;
			$content = ob_get_clean();

			// Prepare categories
			$categories = [];
			if ( ! empty( $file_data['categories'] ) ) {
				$categories = array_map( 'trim', explode( ',', $file_data['categories'] ) );
			}

			// Register the pattern
			register_block_pattern(
				$file_data['slug'],
				[
					'title'       => $file_data['title'],
					'description' => $file_data['description'] ?? '',
					'content'     => $content,
					'categories'  => $categories,
				]
			);
		}

	/**
	 * Add logo size body classes based on admin settings.
	 *
	 * @param array $classes Existing body classes.
	 *
	 * @return array Modified body classes.
	 * @since 1.0.0
	 */
	public function add_logo_size_body_classes( array $classes ): array {
		// Add header logo size class
		$header_logo_size = get_option( 'nova_theme_header_logo_size', 'medium' );
		if ( ! empty( $header_logo_size ) ) {
			$classes[] = 'nova-header-logo-' . sanitize_html_class( $header_logo_size );
		}

		// Add footer logo size class
		$footer_logo_size = get_option( 'nova_theme_footer_logo_size', 'medium' );
		if ( ! empty( $footer_logo_size ) ) {
			$classes[] = 'nova-footer-logo-' . sanitize_html_class( $footer_logo_size );
		}

		return $classes;
	}
}

endif;
