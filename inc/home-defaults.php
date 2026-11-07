<?php
/**
 * Default homepage texts/images shown in the Customizer (so admins can see and edit them).
 *
 * @package mrzaib
 */

function mrzaib_seo_default_intro() {
	return '<strong>MR ZAIB Luxury Perfumes</strong> is a Pakistani perfume brand creating <strong>long lasting perfumes</strong> for men, women and unisex lovers. Our promise is simple: <em>Leave Your Presence</em>. Choose from four signature 50ml fragrances, the fresh aquatic Kahn-vahl and the royal oriental Zay-ab for men, the fruity floral Hoor for women and the bold unisex Arvon, each at an affordable Rs. 2,500 with Cash on Delivery all over Pakistan. Whether you want an everyday signature scent, a wedding perfume or a thoughtful gift, MR ZAIB has the <strong>best perfume in Pakistan</strong> for you.';
}

function mrzaib_seo_default_more() {
	return '<p>At <strong>MR ZAIB</strong>, we put most of our cost into the perfume oil itself, so you get a high concentration, <strong>super macerated perfume</strong> at an affordable price of Rs. 2,500. We share the full top, middle and base notes of every fragrance, so you can choose a scent that matches your personality before you buy.</p>' .
		'<h3>Kahn-vahl: Long Lasting Perfume for Men</h3>' .
		'<p><a href="' . esc_url( mrzaib_product_link( 'kahn-vahl-perfume' ) ) . '">Kahn-vahl perfume for men</a> is a fresh aquatic fragrance in a deep blue bottle. Top notes of coconut water, a heart of jasmine and iris, and a warm base of vanilla, sandalwood and amber make it a versatile scent for the office, outings and summer evenings.</p>' .
		'<h3>Zay-ab: Oriental Perfume for Men</h3>' .
		'<p><a href="' . esc_url( mrzaib_product_link( 'zay-ab-perfume' ) ) . '">Zay-ab perfume for men</a> (Zayab) is a royal oriental fragrance in a crystal-cut red bottle. Aldehydes, lily of the valley and rose open into frankincense, mystikal and labdanum, settling on white musk, benzoin and ambergris. A smoky, warm scent for weddings, Eid, Jummah and winter nights.</p>' .
		'<h3>Hoor: Floral Perfume for Women</h3>' .
		'<p><a href="' . esc_url( mrzaib_product_link( 'hoor-perfume' ) ) . '">Hoor perfume for women</a>, inspired by Myton Floral, is a sweet fruity floral scent in a pink bottle. Black currant, pineapple, orange and red apple bloom into rose, freesia and peony, with a soft base of musk, iris and sandalwood. A graceful ladies perfume for every day, gifts and celebrations.</p>' .
		'<h3>Arvon: Unisex Perfume for Men and Women</h3>' .
		'<p><a href="' . esc_url( mrzaib_product_link( 'arvon-perfume' ) ) . '">Arvon unisex perfume</a> is a bold fragrance in a black bottle. Pineapple, red apple, plum, nutmeg, cinnamon and bitter orange open into magnolia, orchid, lily-of-the-valley, jasmine, Moroccan rose and iris, finishing on cacao, vanilla, white musk, amber, sandalwood and cedar.</p>' .
		'<h3>Shop Perfumes by Category</h3>' .
		'<p>Explore <a href="' . esc_url( mrzaib_cat_link( 'men' ) ) . '">perfumes for men</a>, <a href="' . esc_url( mrzaib_cat_link( 'women' ) ) . '">perfumes for women</a> and <a href="' . esc_url( mrzaib_cat_link( 'unisex' ) ) . '">unisex perfumes</a>, traditional <a href="' . esc_url( mrzaib_cat_link( 'attar' ) ) . '">Attar</a>, and perfume <a href="' . esc_url( mrzaib_cat_link( 'tester' ) ) . '">Testers</a> to try a scent before buying the full bottle.</p>' .
		'<h3>Easy Returns and Cash on Delivery</h3>' .
		'<p>Not sure about a scent? Our 15 days easy returns policy has you covered, and you can pay cash when your order arrives anywhere in Pakistan, from Karachi and Lahore to Islamabad.</p>' .
		'<p><strong>Buy high quality, long lasting perfumes online in Pakistan at affordable prices.</strong></p>';
}

