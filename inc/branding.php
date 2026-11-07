<?php
/**
 * MR ZAIB branding: favicon (WordPress Site Icon) and the link-share (Open Graph) image.
 *
 * - Once in wp-admin, the MZ crown favicon and the 1200x630 share image are copied into
 *   the Media Library. The favicon becomes the Site Icon when none is set yet
 *   (change it any time in Appearance > Customize > Site Identity).
 * - Until a Site Icon exists, the theme prints its own favicon links.
 * - Open Graph / Twitter title, description and image are written to Yoast SEO
 *   (see inc/yoast-setup.php), so WhatsApp, Facebook, X and others show a full preview.
 *
 * @package mrzaib
 */

defined( 'ABSPATH' ) || exit;

define( 'MRZAIB_BRAND_V', 1 );

/** Import a theme image into Media once (tagged with _mrzaib_src), return its ID. */
function mrzaib_brand_media( $rel, $title, $alt ) {
	$q = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'fields' => 'ids', 'meta_key' => '_mrzaib_src', 'meta_value' => $rel ) );
	if ( $q ) {
		return (int) $q[0];
	}
	$file = MRZAIB_DIR . '/' . $rel;
	if ( ! file_exists( $file ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( basename( $file ) );
	copy( $file, $tmp );
	$id = media_handle_sideload( array( 'name' => 'mr-zaib-' . basename( $file ), 'tmp_name' => $tmp ), 0, $title );
	if ( is_wp_error( $id ) ) {
		@unlink( $tmp ); // phpcs:ignore
		return 0;
	}
	update_post_meta( $id, '_mrzaib_src', $rel );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return (int) $id;
}

/** Media ID of the share (OG) image. */
function mrzaib_og_image_id() {
	return mrzaib_brand_media( 'assets/images/og-image.jpg', 'MR ZAIB Luxury Perfumes', 'MR ZAIB long lasting luxury perfumes Arvon, Zay-ab, Kahn-vahl and Hoor' );
}

add_action( 'admin_init', function () {
	if ( (int) get_option( 'mrzaib_brand_v' ) >= MRZAIB_BRAND_V || ! current_user_can( 'manage_options' ) || wp_doing_ajax() ) {
		return;
	}
	if ( ! get_option( 'site_icon' ) ) {
		$icon = mrzaib_brand_media( 'assets/images/favicon-512.png', 'MR ZAIB Site Icon', 'MR ZAIB crown logo' );
		if ( $icon ) {
			update_option( 'site_icon', $icon );
		}
	}
	mrzaib_og_image_id();
	update_option( 'mrzaib_brand_v', MRZAIB_BRAND_V );
}, 25 );

/* Theme favicon until a Site Icon is set. */
add_action( 'wp_head', function () {
	if ( has_site_icon() ) {
		return;
	}
	$u = MRZAIB_URI . '/assets/images/';
	echo '<link rel="icon" href="' . esc_url( $u . 'favicon.ico' ) . '" sizes="any">' . "\n";
	echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $u . 'favicon-32.png' ) . '">' . "\n";
	echo '<link rel="icon" type="image/png" sizes="512x512" href="' . esc_url( $u . 'favicon-512.png' ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $u . 'apple-touch-icon.png' ) . '">' . "\n";
}, 2 );

add_action( 'wp_head', function () {
	echo '<meta name="theme-color" content="#000000">' . "\n";
}, 3 );
