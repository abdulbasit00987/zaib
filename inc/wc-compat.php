<?php
/**
 * Safe fallbacks when WooCommerce is not active, so a fresh install never crashes.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'mrzaib_wc_compat', 1 );
function mrzaib_wc_compat() {
	if ( class_exists( 'WooCommerce' ) ) {
		return;
	}
	foreach ( array( 'is_woocommerce', 'is_shop', 'is_product', 'is_product_category', 'is_product_tag', 'is_product_taxonomy', 'is_cart', 'is_checkout', 'is_account_page', 'is_wc_endpoint_url' ) as $fn ) {
		if ( ! function_exists( $fn ) ) {
			eval( 'function ' . $fn . '() { return false; }' ); // phpcs:ignore
		}
	}
}

add_action( 'admin_notices', function () {
	if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>MR ZAIB:</strong> ' . esc_html__( 'Please install and activate WooCommerce, then open Appearance > MR ZAIB Setup and click "Set Up MR ZAIB Store".', 'mrzaib' ) . ' <a href="' . esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ) . '">' . esc_html__( 'Install WooCommerce', 'mrzaib' ) . '</a></p></div>';
} );