/* SEO text v2 (4 perfumes): a saved Customizer value that is still the old theme text is
   cleared once, so the new text shows. Text the admin wrote is left alone. */
add_action( 'init', function () {
	if ( (int) get_option( 'mrzaib_seo_text_v' ) >= 2 ) {
		return;
	}
	foreach ( array( 'mrzaib_seo_intro' => 'MR ZAIB has a scent for you.', 'mrzaib_seo_more' => 'Arvon: Unisex Perfume for Men and Women' ) as $mod => $old ) {
		$v = (string) get_theme_mod( $mod, '' );
		if ( '' !== $v && false !== strpos( $v, $old ) && false === strpos( $v, 'Zay-ab' ) ) {
			remove_theme_mod( $mod );
		}
	}
	update_option( 'mrzaib_seo_text_v', 2 );
}, 20 );

/** Theme icon file for feature N (1-4). */
function mrzaib_feat_default_icon( $n ) {
	$k = array( 1 => 'fcart', 2 => 'award', 3 => 'box', 4 => 'thumb' );
	return isset( $k[ $n ] ) ? MRZAIB_URI . '/assets/images/icons/feat-' . $k[ $n ] . '.png' : '';
}

/** Theme demo video / cover for slot N (1-4). */
function mrzaib_video_default( $n, $ext = 'mp4' ) {
	$v = array( 1 => 'kahn-vahl', 2 => 'arvon', 3 => 'mr-zaib-collection', 4 => 'mr-zaib-gift' );
	return isset( $v[ $n ] ) ? MRZAIB_URI . '/assets/video/' . $v[ $n ] . '.' . $ext : '';
}

/** Default product (ID) for video slot N: Kahn-vahl / Arvon when they exist. */
function mrzaib_video_default_product( $n ) {
	$slug = 1 === $n % 2 ? 'kahn-vahl' : 'arvon';
	foreach ( array( $slug, $slug . '-perfume' ) as $s ) {
		$p = get_page_by_path( $s, OBJECT, 'product' );
		if ( $p ) {
			return (int) $p->ID;
		}
	}
	return 0;
}

/** Product chosen for video slot N (accepts old slug values too). */
function mrzaib_video_product( $n ) {
	$v = get_theme_mod( 'mrzaib_video_product_' . $n, mrzaib_video_default_product( $n ) );
	if ( $v && ! is_numeric( $v ) ) {
		$p = get_page_by_path( sanitize_title( $v ), OBJECT, 'product' );
		$v = $p ? $p->ID : 0;
	}
	$prod = ( $v && function_exists( 'wc_get_product' ) ) ? wc_get_product( (int) $v ) : null;
	return ( $prod && 'publish' === $prod->get_status() ) ? $prod : null;
}

/**
 * Copy the theme's homepage videos, covers, feature icons and promo banners into the
 * WordPress Media Library once, and point the Customizer fields at them, so they show
 * as real media (with previews) and can be picked again from Media.
 * Only fields still using the theme default (or empty) are changed.
 */
define( 'MRZAIB_HOME_MEDIA_V', 3 );

function mrzaib_home_media_items() {
	$items = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$items[ 'video_' . $i ]        = array( 'assets/video/' . basename( mrzaib_video_default( $i ) ), mrzaib_video_default( $i ) );
		$items[ 'video_poster_' . $i ] = array( 'assets/video/' . basename( mrzaib_video_default( $i, 'jpg' ) ), mrzaib_video_default( $i, 'jpg' ) );
		$items[ 'feat_icon_' . $i ]    = array( 'assets/images/icons/' . basename( mrzaib_feat_default_icon( $i ) ), mrzaib_feat_default_icon( $i ) );
	}
	foreach ( mrzaib_hero_defaults() as $i => $d ) {
		$items[ 'hero_img_' . $i ] = array( 'assets/images/hero-' . $i . '-desktop.jpg', $d[0] );
		$items[ 'hero_mob_' . $i ] = array( 'assets/images/hero-' . $i . '-mobile.jpg', $d[1] );
	}
	/* Third value = version tag: a new tag imports the updated file again (v3 = 4 bottles). */
	$items['promo_img_d'] = array( 'assets/images/promo-band-desktop.jpg', MRZAIB_URI . '/assets/images/promo-band-desktop.jpg', 'v3' );
	$items['promo_img_m'] = array( 'assets/images/promo-band-mobile.jpg', MRZAIB_URI . '/assets/images/promo-band-mobile.jpg', 'v3' );
	return $items;
}

