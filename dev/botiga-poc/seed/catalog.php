<?php
/**
 * Deterministic synthetic catalog for the local Botiga prototype.
 */

defined( 'ABSPATH' ) || exit( 1 );

if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product_Simple' ) ) {
	throw new RuntimeException( 'WooCommerce must be active before seeding.' );
}

/**
 * Return an existing term ID or create the term.
 *
 * @param string $taxonomy Taxonomy name.
 * @param string $name     Display name.
 * @param string $slug     Stable slug.
 * @return int
 */
function fashion_poc_upsert_term( $taxonomy, $name, $slug ) {
	$term = get_term_by( 'slug', $slug, $taxonomy );
	if ( $term instanceof WP_Term ) {
		return (int) $term->term_id;
	}

	$created = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
	if ( is_wp_error( $created ) ) {
		throw new RuntimeException( $created->get_error_message() );
	}

	return (int) $created['term_id'];
}

/**
 * Convert a six-character hex color to RGB values.
 *
 * @param string $hex Color without opacity.
 * @return int[]
 */
function fashion_poc_hex_rgb( $hex ) {
	$clean = ltrim( $hex, '#' );
	return array(
		hexdec( substr( $clean, 0, 2 ) ),
		hexdec( substr( $clean, 2, 2 ) ),
		hexdec( substr( $clean, 4, 2 ) ),
	);
}

/**
 * Create one project-owned neutral PNG per category.
 *
 * @param string   $key     Stable asset key.
 * @param string   $label   English image label.
 * @param string[] $palette Three hex colors.
 * @return int Attachment ID.
 */
function fashion_poc_image( $key, $label, $palette ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_key'       => '_fashion_poc_asset_key',
			'meta_value'     => $key,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		throw new RuntimeException( 'PHP GD is required for synthetic prototype images.' );
	}

	$upload = wp_upload_dir();
	if ( ! empty( $upload['error'] ) ) {
		throw new RuntimeException( $upload['error'] );
	}

	$filename = 'fashion-poc-' . sanitize_file_name( $key ) . '.png';
	$path     = trailingslashit( $upload['path'] ) . $filename;
	$image    = imagecreatetruecolor( 960, 1200 );
	$rgb      = array_map( 'fashion_poc_hex_rgb', $palette );
	$colors   = array_map(
		static function ( $values ) use ( $image ) {
			return imagecolorallocate( $image, $values[0], $values[1], $values[2] );
		},
		$rgb
	);

	imagefill( $image, 0, 0, $colors[0] );
	imagefilledellipse( $image, 480, 505, 610, 610, $colors[1] );
	imagefilledrectangle( $image, 250, 425, 710, 920, $colors[2] );
	imagefilledellipse( $image, 480, 815, 500, 170, $colors[0] );
	$ink = imagecolorallocate( $image, 24, 24, 24 );
	imagestring( $image, 5, 42, 42, 'FASHION / ' . strtoupper( $label ), $ink );
	imagestring( $image, 3, 42, 1120, 'SYNTHETIC PROTOTYPE IMAGE', $ink );

	if ( ! imagepng( $image, $path, 8 ) ) {
		imagedestroy( $image );
		throw new RuntimeException( 'Could not write synthetic image: ' . $filename );
	}
	imagedestroy( $image );

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => 'Synthetic ' . $label . ' study',
			'post_status'    => 'inherit',
		),
		$path
	);
	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		throw new RuntimeException( 'Could not register synthetic image.' );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $path ) );
	update_post_meta( $attachment_id, '_fashion_poc_asset_key', $key );
	update_post_meta( $attachment_id, '_wp_attachment_image_alt', '합성 ' . $label . ' 상품 이미지' );

	return (int) $attachment_id;
}

$category_specs = array(
	'shoes'       => array( '신발', 'shoes', array( '#e8e5df', '#c7c2b8', '#f7f5f0' ) ),
	'bags'        => array( '가방', 'bags', array( '#e6e2dc', '#aaa39a', '#f4f1ed' ) ),
	'apparel'     => array( '의류', 'apparel', array( '#ececec', '#b9b9b9', '#fafafa' ) ),
	'fragrance'   => array( '향수', 'fragrance', array( '#e8e4df', '#9e958b', '#f8f6f2' ) ),
	'accessories' => array( '액세서리', 'accessories', array( '#e3e4e5', '#a8aaad', '#f6f6f6' ) ),
);

$category_ids = array();
$image_ids    = array();
foreach ( $category_specs as $key => $spec ) {
	$category_ids[ $key ] = fashion_poc_upsert_term( 'product_cat', $spec[0], $spec[1] );
	$image_ids[ $key ]    = fashion_poc_image( $key, $spec[1], $spec[2] );
}

$tag_ids = array(
	'new'  => fashion_poc_upsert_term( 'product_tag', 'NEW', 'new' ),
	'best' => fashion_poc_upsert_term( 'product_tag', 'BEST', 'best' ),
);

