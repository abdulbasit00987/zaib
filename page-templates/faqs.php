<?php
/**
 * Template Name: FAQs Page
 *
 * @package mrzaib
 */
get_header();
$faqs = function_exists( 'mrzaib_faq_groups' ) ? mrzaib_faq_groups() : array();
?>
<main id="MainContent" class="site-main section">
	<div class="container narrow">
		<h1 class="page-title center"><?php the_title(); ?></h1>
		<?php while ( have_posts() ) : the_post(); ?><div class="entry-content"><?php the_content(); ?></div><?php endwhile; ?>
		<?php foreach ( $faqs as $group => $items ) : ?>
			<h3 class="faq-group"><?php echo esc_html( $group ); ?></h3>
			<div class="faq-list">
				<?php foreach ( $items as $f ) : ?>
					<details class="faq-item"><summary><?php echo esc_html( $f[0] ); ?><?php echo mrzaib_icon( 'down' ); // phpcs:ignore ?></summary><div class="faq-answer"><p><?php echo esc_html( $f[1] ); ?></p></div></details>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</main>
<?php
get_footer();
