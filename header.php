<?php
/**
 * Header
 *
 * @package mrzaib
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#MainContent"><?php esc_html_e( 'Skip to content', 'mrzaib' ); ?></a>

<?php
$announcements = array_filter( array( mrzaib_opt( 'announce_1', 'Luxury Perfumes | Leave Your Presence' ), mrzaib_opt( 'announce_2', 'Free Shipping on orders over Rs. 3000' ), mrzaib_opt( 'announce_3', 'Cash on Delivery all over Pakistan' ) ) );
if ( $announcements ) :
	?>
<div class="announcement-bar" data-announcement>
	<button class="ann-nav ann-prev" aria-label="<?php esc_attr_e( 'Previous', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'prev' ); // phpcs:ignore ?></button>
	<div class="ann-track">
		<?php foreach ( array_values( $announcements ) as $i => $a ) : ?>
			<p class="ann-item<?php echo 0 === $i ? ' is-active' : ''; ?>"><?php echo esc_html( $a ); ?></p>
		<?php endforeach; ?>
	</div>
	<button class="ann-nav ann-next" aria-label="<?php esc_attr_e( 'Next', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'next' ); // phpcs:ignore ?></button>
</div>
<?php endif; ?>

<div class="header-wrap" data-header-wrap>
<header class="site-header" data-header>
	<div class="container header-inner">
		<div class="header-left">
			<button class="icon-btn menu-toggle" data-open="drawer-menu" aria-label="<?php esc_attr_e( 'Menu', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'menu' ); // phpcs:ignore ?></button>
			<button class="icon-btn search-toggle hide-desktop" data-open="search-overlay" aria-label="<?php esc_attr_e( 'Search', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'search' ); // phpcs:ignore ?></button>
			<div class="header-search hide-mobile"><?php mrzaib_search_form(); ?></div>
		</div>
		<div class="header-logo">
			<?php if ( is_front_page() ) : ?><h1 class="site-title"><?php endif; ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link" rel="home">
				<?php
				$logo_id = get_theme_mod( 'custom_logo' );
				if ( $logo_id ) {
					echo wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'logo-img', 'alt' => get_bloginfo( 'name' ) ) );
				} else {
					echo '<img class="logo-img" src="' . mrzaib_img( 'logo-gold.png' ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">'; // phpcs:ignore
				}
				?>
				<span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
			</a>
			<?php if ( is_front_page() ) : ?></h1><?php endif; ?>
		</div>
		<div class="header-right">
			<?php if ( class_exists( 'WooCommerce' ) ) : ?>
				<a class="icon-btn account-btn hide-mobile" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>" aria-label="<?php esc_attr_e( 'Account', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'user' ); // phpcs:ignore ?></a>
				<?php $mz_count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?>
				<a class="icon-btn cart-toggle" href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' ) ); ?>" data-open="drawer-cart" aria-label="<?php esc_attr_e( 'Cart', 'mrzaib' ); ?>">
					<span class="cart-ico"><?php echo mrzaib_icon( 'bag' ); // phpcs:ignore ?><span class="cart-count<?php echo $mz_count ? '' : ' is-empty'; ?>"><?php echo esc_html( $mz_count ); ?></span></span>
					<span class="cart-label hide-mobile"><?php esc_html_e( 'Cart', 'mrzaib' ); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<nav class="main-nav hide-mobile" aria-label="<?php esc_attr_e( 'Main', 'mrzaib' ); ?>">
		<div class="container">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_id' => 'primary-menu', 'depth' => 3 ) );
			} else {
				mrzaib_fallback_menu();
			}
			?>
		</div>
	</nav>
</header>
</div>

<!-- Drawer: menu -->
<div class="drawer drawer-left" id="drawer-menu" aria-hidden="true">
	<div class="drawer-panel">
		<div class="drawer-head">
			<img class="drawer-logo" src="<?php echo mrzaib_img( 'logo-gold.png' ); // phpcs:ignore ?>" alt="">
			<button class="icon-btn" data-close aria-label="<?php esc_attr_e( 'Close', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'close' ); // phpcs:ignore ?></button>
		</div>
		<div class="drawer-promo">
			<a href="<?php echo esc_url( mrzaib_cat_link( 'men' ) ); ?>"><img src="<?php echo mrzaib_img( 'cat-men.jpg' ); // phpcs:ignore ?>" alt=""><span><?php esc_html_e( 'Men', 'mrzaib' ); ?></span></a>
			<a href="<?php echo esc_url( mrzaib_cat_link( 'unisex' ) ); ?>"><img src="<?php echo mrzaib_img( 'cat-unisex.jpg' ); // phpcs:ignore ?>" alt=""><span><?php esc_html_e( 'Unisex', 'mrzaib' ); ?></span></a>
		</div>
		<nav class="drawer-nav">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false, 'menu_id' => 'drawer-menu-list', 'depth' => 3 ) );
			} else {
				mrzaib_fallback_menu();
			}
			?>
		</nav>
		<div class="drawer-care">
			<p class="care-title"><?php esc_html_e( 'CUSTOMER CARE', 'mrzaib' ); ?></p>
			<div class="care-icons">
				<?php if ( mrzaib_opt( 'whatsapp' ) ) : ?><a href="https://wa.me/<?php echo esc_attr( mrzaib_opt( 'whatsapp' ) ); ?>" aria-label="WhatsApp"><?php echo mrzaib_icon( 'whatsapp' ); // phpcs:ignore ?></a><?php endif; ?>
				<?php if ( mrzaib_opt( 'phone' ) ) : ?><a href="tel:<?php echo esc_attr( mrzaib_opt( 'phone' ) ); ?>" aria-label="Phone"><?php echo mrzaib_icon( 'phone' ); // phpcs:ignore ?></a><?php endif; ?>
				<?php if ( mrzaib_opt( 'email' ) ) : ?><a href="mailto:<?php echo esc_attr( mrzaib_opt( 'email' ) ); ?>" aria-label="Email"><?php echo mrzaib_icon( 'mail' ); // phpcs:ignore ?></a><?php endif; ?>
			</div>
			<div class="care-links">
				<a href="<?php echo esc_url( mrzaib_page_link( 'track-order' ) ); ?>"><?php echo mrzaib_icon( 'truck' ); // phpcs:ignore ?><?php esc_html_e( 'Tracking', 'mrzaib' ); ?></a>
			</div>
		</div>
	</div>
	<div class="drawer-overlay" data-close></div>
</div>

<!-- Drawer: cart -->
<?php if ( class_exists( 'WooCommerce' ) ) : ?>
<div class="drawer drawer-right" id="drawer-cart" aria-hidden="true">
	<div class="drawer-panel">
		<div class="drawer-head">
			<h3><?php esc_html_e( 'Your cart', 'mrzaib' ); ?></h3>
			<button class="icon-btn" data-close aria-label="<?php esc_attr_e( 'Close', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'close' ); // phpcs:ignore ?></button>
		</div>
		<p class="drawer-ship-note"><?php printf( esc_html__( 'Spend over Rs. %s and get free delivery', 'mrzaib' ), esc_html( mrzaib_opt( 'free_ship', '3000' ) ) ); ?></p>
		<div class="drawer-cart-body"><?php woocommerce_mini_cart(); ?></div>
	</div>
	<div class="drawer-overlay" data-close></div>
</div>
<?php endif; ?>

<!-- Search overlay -->
<div class="search-overlay" id="search-overlay" aria-hidden="true">
	<div class="container">
		<div class="search-overlay-inner">
			<div class="search-overlay-form"><?php mrzaib_search_form( 'search-form-big' ); ?></div>
			<button type="button" class="icon-btn" data-close aria-label="<?php esc_attr_e( 'Close', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'close' ); // phpcs:ignore ?></button>
		</div>
	</div>
</div>
