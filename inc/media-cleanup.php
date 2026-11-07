<?php
/**
 * Media > Clean Unused Media: lists Media Library files that nothing on the site uses
 * (product images/galleries, category images, logo/site icon, Customizer, pages/posts,
 * product data, reviews, settings) and deletes the ones the admin ticks.
 * The WooCommerce placeholder image is never listed.
 *
 * @package MR_ZAIB
 */

defined( 'ABSPATH' ) || exit;

/** IDs of attachments in use. */
function mrzaib_used_media_ids() {
	global $wpdb;
	$used = array();
	$add  = function ( $v ) use ( &$used ) {
		foreach ( preg_split( '/[^0-9]+/', (string) $v ) as $n ) {
			if ( (int) $n > 0 ) {
				$used[ (int) $n ] = true;
			}
		}
	};
	// Featured images, product galleries, note images, variation images.
	foreach ( $wpdb->get_col( "SELECT pm.meta_value FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key IN ('_thumbnail_id','_product_image_gallery','_mrzaib_note_imgs') AND p.post_type <> 'attachment'" ) as $v ) { // phpcs:ignore
		$add( maybe_unserialize( $v ) ? implode( ',', (array) maybe_unserialize( $v ) ) : $v );
	}
	// Category / term images.
	foreach ( $wpdb->get_col( "SELECT meta_value FROM {$wpdb->termmeta} WHERE meta_key IN ('thumbnail_id','mrzaib_cat_banner')" ) as $v ) { // phpcs:ignore
		$add( $v );
	}
	// Review photos.
	foreach ( $wpdb->get_col( "SELECT meta_value FROM {$wpdb->commentmeta} WHERE meta_key = 'mz_images'" ) as $v ) { // phpcs:ignore
		$add( implode( ',', (array) maybe_unserialize( $v ) ) );
	}
	// Logo, site icon, WooCommerce placeholder, theme media map.
	$add( get_theme_mod( 'custom_logo' ) );
	$add( get_option( 'site_icon' ) );
	$add( get_option( 'woocommerce_placeholder_image' ) );
	$add( implode( ',', (array) get_option( 'mrzaib_home_media', array() ) ) );
	return $used;
}

/** Unused attachments: array of WP_Post. */
function mrzaib_unused_media() {
	global $wpdb;
	$used = mrzaib_used_media_ids();
	$all  = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => -1 ) );
	$out  = array();
	// Text that can mention a file by URL: posts, post meta, options (Customizer), comments.
	foreach ( $all as $a ) {
		if ( isset( $used[ $a->ID ] ) ) {
			continue;
		}
		$file = (string) get_post_meta( $a->ID, '_wp_attached_file', true );
		$name = pathinfo( $file, PATHINFO_FILENAME );
		if ( false !== stripos( $name, 'woocommerce-placeholder' ) ) {
			continue;
		}
		if ( '' === $name ) {
			continue;
		}
		$like = '%' . $wpdb->esc_like( $name ) . '%';
		$hit  = $wpdb->get_var( $wpdb->prepare( "SELECT 1 FROM {$wpdb->posts} WHERE post_type NOT IN ('attachment','revision') AND post_status <> 'trash' AND post_content LIKE %s LIMIT 1", $like ) ) // phpcs:ignore
			|| $wpdb->get_var( $wpdb->prepare( "SELECT 1 FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE p.post_type <> 'attachment' AND pm.meta_value LIKE %s LIMIT 1", $like ) ) // phpcs:ignore
			|| $wpdb->get_var( $wpdb->prepare( "SELECT 1 FROM {$wpdb->options} WHERE option_name NOT LIKE %s AND option_value LIKE %s LIMIT 1", $wpdb->esc_like( '_transient' ) . '%', $like ) ) // phpcs:ignore
			|| $wpdb->get_var( $wpdb->prepare( "SELECT 1 FROM {$wpdb->termmeta} WHERE meta_value LIKE %s LIMIT 1", $like ) ); // phpcs:ignore
		if ( ! $hit ) {
			$out[] = $a;
		}
	}
	return $out;
}

add_action( 'admin_menu', function () {
	add_media_page( 'Clean Unused Media', 'Clean Unused Media', 'delete_others_posts', 'mrzaib-media-clean', 'mrzaib_media_clean_page' );
} );

function mrzaib_media_clean_page() {
	if ( ! current_user_can( 'delete_others_posts' ) ) {
		return;
	}
	$deleted = 0;
	if ( isset( $_POST['mz_del'] ) && check_admin_referer( 'mrzaib_media_clean' ) ) {
		$unused = wp_list_pluck( mrzaib_unused_media(), 'ID' );
		foreach ( array_map( 'intval', (array) wp_unslash( $_POST['mz_del'] ) ) as $id ) {
			if ( in_array( $id, $unused, true ) && wp_delete_attachment( $id, true ) ) {
				$deleted++;
			}
		}
	}
	$list = mrzaib_unused_media();
	echo '<div class="wrap"><h1>Clean Unused Media</h1>';
	if ( $deleted ) {
		echo '<div class="notice notice-success"><p>' . (int) $deleted . ' unused file(s) deleted permanently.</p></div>';
	}
	echo '<p>These images and videos are not used anywhere on the site (products, categories, logo, Customizer, pages, reviews). The WooCommerce placeholder is always kept. Untick anything you want to keep, then click Delete.</p>';
	if ( ! $list ) {
		echo '<p><strong>No unused media found.</strong></p></div>';
		return;
	}
	echo '<form method="post" onsubmit="return confirm(\'Delete the ticked files permanently? This cannot be undone.\');">';
	wp_nonce_field( 'mrzaib_media_clean' );
	echo '<p><label><input type="checkbox" checked onclick="document.querySelectorAll(\'.mz-mc input\').forEach(function(c){c.checked=this.checked}.bind(this))"> Select all (' . count( $list ) . ')</label></p>';
	echo '<div class="mz-mc" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px">';
	foreach ( $list as $a ) {
		$is_img = wp_attachment_is_image( $a->ID );
		$thumb  = $is_img ? wp_get_attachment_image( $a->ID, 'thumbnail', false, array( 'style' => 'width:100%;height:120px;object-fit:cover;display:block' ) ) : '<div style="height:120px;display:flex;align-items:center;justify-content:center;background:#f0f0f1">' . esc_html( strtoupper( pathinfo( get_attached_file( $a->ID ), PATHINFO_EXTENSION ) ) ) . '</div>';
		echo '<label style="border:1px solid #dcdcde;background:#fff;padding:6px;display:block;cursor:pointer">' . $thumb . '<span style="display:flex;gap:6px;margin-top:6px;font-size:12px;word-break:break-all"><input type="checkbox" name="mz_del[]" value="' . (int) $a->ID . '" checked> ' . esc_html( basename( get_attached_file( $a->ID ) ) ) . '</span></label>'; // phpcs:ignore
	}
	echo '</div><p><button class="button button-primary button-large" style="background:#b32d2e;border-color:#b32d2e">Delete selected permanently</button></p></form></div>';
}
