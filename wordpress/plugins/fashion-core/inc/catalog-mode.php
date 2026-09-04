<?php
/**
 * Display-only catalog business rules.
 *
 * @package Fashion_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Prevent public product purchase flows.
 *
 * @param bool            $purchasable Existing state.
 * @param WC_Product|null $product     Current product.
 * @return bool
 */
function fashion_core_product_is_not_purchasable( $purchasable, $product = null ) {
	unset( $purchasable, $product );
	return false;
}
add_filter( 'woocommerce_is_purchasable', 'fashion_core_product_is_not_purchasable', 100, 2 );
add_filter( 'woocommerce_variation_is_purchasable', 'fashion_core_product_is_not_purchasable', 100, 2 );

/**
 * Remove the standard purchase controls after WooCommerce registers them.
 */
function fashion_core_remove_purchase_actions() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
}
add_action( 'wp', 'fashion_core_remove_purchase_actions', 100 );

/**
 * Return the safe public destination for disabled transaction pages.
 *
 * @return string
 */
function fashion_core_catalog_destination_url() {
	if ( function_exists( 'wc_get_page_id' ) ) {
		$shop_page_id = (int) wc_get_page_id( 'shop' );
		if ( $shop_page_id > 0 && 'publish' === get_post_status( $shop_page_id ) ) {
			$shop_url = get_permalink( $shop_page_id );
			if ( is_string( $shop_url ) && '' !== $shop_url ) {
				return $shop_url;
			}
		}
	}

	return home_url( '/' );
}

/**
 * Redirect front-end transaction pages back to the public catalog.
 */
function fashion_core_redirect_transaction_pages() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$is_transaction_page =
		( function_exists( 'is_cart' ) && is_cart() ) ||
		( function_exists( 'is_checkout' ) && is_checkout() ) ||
		( function_exists( 'is_account_page' ) && is_account_page() );

	if ( ! $is_transaction_page ) {
		return;
	}

	wp_safe_redirect( fashion_core_catalog_destination_url(), 302, 'fashion-core' );
	exit;
}
add_action( 'template_redirect', 'fashion_core_redirect_transaction_pages', 1 );
