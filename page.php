<?php
/**
 * Default page.
 *
 * @package mrzaib
 */
get_header();
$is_wc = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
?>
<main id="MainContent" class="site-main section">
	<div class="container<?php echo $is_wc ? '' : ' narrow'; ?>">
		<?php while ( have_posts() ) : the_post(); ?>
			<h1 class="page-title center"><?php the_title(); ?></h1>
			<div class="entry-content"><?php the_content(); wp_link_pages( array( 'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'mrzaib' ), 'after' => '</nav>' ) ); ?></div>
		<?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
