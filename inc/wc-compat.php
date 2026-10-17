<?php
/**
 * Safe fallbacks when WooCommerce is not active, so a fresh install never crashes.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce conditional tag that is safe when WooCommerce is inactive.
 * Never declare WooCommerce's own function names here: activating WooCommerce would then crash.
 */
function mrzaib_is( $tag ) {
	return function_exists( $tag ) ? (bool) call_user_func( $tag ) : false;
}

add_action( 'admin_notices', function () {
	if ( class_exists( 'WooCommerce' ) || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>MR ZAIB:</strong> ' . esc_html__( 'Please install and activate WooCommerce, then open Appearance > MR ZAIB Setup and click "Set Up MR ZAIB Store".', 'mrzaib' ) . ' <a href="' . esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ) . '">' . esc_html__( 'Install WooCommerce', 'mrzaib' ) . '</a></p></div>';
} );
