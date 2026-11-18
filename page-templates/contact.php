<?php
/**
 * Template Name: Contact Page
 *
 * @package mrzaib
 */
get_header();
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
			<form class="contact-form" id="mz-form" method="post">
				<?php if ( function_exists( 'mrzaib_form_notice' ) ) { mrzaib_form_notice( 'contact' ); } ?>
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
