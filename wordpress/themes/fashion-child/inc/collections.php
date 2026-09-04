<?php
/**
 * Standard WooCommerce collections and product cards.
 *
 * @package Fashion_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return a home collection backed by WooCommerce product data.
 *
 * @param string $collection Collection key: new, best, or sale.
 * @param int    $limit      Maximum product count.
 * @return WC_Product[]
 */
function fashion_child_get_collection( $collection, $limit = 6 ) {
	$args = array(
		'limit'   => max( 1, (int) $limit ),
		'status'  => 'publish',
		'orderby' => 'date',
		'order'   => 'DESC',
		'return'  => 'objects',
	);

	if ( 'new' === $collection || 'best' === $collection ) {
		$args['tag'] = array( $collection );
	} elseif ( 'sale' === $collection ) {
		$sale_ids = wc_get_product_ids_on_sale();
		if ( empty( $sale_ids ) ) {
			return array();
		}
		$args['include'] = $sale_ids;
	} else {
		return array();
	}

	return array_values(
		array_filter(
			wc_get_products( $args ),
			static function ( $product ) {
				return $product instanceof WC_Product;
			}
		)
	);
}

/**
 * Return the formal brand name supplied by the core plugin.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function fashion_child_product_brand( WC_Product $product ) {
	if ( ! function_exists( 'fashion_core_get_product_brand' ) ) {
		return '';
	}

	$brand = fashion_core_get_product_brand( $product );
	return $brand instanceof WP_Term ? $brand->name : '';
}

/**
 * Render data-backed NEW, BEST, and SALE badges.
 *
 * @param WC_Product $product Product object.
 */
function fashion_child_render_product_badges( WC_Product $product ) {
	$badges = array();
	if ( has_term( 'new', 'product_tag', $product->get_id() ) ) {
		$badges[] = 'NEW';
	}
	if ( has_term( 'best', 'product_tag', $product->get_id() ) ) {
		$badges[] = 'BEST';
	}
	if ( $product->is_on_sale() ) {
		$badges[] = 'SALE';
	}

	if ( empty( $badges ) ) {
		return;
	}

	echo '<div class="fashion-product-badges" aria-label="상품 태그">';
	foreach ( $badges as $badge ) {
		printf( '<span>%s</span>', esc_html( $badge ) );
	}
	echo '</div>';
}

/**
 * Render one compact product-discovery card.
 *
 * @param WC_Product $product Product object.
 */
function fashion_child_render_product_card( WC_Product $product ) {
	if ( ! function_exists( 'fashion_core_get_product_identity' ) ) {
		return;
	}

	$identity = fashion_core_get_product_identity( $product );
	if ( ! is_array( $identity ) ) {
		return;
	}

	$brand = fashion_child_product_brand( $product );
	?>
	<article class="fashion-product-card" data-product-sku="<?php echo esc_attr( $identity['sku'] ); ?>">
		<a class="fashion-product-card__media" href="<?php echo esc_url( $identity['url'] ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
			<?php fashion_child_render_product_badges( $product ); ?>
			<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'fashion-product-card__image', 'loading' => 'lazy' ) ) ); ?>
		</a>
		<div class="fashion-product-card__body">
			<?php if ( '' !== $brand ) : ?>
				<p class="fashion-product-card__brand"><?php echo esc_html( $brand ); ?></p>
			<?php endif; ?>
			<h3 class="fashion-product-card__title"><a href="<?php echo esc_url( $identity['url'] ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
			<div class="fashion-product-card__price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</div>
	</article>
	<?php
}

/**
 * Render one named product collection.
 *
 * @param string $key      Collection key.
 * @param string $eyebrow  Small heading.
 * @param string $title    Main heading.
 */