$products = array(
	array( 'FPOC-001', 'NORTH ARC', '클라우드 러너 스톤', 'shoes', 219000, 169000, array( 'new', 'best' ) ),
	array( 'FPOC-002', 'STILL FORM', '레더 코트 스니커즈', 'shoes', 198000, 149000, array( 'best' ) ),
	array( 'FPOC-003', 'MONO FIELD', '에어 메시 트레이너', 'shoes', 179000, null, array( 'new' ) ),
	array( 'FPOC-004', 'NORTH ARC', '트레일 로우 블랙', 'shoes', 239000, null, array() ),
	array( 'FPOC-005', 'PALE LINE', '소프트 스웨이드 뮬', 'shoes', 189000, 139000, array( 'new' ) ),
	array( 'FPOC-006', 'OAK EDIT', '아카이브 숄더 백', 'bags', 329000, 279000, array( 'best' ) ),
	array( 'FPOC-007', 'STILL FORM', '미니 버킷 백 차콜', 'bags', 248000, null, array( 'new' ) ),
	array( 'FPOC-008', 'MONO FIELD', '플랫 크로스 백', 'bags', 219000, 179000, array() ),
	array( 'FPOC-009', 'PALE LINE', '오버 토트 백 샌드', 'bags', 289000, null, array( 'best' ) ),
	array( 'FPOC-010', 'OAK EDIT', '나일론 데이 팩', 'bags', 159000, 129000, array( 'new' ) ),
	array( 'FPOC-011', 'NORTH ARC', '울 블렌드 하프 코트', 'apparel', 428000, 349000, array( 'best' ) ),
	array( 'FPOC-012', 'STILL FORM', '컴팩트 후드 재킷', 'apparel', 298000, null, array( 'new' ) ),
	array( 'FPOC-013', 'MONO FIELD', '헤비 코튼 스웨트셔츠', 'apparel', 138000, 109000, array() ),
	array( 'FPOC-014', 'PALE LINE', '와이드 플리츠 팬츠', 'apparel', 168000, null, array( 'best' ) ),
	array( 'FPOC-015', 'OAK EDIT', '메리노 리브 니트', 'apparel', 189000, 149000, array( 'new' ) ),
	array( 'FPOC-016', 'QUIET LAB', '오 드 퍼퓸 넘버 11', 'fragrance', 149000, 119000, array( 'best' ) ),
	array( 'FPOC-017', 'PALE LINE', '시더 머스크 오 드 퍼퓸', 'fragrance', 158000, null, array( 'new' ) ),
	array( 'FPOC-018', 'MONO FIELD', '그레이 티 퍼퓸', 'fragrance', 138000, 109000, array() ),
	array( 'FPOC-019', 'QUIET LAB', '화이트 우드 미스트', 'fragrance', 98000, null, array( 'best' ) ),
	array( 'FPOC-020', 'OAK EDIT', '앰버 레인 향수', 'fragrance', 169000, 139000, array( 'new' ) ),
	array( 'FPOC-021', 'NORTH ARC', '브러시드 실버 링', 'accessories', 89000, 69000, array( 'best' ) ),
	array( 'FPOC-022', 'STILL FORM', '슬림 레더 벨트', 'accessories', 118000, null, array( 'new' ) ),
	array( 'FPOC-023', 'PALE LINE', '오벌 프레임 아이웨어', 'accessories', 159000, 129000, array() ),
	array( 'FPOC-024', 'MONO FIELD', '소프트 울 머플러', 'accessories', 108000, null, array( 'best' ) ),
	array( 'FPOC-025', 'QUIET LAB', '미니멀 체인 네크리스', 'accessories', 129000, 99000, array( 'new' ) ),
);

$sale_start = strtotime( '-1 day' );
$sale_end   = strtotime( '+14 days 23:59:59' );

