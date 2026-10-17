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
			<h4><?php esc_html_e( 'About', 'mrzaib' ); ?></h4>
			<?php mrzaib_footer_menu( 'footer-about', array( 'FAQs' => 'page:faqs', 'Contact Us' => 'page:contact-us', 'Return & Refund Policy' => 'page:refund-policy', 'Blogs' => 'page:blog' ) ); ?>
		</div>
		<div class="footer-col">
			<h4><?php esc_html_e( 'Support', 'mrzaib' ); ?></h4>
			<?php mrzaib_footer_menu( 'footer-support', array( 'My Account' => 'page:my-account', 'Return Form' => 'page:returns', 'Privacy Policy' => 'page:privacy-policy-2', 'Shipping Policy' => 'page:shipping-policy', 'Terms of Service' => 'page:terms-of-service', 'Track Your Order' => 'page:track-order' ) ); ?>
		</div>
		<div class="footer-col">
			<h4><?php esc_html_e( 'Quick Links', 'mrzaib' ); ?></h4>
			<?php mrzaib_footer_menu( 'footer-quick', array( 'Men' => 'men', 'Women' => 'women', 'Unisex' => 'unisex', 'Ethereal' => 'ethereal', 'Tester' => 'tester' ) ); ?>
		</div>
		<div class="footer-col">
			<h4><?php esc_html_e( 'Get in touch', 'mrzaib' ); ?></h4>
			<ul class="footer-links">
				<?php if ( mrzaib_opt( 'phone' ) ) : ?><li><a href="tel:<?php echo esc_attr( mrzaib_opt( 'phone' ) ); ?>"><?php echo esc_html( mrzaib_opt( 'phone' ) ); ?></a></li><?php endif; ?>
				<li><a href="<?php echo esc_url( mrzaib_page_link( 'contact-us' ) ); ?>"><?php esc_html_e( 'Send us a message', 'mrzaib' ); ?></a></li>
			</ul>
			<?php $has_social = false; foreach ( array( 'facebook', 'x', 'instagram', 'tiktok', 'youtube' ) as $s ) { if ( mrzaib_opt( 'social_' . $s ) && '#' !== mrzaib_opt( 'social_' . $s ) ) { $has_social = true; } } ?>
			<?php if ( $has_social ) : ?><p class="follow-title"><?php esc_html_e( 'Follow us', 'mrzaib' ); ?></p><?php endif; ?>
			<div class="socials">
				<?php foreach ( array( 'facebook', 'x', 'instagram', 'tiktok', 'youtube' ) as $s ) : if ( ! mrzaib_opt( 'social_' . $s ) || '#' === mrzaib_opt( 'social_' . $s ) ) { continue; } ?>
					<a href="<?php echo esc_url( mrzaib_opt( 'social_' . $s ) ); ?>" aria-label="<?php echo esc_attr( ucfirst( $s ) ); ?>" target="_blank" rel="noopener"><?php echo mrzaib_icon( $s ); // phpcs:ignore ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div class="container footer-mid">
		<div class="footer-address">
			<?php if ( mrzaib_opt( 'address' ) || mrzaib_opt( 'email' ) ) : ?><strong><?php esc_html_e( 'Address', 'mrzaib' ); ?></strong><?php endif; ?>
			<?php if ( mrzaib_opt( 'address' ) ) : ?><p><?php echo esc_html( mrzaib_opt( 'address' ) ); ?></p><?php endif; ?>
			<?php if ( mrzaib_opt( 'email' ) ) : ?><p><?php esc_html_e( 'Email:', 'mrzaib' ); ?> <?php echo esc_html( mrzaib_opt( 'email' ) ); ?></p><?php endif; ?>
		</div>
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