function fashion_child_render_collection( $key, $eyebrow, $title ) {
	$products = fashion_child_get_collection( $key, 6 );
	?>
	<section class="fashion-collection" id="<?php echo esc_attr( $key ); ?>">
		<header class="fashion-section-heading">
			<div>
				<p><?php echo esc_html( $eyebrow ); ?></p>
				<h2><?php echo esc_html( $title ); ?></h2>
			</div>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( '전체 보기', 'fashion-child' ); ?></a>
		</header>
		<div class="fashion-product-grid">
			<?php foreach ( $products as $product ) : ?>
				<?php fashion_child_render_product_card( $product ); ?>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
}

/**
 * Render the current product brand in Botiga's standard archive card.
 */
function fashion_child_render_loop_brand() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$brand = fashion_child_product_brand( $product );
	if ( '' !== $brand ) {
		printf( '<p class="fashion-loop-brand">%s</p>', esc_html( $brand ) );
	}
}
add_action( 'woocommerce_shop_loop_item_title', 'fashion_child_render_loop_brand', 5 );

/**
 * Render data-backed badges over the standard archive image.
 */
function fashion_child_render_loop_badges() {
	global $product;
	if ( $product instanceof WC_Product ) {
		fashion_child_render_product_badges( $product );
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'fashion_child_render_loop_badges', 20 );

/**
 * Localize the standard Shop archive title without changing category names.
 *
 * @param string $title Existing archive title.
 * @return string
 */
function fashion_child_catalog_title( $title ) {
	return 'Shop' === $title ? '전체 상품' : $title;
}
add_filter( 'woocommerce_page_title', 'fashion_child_catalog_title' );

/**
 * Add a compact archive affordance while retaining native ordering controls.
 */
function fashion_child_render_archive_tools() {
	if ( ! is_shop() && ! is_product_category() ) {
		return;
	}

	echo '<div class="fashion-archive-tools">';
	echo '<span>' . esc_html__( '카테고리 탐색', 'fashion-child' ) . '</span>';
	echo '<span>' . esc_html__( '정렬 · 필터', 'fashion-child' ) . '</span>';
	echo '</div>';
}
add_action( 'woocommerce_before_shop_loop', 'fashion_child_render_archive_tools', 15 );

/**
 * Replace WooCommerce's default SALE badge with the shared badge set.
 */
function fashion_child_remove_default_loop_sale_flash() {
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
}
add_action( 'wp', 'fashion_child_remove_default_loop_sale_flash', 100 );

/**
 * Return recent approved WooCommerce reviews for homepage presentation.
 *
 * @param int $limit Maximum review count.
 * @return WP_Comment[]
 */
function fashion_child_get_recent_reviews( $limit = 2 ) {
	return get_comments(
		array(
			'status'  => 'approve',
			'type'    => 'review',
			'number'  => max( 1, (int) $limit ),
			'orderby' => 'comment_date_gmt',
			'order'   => 'DESC',
		)
	);
}

/**
 * Render the homepage review section from standard WooCommerce reviews.
 */
function fashion_child_render_reviews() {
	$reviews = fashion_child_get_recent_reviews( 2 );
	?>
	<section class="fashion-review" aria-labelledby="fashion-review-title">
		<header class="fashion-section-heading">
			<div>
				<p>REAL NOTES</p>
				<h2 id="fashion-review-title"><?php esc_html_e( '먼저 경험한 이야기', 'fashion-child' ); ?></h2>
			</div>
		</header>
		<div class="fashion-review__grid">
			<?php if ( empty( $reviews ) ) : ?>
				<p class="fashion-review__empty"><?php esc_html_e( '아직 등록된 리뷰가 없습니다.', 'fashion-child' ); ?></p>
			<?php else : ?>
				<?php foreach ( $reviews as $review ) : ?>
					<?php $rating = max( 0, min( 5, (int) get_comment_meta( $review->comment_ID, 'rating', true ) ) ); ?>
					<blockquote>
						<p>“<?php echo esc_html( get_comment_text( $review ) ); ?>”</p>
						<footer>
							<span aria-label="<?php echo esc_attr( sprintf( '별점 %d점', $rating ) ); ?>"><?php echo esc_html( str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating ) ); ?></span>
							<?php echo esc_html( $review->comment_author ); ?>
						</footer>
					</blockquote>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</section>
	<?php
}
