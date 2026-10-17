<?php
/**
 * Template Name: Store Locator Page
 *
 * @package mrzaib
 */
get_header();
$stores = array(
	array( 'Karachi', 'MR ZAIB Flagship', 'Shop No. 00, Ground Floor, Your Mall, Karachi', '11am – 10pm' ),
	array( 'Karachi', 'MR ZAIB Kiosk', 'Level 1, Sample Mall, Clifton, Karachi', '11am – 10pm' ),
	array( 'Lahore', 'MR ZAIB Lahore', 'Ground Floor, Sample Mall, Gulberg, Lahore', '11am – 10pm' ),
	array( 'Islamabad', 'MR ZAIB Islamabad', 'Sample Centre, F-7, Islamabad', '11am – 10pm' ),
);
?>
<main id="MainContent" class="site-main section">
	<div class="container">
		<h1 class="page-title center"><?php the_title(); ?></h1>
		<p class="center muted"><?php esc_html_e( 'Visit us and try every scent in person.', 'mrzaib' ); ?></p>
		<div class="store-grid">
			<?php foreach ( $stores as $s ) : ?>
				<div class="store-card">
					<span class="store-city"><?php echo esc_html( $s[0] ); ?></span>
					<h3><?php echo esc_html( $s[1] ); ?></h3>
					<p><?php echo esc_html( $s[2] ); ?></p>
					<p class="muted"><?php echo esc_html( $s[3] ); ?></p>
					<a class="btn btn-outline" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=<?php echo rawurlencode( $s[2] ); ?>"><?php esc_html_e( 'Get Directions', 'mrzaib' ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
		<?php while ( have_posts() ) : the_post(); ?><div class="entry-content"><?php the_content(); ?></div><?php endwhile; ?>
	</div>
</main>
<?php
get_footer();
