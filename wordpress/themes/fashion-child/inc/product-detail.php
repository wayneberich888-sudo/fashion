<?php
/**
 * Product identity and consultation controls.
 *
 * @package Fashion_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return a future WooCommerce Sale End in milliseconds.
 *
 * @param WC_Product $product Current WooCommerce product.
 * @return int|null
 */
function fashion_child_sale_end_ms( WC_Product $product ) {
	$sale_end = $product->get_date_on_sale_to();
	if ( ! $sale_end instanceof WC_DateTime ) {
		return null;
	}

	$timestamp = $sale_end->getTimestamp();
	return $timestamp > time() ? $timestamp * 1000 : null;
}

/**
 * Render the synthetic brand above the product title.
 */
function fashion_child_render_product_brand() {
	global $product;
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$brand = (string) $product->get_meta( '_fashion_brand', true );
	if ( '' !== $brand ) {
		printf( '<p class="fashion-product-brand">%s</p>', esc_html( $brand ) );
	}
}
add_action( 'woocommerce_single_product_summary', 'fashion_child_render_product_brand', 1 );

/**
 * Render sale timing, current SKU, and local-only consultation controls.
 *
 * @param WC_Product|null $support_product Optional product for runtime tests.
 */
function fashion_child_render_product_support( $support_product = null ) {
	if ( ! $support_product instanceof WC_Product ) {
		global $product;
		$support_product = $product;
	}

	if ( ! $support_product instanceof WC_Product ) {
		return;
	}

	$sku       = $support_product->get_sku();
	$url       = get_permalink( $support_product->get_id() );
	$sale_end  = fashion_child_sale_end_ms( $support_product );
	$status_id = 'fashion-support-status-' . $support_product->get_id();
	?>
	<section
		class="fashion-product-support"
		data-product-sku="<?php echo esc_attr( $sku ); ?>"
		data-product-url="<?php echo esc_url( $url ); ?>"
		aria-label="<?php esc_attr_e( '상품 상담 도구', 'fashion-child' ); ?>"
	>
		<div class="fashion-product-identity">
			<span class="fashion-product-identity__label">SKU</span>
			<strong class="fashion-product-sku"><?php echo esc_html( $sku ? $sku : '확인 불가' ); ?></strong>
		</div>

		<?php if ( null !== $sale_end ) : ?>
			<div class="fashion-sale-clock" data-sale-end="<?php echo esc_attr( $sale_end ); ?>">
				<span class="fashion-sale-clock__label"><?php esc_html_e( '세일 종료까지', 'fashion-child' ); ?></span>
				<strong class="fashion-sale-clock__value">--:--:--</strong>
			</div>
		<?php endif; ?>

		<div class="fashion-product-actions">
			<button class="fashion-action fashion-action--kakao" type="button" data-kakao-consult <?php disabled( '' === $sku ); ?> aria-describedby="<?php echo esc_attr( $status_id ); ?>">
				<svg aria-hidden="true" viewBox="0 0 24 24" width="20" height="20"><path d="M4 5.8h16v10.4H9.5L5.2 19v-2.8H4z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
				<span><?php esc_html_e( '카카오 상담', 'fashion-child' ); ?></span>
			</button>
			<button class="fashion-action fashion-action--copy" type="button" data-copy-product-link aria-describedby="<?php echo esc_attr( $status_id ); ?>">
				<svg aria-hidden="true" viewBox="0 0 24 24" width="20" height="20"><rect x="8" y="8" width="11" height="11" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2" fill="none" stroke="currentColor" stroke-width="1.7"/></svg>
				<span><?php esc_html_e( '상품 링크 복사', 'fashion-child' ); ?></span>
			</button>
		</div>
		<p class="fashion-support-status" id="<?php echo esc_attr( $status_id ); ?>" aria-live="polite"></p>
		<p class="fashion-prototype-note"><?php esc_html_e( '로컬 프로토타입 · 실제 상담 계정에 연결되지 않습니다.', 'fashion-child' ); ?></p>
	</section>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'fashion_child_render_product_support', 25 );
