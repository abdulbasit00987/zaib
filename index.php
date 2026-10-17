<?php
/**
 * Blog / fallback template.
 *
 * @package mrzaib
 */
get_header();
?>
<main id="MainContent" class="site-main section">
	<div class="container">
		<header class="page-header">
			<h1 class="page-title">
				<?php
				if ( is_home() ) {
					esc_html_e( 'Blogs', 'mrzaib' );
				} elseif ( is_search() ) {
					printf( esc_html__( 'Search results for "%s"', 'mrzaib' ), esc_html( get_search_query() ) );
				} else {
					the_archive_title();
				}
				?>
			</h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="blog-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class( 'blog-card' ); ?>>
						<a class="blog-media" href="<?php the_permalink(); ?>"><?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'medium_large' ); } else { echo '<img src="' . mrzaib_img( 'hero-3-mobile.jpg' ) . '" alt="">'; } // phpcs:ignore ?></a>
						<div class="blog-body">
							<p class="blog-date"><?php echo esc_html( get_the_date() ); ?></p>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( get_the_excerpt() ); ?></p>
							<a class="link-arrow" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more →', 'mrzaib' ); ?></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found.', 'mrzaib' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
