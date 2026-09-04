<?php
/**
 * Runtime data contract for the isolated Botiga storefront foundation.
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
fashion_poc_assert(
	in_array( 'fashion-core/fashion-core.php', (array) get_option( 'active_plugins', array() ), true ),
	'fashion-core is not active'
);
fashion_poc_assert( function_exists( 'fashion_core_get_product_identity' ), 'fashion-core identity API is missing' );
fashion_poc_assert( function_exists( 'fashion_core_brand_taxonomy' ), 'fashion-core brand API is missing' );
fashion_poc_assert( function_exists( 'fashion_core_get_product_brand' ), 'fashion-core brand resolver is missing' );
fashion_poc_assert( function_exists( 'fashion_core_catalog_destination_url' ), 'fashion-core catalog destination is missing' );

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
fashion_poc_assert( '' === (string) $featured->get_meta( '_fashion_sale_end', true ), 'parallel sale-end metadata is forbidden' );

$identity = fashion_core_get_product_identity( $featured );
fashion_poc_assert( is_array( $identity ), 'fashion-core did not return product identity' );
fashion_poc_assert(
	array( 'id', 'sku', 'url', 'regular_price', 'sale_price', 'sale_end' ) === array_keys( $identity ),
	'product identity keys are unstable'
);
fashion_poc_assert( $featured_id === $identity['id'], 'product identity ID differs from WooCommerce' );
fashion_poc_assert( 'FPOC-001' === $identity['sku'], 'product identity SKU differs from WooCommerce' );
fashion_poc_assert( get_permalink( $featured_id ) === $identity['url'], 'product identity URL differs from the permalink' );
fashion_poc_assert( $featured->get_regular_price() === $identity['regular_price'], 'Regular Price differs from WooCommerce' );
fashion_poc_assert( $featured->get_sale_price() === $identity['sale_price'], 'Sale Price differs from WooCommerce' );
fashion_poc_assert(
	$featured->get_date_on_sale_to()->getTimestamp() === $identity['sale_end'],
	'countdown timestamp must equal WooCommerce Sale End'
);

$brand_taxonomy = fashion_core_brand_taxonomy();
fashion_poc_assert( 'product_brand' === $brand_taxonomy, 'WooCommerce product_brand must be reused in this runtime' );
fashion_poc_assert( taxonomy_exists( 'product_brand' ), 'WooCommerce product_brand taxonomy is missing' );
fashion_poc_assert( ! taxonomy_exists( 'fashion_brand' ), 'a parallel project brand taxonomy was registered' );

$brand_slugs = array();
foreach ( $products as $product ) {
	$brand_terms = wp_get_post_terms( $product->get_id(), $brand_taxonomy );
	fashion_poc_assert( ! is_wp_error( $brand_terms ), 'brand lookup returned an error' );
	fashion_poc_assert( 1 === count( $brand_terms ), 'every product must have exactly one formal brand' );
	fashion_poc_assert( '' === (string) $product->get_meta( '_fashion_brand', true ), 'legacy brand meta remains on a product' );
	$brand_slugs[] = $brand_terms[0]->slug;
}
$brand_slugs = array_values( array_unique( $brand_slugs ) );
fashion_poc_assert( count( $brand_slugs ) >= 2, 'at least two formal brands are required' );

foreach ( array_slice( $brand_slugs, 0, 2 ) as $brand_slug ) {
	$brand_query = new WP_Query(
		array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => $brand_taxonomy,
					'field'    => 'slug',
					'terms'    => $brand_slug,
				),
			),
		)
	);
	fashion_poc_assert( $brand_query->have_posts(), 'formal brand query returned no products: ' . $brand_slug );
}
wp_reset_postdata();

$featured_brand = fashion_core_get_product_brand( $featured );
fashion_poc_assert( $featured_brand instanceof WP_Term, 'featured product brand is missing' );
fashion_poc_assert( 'NORTH ARC' === $featured_brand->name, 'featured product brand is incorrect' );

fashion_poc_assert( function_exists( 'fashion_child_render_product_support' ), 'product support renderer is missing' );
ob_start();
fashion_child_render_product_support( $featured );
$support_markup = ob_get_clean();
fashion_poc_assert( false !== strpos( $support_markup, 'data-product-sku="' . esc_attr( $identity['sku'] ) . '"' ), 'theme support SKU differs from fashion-core' );
fashion_poc_assert( false !== strpos( $support_markup, 'data-product-url="' . esc_url( $identity['url'] ) . '"' ), 'theme support URL differs from fashion-core' );
fashion_poc_assert( false !== strpos( $support_markup, 'data-sale-end="' . ( $identity['sale_end'] * 1000 ) . '"' ), 'theme Sale End differs from fashion-core' );
fashion_poc_assert( false !== strpos( $support_markup, '카카오' ), 'Kakao consultation CTA is missing' );
fashion_poc_assert( false === strpos( $support_markup, '로컬 프로토타입' ), 'customer-facing prototype copy remains' );

fashion_poc_assert( false === apply_filters( 'woocommerce_is_purchasable', true, $featured ), 'fixture product is still purchasable' );
fashion_poc_assert( false === apply_filters( 'woocommerce_variation_is_purchasable', true, $featured ), 'variation purchase filter is not disabled' );
fashion_poc_assert( false === function_exists( 'fashion_child_catalog_is_purchasable' ), 'purchase business rule remains in the theme' );
fashion_poc_assert( false === (bool) apply_filters( 'theme_mod_enable_header_cart', true ), 'desktop header cart remains enabled' );
fashion_poc_assert( false === (bool) apply_filters( 'theme_mod_enable_mobile_header_cart', true ), 'mobile header cart remains enabled' );
fashion_poc_assert( wc_get_page_permalink( 'shop' ) === fashion_core_catalog_destination_url(), 'catalog redirect destination is not Shop' );

fashion_poc_assert( function_exists( 'fashion_child_get_collection' ), 'product collection query is missing' );
fashion_poc_assert( function_exists( 'fashion_child_render_product_card' ), 'product card renderer is missing' );

foreach ( array( 'new', 'best', 'sale' ) as $collection_name ) {
	$collection_products = fashion_child_get_collection( $collection_name, 6 );
	fashion_poc_assert( ! empty( $collection_products ), 'empty home collection: ' . $collection_name );
	fashion_poc_assert( $collection_products[0] instanceof WC_Product, 'collection contains a non-product item' );
}

ob_start();
fashion_child_render_product_card( $featured );
$card_markup = ob_get_clean();
fashion_poc_assert( false !== strpos( $card_markup, 'NORTH ARC' ), 'product card lacks taxonomy-backed brand' );
fashion_poc_assert( false !== strpos( $card_markup, '클라우드 러너 스톤' ), 'product card lacks Korean name' );
fashion_poc_assert( false !== strpos( $card_markup, $featured->get_price_html() ), 'product card lacks WooCommerce price HTML' );
fashion_poc_assert( false !== strpos( $card_markup, esc_url( $identity['url'] ) ), 'product card URL differs from fashion-core' );
fashion_poc_assert( false !== strpos( $card_markup, 'data-product-sku="FPOC-001"' ), 'product card SKU differs from fashion-core' );
fashion_poc_assert( count( $featured->get_gallery_image_ids() ) >= 2, 'featured product gallery needs at least two supporting images' );
fashion_poc_assert( function_exists( 'fashion_child_render_loop_brand' ), 'archive brand renderer is missing' );
fashion_poc_assert( function_exists( 'fashion_child_render_loop_badges' ), 'archive badge renderer is missing' );
fashion_poc_assert( function_exists( 'fashion_child_catalog_title' ), 'archive title adapter is missing' );
fashion_poc_assert( '전체 상품' === fashion_child_catalog_title( 'Shop' ), 'Shop title is not Korean' );

$previous_product    = $GLOBALS['product'] ?? null;
$GLOBALS['product'] = $featured;
ob_start();
fashion_child_render_loop_brand();
fashion_child_render_loop_badges();
$loop_meta_markup    = ob_get_clean();
$GLOBALS['product'] = $previous_product;
fashion_poc_assert( false !== strpos( $loop_meta_markup, 'NORTH ARC' ), 'archive card lacks taxonomy-backed brand' );
fashion_poc_assert( false !== strpos( $loop_meta_markup, 'NEW' ), 'archive card lacks NEW badge' );
fashion_poc_assert( false !== strpos( $loop_meta_markup, 'BEST' ), 'archive card lacks BEST badge' );
fashion_poc_assert( false !== strpos( $loop_meta_markup, 'SALE' ), 'archive card lacks SALE badge' );

fashion_poc_assert( has_nav_menu( 'primary' ), 'safe primary catalog menu is not assigned' );
$menu_locations = get_nav_menu_locations();
$menu_items     = wp_get_nav_menu_items( $menu_locations['primary'] );
$menu_titles    = array_map( static fn( $item ) => strtolower( $item->title ), $menu_items );
foreach ( array( 'cart', 'checkout', 'my account' ) as $forbidden_menu_title ) {
	fashion_poc_assert( ! in_array( $forbidden_menu_title, $menu_titles, true ), 'transaction page appears in primary menu' );
}

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
fashion_poc_assert( array() === $orders, 'the foundation runtime must contain no orders' );

printf(
	"RUNTIME_DATA_PASS products=%d unique_skus=%d brands=%d orders=0\n",
	count( $products ),
	count( array_unique( $skus ) ),
	count( $brand_slugs )
);
