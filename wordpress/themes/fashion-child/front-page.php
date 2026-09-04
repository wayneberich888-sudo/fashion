<?php
/**
 * Mobile-first B-Lite discovery home.
 *
 * @package Fashion_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

$shop_url = wc_get_page_permalink( 'shop' );
?>

<main id="primary" class="site-main fashion-home">
	<nav class="fashion-quick-nav" aria-label="빠른 상품 탐색">
		<a href="#new">NEW</a>
		<a href="#best">BEST</a>
		<a href="#sale">SALE</a>
		<?php foreach ( array( 'shoes', 'bags', 'apparel', 'fragrance', 'accessories' ) as $category_slug ) : ?>
			<?php $category = get_term_by( 'slug', $category_slug, 'product_cat' ); ?>
			<?php if ( $category instanceof WP_Term && ! is_wp_error( get_term_link( $category ) ) ) : ?>
				<a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>

	<section class="fashion-hero" aria-labelledby="fashion-hero-title">
		<div class="fashion-hero__copy">
			<p class="fashion-kicker">NEW SEASON · SEOUL</p>
			<h1 id="fashion-hero-title">매일 새롭게 발견하는<br>조용한 스타일</h1>
			<p>신발부터 향수까지, 지금 주목할 상품을 한눈에 만나보세요.</p>
			<a class="fashion-text-link" href="<?php echo esc_url( $shop_url ); ?>">컬렉션 보기 <span aria-hidden="true">→</span></a>
		</div>
		<div class="fashion-hero__art" role="img" aria-label="검정과 회색으로 구성한 패션 에디토리얼 이미지">
			<span class="fashion-hero__shape fashion-hero__shape--one"></span>
			<span class="fashion-hero__shape fashion-hero__shape--two"></span>
			<span class="fashion-hero__index">01 / 25</span>
		</div>
	</section>

	<?php fashion_child_render_collection( 'new', 'JUST IN', '새롭게 도착했어요' ); ?>
	<?php fashion_child_render_collection( 'best', 'MOST WANTED', '지금 가장 많이 보는 상품' ); ?>

	<section class="fashion-editorial" aria-labelledby="fashion-editorial-title">
		<div class="fashion-editorial__art" role="img" aria-label="스톤 컬러 스타일링 이미지">
			<span>EDITORIAL 01</span>
		</div>
		<div class="fashion-editorial__copy">
			<p class="fashion-kicker">STYLE NOTE</p>
			<h2 id="fashion-editorial-title">톤을 낮추고<br>질감을 더하는 방법</h2>
			<p>차분한 스톤 컬러와 구조적인 실루엣으로 완성한 이번 주 에디토리얼.</p>
			<a class="fashion-text-link" href="#new">상품 다시 보기 <span aria-hidden="true">→</span></a>
		</div>
	</section>

	<?php fashion_child_render_collection( 'sale', 'LIMITED TIME', '세일이 끝나기 전에' ); ?>

	<?php fashion_child_render_reviews(); ?>
</main>

<nav class="fashion-bottom-nav" aria-label="모바일 빠른 메뉴">
	<a class="is-current" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M4 10.5 12 4l8 6.5V20h-5v-6H9v6H4z"/></svg>
		<span>홈</span>
	</a>
	<a href="<?php echo esc_url( $shop_url ); ?>">
		<svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="8" cy="8" r="3"/><circle cx="16" cy="8" r="3"/><circle cx="8" cy="16" r="3"/><circle cx="16" cy="16" r="3"/></svg>
		<span>상품</span>
	</a>
	<a href="<?php echo esc_url( home_url( '/?s=&post_type=product' ) ); ?>">
		<svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6"/><path d="m15 15 5 5"/></svg>
		<span>검색</span>
	</a>
	<a href="#fashion-review-title">
		<svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 5h14v11H9l-4 3z"/></svg>
		<span>리뷰</span>
	</a>
</nav>

<?php
get_footer();
