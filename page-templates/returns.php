<?php
/**
 * Template Name: Return Form Page
 *
 * @package mrzaib
 */
get_header();
?>
<main id="MainContent" class="site-main section">
	<div class="container narrow">
		<h1 class="page-title center"><?php the_title(); ?></h1>
		<p class="center muted"><?php esc_html_e( 'Not happy with your fragrance? Request a return or exchange within 15 days of delivery.', 'mrzaib' ); ?></p>
		<form class="contact-form" id="mz-form" method="post">
			<?php if ( function_exists( 'mrzaib_form_notice' ) ) { mrzaib_form_notice( 'return' ); } ?>
			<?php wp_nonce_field( 'mrzaib_return' ); ?>
			<div class="field-row"><input name="r_name" required placeholder="<?php esc_attr_e( 'Full name', 'mrzaib' ); ?>"><input name="r_phone" required placeholder="<?php esc_attr_e( 'Phone number', 'mrzaib' ); ?>"></div>
			<div class="field-row"><input type="email" name="r_email" required placeholder="<?php esc_attr_e( 'Email', 'mrzaib' ); ?>"><input name="r_order" required placeholder="<?php esc_attr_e( 'Order number', 'mrzaib' ); ?>"></div>
			<input name="r_product" required placeholder="<?php esc_attr_e( 'Product name', 'mrzaib' ); ?>">
			<select name="r_type"><option><?php esc_html_e( 'Exchange', 'mrzaib' ); ?></option><option><?php esc_html_e( 'Refund', 'mrzaib' ); ?></option></select>
			<textarea name="r_reason" rows="5" required placeholder="<?php esc_attr_e( 'Reason for return', 'mrzaib' ); ?>"></textarea>
			<button class="btn btn-dark" name="mrzaib_return" value="1"><?php esc_html_e( 'Submit Request', 'mrzaib' ); ?></button>
		</form>
	</div>
</main>
<?php
get_footer();
