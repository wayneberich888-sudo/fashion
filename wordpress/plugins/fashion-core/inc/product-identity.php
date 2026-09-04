<?php
/**
 * Canonical WooCommerce product identity.
 *
 * @package Fashion_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve a product object, ID, or the current global product.
 *
 * @param WC_Product|int|null $product Product source.
 * @return WC_Product|null
 */
function fashion_core_get_product( $product = null ) {
	if ( $product instanceof WC_Product ) {
		return $product;
	}

	if ( is_numeric( $product ) && function_exists( 'wc_get_product' ) ) {
		$resolved = wc_get_product( (int) $product );
		return $resolved instanceof WC_Product ? $resolved : null;
	}

	if ( null === $product ) {
		global $product;
		return $product instanceof WC_Product ? $product : null;
	}

	return null;
}

/**
 * Return the current product's canonical storefront identity.
 *
 * @param WC_Product|int|null $product Product source.
 * @return array|null
 */
function fashion_core_get_product_identity( $product = null ) {
	$product = fashion_core_get_product( $product );
	if ( ! $product instanceof WC_Product ) {
		return null;
	}

	$sale_end = $product->get_date_on_sale_to();

	return array(
		'id'            => $product->get_id(),
		'sku'           => $product->get_sku(),
		'url'           => get_permalink( $product->get_id() ),
		'regular_price' => $product->get_regular_price(),
		'sale_price'    => $product->get_sale_price(),
		'sale_end'      => $sale_end instanceof WC_DateTime ? $sale_end->getTimestamp() : null,
	);
}
