<?php
/**
 * Single blog article.
 *
 * @package mrzaib
 */
get_header();
while ( have_posts() ) :
	the_post();
	$mz_id   = get_the_ID();
	$mz_cat  = get_the_category();
	$mz_blog = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
	$mz_prod = function_exists( 'mrzaib_blog_product' ) ? mrzaib_blog_product( $mz_id ) : null;
	$mz_url  = rawurlencode( get_permalink() );
	$mz_tt   = rawurlencode( get_the_title() );
	?>
<main id="MainContent" class="site-main mzb-single">
	<header class="mzb-hero mzb-hero-single">
		<div class="container">
			<nav class="mzb-crumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'mrzaib' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'mrzaib' ); ?></a><span aria-hidden="true">/</span><a href="<?php echo esc_url( $mz_blog ); ?>"><?php esc_html_e( 'Blog', 'mrzaib' ); ?></a><span aria-hidden="true">/</span><span><?php the_title(); ?></span>
			</nav>
			<?php if ( $mz_cat ) : ?><a class="mzb-chip" href="<?php echo esc_url( get_category_link( $mz_cat[0] ) ); ?>"><?php echo esc_html( $mz_cat[0]->name ); ?></a><?php endif; ?>
			<h1 class="mzb-hero-title"><?php the_title(); ?></h1>
			<p class="mzb-meta mzb-meta-light"><span><?php esc_html_e( 'By MR ZAIB', 'mrzaib' ); ?></span><span><?php echo esc_html( get_the_date() ); ?></span><span><?php echo (int) mrzaib_read_time(); ?> <?php esc_html_e( 'min read', 'mrzaib' ); ?></span></p>
		</div>
	</header>

	<div class="container">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="mzb-cover"><?php the_post_thumbnail( 'full', array( 'sizes' => '(max-width: 1100px) 100vw, 1060px', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?></div>
		<?php endif; ?>

		<div class="mzb-layout<?php echo $mz_prod ? ' has-aside' : ''; ?>">
			<article <?php post_class( 'mzb-article' ); ?>>
				<?php if ( has_excerpt() ) : ?><p class="mzb-lead"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
				<div class="entry-content mzb-content"><?php the_content(); ?></div>

				<footer class="mzb-foot">
					<?php $mz_tags = get_the_tags(); if ( $mz_tags ) : ?>
						<div class="mzb-tags">
							<?php foreach ( $mz_tags as $t ) : ?><a href="<?php echo esc_url( get_tag_link( $t ) ); ?>">#<?php echo esc_html( $t->name ); ?></a><?php endforeach; ?>
						</div>
					<?php endif; ?>
					<div class="mzb-share">
						<span><?php esc_html_e( 'Share', 'mrzaib' ); ?></span>
						<a href="https://wa.me/?text=<?php echo $mz_tt . '%20' . $mz_url; // phpcs:ignore ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'mrzaib' ); ?>"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3z"/></svg></a>
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $mz_url; // phpcs:ignore ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on Facebook', 'mrzaib' ); ?>"><svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true"><path fill="currentColor" d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H8v4h2v8h4v-8h3l1-4h-4V8z"/></svg></a>
						<a href="https://twitter.com/intent/tweet?url=<?php echo $mz_url; ?>&amp;text=<?php echo $mz_tt; // phpcs:ignore ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Share on X', 'mrzaib' ); ?>"><svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M17.8 3h3.1l-6.8 7.7L22 21h-6.2l-4.9-6.4L5.3 21H2.2l7.2-8.3L2 3h6.4l4.4 5.8L17.8 3zm-1.1 16.2h1.7L7.4 4.7H5.5l11.2 14.5z"/></svg></a>
						<button type="button" class="mzb-copy" data-url="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Copy link', 'mrzaib' ); ?>"><svg viewBox="0 0 24 24" width="17" height="17" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg></button>
					</div>
				</footer>
			</article>

			<?php if ( $mz_prod ) : ?>
				<aside class="mzb-aside">
					<div class="mzb-prod">
						<p class="mzb-prod-k"><?php esc_html_e( 'Featured in this story', 'mrzaib' ); ?></p>
						<a class="mzb-prod-img" href="<?php echo esc_url( $mz_prod->get_permalink() ); ?>"><?php echo $mz_prod->get_image( 'woocommerce_thumbnail', array( 'sizes' => '(max-width: 900px) 60vw, 300px', 'loading' => 'lazy' ) ); // phpcs:ignore ?></a>
						<h2 class="mzb-prod-name"><a href="<?php echo esc_url( $mz_prod->get_permalink() ); ?>"><?php echo esc_html( $mz_prod->get_name() ); ?></a></h2>
						<?php $mz_sub = get_post_meta( $mz_prod->get_id(), '_mrzaib_subtitle', true ); if ( $mz_sub ) : ?><p class="mzb-prod-sub"><?php echo esc_html( $mz_sub ); ?></p><?php endif; ?>
						<p class="mzb-prod-price"><?php echo $mz_prod->get_price_html(); // phpcs:ignore ?></p>
						<a class="btn mzb-prod-btn" href="<?php echo esc_url( $mz_prod->get_permalink() ); ?>"><?php esc_html_e( 'Shop now', 'mrzaib' ); ?></a>
						<p class="mzb-prod-note"><?php esc_html_e( 'Cash on Delivery all over Pakistan', 'mrzaib' ); ?></p>
					</div>
				</aside>
			<?php endif; ?>
		</div>

		<?php
		$mz_rel = get_posts( array( 'post_type' => 'post', 'numberposts' => 3, 'post__not_in' => array( $mz_id ), 'orderby' => 'date' ) );
		if ( $mz_rel ) :
			?>
			<section class="mzb-related">
				<h2 class="section-title center"><?php esc_html_e( 'More from the Journal', 'mrzaib' ); ?></h2>
				<div class="mzb-grid">
					<?php foreach ( $mz_rel as $r ) : ?>
						<article class="mzb-card">
							<a class="mzb-media" href="<?php echo esc_url( get_permalink( $r ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo get_the_post_thumbnail( $r, 'medium_large', array( 'sizes' => '(max-width: 600px) 100vw, (max-width: 1000px) 50vw, 400px', 'loading' => 'lazy' ) ); // phpcs:ignore ?></a>
							<div class="mzb-body">
								<p class="mzb-meta"><span><?php echo (int) mrzaib_read_time( $r ); ?> <?php esc_html_e( 'min read', 'mrzaib' ); ?></span></p>
								<h3 class="mzb-card-title"><a href="<?php echo esc_url( get_permalink( $r ) ); ?>"><?php echo esc_html( get_the_title( $r ) ); ?></a></h3>
								<a class="mzb-more" href="<?php echo esc_url( get_permalink( $r ) ); ?>"><?php esc_html_e( 'Read more', 'mrzaib' ); ?> <span aria-hidden="true">&rarr;</span></a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>
		<?php if ( comments_open() || get_comments_number() ) { comments_template(); } ?>
	</div>
</main>
	<?php
endwhile;
get_footer();
