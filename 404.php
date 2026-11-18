<?php
/**
 * 404
 *
 * @package mrzaib
 */
get_header();
?>
<main id="MainContent" class="site-main section">
	<div class="container narrow center">
		<h1 class="page-title">404</h1>
		<p><?php esc_html_e( 'This page could not be found.', 'mrzaib' ); ?></p>
		<p><a class="btn btn-dark" href="<?php echo esc_url( mrzaib_shop_link() ); ?>"><?php esc_html_e( 'Continue shopping', 'mrzaib' ); ?></a></p>
	</div>
</main>
<?php
get_footer();
