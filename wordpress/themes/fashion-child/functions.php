<?php
/**
 * Bootstrap for the Fashion child theme.
 *
 * @package Fashion_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register child-theme capabilities.
 */
function fashion_child_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'fashion_child_setup' );

/**
 * Load parent and child assets.
 */
function fashion_child_enqueue_assets() {
	wp_enqueue_style(
		'botiga-parent',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme( 'botiga' )->get( 'Version' )
	);

	wp_enqueue_style(
		'fashion-child',
		get_stylesheet_uri(),
		array( 'botiga-parent' ),
		wp_get_theme()->get( 'Version' )
	);

	$catalog_css = get_stylesheet_directory() . '/assets/css/catalog.css';
	if ( file_exists( $catalog_css ) ) {
		wp_enqueue_style(
			'fashion-catalog',
			get_stylesheet_directory_uri() . '/assets/css/catalog.css',
			array( 'fashion-child' ),
			(string) filemtime( $catalog_css )
		);
	}

	$catalog_js = get_stylesheet_directory() . '/assets/js/catalog.js';
	if ( file_exists( $catalog_js ) ) {
		wp_enqueue_script(
			'fashion-catalog',
			get_stylesheet_directory_uri() . '/assets/js/catalog.js',
			array(),
			(string) filemtime( $catalog_js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fashion_child_enqueue_assets', 20 );

foreach ( array( 'storefront-presentation.php', 'collections.php', 'product-detail.php' ) as $fashion_child_include ) {
	$fashion_child_path = get_stylesheet_directory() . '/inc/' . $fashion_child_include;
	if ( file_exists( $fashion_child_path ) ) {
		require_once $fashion_child_path;
	}
}
