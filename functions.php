<?php
/**
 * MR ZAIB theme functions.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MRZAIB_VERSION', '1.0.0' );
define( 'MRZAIB_DIR', get_template_directory() );
define( 'MRZAIB_URI', get_template_directory_uri() );

/* ------------------------------------------------------------------
 * Setup
 * ------------------------------------------------------------------ */
function mrzaib_setup() {
	load_theme_textdomain( 'mrzaib', MRZAIB_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array( 'height' => 160, 'width' => 600, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );

	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 900,
		'product_grid'          => array( 'default_rows' => 4, 'default_columns' => 4, 'min_columns' => 2, 'max_columns' => 5 ),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary'        => __( 'Main Menu (Shop mega menu + links)', 'mrzaib' ),
		'footer-about'   => __( 'Footer: About', 'mrzaib' ),
		'footer-support' => __( 'Footer: Support', 'mrzaib' ),
		'footer-quick'   => __( 'Footer: Quick Links', 'mrzaib' ),
	) );
}
add_action( 'after_setup_theme', 'mrzaib_setup' );

/* ------------------------------------------------------------------
 * Assets
 * ------------------------------------------------------------------ */
function mrzaib_assets() {
	wp_enqueue_style( 'mrzaib-fonts', 'https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap', array(), null );
	wp_enqueue_style( 'mrzaib-main', MRZAIB_URI . '/assets/css/main.css', array(), MRZAIB_VERSION . '.' . filemtime( MRZAIB_DIR . '/assets/css/main.css' ) );
	wp_enqueue_style( 'mrzaib-style', get_stylesheet_uri(), array( 'mrzaib-main' ), MRZAIB_VERSION );
	wp_enqueue_script( 'mrzaib-main', MRZAIB_URI . '/assets/js/main.js', array( 'jquery' ), MRZAIB_VERSION . '.' . filemtime( MRZAIB_DIR . '/assets/js/main.js' ), true );
	if ( function_exists( 'is_product' ) && is_product() ) {
		wp_enqueue_script( 'mrzaib-reviews', MRZAIB_URI . '/assets/js/reviews.js', array(), MRZAIB_VERSION . '.' . filemtime( MRZAIB_DIR . '/assets/js/reviews.js' ), true );
	}
	if ( class_exists( 'WooCommerce' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}
	wp_localize_script( 'mrzaib-main', 'MRZAIB', array( 'ajax' => admin_url( 'admin-ajax.php' ) ) );
}
add_action( 'wp_enqueue_scripts', 'mrzaib_assets' );

/* ------------------------------------------------------------------
 * Helpers
 * ------------------------------------------------------------------ */
function mrzaib_img( $file ) {
	$path = MRZAIB_DIR . '/assets/images/' . $file;
	return esc_url( MRZAIB_URI . '/assets/images/' . $file . ( file_exists( $path ) ? '?v=' . filemtime( $path ) : '' ) );
}

function mrzaib_opt( $key, $default = '' ) {
	$v = get_theme_mod( 'mrzaib_' . $key, $default );
	return ( '' === $v || null === $v ) ? $default : $v;
}

function mrzaib_cat_link( $slug ) {
	if ( function_exists( 'get_term_link' ) && taxonomy_exists( 'product_cat' ) ) {
		$link = get_term_link( $slug, 'product_cat' );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

function mrzaib_product_link( $slug ) {
	$pp = get_page_by_path( $slug, OBJECT, 'product' );
	return $pp ? get_permalink( $pp->ID ) : mrzaib_shop_link();
}

function mrzaib_shop_link() {
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

function mrzaib_page_link( $slug ) {
	$p = get_page_by_path( $slug );
	return $p ? get_permalink( $p ) : home_url( '/' . $slug . '/' );
}

function mrzaib_icon( $name ) {
	$i = array(
		'menu'    => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 6h18M3 12h18M3 18h18"/></svg>',
		'search'  => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
		'user'    => '<svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="10" cy="6.5" r="3.5"/><path d="M3 18c.8-3.6 3.6-5.6 7-5.6s6.2 2 7 5.6"/></svg>',
		'bag'     => '<svg viewBox="0 0 20 20" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"><path d="M3.5 6.5h13l-.9 11H4.4l-.9-11Z"/><path d="M7 8.5V5.5a3 3 0 0 1 6 0v3" stroke-linecap="round"/></svg>',
		'eye'     => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>',
		'cartadd' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M5 8h14l-1 13H6L5 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/><path d="M12 12v5M9.5 14.5h5"/></svg>',
		'fcart'   => '<svg viewBox="0 0 64 64" width="58" height="58" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"><path d="M4 10h8l7 30h32l6-22H15"/><circle cx="23" cy="50" r="4"/><circle cx="46" cy="50" r="4"/><path d="M26 26h22M28 33h18"/></svg>',
		'award'   => '<svg viewBox="0 0 64 64" width="58" height="58" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"><circle cx="32" cy="24" r="17"/><circle cx="32" cy="24" r="11"/><path d="m32 17 2.2 4.5 5 .7-3.6 3.5.9 5-4.5-2.4-4.5 2.4.9-5-3.6-3.5 5-.7Z"/><path d="M21 37 15 58l8-3 5 6 4-16M43 37l6 21-8-3-5 6-4-16"/></svg>',
		'box'     => '<svg viewBox="0 0 64 64" width="58" height="58" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"><path d="M32 6 56 18v28L32 58 8 46V18Z"/><path d="m8 18 24 12 24-12M32 30v28M20 12l24 12v10"/></svg>',
		'thumb'   => '<svg viewBox="0 0 64 64" width="58" height="58" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"><path d="M10 30h9v26h-9zM19 52c4 2 8 4 13 4h13a5 5 0 0 0 5-5 5 5 0 0 0 3-8 5 5 0 0 0 0-8 5 5 0 0 0-4-8H37l2-10a5 5 0 0 0-9-3l-11 15"/><path d="M48 6v6M58 12l-5 4M60 24h-6"/></svg>',
		'qsearch' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4-4"/></svg>',
		'qcart'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M2 3h3l2.5 12h11L21 7H6.2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>',
		'starf'   => '<svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="m12 2.5 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4l-5.9 3.1 1.2-6.5L2.5 9.4l6.6-.9L12 2.5Z"/></svg>',
		'close'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 6l12 12M18 6 6 18"/></svg>',
		'chev'    => '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 6 6 6-6 6"/></svg>',
		'down'    => '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 9 6 6 6-6"/></svg>',
		'prev'    => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 6-6 6 6 6"/></svg>',
		'next'    => '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 6 6 6-6 6"/></svg>',
		'whatsapp'=> '<svg viewBox="0 0 24 24" width="26" height="26" fill="currentColor"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.3-.4.7-1.4.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.2c0-.1-.2-.2-.5-.3Z"/></svg>',
		'phone'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A17 17 0 0 1 3 5a2 2 0 0 1 2-2Z"/></svg>',
		'mail'    => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
		'store'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 9 5 4h14l2 5M4 9v11h16V9M3 9h18M9 20v-6h6v6"/></svg>',
		'truck'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M1 6h13v10H1zM14 10h4l3 3v3h-7"/><circle cx="5" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>',
		'star'    => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1 6.2L12 17.3 6.5 20.2l1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>',
		'users'   => '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="9" cy="8" r="3.5"/><path d="M2 20c1-3.5 3.8-5.5 7-5.5s6 2 7 5.5"/><circle cx="17" cy="9" r="2.5"/><path d="M16 14.5c3 0 5 1.8 6 4.5"/></svg>',
		'clock'   => '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
		'return'  => '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M4 10h11a5 5 0 0 1 0 10H8"/><path d="m8 6-4 4 4 4"/></svg>',
		'drop'    => '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11Z"/></svg>',
		'pin'     => '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M12 21s7-6.2 7-12a7 7 0 0 0-14 0c0 5.8 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>',
		'shield'  => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z"/></svg>',
		'check'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 12 4 4 10-10"/></svg>',
		'facebook'=> '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H7v4h3v8h4v-8h3l1-4h-4V8.5a.5.5 0 0 1 .5-.5Z"/></svg>',
		'instagram'=> '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>',
		'tiktok'  => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M16 3c.3 2.3 1.7 3.8 4 4v3.2a7.6 7.6 0 0 1-4-1.2V15a6 6 0 1 1-6-6v3.3A2.7 2.7 0 1 0 12.7 15V3H16Z"/></svg>',
		'snapchat'=> '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2.5c2.9 0 5.2 2.2 5.2 5.3v2.4c.4.2.9.1 1.4-.1.6-.2 1.1.5.7 1-.4.4-1.2.8-1.9 1 .4 1.9 2.1 3.6 3.7 4 .5.2.5.8 0 1-.8.4-1.9.6-2.4.8-.2.4-.2 1-.6 1.1-.6.2-1.5-.2-2.6 0-1 .2-1.6 1.5-3.5 1.5s-2.5-1.3-3.5-1.5c-1.1-.2-2 .2-2.6 0-.4-.1-.4-.7-.6-1.1-.5-.2-1.6-.4-2.4-.8-.5-.2-.5-.8 0-1 1.6-.4 3.3-2.1 3.7-4-.7-.2-1.5-.6-1.9-1-.4-.5.1-1.2.7-1 .5.2 1 .3 1.4.1V7.8c0-3.1 2.3-5.3 5.2-5.3Z"/></svg>',
		'pinterest'=> '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.3-5.4s-.3-.7-.3-1.6c0-1.5.9-2.6 2-2.6.9 0 1.4.7 1.4 1.5 0 .9-.6 2.3-.9 3.6-.3 1.1.5 2 1.6 2 1.9 0 3.3-2 3.3-4.9 0-2.6-1.9-4.4-4.5-4.4-3.1 0-4.9 2.3-4.9 4.7 0 .9.4 1.9.8 2.5.1.1.1.2.1.3l-.3 1.2c0 .2-.2.3-.4.2-1.4-.6-2.2-2.6-2.2-4.2 0-3.4 2.5-6.6 7.2-6.6 3.8 0 6.7 2.7 6.7 6.3 0 3.7-2.4 6.8-5.6 6.8-1.1 0-2.2-.6-2.5-1.3l-.7 2.6c-.3 1-.9 2.2-1.4 2.9A10 10 0 1 0 12 2Z"/></svg>',
		'youtube' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M22 8.2a3 3 0 0 0-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 0 0 2 8.2 31 31 0 0 0 1.6 12 31 31 0 0 0 2 15.8a3 3 0 0 0 2.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 0 0 2.1-2.1 31 31 0 0 0 .4-3.8 31 31 0 0 0-.4-3.8ZM10 15V9l5.2 3L10 15Z"/></svg>',
		'x'       => '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17.8 3h3.3l-7.2 8.2L22.4 21h-6.6l-5.2-6.8L4.7 21H1.4l7.7-8.8L1 3h6.8l4.7 6.2L17.8 3Zm-1.2 16h1.8L7 4.9H5.1L16.6 19Z"/></svg>',
	);
	return isset( $i[ $name ] ) ? $i[ $name ] : '';
}

/* ------------------------------------------------------------------
 * Includes
 * ------------------------------------------------------------------ */
require MRZAIB_DIR . '/inc/wc-compat.php';
require MRZAIB_DIR . '/inc/search-suggest.php';
require MRZAIB_DIR . '/inc/home-defaults.php';
require MRZAIB_DIR . '/inc/customizer.php';
require MRZAIB_DIR . '/inc/menus.php';
require MRZAIB_DIR . '/inc/mobile-nav.php';
require MRZAIB_DIR . '/inc/woocommerce.php';
require MRZAIB_DIR . '/inc/product-ui.php';
require MRZAIB_DIR . '/inc/header-cart.php';
require MRZAIB_DIR . '/inc/reviews.php';
require MRZAIB_DIR . '/inc/store-content.php';
require MRZAIB_DIR . '/inc/demo-import.php';
require MRZAIB_DIR . '/inc/yoast-setup.php';

/* ------------------------------------------------------------------
 * Widgets (optional sidebar for shop/blog)
 * ------------------------------------------------------------------ */
function mrzaib_widgets() {
	register_sidebar( array(
		'name'          => __( 'Shop Sidebar / Filters', 'mrzaib' ),
		'id'            => 'shop-sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'mrzaib_widgets' );

/* Excerpt length for blog cards */
add_filter( 'excerpt_length', function () { return 22; } );


/**
 * Product search form. Uses "Advanced Woo Search" (live AJAX results with
 * product images & prices) when that plugin is active, otherwise a normal
 * WooCommerce product search.
 */
function mrzaib_search_form( $class = 'search-form-pill' ) {
	?>
	<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="mz-search <?php echo esc_attr( $class ); ?>" data-predictive>
		<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search', 'mrzaib' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" aria-label="<?php esc_attr_e( 'Search', 'mrzaib' ); ?>" autocomplete="off" role="combobox" aria-expanded="false" aria-haspopup="listbox">
		<?php if ( class_exists( 'WooCommerce' ) ) : ?><input type="hidden" name="post_type" value="product"><?php endif; ?>
		<button type="submit" class="mz-search-go" aria-label="<?php esc_attr_e( 'Search', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'search' ); // phpcs:ignore ?></button>
		<button type="button" class="mz-search-close" aria-label="<?php esc_attr_e( 'Close search', 'mrzaib' ); ?>"><?php echo mrzaib_icon( 'close' ); // phpcs:ignore ?></button>
		<div class="mz-predictive" role="listbox" hidden></div>
	</form>
	<?php
}
