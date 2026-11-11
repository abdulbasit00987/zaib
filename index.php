<?php
/**
 * Blog page, blog categories/tags and fallback listing.
 *
 * @package mrzaib
 */
get_header();
$mz_paged = max( 1, (int) get_query_var( 'paged' ) );
if ( is_home() ) {
	$mz_title = get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Blog', 'mrzaib' );
	$mz_title = 'Blog' === $mz_title ? __( 'The Perfume Journal', 'mrzaib' ) : $mz_title;
	$mz_sub   = __( 'Fragrance notes, wearing tips and the stories behind every MR ZAIB perfume.', 'mrzaib' );
} elseif ( is_search() ) {
	/* translators: %s: search query */
	$mz_title = sprintf( __( 'Search results for "%s"', 'mrzaib' ), get_search_query() );
	$mz_sub   = '';
} else {
	$mz_title = wp_strip_all_tags( get_the_archive_title() );
	$mz_title = preg_replace( '/^(Category|Tag|Archives|Author): /', '', $mz_title );
	$mz_sub   = wp_strip_all_tags( get_the_archive_description() );
}
$mz_cats = get_categories( array( 'hide_empty' => true ) );
$mz_blog = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
?>
<main id="MainContent" class="site-main mzb-archive">
	<header class="mzb-hero">
		<div class="container">
			<p class="mzb-kicker"><?php esc_html_e( 'MR ZAIB Journal', 'mrzaib' ); ?></p>
			<h1 class="mzb-hero-title"><?php echo esc_html( $mz_title ); ?></h1>
			<?php if ( $mz_sub ) : ?><p class="mzb-hero-sub"><?php echo esc_html( $mz_sub ); ?></p><?php endif; ?>
			<?php if ( count( $mz_cats ) > 1 || is_category() || is_tag() ) : ?>
				<nav class="mzb-pills" aria-label="<?php esc_attr_e( 'Blog categories', 'mrzaib' ); ?>">
					<a href="<?php echo esc_url( $mz_blog ); ?>"<?php echo is_home() ? ' class="is-active"' : ''; ?>><?php esc_html_e( 'All', 'mrzaib' ); ?></a>
					<?php foreach ( $mz_cats as $c ) : ?>
						<a href="<?php echo esc_url( get_category_link( $c ) ); ?>"<?php echo is_category( $c->term_id ) ? ' class="is-active"' : ''; ?>><?php echo esc_html( $c->name ); ?></a>
					<?php endforeach; ?>
				</nav>
			<?php endif; ?>
		</div>
	</header>

	<div class="container mzb-list">
		<?php if ( have_posts() ) : ?>
			<?php
			$mz_first = is_home() && 1 === $mz_paged;
			if ( $mz_first ) :
				the_post();
				$mz_cat = get_the_category();
				?>
				<article <?php post_class( 'mzb-featured' ); ?>>
					<a class="mzb-media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'sizes' => '(max-width: 900px) 100vw, 640px', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); } ?></a>
					<div class="mzb-body">
						<p class="mzb-meta"><?php if ( $mz_cat ) : ?><span class="mzb-chip"><?php echo esc_html( $mz_cat[0]->name ); ?></span><?php endif; ?><span><?php echo esc_html( get_the_date() ); ?></span><span><?php echo (int) mrzaib_read_time(); ?> <?php esc_html_e( 'min read', 'mrzaib' ); ?></span></p>
						<h2 class="mzb-featured-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="mzb-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<a class="btn mzb-read" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read the story', 'mrzaib' ); ?></a>
					</div>
				</article>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>
				<div class="mzb-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						$mz_cat = get_the_category();
						?>
						<article <?php post_class( 'mzb-card' ); ?>>
							<a class="mzb-media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'medium_large', array( 'sizes' => '(max-width: 600px) 100vw, (max-width: 1000px) 50vw, 400px' ) );
								} else {
									echo '<img src="' . esc_url( mrzaib_img( 'hero-3-mobile.jpg' ) ) . '" alt="" loading="lazy">';
								}
								?>
							</a>
							<div class="mzb-body">
								<p class="mzb-meta"><?php if ( $mz_cat ) : ?><span class="mzb-chip"><?php echo esc_html( $mz_cat[0]->name ); ?></span><?php endif; ?><span><?php echo (int) mrzaib_read_time(); ?> <?php esc_html_e( 'min read', 'mrzaib' ); ?></span></p>
								<h2 class="mzb-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								<p class="mzb-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
								<a class="mzb-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'mrzaib' ); ?> <span aria-hidden="true">&rarr;</span></a>
							</div>
						</article>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>
			<div class="mzb-pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '&larr;', 'next_text' => '&rarr;' ) ); ?></div>
		<?php else : ?>
			<div class="mzb-empty">
				<p><?php esc_html_e( 'No articles yet. Please check back soon.', 'mrzaib' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
