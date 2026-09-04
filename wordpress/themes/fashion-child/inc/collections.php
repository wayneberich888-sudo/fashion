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
 * Return the prototype brand stored on the standard product record.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function fashion_child_product_brand( WC_Product $product ) {
	return (string) $product->get_meta( '_fashion_brand', true );
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
	$url   = get_permalink( $product->get_id() );
	$brand = fashion_child_product_brand( $product );
	?>
	<article class="fashion-product-card" data-product-sku="<?php echo esc_attr( $product->get_sku() ); ?>">
		<a class="fashion-product-card__media" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
			<?php fashion_child_render_product_badges( $product ); ?>
			<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'fashion-product-card__image', 'loading' => 'lazy' ) ) ); ?>
		</a>
		<div class="fashion-product-card__body">
			<?php if ( '' !== $brand ) : ?>
				<p class="fashion-product-card__brand"><?php echo esc_html( $brand ); ?></p>
			<?php endif; ?>
			<h3 class="fashion-product-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
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
