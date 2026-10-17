<?php
/**
 * Single blog post.
 *
 * @package mrzaib
 */
get_header();
?>
<main id="MainContent" class="site-main section">
	<div class="container narrow">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'single-post' ); ?>>
				<p class="blog-date center"><?php echo esc_html( get_the_date() ); ?></p>
				<h1 class="page-title center"><?php the_title(); ?></h1>
				<?php if ( has_post_thumbnail() ) : ?><div class="single-media"><?php the_post_thumbnail( 'large' ); ?></div><?php endif; ?>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
			<?php if ( comments_open() || get_comments_number() ) { comments_template(); } ?>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
