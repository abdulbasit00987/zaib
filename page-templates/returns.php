<?php
/**
 * Template Name: Return Form Page
 *
 * @package mrzaib
 */
get_header();
$sent = false;
if ( isset( $_POST['mrzaib_return'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'mrzaib_return' ) ) {
	$fields = array( 'r_name', 'r_phone', 'r_email', 'r_order', 'r_product', 'r_reason', 'r_type' );
	$body   = '';
	foreach ( $fields as $f ) {
		$body .= $f . ': ' . sanitize_text_field( wp_unslash( $_POST[ $f ] ?? '' ) ) . "\n";
	}
	wp_mail( mrzaib_opt( 'email', get_option( 'admin_email' ) ), 'Return request', $body );
	$sent = true;
}
?>
<main id="MainContent" class="site-main section">
	<div class="container narrow">
		<h1 class="page-title center"><?php the_title(); ?></h1>
		<p class="center muted"><?php esc_html_e( 'Not happy with your fragrance? Request a return or exchange within 15 days of delivery.', 'mrzaib' ); ?></p>
		<form class="contact-form" method="post">
			<?php if ( $sent ) : ?><div class="form-success"><?php esc_html_e( 'Return request received. Our team will contact you within 24 hours.', 'mrzaib' ); ?></div><?php endif; ?>
			<?php wp_nonce_field( 'mrzaib_return' ); ?>
			<div class="field-row"><input name="r_name" required placeholder="<?php esc_attr_e( 'Full name', 'mrzaib' ); ?>"><input name="r_phone" required placeholder="<?php esc_attr_e( 'Phone number', 'mrzaib' ); ?>"></div>
			<div class="field-row"><input type="email" name="r_email" placeholder="<?php esc_attr_e( 'Email', 'mrzaib' ); ?>"><input name="r_order" required placeholder="<?php esc_attr_e( 'Order number', 'mrzaib' ); ?>"></div>
			<input name="r_product" required placeholder="<?php esc_attr_e( 'Product name', 'mrzaib' ); ?>">
			<select name="r_type"><option><?php esc_html_e( 'Exchange', 'mrzaib' ); ?></option><option><?php esc_html_e( 'Refund', 'mrzaib' ); ?></option></select>
			<textarea name="r_reason" rows="5" required placeholder="<?php esc_attr_e( 'Reason for return', 'mrzaib' ); ?>"></textarea>
			<button class="btn btn-dark" name="mrzaib_return" value="1"><?php esc_html_e( 'Submit Request', 'mrzaib' ); ?></button>
		</form>
	</div>
</main>
<?php
get_footer();
