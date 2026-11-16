<?php
/**
 * Default homepage texts/images shown in the Customizer (so admins can see and edit them).
 *
 * @package mrzaib
 */

function mrzaib_seo_default_intro() {
	return '<strong>MR ZAIB Luxury Perfumes</strong> is a Pakistani perfume brand creating <strong>long lasting perfumes</strong> for men, women and unisex lovers. Our promise is simple: <em>Leave Your Presence</em>. Choose from thirteen signature 50ml fragrances: <strong>perfumes for men</strong> like Kahn-vahl, Zay-ab, RAAZ, ZAUQ, Mehfil, Dazor, Brave and Baraan, <strong>perfumes for women</strong> like Hoor and Kireza, and <strong>unisex perfumes</strong> like Arvon, Velora and OUD X, with prices from Rs. 2,500 and Cash on Delivery all over Pakistan. Whether you want a fresh everyday scent, an <strong>oud perfume</strong>, a bakhoor-style Arabic fragrance, a wedding perfume or a thoughtful gift, MR ZAIB has the <strong>best perfume in Pakistan</strong> for you.';
}

function mrzaib_seo_default_more() {
	$p = function ( $slug, $text ) {
		return '<a href="' . esc_url( mrzaib_product_link( $slug ) ) . '">' . $text . '</a>';
	};
	return '<p>At <strong>MR ZAIB</strong>, we put most of our cost into the perfume oil itself, so you get a high concentration, <strong>super macerated perfume</strong> at affordable prices from Rs. 2,500. We share the full top, middle and base notes of every fragrance, so you can choose a scent that matches your personality before you buy.</p>' .
		'<h3>Best Long Lasting Perfumes for Men in Pakistan</h3>' .
		'<p>' . $p( 'kahn-vahl-perfume', 'Kahn-vahl perfume for men' ) . ' is a fresh aquatic fragrance in a deep blue bottle, with coconut water, jasmine, iris, vanilla, sandalwood and amber for the office, outings and summer evenings.</p>' .
		'<p>' . $p( 'zay-ab-perfume', 'Zay-ab perfume for men' ) . ' (Zayab) is a royal oriental scent of rose, frankincense, labdanum, white musk, benzoin and ambergris, made for weddings, Eid, Jummah and winter nights.</p>' .
		'<p>' . $p( 'raaz-perfume', 'RAAZ perfume for men' ) . ' is a warm, spicy tobacco fragrance: red apple and mandarin, then pepper and sage, on a rich base of tobacco, benzoin, woods and patchouli. A confident evening and winter perfume.</p>' .
		'<p>' . $p( 'zauq-perfume', 'ZAUQ perfume for men' ) . ' is an aromatic woody scent of amber, citrus, herbs, musk and warm spice, a refined everyday perfume for the office and evenings.</p>' .
		'<p>' . $p( 'mehfil-perfume', 'Mehfil perfume for men' ) . ', inspired by Creed Aventus, opens with bergamot, black currant, apple and pink pepper, with a pineapple and jasmine heart and a smoky base of birch, oak moss, musk and ambroxan. The scent that owns every gathering.</p>' .
		'<p>' . $p( 'dazor-perfume', 'Dazor perfume for men' ) . ', inspired by Dunhill Desire, is fresh and fruity with apple, lemon, bergamot and orange blossom, rose, teak wood and patchouli, and a smooth vanilla musk finish. A great daily perfume for office and university.</p>' .
		'<p>' . $p( 'brave-perfume', 'Brave perfume for men' ) . ' is a juicy, fearless fragrance of lemon, mandarin, guava and peach, licorice and white flowers, and a creamy sandalwood, musk and cedar base. Perfect for summer and parties.</p>' .
		'<p>' . $p( 'baraan-perfume', 'Baraan perfume for men' ) . ', inspired by Bakhour, brings the warmth of traditional bakhoor in amber, woods and vanilla with a soft floral fruity glow. A rich Arabic-style perfume for Eid, Jummah and weddings.</p>' .
		'<h3>Best Long Lasting Perfumes for Women in Pakistan</h3>' .
		'<p>' . $p( 'hoor-perfume', 'Hoor perfume for women' ) . ', inspired by Myton Floral, is a sweet fruity floral scent in a pink bottle: black currant, pineapple and red apple bloom into rose, freesia and peony over musk, iris and sandalwood.</p>' .
		'<p>' . $p( 'kireza-perfume', 'Kireza perfume for women' ) . ', inspired by Megamari, is a bold, glamorous fragrance of raspberry, rose and black currant with a fresh marine heart and a musk, ambroxan and cedar base, made for parties, weddings and evenings out.</p>' .
		'<h3>Best Unisex Perfumes for Men and Women</h3>' .
		'<p>' . $p( 'arvon-perfume', 'Arvon unisex perfume' ) . ' blends pineapple, red apple and spice with Moroccan rose, jasmine and iris, finishing on cacao, vanilla, white musk and amber.</p>' .
		'<p>' . $p( 'velora-perfume', 'Velora unisex perfume' ) . ', inspired by Bidford, is a dark rose and leather scent: tomato leaf, spices and tuberose, dried rose and iris, on amber, metallic notes and leather.</p>' .
		'<p>' . $p( 'oud-x-perfume', 'OUD X unisex oud perfume' ) . ', inspired by Ouray, blends bergamot and citrus with cinnamon and apple, drying down to ambergris, musk and rich woods, a modern oud-style fragrance for men and women.</p>' .
		'<h3>Shop Perfumes by Category</h3>' .
		'<p>Explore <a href="' . esc_url( mrzaib_cat_link( 'men' ) ) . '">perfumes for men</a>, <a href="' . esc_url( mrzaib_cat_link( 'women' ) ) . '">perfumes for women</a> and <a href="' . esc_url( mrzaib_cat_link( 'unisex' ) ) . '">unisex perfumes</a>, traditional <a href="' . esc_url( mrzaib_cat_link( 'attar' ) ) . '">Attar</a>, and perfume <a href="' . esc_url( mrzaib_cat_link( 'tester' ) ) . '">Testers</a> to try a scent before buying the full bottle.</p>' .
		'<h3>Easy Returns and Cash on Delivery</h3>' .
		'<p>Not sure about a scent? Our 15 days easy returns policy has you covered, and you can pay cash when your order arrives anywhere in Pakistan, from Karachi and Lahore to Islamabad, Rawalpindi, Faisalabad, Multan and Peshawar.</p>' .
		'<p><strong>Buy high quality, long lasting perfumes online in Pakistan at affordable prices with MR ZAIB.</strong></p>';
}

