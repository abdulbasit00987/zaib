<?php
/**
 * Template Name: Contact Page
 *
 * @package mrzaib
 */
get_header();
$sent = false;
if ( isset( $_POST['mrzaib_contact'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'mrzaib_contact' ) ) {
	$name  = sanitize_text_field( wp_unslash( $_POST['c_name'] ?? '' ) );
	$email = sanitize_email( wp_unslash( $_POST['c_email'] ?? '' ) );
	$phone = sanitize_text_field( wp_unslash( $_POST['c_phone'] ?? '' ) );
	$msg   = sanitize_textarea_field( wp_unslash( $_POST['c_msg'] ?? '' ) );
	$to    = mrzaib_opt( 'email', get_option( 'admin_email' ) );
	wp_mail( $to, 'Website contact: ' . $name, "Name: $name\nEmail: $email\nPhone: $phone\n\n$msg", array( 'Reply-To: ' . $email ) );
	$sent = true;
}
?>
<main id="MainContent" class="site-main section">
	<div class="container">
		<h1 class="page-title center"><?php the_title(); ?></h1>
		<div class="contact-grid">
			<div class="contact-info">
				<h3><?php esc_html_e( 'Customer Care', 'mrzaib' ); ?></h3>
				<?php if ( mrzaib_opt( 'phone' ) ) : ?><p><?php echo mrzaib_icon( 'phone' ); // phpcs:ignore ?> <a href="tel:<?php echo esc_attr( mrzaib_opt( 'phone' ) ); ?>"><?php echo esc_html( mrzaib_opt( 'phone' ) ); ?></a></p><?php endif; ?>
				<?php if ( mrzaib_opt( 'whatsapp' ) ) : ?><p><?php echo mrzaib_icon( 'whatsapp' ); // phpcs:ignore ?> <a href="https://wa.me/<?php echo esc_attr( mrzaib_opt( 'whatsapp' ) ); ?>">WhatsApp</a></p><?php endif; ?>
				<?php if ( mrzaib_opt( 'email' ) ) : ?><p><?php echo mrzaib_icon( 'mail' ); // phpcs:ignore ?> <a href="mailto:<?php echo esc_attr( mrzaib_opt( 'email' ) ); ?>"><?php echo esc_html( mrzaib_opt( 'email' ) ); ?></a></p><?php endif; ?>
				<?php if ( mrzaib_opt( 'address' ) ) : ?><p><?php echo mrzaib_icon( 'store' ); // phpcs:ignore ?> <?php echo esc_html( mrzaib_opt( 'address' ) ); ?></p><?php endif; ?>
				<p class="muted"><?php esc_html_e( 'Monday to Saturday, 10am to 7pm', 'mrzaib' ); ?></p>
				<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
			</div>
			<form class="contact-form" method="post">
				<?php if ( $sent ) : ?><div class="form-success"><?php esc_html_e( 'Thanks! We will get back to you soon.', 'mrzaib' ); ?></div><?php endif; ?>
				<?php wp_nonce_field( 'mrzaib_contact' ); ?>
				<div class="field-row"><input name="c_name" required placeholder="<?php esc_attr_e( 'Name', 'mrzaib' ); ?>"><input type="email" name="c_email" required placeholder="<?php esc_attr_e( 'Email', 'mrzaib' ); ?>"></div>
				<input name="c_phone" placeholder="<?php esc_attr_e( 'Phone number', 'mrzaib' ); ?>">
				<textarea name="c_msg" rows="6" required placeholder="<?php esc_attr_e( 'Comment', 'mrzaib' ); ?>"></textarea>
				<button class="btn btn-dark" name="mrzaib_contact" value="1"><?php esc_html_e( 'Send', 'mrzaib' ); ?></button>
			</form>
		</div>
	</div>
</main>
<?php
get_footer();
