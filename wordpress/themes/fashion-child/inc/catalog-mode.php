<?php
/**
 * Display-only WooCommerce behavior for the prototype.
 *
 * @package Fashion_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Remove purchase components from Botiga's component arrays.
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
 * Remove WooCommerce icon groups from Botiga's default header positions.
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
 * Remove standard purchase controls after WooCommerce and Botiga register them.
 */
function fashion_child_remove_purchase_actions() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	remove_action( 'woocommerce_before_add_to_cart_button', 'botiga_single_addtocart_wrapper_open' );
	remove_action( 'woocommerce_after_add_to_cart_button', 'botiga_single_addtocart_wrapper_close' );
}
add_action( 'wp', 'fashion_child_remove_purchase_actions', 100 );

/**
 * Mark products non-purchasable in the public prototype.
 *
 * @param bool       $purchasable Existing state.
 * @param WC_Product $product     Current product.
 * @return bool
 */
function fashion_child_catalog_is_purchasable( $purchasable, $product ) {
	unset( $purchasable, $product );
	return false;
}
add_filter( 'woocommerce_is_purchasable', 'fashion_child_catalog_is_purchasable', 100, 2 );

/**
 * Add a stable body class for the display-only presentation.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function fashion_child_catalog_body_class( $classes ) {
	$classes[] = 'fashion-catalog-only';
	return $classes;
}
add_filter( 'body_class', 'fashion_child_catalog_body_class' );
