<?php
/**
 * Formal product-brand taxonomy adapter.
 *
 * @package Fashion_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Reuse the WooCommerce brand taxonomy, or register one fallback taxonomy.
 */
function fashion_core_register_brand_taxonomy() {
	if ( taxonomy_exists( 'product_brand' ) || taxonomy_exists( 'fashion_brand' ) ) {
		return;
	}

	register_taxonomy(
		'fashion_brand',
		array( 'product' ),
		array(
			'labels'            => array(
				'name'          => __( 'Brands', 'fashion-core' ),
				'singular_name' => __( 'Brand', 'fashion-core' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'query_var'         => 'fashion_brand',
			'rewrite'           => array( 'slug' => 'brand' ),
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'fashion_core_register_brand_taxonomy', 100 );

/**
 * Return the one formal brand taxonomy used by the current runtime.
 *
 * @return string
 */
function fashion_core_brand_taxonomy() {
	return taxonomy_exists( 'product_brand' ) ? 'product_brand' : 'fashion_brand';
}

/**
 * Return the first formal brand assigned to a product.
 *
 * @param WC_Product|int|null $product Product source.
 * @return WP_Term|null
 */
function fashion_core_get_product_brand( $product = null ) {
	$product = fashion_core_get_product( $product );
	if ( ! $product instanceof WC_Product ) {
		return null;
	}

	$terms = wp_get_post_terms( $product->get_id(), fashion_core_brand_taxonomy() );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}

	return $terms[0] instanceof WP_Term ? $terms[0] : null;
}
