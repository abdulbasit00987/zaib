<?php
/**
 * Footer
 *
 * @package mrzaib
 */
$free = mrzaib_opt( 'free_ship', '3000' );
?>

<section class="newsletter">
	<div class="container newsletter-inner">
		<p class="nl-text"><?php esc_html_e( "Let's keep in touch! Get email offers & the latest news from us", 'mrzaib' ); ?></p>
		<form class="nl-form" action="#" method="post" data-newsletter>
			<input type="email" name="email" required placeholder="<?php esc_attr_e( 'Enter your email', 'mrzaib' ); ?>">
			<button type="submit" class="btn btn-dark"><?php esc_html_e( 'Subscribe', 'mrzaib' ); ?></button>
			<p class="nl-msg" hidden><?php esc_html_e( 'Thanks for subscribing!', 'mrzaib' ); ?></p>
		</form>
	</div>
</section>

<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-col">
			<h4><?php esc_html_e( 'Support', 'mrzaib' ); ?></h4>
			<?php mrzaib_footer_menu( 'footer-support', array( 'My Account' => 'page:my-account', 'Return Form' => 'page:returns', 'Privacy Policy' => 'page:privacy-policy-2', 'Shipping Policy' => 'page:shipping-policy', 'Terms of Service' => 'page:terms-of-service', 'Track Your Order' => 'page:track-order' ) ); ?>
		</div>
		<div class="footer-col">
			<h4><?php esc_html_e( 'Quick Links', 'mrzaib' ); ?></h4>
			<?php mrzaib_footer_menu( 'footer-quick', array( 'Men' => 'men', 'Women' => 'women', 'Unisex' => 'unisex', 'Attar' => 'attar', 'Tester' => 'tester' ) ); ?>
		</div>
		<div class="footer-col">
			<h4><?php esc_html_e( 'Get in touch', 'mrzaib' ); ?></h4>
			<ul class="footer-links">
				<?php if ( mrzaib_opt( 'phone' ) ) : ?><li><a class="ft-contact" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', mrzaib_opt( 'phone' ) ) ); ?>"><?php echo mrzaib_icon( 'phone' ); // phpcs:ignore ?><span><?php echo esc_html( mrzaib_opt( 'phone' ) ); ?></span></a></li><?php endif; ?>
				<?php if ( mrzaib_opt( 'email' ) ) : ?><li><a class="ft-contact" href="mailto:<?php echo esc_attr( mrzaib_opt( 'email' ) ); ?>"><?php echo mrzaib_icon( 'mail' ); // phpcs:ignore ?><span><?php echo esc_html( mrzaib_opt( 'email' ) ); ?></span></a></li><?php endif; ?>
			</ul>
			<?php $has_social = false; foreach ( array( 'facebook', 'x', 'instagram', 'tiktok', 'youtube', 'snapchat', 'pinterest' ) as $s ) { if ( mrzaib_opt( 'social_' . $s ) && '#' !== mrzaib_opt( 'social_' . $s ) ) { $has_social = true; } } ?>
			<?php if ( $has_social ) : ?><p class="follow-title"><?php esc_html_e( 'Follow us', 'mrzaib' ); ?></p><?php endif; ?>
			<div class="socials">
				<?php foreach ( array( 'facebook', 'x', 'instagram', 'tiktok', 'youtube', 'snapchat', 'pinterest' ) as $s ) : if ( ! mrzaib_opt( 'social_' . $s ) || '#' === mrzaib_opt( 'social_' . $s ) ) { continue; } ?>
					<a href="<?php echo esc_url( mrzaib_opt( 'social_' . $s ) ); ?>" aria-label="<?php echo esc_attr( ucfirst( $s ) ); ?>" target="_blank" rel="noopener"><?php echo mrzaib_icon( $s ); // phpcs:ignore ?></a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php if ( mrzaib_opt( 'address' ) ) : ?>
		<div class="footer-col footer-address">
			<h4><?php esc_html_e( 'Address', 'mrzaib' ); ?></h4>
			<p><?php echo esc_html( mrzaib_opt( 'address' ) ); ?></p>
		</div>
		<?php endif; ?>
	</div>
	<div class="container footer-mid">
		<div class="footer-pay">
			<strong><?php esc_html_e( 'We accept', 'mrzaib' ); ?></strong>
			<div class="pay-icons"><span>Cash on Delivery</span></div>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'mrzaib' ); ?></div>
	</div>
</footer>

<?php if ( mrzaib_opt( 'whatsapp' ) ) : ?><a class="wa-float" href="https://wa.me/<?php echo esc_attr( mrzaib_opt( 'whatsapp' ) ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?php echo mrzaib_icon( 'whatsapp' ); // phpcs:ignore ?></a><?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
