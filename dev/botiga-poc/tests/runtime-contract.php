<?php
/**
 * Runtime data contract for the isolated Botiga prototype.
 */

defined( 'ABSPATH' ) || exit( 1 );

/**
 * Fail with an actionable contract message.
 *
 * @param bool   $condition Condition under test.
 * @param string $message   Failure description.
 */
function fashion_poc_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( 'RUNTIME_DATA_FAIL: ' . $message );
	}
}

fashion_poc_assert( function_exists( 'wc_get_products' ), 'WooCommerce is not loaded' );

$products = wc_get_products(
	array(
		'limit'  => -1,
		'status' => 'publish',
	)
);

fashion_poc_assert( 25 === count( $products ), 'expected exactly 25 published products' );

$skus = array_map(
	static function ( WC_Product $product ) {
		return $product->get_sku();
	},
	$products
);

fashion_poc_assert( 25 === count( array_unique( $skus ) ), 'every product must have a unique SKU' );
fashion_poc_assert( ! in_array( '', $skus, true ), 'every product must have a non-empty SKU' );

$simple_products = array_filter(
	$products,
	static function ( WC_Product $product ) {
		return $product->is_type( 'simple' );
	}
);
fashion_poc_assert( 25 === count( $simple_products ), 'every fixture product must be Simple Product' );

$featured_id = (int) get_option( 'fashion_poc_featured_product_id' );
$featured    = wc_get_product( $featured_id );
fashion_poc_assert( $featured instanceof WC_Product_Simple, 'featured sale product is missing' );
fashion_poc_assert( 'FPOC-001' === $featured->get_sku(), 'featured product must be FPOC-001' );
fashion_poc_assert( '' !== $featured->get_regular_price(), 'featured product regular price is missing' );
fashion_poc_assert( '' !== $featured->get_sale_price(), 'featured product sale price is missing' );
fashion_poc_assert( $featured->get_date_on_sale_to() instanceof WC_DateTime, 'featured Sale End is missing' );
fashion_poc_assert( $featured->get_date_on_sale_to()->getTimestamp() > time(), 'featured Sale End is not in the future' );
fashion_poc_assert( function_exists( 'fashion_child_sale_end_ms' ), 'sale-end adapter is missing' );
fashion_poc_assert(
	$featured->get_date_on_sale_to()->getTimestamp() * 1000 === fashion_child_sale_end_ms( $featured ),
	'countdown timestamp must equal WooCommerce Sale End'
);
fashion_poc_assert( '' === (string) $featured->get_meta( '_fashion_sale_end', true ), 'parallel sale-end metadata is forbidden' );

fashion_poc_assert( function_exists( 'fashion_child_render_product_support' ), 'product support renderer is missing' );
ob_start();
fashion_child_render_product_support( $featured );
$support_markup = ob_get_clean();
fashion_poc_assert( false !== strpos( $support_markup, 'data-product-sku="FPOC-001"' ), 'support markup lacks current SKU' );
fashion_poc_assert( false !== strpos( $support_markup, 'data-product-url="' . esc_url( get_permalink( $featured_id ) ) . '"' ), 'support markup lacks current product URL' );
fashion_poc_assert( false !== strpos( $support_markup, 'data-sale-end="' ), 'support markup lacks WooCommerce sale end' );
fashion_poc_assert( false !== strpos( $support_markup, '카카오' ), 'Kakao consultation CTA is missing' );
fashion_poc_assert( false === apply_filters( 'woocommerce_is_purchasable', true, $featured ), 'fixture product is still purchasable on the front end' );
fashion_poc_assert( false === (bool) apply_filters( 'theme_mod_enable_header_cart', true ), 'desktop header cart remains enabled' );
fashion_poc_assert( false === (bool) apply_filters( 'theme_mod_enable_mobile_header_cart', true ), 'mobile header cart remains enabled' );

$expected_categories = array( '신발', '가방', '의류', '향수', '액세서리' );
foreach ( $expected_categories as $category_name ) {
	$term = get_term_by( 'name', $category_name, 'product_cat' );
	fashion_poc_assert( $term instanceof WP_Term, 'missing category: ' . $category_name );
}

$review_count = get_comments(
	array(
		'post_id' => $featured_id,
		'status'  => 'approve',
		'count'   => true,
	)
);
fashion_poc_assert( (int) $review_count >= 2, 'featured product needs synthetic review evidence' );

$orders = wc_get_orders(
	array(
		'limit'  => 1,
		'return' => 'ids',
	)
);
fashion_poc_assert( array() === $orders, 'the prototype must contain no orders' );

printf( "RUNTIME_DATA_PASS products=%d unique_skus=%d orders=0\n", count( $products ), count( array_unique( $skus ) ) );