foreach ( $products as $index => $data ) {
	list( $sku, $brand, $name, $category_key, $regular_price, $sale_price, $tags ) = $data;
	$product_id = wc_get_product_id_by_sku( $sku );
	$product    = $product_id ? wc_get_product( $product_id ) : new WC_Product_Simple();

	if ( ! $product instanceof WC_Product_Simple ) {
		throw new RuntimeException( 'Fixture SKU is not a Simple Product: ' . $sku );
	}

	$product->set_name( $name );
	$product->set_slug( strtolower( $sku ) . '-' . sanitize_title( $name ) );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_sku( $sku );
	$product->set_regular_price( (string) $regular_price );
	$product->set_price( (string) ( null === $sale_price ? $regular_price : $sale_price ) );
	$product->set_manage_stock( false );
	$product->set_stock_status( 'instock' );
	$product->set_category_ids( array( $category_ids[ $category_key ] ) );
	$product->set_tag_ids( array_map( static fn( $tag ) => $tag_ids[ $tag ], $tags ) );
	$product->set_image_id( $image_ids[ $category_key ] );
	$product->set_gallery_image_ids(
		array_slice(
			array_values( array_diff( $image_ids, array( $image_ids[ $category_key ] ) ) ),
			0,
			2
		)
	);
	$product->set_short_description( '절제된 실루엣과 편안한 사용감을 담은 합성 프로토타입 상품입니다.' );
	$product->set_description( '본 상품과 이미지는 테마 검증을 위해 만든 가상 데이터입니다. 실제 판매 또는 주문에 사용되지 않습니다.' );
	$product->update_meta_data( '_fashion_brand', $brand );

	if ( null !== $sale_price ) {
		$product->set_sale_price( (string) $sale_price );
		$product->set_date_on_sale_from( $sale_start );
		$product->set_date_on_sale_to( $sale_end );
	} else {
		$product->set_sale_price( '' );
		$product->set_date_on_sale_from( null );
		$product->set_date_on_sale_to( null );
	}

	$product_id = $product->save();
	if ( 0 === $index ) {
		update_option( 'fashion_poc_featured_product_id', $product_id, false );
	}
}

$featured_id = (int) get_option( 'fashion_poc_featured_product_id' );
$reviews     = array(
	array( 'review-1', '민서 K.', '소재가 차분하고 사진보다 실루엣이 더 깔끔해요.', 5 ),
	array( 'review-2', '지우 P.', '가볍게 매치하기 좋고 포장도 단정했습니다.', 4 ),
);

foreach ( $reviews as $review ) {
	$existing_review = get_comments(
		array(
			'post_id'    => $featured_id,
			'status'     => 'approve',
			'number'     => 1,
			'meta_key'   => '_fashion_poc_review_key',
			'meta_value' => $review[0],
		)
	);
	if ( $existing_review ) {
		continue;
	}

	$comment_id = wp_insert_comment(
		array(
			'comment_post_ID'      => $featured_id,
			'comment_author'       => $review[1],
			'comment_author_email' => $review[0] . '@example.invalid',
			'comment_content'      => $review[2],
			'comment_type'         => 'review',
			'comment_approved'     => 1,
			'comment_meta'         => array(
				'rating'                  => $review[3],
				'verified'                => 0,
				'_fashion_poc_review_key' => $review[0],
			),
		)
	);
	if ( ! $comment_id ) {
		throw new RuntimeException( 'Could not create synthetic review.' );
	}
}

$shop_page_id = wc_get_page_id( 'shop' );
if ( $shop_page_id <= 0 || 'trash' === get_post_status( $shop_page_id ) ) {
	$shop_page_id = wp_insert_post(
		array(
			'post_title'  => '전체 상품',
			'post_name'   => 'shop',
			'post_type'   => 'page',
			'post_status' => 'publish',
		),
		true
	);
	if ( is_wp_error( $shop_page_id ) ) {
		throw new RuntimeException( $shop_page_id->get_error_message() );
	}
	update_option( 'woocommerce_shop_page_id', (int) $shop_page_id );
}

update_option( 'woocommerce_currency', 'KRW' );
update_option( 'woocommerce_price_num_decimals', '0' );
update_option( 'woocommerce_enable_reviews', 'yes' );
update_option( 'posts_per_page', 12 );

$menu = wp_get_nav_menu_object( 'fashion-catalog' );
if ( ! $menu instanceof WP_Term ) {
	$menu_id = wp_create_nav_menu( 'FASHION Catalog' );
	if ( is_wp_error( $menu_id ) ) {
		throw new RuntimeException( $menu_id->get_error_message() );
	}
	$menu = wp_get_nav_menu_object( $menu_id );
}

$menu_items = wp_get_nav_menu_items( $menu->term_id );
if ( empty( $menu_items ) ) {
	$home_url = home_url( '/' );
	$links    = array(
		array( 'NEW', $home_url . '#new' ),
		array( 'BEST', $home_url . '#best' ),
		array( 'SALE', $home_url . '#sale' ),
		array( '전체', get_permalink( $shop_page_id ) ),
	);
	foreach ( $category_specs as $key => $spec ) {
		$term_url = get_term_link( $category_ids[ $key ], 'product_cat' );
		if ( ! is_wp_error( $term_url ) ) {
			$links[] = array( $spec[0], $term_url );
		}
	}

	foreach ( $links as $link ) {
		wp_update_nav_menu_item(
			$menu->term_id,
			0,
			array(
				'menu-item-title'  => $link[0],
				'menu-item-url'    => $link[1],
				'menu-item-status' => 'publish',
			)
		);
	}
}

$locations            = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = (int) $menu->term_id;
set_theme_mod( 'nav_menu_locations', $locations );

printf( "CATALOG_SEED_PASS products=%d images=%d reviews=%d\n", count( $products ), count( $image_ids ), count( $reviews ) );