/* SEO text v4 (13 perfumes): a saved Customizer value that is still an older theme text is
   cleared once, so the new text shows. Text the admin wrote is left alone. */
add_action( 'init', function () {
	if ( (int) get_option( 'mrzaib_seo_text_v' ) >= 4 ) {
		return;
	}
	$marks = array(
		'mrzaib_seo_intro' => array( 'MR ZAIB has a scent for you.', 'has the <strong>best perfume in Pakistan</strong> for you.' ),
		'mrzaib_seo_more'  => array( 'Arvon: Unisex Perfume for Men and Women', 'Shop Perfumes by Category' ),
	);
	foreach ( $marks as $mod => $olds ) {
		$v = (string) get_theme_mod( $mod, '' );
		if ( '' === $v || false !== strpos( $v, 'Baraan' ) ) {
			continue;
		}
		foreach ( $olds as $old ) {
			if ( false !== strpos( $v, $old ) ) {
				remove_theme_mod( $mod );
				break;
			}
		}
	}
	update_option( 'mrzaib_seo_text_v', 4 );
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
define( 'MRZAIB_HOME_MEDIA_V', 6 );

function mrzaib_home_media_items() {
	$items = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$items[ 'video_' . $i ]        = array( 'assets/video/' . basename( mrzaib_video_default( $i ) ), mrzaib_video_default( $i ) );
		$items[ 'video_poster_' . $i ] = array( 'assets/video/' . basename( mrzaib_video_default( $i, 'jpg' ) ), mrzaib_video_default( $i, 'jpg' ) );
		$items[ 'feat_icon_' . $i ]    = array( 'assets/images/icons/' . basename( mrzaib_feat_default_icon( $i ) ), mrzaib_feat_default_icon( $i ) );
	}
	foreach ( mrzaib_hero_defaults() as $i => $d ) {
		$tag                       = $i >= 8 ? 'v2' : ''; // v2 = podium scenes for RAAZ to Baraan.
		$items[ 'hero_img_' . $i ] = array( 'assets/images/hero-' . $i . '-desktop.jpg', $d[0], $tag );
		$items[ 'hero_mob_' . $i ] = array( 'assets/images/hero-' . $i . '-mobile.jpg', $d[1], $tag );
	}
	/* Third value = version tag: a new tag imports the updated file again (v6 = 13 bottles on marble riser). */
	$items['promo_img_d'] = array( 'assets/images/promo-band-desktop.jpg', MRZAIB_URI . '/assets/images/promo-band-desktop.jpg', 'v6' );
	$items['promo_img_m'] = array( 'assets/images/promo-band-mobile.jpg', MRZAIB_URI . '/assets/images/promo-band-mobile.jpg', 'v6' );
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