/** Attachment ID already imported for a theme file, else 0. */
function mrzaib_home_media_find( $rel ) {
	$q = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'fields' => 'ids', 'meta_key' => '_mrzaib_src', 'meta_value' => $rel ) );
	return $q ? (int) $q[0] : 0;
}

function mrzaib_import_home_media( $force = false ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$map = (array) get_option( 'mrzaib_home_media', array() );
	$n   = 0;
	foreach ( mrzaib_home_media_items() as $key => $it ) {
		$rel       = $it[0];
		$theme_url = $it[1];
		$tag       = empty( $it[2] ) ? $rel : $rel . '#' . $it[2];
		$cur       = get_theme_mod( 'mrzaib_' . $key, '' );
		$cur_base  = $cur ? strtok( $cur, '?' ) : '';
		$old_id    = empty( $map[ $key ] ) ? 0 : (int) $map[ $key ];
		$ours      = $old_id && $cur_base && (string) wp_parse_url( $cur_base, PHP_URL_PATH ) === (string) wp_parse_url( (string) wp_get_attachment_url( $old_id ), PHP_URL_PATH );
		if ( $cur && ! $force && ! $ours && strtok( $theme_url, '?' ) !== $cur_base && false === strpos( $cur_base, '/themes/' ) ) {
			continue; // Admin already chose own media.
		}
		$file = MRZAIB_DIR . '/' . $rel;
		if ( ! file_exists( $file ) ) {
			continue;
		}
		$id = mrzaib_home_media_find( $tag );
		if ( ! $id ) {
			$tmp = wp_tempnam( basename( $file ) );
			copy( $file, $tmp );
			$fa = array( 'name' => 'mr-zaib-' . basename( $file ), 'tmp_name' => $tmp );
			$id = media_handle_sideload( $fa, 0, 'MR ZAIB ' . ucwords( str_replace( array( '-', '_' ), ' ', pathinfo( $file, PATHINFO_FILENAME ) ) ) );
			if ( is_wp_error( $id ) ) {
				@unlink( $tmp ); // phpcs:ignore
				continue;
			}
			update_post_meta( $id, '_mrzaib_src', $tag );
			if ( preg_match( '/^hero_(img|mob)_(\d+)$/', $key, $m ) ) {
				$hd = mrzaib_hero_defaults();
				if ( ! empty( $hd[ (int) $m[2] ][3] ) ) {
					update_post_meta( $id, '_wp_attachment_image_alt', $hd[ (int) $m[2] ][3] );
				}
			}
			$n++;
		}
		if ( $old_id && $old_id !== (int) $id && get_post_meta( $old_id, '_mrzaib_src', true ) ) {
			wp_delete_attachment( $old_id, true ); // Older theme copy replaced by the new one.
		}
		$map[ $key ] = (int) $id;
		set_theme_mod( 'mrzaib_' . $key, wp_get_attachment_url( $id ) );
	}
	update_option( 'mrzaib_home_media', $map, false );
	update_option( 'mrzaib_home_media_v', MRZAIB_HOME_MEDIA_V );
	return $n;
}

add_action( 'admin_init', function () {
	if ( (int) get_option( 'mrzaib_home_media_v' ) >= MRZAIB_HOME_MEDIA_V || ! current_user_can( 'upload_files' ) || wp_doing_ajax() ) {
		return;
	}
	mrzaib_import_home_media();
} );

/** URL of the theme media imported for a field (e.g. 'feat_icon_1'), or ''. */
function mrzaib_home_media_url( $key ) {
	$map = (array) get_option( 'mrzaib_home_media', array() );
	return empty( $map[ $key ] ) ? '' : (string) wp_get_attachment_url( $map[ $key ] );
}
