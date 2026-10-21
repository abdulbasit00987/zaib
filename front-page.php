<?php
/**
 * Homepage
 *
 * @package mrzaib
 */

get_header();

if ( ! class_exists( 'WooCommerce' ) ) {
	echo '<main id="MainContent" class="container section"><div class="notice-box">';
	echo '<h2>MR ZAIB</h2><p>Please install and activate <strong>WooCommerce</strong>, then go to <em>Appearance &rarr; MR ZAIB Setup</em> and click <strong>Import Demo Website</strong>.</p>';
	echo '</div></main>';
	get_footer();
	return;
}

$all_products = mrzaib_products_query( array( 'limit' => 12 ) );
?>
<main id="MainContent" class="site-main home">

	<!-- HERO SLIDER -->
	<section class="hero" data-slider data-autoplay="5000">
		<div class="slides">
			<?php foreach ( mrzaib_hero_slides() as $n => $sl ) : ?>
				<a class="slide<?php echo 0 === $n ? ' is-active' : ''; ?>" href="<?php echo esc_url( $sl[2] ); ?>">
					<picture>
						<source media="(max-width: 749px)" srcset="<?php echo esc_url( $sl[1] ); ?>">
						<img src="<?php echo esc_url( $sl[0] ); ?>" alt="<?php echo esc_attr( $sl[3] ); ?>" <?php echo 0 === $n ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
					</picture>
				</a>
			<?php endforeach; ?>
		</div>
		<button class="slider-arrow prev" aria-label="Previous"><?php echo mrzaib_icon( 'prev' ); // phpcs:ignore ?></button>
		<button class="slider-arrow next" aria-label="Next"><?php echo mrzaib_icon( 'next' ); // phpcs:ignore ?></button>
		<div class="slider-dots"></div>
	</section>

	<!-- TOP PERFUME CATEGORIES -->
	<section class="section collections">
		<div class="container">
			<h2 class="section-title center"><?php echo esc_html( mrzaib_opt( 'col_title', 'Top Perfume Categories' ) ); ?></h2>
			<?php $cats = mrzaib_home_categories(); ?>
			<div class="cc-wrap is-slider">
				<button type="button" class="slider-arrow prev cc-prev is-off" aria-label="Previous"><?php echo mrzaib_icon( 'prev' ); // phpcs:ignore ?></button>
				<div class="collection-grid scroll-x">
					<?php foreach ( $cats as $c ) : ?>
						<a class="collection-card" href="<?php echo esc_url( $c['link'] ); ?>">
							<span class="cc-media"><img src="<?php echo esc_url( $c['img'] ); ?>" alt="<?php echo esc_attr( 'MR ZAIB ' . $c['name'] . ' perfumes' ); ?>" loading="lazy"></span>
							<span class="cc-title"><?php echo esc_html( $c['name'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
				<button type="button" class="slider-arrow next cc-next is-off" aria-label="Next"><?php echo mrzaib_icon( 'next' ); // phpcs:ignore ?></button>
			</div>
		</div>
	</section>

	<!-- OUR BEST SELLERS (TABS) -->
	<?php if ( $all_products ) : ?>
	<section class="section best-sellers" data-tabs>
		<div class="container">
			<h2 class="section-title center"><?php echo esc_html( mrzaib_opt( 'bs_title', 'Our Best Sellers' ) ); ?></h2>
			<p class="section-sub center"><?php echo esc_html( mrzaib_opt( 'bs_sub', "Perfumes you won't find anywhere else" ) ); ?></p>
			<?php
			$tabs = array();
			// Every top-level category that has products (new categories appear automatically), in WooCommerce category order.
			$bs_terms = get_terms( array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => false,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
				'menu_order' => 'asc',
			) );
			foreach ( is_wp_error( $bs_terms ) ? array() : $bs_terms as $term ) {
				$found = mrzaib_products_query( array( 'limit' => 4, 'category' => array( $term->slug ), 'mz_top_rated' => true ) ); // Top rated first; includes sub-categories.
				if ( $found ) {
					$tabs[ $term->slug ] = array( $term->name, $term, $found );
				}
			}
			if ( ! $tabs ) {
				$tabs['all'] = array( __( 'All Perfumes', 'mrzaib' ), null );
			}
			?>
			<div class="tab-buttons" role="tablist">
				<?php $n = 0; foreach ( $tabs as $slug => $t ) : ?>
					<button class="tab-btn<?php echo 0 === $n ? ' is-active' : ''; ?>" data-tab="<?php echo esc_attr( $slug ); ?>" role="tab"><?php echo esc_html( $t[0] ); ?></button>
				<?php $n++; endforeach; ?>
			</div>
			<?php $n = 0; foreach ( $tabs as $slug => $t ) :
				$items = 'all' === $slug ? mrzaib_products_query( array( 'limit' => 4, 'mz_top_rated' => true ) ) : $t[2];
				$total = $t[1] ? (int) $t[1]->count : count( $all_products );
				$more  = max( 0, $total - count( $items ) );
				$link  = 'all' === $slug ? mrzaib_shop_link() : mrzaib_cat_link( $slug );
				$bgid  = $t[1] ? get_term_meta( $t[1]->term_id, 'thumbnail_id', true ) : 0;
				$bg    = $bgid ? wp_get_attachment_image_url( $bgid, 'medium_large' ) : mrzaib_img( 'hero-3-mobile.jpg' );
				?>
				<div class="tab-panel<?php echo 0 === $n ? ' is-active' : ''; ?>" data-panel="<?php echo esc_attr( $slug ); ?>">
					<div class="product-grid grid-5 bs-grid scroll-x<?php echo count( $items ) < 3 ? ' is-few' : ''; ?>">
						<?php foreach ( $items as $p ) { mrzaib_product_card( $p ); } ?>
						<a class="discover-card" href="<?php echo esc_url( $link ); ?>">
							<img src="<?php echo esc_url( $bg ); ?>" alt="" loading="lazy">
							<span class="dc-panel">
								<span class="dc-title"><?php echo $more ? esc_html( sprintf( __( 'Discover %d More', 'mrzaib' ), $more ) ) : esc_html__( 'Discover More', 'mrzaib' ); ?> &#8599;</span>
								<span class="dc-btn"><?php esc_html_e( 'Explore', 'mrzaib' ); ?></span>
							</span>
						</a>
					</div>
				</div>
			<?php $n++; endforeach; ?>
		</div>
	</section>
	<?php endif; ?>

	<!-- NEW ARRIVALS: newest products first -->
	<?php get_template_part( 'template-parts/product-row', null, array( 'title' => mrzaib_opt( 'row1_title', 'New Arrivals' ), 'mode' => 'new', 'limit' => 5 ) ); ?>

	<!-- VIDEO SHOWCASE -->
	<?php
	$vids = array(
		1 => array( 'kahn-vahl', 'kahn-vahl' ),
		2 => array( 'arvon', 'arvon' ),
		3 => array( 'mr-zaib-collection', 'kahn-vahl' ),
		4 => array( 'mr-zaib-gift', 'arvon' ),
	);
	$tiles  = array();
	$vcount = min( (int) get_theme_mod( 'mrzaib_video_count', 4 ), defined( 'MRZAIB_VIDEO_MAX' ) ? MRZAIB_VIDEO_MAX : 12 );
	for ( $i = 1; $i <= $vcount; $i++ ) {
		$v     = isset( $vids[ $i ] ) ? $vids[ $i ] : array( '', 1 === $i % 2 ? 'kahn-vahl' : 'arvon' );
		// Video: Customizer value. Removed (empty) = no video, the cover image is shown instead.
		$vurl     = (string) get_theme_mod( 'mrzaib_video_' . $i, mrzaib_video_default( $i ) );
		$is_theme = ( $vurl === mrzaib_video_default( $i ) || $vurl === mrzaib_home_media_url( 'video_' . $i ) );
		$poster   = (string) get_theme_mod( 'mrzaib_video_poster_' . $i, mrzaib_video_default( $i, 'jpg' ) );
		// The theme's cover belongs to the theme video: hide it when an own video was uploaded.
		if ( $vurl && ! $is_theme && ( $poster === mrzaib_video_default( $i, 'jpg' ) || $poster === mrzaib_home_media_url( 'video_poster_' . $i ) ) ) {
			$poster = '';
		}
		if ( ! $vurl && ! $poster ) {
			continue; // Nothing to show in this slot.
		}
		$prod = mrzaib_video_product( $i );
		if ( ! $prod && $all_products ) {
			$prod = $all_products[ ( $i - 1 ) % count( $all_products ) ];
		}
		if ( $prod ) {
			$tiles[] = array( $vurl, $poster, $prod );
		}
	}
	if ( $tiles ) :
		?>
		<section class="section video-showcase">
			<div class="container">
				<?php $vs_slider = count( $tiles ) > 4; ?>
				<div class="vs-wrap<?php echo $vs_slider ? ' is-slider' : ''; ?>">
				<?php if ( $vs_slider ) : ?>
					<button type="button" class="slider-arrow prev vs-prev" aria-label="Previous"><?php echo mrzaib_icon( 'prev' ); // phpcs:ignore ?></button>
					<button type="button" class="slider-arrow next vs-next" aria-label="Next"><?php echo mrzaib_icon( 'next' ); // phpcs:ignore ?></button>
				<?php endif; ?>
				<div class="vs-grid scroll-x">
					<?php foreach ( $tiles as $t ) : list( $vurl, $poster, $prod ) = $t; ?>
						<div class="vs-tile">
							<?php if ( $vurl ) : ?>
								<video class="vs-video" muted loop playsinline autoplay preload="metadata" <?php echo $poster ? 'poster="' . esc_url( $poster ) . '"' : ''; ?> data-src="<?php echo esc_url( $vurl ); ?>"></video>
							<?php else : ?>
								<img class="vs-video vs-cover" src="<?php echo esc_url( $poster ); ?>" alt="<?php echo esc_attr( $prod->get_name() ); ?>" loading="lazy">
							<?php endif; ?>
							<a class="vs-badge" href="<?php echo esc_url( get_permalink( $prod->get_id() ) ); ?>">
								<span class="vs-thumb"><?php echo $prod->get_image( 'thumbnail' ); // phpcs:ignore ?></span>
								<span class="vs-meta"><span class="vs-name"><?php echo esc_html( $prod->get_name() ); ?></span><span class="vs-price"><?php echo wp_kses_post( wc_price( $prod->get_price() ) ); ?></span></span>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- FEATURED COLLECTION: products marked Featured (star) in Products -->
	<?php get_template_part( 'template-parts/product-row', null, array( 'title' => mrzaib_opt( 'row2_title', 'Featured Collection' ), 'mode' => 'featured', 'limit' => 5 ) ); ?>

	<!-- PROMO BANNER -->
	<section class="section promo-band-wrap">
		<div class="container">
			<a class="promo-band" href="<?php echo esc_url( mrzaib_opt( 'promo_link', mrzaib_shop_link() ) ); ?>">
				<picture>
					<source media="(max-width: 749px)" srcset="<?php echo esc_url( mrzaib_opt( 'promo_img_m' ) ? mrzaib_opt( 'promo_img_m' ) : mrzaib_img( 'promo-band-mobile.jpg' ) ); ?>">
					<img src="<?php echo esc_url( mrzaib_opt( 'promo_img_d' ) ? mrzaib_opt( 'promo_img_d' ) : mrzaib_img( 'promo-band-desktop.jpg' ) ); ?>" alt="<?php esc_attr_e( 'MR ZAIB perfumes made for moments that leave your presence: Kahn-vahl and Arvon', 'mrzaib' ); ?>" loading="lazy" width="1920" height="384">
				</picture>
			</a>
		</div>
	</section>

	<!-- FEATURES -->
	<section class="section features">
		<div class="container feature-grid">
			<?php
			$feats = array(
				array( 'fcart', mrzaib_opt( 'feat_1', '1M+ Customers' ) ),
				array( 'award', mrzaib_opt( 'feat_2', 'Long Lasting Fragrances' ) ),
				array( 'box', mrzaib_opt( 'feat_3', '15 Days Easy Returns Policy' ) ),
				array( 'thumb', mrzaib_opt( 'feat_4', 'Super Macerated Perfumes' ) ),
			);
			foreach ( $feats as $fi => $f ) :
				$ficon = get_theme_mod( 'mrzaib_feat_icon_' . ( $fi + 1 ), mrzaib_feat_default_icon( $fi + 1 ) );
				if ( $ficon === mrzaib_feat_default_icon( $fi + 1 ) || ( $ficon && $ficon === mrzaib_home_media_url( 'feat_icon_' . ( $fi + 1 ) ) ) ) {
					$ficon = ''; // Theme icon: print inline SVG.
				}
				if ( ! $f[1] ) {
					continue;
				}
				?>
				<div class="feature"><span class="f-icon"><?php echo $ficon ? '<img src="' . esc_url( $ficon ) . '" alt="" width="40" height="40" loading="lazy">' : mrzaib_icon( $f[0] ); // phpcs:ignore ?></span><h3><?php echo esc_html( $f[1] ); ?></h3></div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- SEO TEXT -->
	<section class="section seo-block">
		<div class="container narrow">
			<h2 class="section-title center"><?php echo esc_html( mrzaib_opt( 'seo_title2', 'Best Perfumes in Pakistan by MR ZAIB' ) ); ?></h2>
			<div class="seo-content" data-readmore>
				<p><?php echo wp_kses_post( mrzaib_opt( 'seo_intro', mrzaib_seo_default_intro() ) ); ?> <button class="readmore-btn" type="button" data-more="<?php esc_attr_e( 'Read More...', 'mrzaib' ); ?>" data-less="<?php esc_attr_e( 'Show Less', 'mrzaib' ); ?>"><?php esc_html_e( 'Read More...', 'mrzaib' ); ?></button></p>
				<div class="seo-more">
					<?php echo wp_kses_post( mrzaib_opt( 'seo_more', mrzaib_seo_default_more() ) ); ?>
				</div>
			</div>
		</div>
	</section>

	<!-- TRUSTED BY FRAGHEADS -->
	<section class="trusted">
		<picture class="trusted-bg">
			<source media="(max-width: 749px)" srcset="<?php echo esc_url( mrzaib_img( 'stats-bg-mobile.jpg' ) ); ?>">
			<img src="<?php echo esc_url( mrzaib_img( 'stats-bg.jpg' ) ); ?>" alt="" loading="lazy">
		</picture>
		<div class="container trusted-inner">
			<span class="trusted-pill"><?php echo esc_html( mrzaib_opt( 'trust_pill', 'Trusted by Fragheads' ) ); ?></span>
			<h2><?php echo esc_html( mrzaib_opt( 'trust_title', "Pakistan's Luxury Perfume Brand." ) ); ?></h2>
			<p class="trusted-sub"><?php echo esc_html( mrzaib_opt( 'trust_sub', 'Pakistanis choose MR ZAIB for quality they can feel, at prices that make sense.' ) ); ?></p>
			<div class="trusted-card">
				<?php
				$stats = array(
					array( mrzaib_opt( 'tstat_1', '1' ), mrzaib_opt( 'tstat_1s', 'M+' ), mrzaib_opt( 'tstat_1l', 'customers served across Pakistan' ) ),
					array( mrzaib_opt( 'tstat_2', '98' ), mrzaib_opt( 'tstat_2s', '%' ), mrzaib_opt( 'tstat_2l', 'satisfaction rate based on customer reviews' ) ),
					array( mrzaib_opt( 'tstat_3', '15' ), mrzaib_opt( 'tstat_3s', ' Days' ), mrzaib_opt( 'tstat_3l', 'easy returns on every order' ) ),
				);
				foreach ( $stats as $st ) :
					?>
					<div class="tstat"><strong><span class="num" data-count="<?php echo esc_attr( $st[0] ); ?>">0</span><?php echo esc_html( $st[1] ); ?></strong><p><?php echo esc_html( $st[2] ); ?></p></div>
				<?php endforeach; ?>
			</div>
			<ul class="trusted-badges">
				<li><?php echo mrzaib_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( mrzaib_opt( 'tbadge_1', 'Easy 15-day exchange' ) ); ?></li>
				<li><?php echo mrzaib_icon( 'shield' ); // phpcs:ignore ?><?php echo esc_html( mrzaib_opt( 'tbadge_2', 'Cash on Delivery' ) ); ?></li>
				<li><?php echo mrzaib_icon( 'truck' ); // phpcs:ignore ?><?php echo esc_html( mrzaib_opt( 'tbadge_3', 'Ships across Pakistan' ) ); ?></li>
			</ul>
			<p class="trusted-note"><?php echo esc_html( sprintf( __( 'Spend over %s and get free delivery', 'mrzaib' ), mrzaib_opt( 'free_ship', '3000' ) ) ); ?></p>
		</div>
	</section>

</main>
<?php
get_footer();
