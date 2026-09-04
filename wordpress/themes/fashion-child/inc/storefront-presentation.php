<?php
/**
 * Theme-specific presentation adapters for the display catalog.
 *
 * @package Fashion_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove purchase components from the single-product presentation.
 *
 * @param array $components Component names.
 * @return array
 */
function fashion_child_without_purchase_components( $components ) {
	return array_values(
		array_filter(
			(array) $components,
			static function ( $component ) {
				return 'woocommerce_template_single_add_to_cart' !== $component;
			}
		)
	);
}
add_filter( 'botiga_default_single_product_components', 'fashion_child_without_purchase_components' );
add_filter( 'theme_mod_single_product_elements_order', 'fashion_child_without_purchase_components' );

/**
 * Remove commerce icon groups from the parent theme's header positions.
 *
 * @param array $groups Header component groups.
 * @return array
 */
function fashion_child_catalog_header_components( $groups ) {
	foreach ( (array) $groups as $position => $components ) {
		$groups[ $position ] = array_values(
			array_diff(
				(array) $components,
				array( 'woocommerce_icons', 'mobile_woocommerce_icons' )
			)
		);
	}
	return $groups;
}
add_filter( 'botiga_default_header_components', 'fashion_child_catalog_header_components' );

foreach (
	array(
		'enable_header_cart',
		'enable_header_account',
		'enable_mobile_header_cart',
		'enable_mobile_header_account',
		'enable_mobile_header_offcanvas_cart',
		'enable_mobile_header_offcanvas_account',
	) as $fashion_child_disabled_theme_mod
) {
	add_filter( 'theme_mod_' . $fashion_child_disabled_theme_mod, '__return_false' );
}

/**
 * Remove empty parent-theme wrappers after the core plugin removes the form.
 */
function fashion_child_remove_purchase_wrappers() {
	remove_action( 'woocommerce_before_add_to_cart_button', 'botiga_single_addtocart_wrapper_open' );
	remove_action( 'woocommerce_after_add_to_cart_button', 'botiga_single_addtocart_wrapper_close' );
}
add_action( 'wp', 'fashion_child_remove_purchase_wrappers', 100 );

/**
 * Add a stable body class for display-only styling.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function fashion_child_catalog_body_class( $classes ) {
	$classes[] = 'fashion-catalog-only';
	return $classes;
}
add_filter( 'body_class', 'fashion_child_catalog_body_class' );
