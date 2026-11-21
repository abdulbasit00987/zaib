<?php
/**
 * Default homepage texts/images shown in the Customizer (so admins can see and edit them).
 *
 * @package mrzaib
 */

function mrzaib_seo_default_intro() {
	return '<strong>MR ZAIB Luxury Perfumes</strong> is a Pakistani perfume brand creating <strong>long lasting perfumes</strong> for men, women and unisex lovers. Our promise is simple: <em>Leave Your Presence</em>. Choose from seventeen signature 50ml fragrances: <strong>perfumes for men</strong> like Kahn-vahl, Zay-ab, RAAZ, ZAUQ, Mehfil, Dazor, Brave, Baraan, Tusqan and Aura, <strong>perfumes for women</strong> like Hoor and Kireza, and <strong>unisex perfumes</strong> like Arvon, Velora, OUD X, Obsession and Elara, with prices from Rs. 2,000 and Cash on Delivery all over Pakistan. Whether you want a fresh everyday scent, an <strong>office perfume</strong>, a <strong>leather perfume</strong>, a sweet <strong>vanilla perfume</strong>, an <strong>oud perfume</strong>, a bakhoor-style Arabic fragrance, a wedding perfume or a thoughtful gift, MR ZAIB has the <strong>best perfume in Pakistan</strong> for you.';
}

function mrzaib_seo_default_more() {
	$p = function ( $slug, $text ) {
		return '<a href="' . esc_url( mrzaib_product_link( $slug ) ) . '">' . $text . '</a>';
	};
	return '<p>At <strong>MR ZAIB</strong>, we put most of our cost into the perfume oil itself, so you get a high concentration, <strong>super macerated perfume</strong> at affordable prices from Rs. 2,000. We share the full top, middle and base notes of every fragrance, so you can choose a scent that matches your personality before you buy.</p>' .
		'<h3>Best Long Lasting Perfumes for Men in Pakistan</h3>' .
		'<p>' . $p( 'kahn-vahl-perfume', 'Kahn-vahl perfume for men' ) . ' is a fresh aquatic fragrance in a deep blue bottle, with coconut water, jasmine, iris, vanilla, sandalwood and amber for the office, outings and summer evenings.</p>' .
		'<p>' . $p( 'zay-ab-perfume', 'Zay-ab perfume for men' ) . ' (Zayab) is a royal oriental scent of rose, frankincense, labdanum, white musk, benzoin and ambergris, made for weddings, Eid, Jummah and winter nights.</p>' .
		'<p>' . $p( 'raaz-perfume', 'RAAZ perfume for men' ) . ' is a warm, spicy tobacco fragrance: red apple and mandarin, then pepper and sage, on a rich base of tobacco, benzoin, woods and patchouli. A confident evening and winter perfume.</p>' .
		'<p>' . $p( 'zauq-perfume', 'ZAUQ perfume for men' ) . ' is an aromatic woody scent of amber, citrus, herbs, musk and warm spice, a refined everyday perfume for the office and evenings.</p>' .
		'<p>' . $p( 'mehfil-perfume', 'Mehfil perfume for men' ) . ', inspired by Creed Aventus, opens with bergamot, black currant, apple and pink pepper, with a pineapple and jasmine heart and a smoky base of birch, oak moss, musk and ambroxan. The scent that owns every gathering.</p>' .
		'<p>' . $p( 'dazor-perfume', 'Dazor perfume for men' ) . ', inspired by Dunhill Desire, is fresh and fruity with apple, lemon, bergamot and orange blossom, rose, teak wood and patchouli, and a smooth vanilla musk finish. A great daily perfume for office and university.</p>' .
		'<p>' . $p( 'brave-perfume', 'Brave perfume for men' ) . ' is a juicy, fearless fragrance of lemon, mandarin, guava and peach, licorice and white flowers, and a creamy sandalwood, musk and cedar base. Perfect for summer and parties.</p>' .
		'<p>' . $p( 'baraan-perfume', 'Baraan perfume for men' ) . ', inspired by Bakhour, brings the warmth of traditional bakhoor in amber, woods and vanilla with a soft floral fruity glow. A rich Arabic-style perfume for Eid, Jummah and weddings.</p>' .
		'<p>' . $p( 'tusqan-perfume', 'Tusqan perfume for men' ) . ', inspired by Tuscan Leather, is a bold <strong>leather perfume</strong> of raspberry, saffron and thyme, olibanum and jasmine, on suede, leather, woods and amber. A luxurious evening and winter scent.</p>' .
		'<p>' . $p( 'aura-perfume', 'Aura perfume for men' ) . ', inspired by Office for Men, is a clean <strong>office perfume for men</strong> of ambroxan, bergamot and orris root, with woods, ambergris, amber and jasmine over musk, patchouli and cashalox. Fresh and professional all day.</p>' .
		'<h3>Best Long Lasting Perfumes for Women in Pakistan</h3>' .
		'<p>' . $p( 'hoor-perfume', 'Hoor perfume for women' ) . ', inspired by Myton Floral, is a sweet fruity floral scent in a pink bottle: black currant, pineapple and red apple bloom into rose, freesia and peony over musk, iris and sandalwood.</p>' .
		'<p>' . $p( 'kireza-perfume', 'Kireza perfume for women' ) . ', inspired by Megamari, is a bold, glamorous fragrance of raspberry, rose and black currant with a fresh marine heart and a musk, ambroxan and cedar base, made for parties, weddings and evenings out.</p>' .
		'<h3>Best Unisex Perfumes for Men and Women</h3>' .
		'<p>' . $p( 'arvon-perfume', 'Arvon unisex perfume' ) . ' blends pineapple, red apple and spice with Moroccan rose, jasmine and iris, finishing on cacao, vanilla, white musk and amber.</p>' .
		'<p>' . $p( 'velora-perfume', 'Velora unisex perfume' ) . ', inspired by Bidford, is a dark rose and leather scent: tomato leaf, spices and tuberose, dried rose and iris, on amber, metallic notes and leather.</p>' .
		'<p>' . $p( 'oud-x-perfume', 'OUD X unisex oud perfume' ) . ', inspired by Ouray, blends bergamot and citrus with cinnamon and apple, drying down to ambergris, musk and rich woods, a modern oud-style fragrance for men and women.</p>' .
		'<p>' . $p( 'obsession-perfume', 'Obsession unisex perfume' ) . ', inspired by Forbidden Craving, is a sweet <strong>vanilla amber perfume</strong>: blood orange, bergamot and cardamom, red apple, peach and rose, on vanilla absolute, amber, benzoin, sandalwood and musk.</p>' .
		'<p>' . $p( 'elara-perfume', 'Elara unisex perfume' ) . ', inspired by Alpena, is a <strong>fresh citrus woody perfume</strong> of lemon, bergamot and grapefruit, thyme, jasmine and orchid, drying down to oakmoss, myrrh, cedar, sandalwood and musk. A clean everyday scent for men and women.</p>' .
		'<h3>Shop Perfumes by Category</h3>' .
		'<p>Explore <a href="' . esc_url( mrzaib_cat_link( 'men' ) ) . '">perfumes for men</a>, <a href="' . esc_url( mrzaib_cat_link( 'women' ) ) . '">perfumes for women</a> and <a href="' . esc_url( mrzaib_cat_link( 'unisex' ) ) . '">unisex perfumes</a>, traditional <a href="' . esc_url( mrzaib_cat_link( 'attar' ) ) . '">Attar</a>, and perfume <a href="' . esc_url( mrzaib_cat_link( 'tester' ) ) . '">Testers</a> to try a scent before buying the full bottle.</p>' .
		'<h3>Easy Returns and Cash on Delivery</h3>' .
		'<p>Not sure about a scent? Our 15 days easy returns policy has you covered, and you can pay cash when your order arrives anywhere in Pakistan, from Karachi and Lahore to Islamabad, Rawalpindi, Faisalabad, Multan and Peshawar.</p>' .
		'<p><strong>Buy high quality, long lasting perfumes online in Pakistan at affordable prices with MR ZAIB.</strong></p>';
}

/** SEO text compared as a whole: link targets, entities, whitespace and the old Rs. 2,500 price ignored. */
function mrzaib_seo_text_norm( $v ) {
	$v = html_entity_decode( (string) $v, ENT_QUOTES, 'UTF-8' );
	$v = preg_replace( '/\shref=("[^"]*"|\'[^\']*\')/i', ' href=""', $v );
	$v = str_replace( 'Rs. 2,500', 'Rs. 2,000', $v );
	return trim( preg_replace( '/\s+/u', ' ', $v ) );
}

/* SEO text v5 (17 perfumes): a saved Customizer value that is exactly an older theme text
   (all earlier theme versions, compared in full) is cleared once, so the new text shows.
   Any text the admin edited, even partly, is left alone. */
add_action( 'init', function () {
	if ( (int) get_option( 'mrzaib_seo_text_v' ) >= 5 ) {
		return;
	}
	$legacy = array(
		'mrzaib_seo_intro' => array( '4823c3321a89b53dc268748635925abf', '69f1dbd24ce6d4f13b7b5be5ff8cd5a4', '9aab0950071b68d3a27c9eb131b9e395', 'f9aef12726eece03dc190530523eaabb' ),
		'mrzaib_seo_more'  => array( '517e98424fcf6224bcfcb448b2054e9b', 'e89f51e0e24785b1772a5998effd3bd1', '70cb6e02b9e548f13f73d01210fc3eee', '53fe61d22943e99d51067bc31ec72874' ),
	);
	foreach ( $legacy as $mod => $hashes ) {
		$v = (string) get_theme_mod( $mod, '' );
		if ( '' !== $v && in_array( md5( mrzaib_seo_text_norm( $v ) ), $hashes, true ) ) {
			remove_theme_mod( $mod );
		}
	}
	update_option( 'mrzaib_seo_text_v', 5 );
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
define( 'MRZAIB_HOME_MEDIA_V', 11 );

function mrzaib_home_media_items() {
	$items = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$items[ 'video_' . $i ]        = array( 'assets/video/' . basename( mrzaib_video_default( $i ) ), mrzaib_video_default( $i ), 'p2' ); // p2 = new price captions.
		$items[ 'video_poster_' . $i ] = array( 'assets/video/' . basename( mrzaib_video_default( $i, 'jpg' ) ), mrzaib_video_default( $i, 'jpg' ) );
		$items[ 'feat_icon_' . $i ]    = array( 'assets/images/icons/' . basename( mrzaib_feat_default_icon( $i ) ), mrzaib_feat_default_icon( $i ) );
	}
	foreach ( mrzaib_hero_defaults() as $i => $d ) {
		$tag                       = 'p3'; // p2 = new prices (2026-11-18): Kahn-vahl Rs. 3,000, others Rs. 2,000.
		$items[ 'hero_img_' . $i ] = array( 'assets/images/' . basename( strtok( $d[0], '?' ) ), $d[0], $tag );
		$items[ 'hero_mob_' . $i ] = array( 'assets/images/' . basename( strtok( $d[1], '?' ) ), $d[1], $tag );
	}
	/* Third value = version tag: a new tag imports the updated file again (v8 = 17 bottles on marble riser, band background kept). */
	$items['promo_img_d'] = array( 'assets/images/promo-band-desktop.jpg', MRZAIB_URI . '/assets/images/promo-band-desktop.jpg', 'v8' );
	$items['promo_img_m'] = array( 'assets/images/promo-band-mobile.jpg', MRZAIB_URI . '/assets/images/promo-band-mobile.jpg', 'v8' );
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
		if ( ! $ours && $cur_base && function_exists( 'attachment_url_to_postid' ) ) {
			$cur_id = (int) attachment_url_to_postid( $cur_base );
			$src    = $cur_id ? (string) get_post_meta( $cur_id, '_mrzaib_src', true ) : '';
			if ( $src && 0 === strpos( $src, strtok( $rel, '#' ) ) ) {
				$ours   = true; // An older theme copy of this same file (e.g. banner with old price).
				$old_id = $cur_id;
			} elseif ( $cur_id && 0 === strpos( $key, 'hero_' ) && preg_match( '/mr-zaib-hero-\d+-(desktop|mobile)/', $cur_base ) ) {
				$ours   = true; // Theme banner re-picked from Media by hand.
			}
		}
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
		$map[ $key ] = (int) $id;
		set_theme_mod( 'mrzaib_' . $key, wp_get_attachment_url( $id ) );
		if ( $old_id && $old_id !== (int) $id ) {
			mrzaib_delete_theme_attachment( $old_id ); // Older theme copy, only when nothing else uses it.
		}
	}
	update_option( 'mrzaib_home_media', $map, false );
	update_option( 'mrzaib_home_media_v', MRZAIB_HOME_MEDIA_V );
	return $n;
}

/**
 * True when an attachment is still used outside the homepage map: its file name or ID in any
 * post content (pages, products, posts, blocks, galleries) or its URL in a theme setting.
 */
function mrzaib_attachment_in_use( $aid ) {
	global $wpdb;
	$aid  = (int) $aid;
	$url  = (string) wp_get_attachment_url( $aid );
	$file = pathinfo( (string) strtok( $url, '?' ), PATHINFO_FILENAME );
	$like = array();
	if ( '' !== $file ) {
		$like[] = '%' . $wpdb->esc_like( $file ) . '%'; // Also matches resized copies (name-300x200.jpg).
	}
	foreach ( array( 'wp-image-' . $aid . '"', 'wp-image-' . $aid . ' ', '"id":' . $aid . ',', '"id":' . $aid . '}', 'ids="' . $aid . ',', 'ids="' . $aid . '"', ',' . $aid . '"', ',' . $aid . ',' ) as $needle ) {
		$like[] = '%' . $wpdb->esc_like( $needle ) . '%';
	}
	foreach ( $like as $l ) {
		$hit = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type NOT IN ('attachment','revision') AND post_content LIKE %s LIMIT 1", $l ) ); // phpcs:ignore
		if ( $hit ) {
			return true;
		}
	}
	if ( '' !== $file ) {
		foreach ( (array) get_theme_mods() as $v ) {
			if ( is_string( $v ) && false !== strpos( $v, $file ) ) {
				return true;
			}
		}
	}
	return false;
}

/** Delete an older theme-imported copy (has _mrzaib_src) only when nothing references it. */
function mrzaib_delete_theme_attachment( $aid ) {
	if ( ! $aid || ! get_post_meta( $aid, '_mrzaib_src', true ) || mrzaib_attachment_in_use( $aid ) ) {
		return false;
	}
	return (bool) wp_delete_attachment( $aid, true );
}

add_action( 'admin_init', function () {
	if ( ! mrzaib_data_ready() ) {
		return;
	}
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

/**
 * Product videos (2026-11): one cinematic video per newer product. Each is added once as a
 * NEW slot after the slots the admin already has (own uploads are never touched), imported
 * into the Media Library with its cover, and linked to its product. A product that does not
 * exist yet is added later, on the first admin visit after it is created.
 */
function mrzaib_product_video_list() {
	return array( 'hoor', 'kireza', 'oud-x', 'raaz', 'velora', 'zauq', 'mehfil', 'dazor', 'brave', 'baraan' );
}

function mrzaib_sideload_theme_file( $rel, $title ) {
	$tag = $rel . '#pv2';
	$id  = mrzaib_home_media_find( $tag );
	if ( $id ) {
		return $id;
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
	update_post_meta( $id, '_mrzaib_src', $tag );
	return (int) $id;
}

function mrzaib_add_product_videos() {
	$done = (array) get_option( 'mrzaib_pvideos_done2', array() );
	$todo = array_diff( mrzaib_product_video_list(), $done );
	if ( ! $todo || ! function_exists( 'wc_get_product' ) ) {
		return 0;
	}
	$count = (int) get_theme_mod( 'mrzaib_video_count', 4 );
	$max   = defined( 'MRZAIB_VIDEO_MAX' ) ? MRZAIB_VIDEO_MAX : 24;
	$added = 0;
	foreach ( $todo as $slug ) {
		$pid = 0;
		foreach ( array( $slug . '-perfume', $slug ) as $s ) {
			$p = get_page_by_path( $s, OBJECT, 'product' );
			if ( $p && 'publish' === $p->post_status ) {
				$pid = (int) $p->ID;
				break;
			}
		}
		if ( ! $pid ) {
			continue; // Product not created yet: try again later.
		}
		// Slot that already holds this product's earlier theme video (v1): replace it in place.
		$slot = 0;
		for ( $i = 1; $i <= $count; $i++ ) {
			if ( (int) get_theme_mod( 'mrzaib_video_product_' . $i ) === $pid && false !== strpos( (string) get_theme_mod( 'mrzaib_video_' . $i ), 'mr-zaib-' . $slug ) ) {
				$slot = $i;
				break;
			}
		}
		if ( ! $slot && $count >= $max ) {
			break;
		}
		$name = get_the_title( $pid );
		$vid  = mrzaib_sideload_theme_file( 'assets/video/' . $slug . '.mp4', 'MR ZAIB ' . $name . ' Video' );
		$pos  = mrzaib_sideload_theme_file( 'assets/video/' . $slug . '.jpg', 'MR ZAIB ' . $name . ' Video Cover' );
		if ( ! $vid ) {
			continue;
		}
		if ( $pos ) {
			update_post_meta( $pos, '_wp_attachment_image_alt', $name . ' perfume by MR ZAIB' );
		}
		if ( ! $slot ) {
			$count++;
			$slot = $count;
		}
		set_theme_mod( 'mrzaib_video_' . $slot, wp_get_attachment_url( $vid ) );
		set_theme_mod( 'mrzaib_video_poster_' . $slot, $pos ? wp_get_attachment_url( $pos ) : '' );
		set_theme_mod( 'mrzaib_video_product_' . $slot, $pid );
		$done[] = $slug;
		$added++;
	}
	if ( $added ) {
		set_theme_mod( 'mrzaib_video_count', $count );
	}
	update_option( 'mrzaib_pvideos_done2', array_values( array_unique( $done ) ), false );
	return $added;
}

add_action( 'admin_init', function () {
	if ( ! mrzaib_data_ready() ) {
		return;
	}
	if ( wp_doing_ajax() || ! current_user_can( 'upload_files' ) ) {
		return;
	}
	if ( count( (array) get_option( 'mrzaib_pvideos_done2', array() ) ) >= count( mrzaib_product_video_list() ) ) {
		return;
	}
	mrzaib_add_product_videos();
}, 30 );

/**
 * New multi-shot videos for Kahn-vahl, Arvon and Zay-ab (user, 2026-11-18): the old video of each
 * product on the homepage is replaced by the new one (first slot linked to that product), extra old
 * slots of the same product are removed, and the product gets a new slot when it had none.
 */
define( 'MRZAIB_MAIN_VIDEOS_V', 1 );

function mrzaib_replace_main_videos() {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return 0;
	}
	$count  = (int) get_theme_mod( 'mrzaib_video_count', 4 );
	$others = array_merge( mrzaib_product_video_list(), array( 'mr-zaib-collection', 'mr-zaib-gift' ) );
	$slots  = array();
	for ( $i = 1; $i <= $count; $i++ ) {
		$slots[] = array(
			'v' => (string) get_theme_mod( 'mrzaib_video_' . $i, mrzaib_video_default( $i ) ),
			'c' => (string) get_theme_mod( 'mrzaib_video_poster_' . $i, mrzaib_video_default( $i, 'jpg' ) ),
			'p' => (int) get_theme_mod( 'mrzaib_video_product_' . $i, mrzaib_video_default_product( $i ) ),
		);
	}
	$n     = 0;
	$after = -1;
	$ok    = true;
	$max   = defined( 'MRZAIB_VIDEO_MAX' ) ? MRZAIB_VIDEO_MAX : 24;
	foreach ( array( 'kahn-vahl', 'arvon', 'zay-ab' ) as $slug ) {
		$pid = 0;
		foreach ( array( $slug . '-perfume', $slug ) as $sl ) {
			$po = get_page_by_path( $sl, OBJECT, 'product' );
			if ( $po && 'publish' === $po->post_status ) {
				$pid = (int) $po->ID;
				break;
			}
		}
		if ( ! $pid ) {
			$ok = false; // Product missing: retried on a later visit.
			continue;
		}
		$has = false;
		foreach ( $slots as $sl ) {
			if ( $sl['p'] === $pid ) {
				$has = true;
				break;
			}
		}
		if ( ! $has && count( $slots ) >= $max ) {
			$ok = false; // Full and no slot of this product: never push out another slot.
			continue;
		}
		$name = get_the_title( $pid );
		$vid  = mrzaib_sideload_theme_file( 'assets/video/' . $slug . '-v2.mp4', 'MR ZAIB ' . $name . ' Video' );
		$pos  = mrzaib_sideload_theme_file( 'assets/video/' . $slug . '-v2.jpg', 'MR ZAIB ' . $name . ' Video Cover' );
		if ( ! $vid ) {
			$ok = false; // Import failed: retried on a later visit.
			continue;
		}
		if ( $pos ) {
			update_post_meta( $pos, '_wp_attachment_image_alt', $name . ' perfume by MR ZAIB' );
		}
		$new   = array( 'v' => wp_get_attachment_url( $vid ), 'c' => $pos ? wp_get_attachment_url( $pos ) : '', 'p' => $pid );
		$first = -1;
		foreach ( $slots as $k => $sl ) {
			if ( $sl['p'] !== $pid ) {
				continue;
			}
			$fn = basename( (string) strtok( $sl['v'], '?' ) );
			$other = false;
			foreach ( $others as $o ) {
				if ( false !== strpos( $fn, $o ) ) {
					$other = true;
				}
			}
			if ( $other ) {
				continue; // Another product's / collection video that only carries this badge.
			}
			if ( $first < 0 ) {
				$first       = $k;
				$slots[ $k ] = $new;
			} else {
				unset( $slots[ $k ] );
			}
		}
		$slots = array_values( $slots );
		if ( $first < 0 && count( $slots ) >= $max ) {
			$ok = false;
			continue;
		}
		if ( $first < 0 ) {
			array_splice( $slots, $after + 1, 0, array( $new ) );
			$after++;
		} else {
			$after = array_search( $new, $slots, true );
		}
		$n++;
	}
	$slots = array_values( $slots ); // Never sliced: unrelated slots are always kept.
	foreach ( $slots as $i => $sl ) {
		set_theme_mod( 'mrzaib_video_' . ( $i + 1 ), $sl['v'] );
		set_theme_mod( 'mrzaib_video_poster_' . ( $i + 1 ), $sl['c'] );
		set_theme_mod( 'mrzaib_video_product_' . ( $i + 1 ), $sl['p'] );
	}
	for ( $i = count( $slots ) + 1; $i <= $count; $i++ ) {
		remove_theme_mod( 'mrzaib_video_' . $i );
		remove_theme_mod( 'mrzaib_video_poster_' . $i );
		remove_theme_mod( 'mrzaib_video_product_' . $i );
	}
	set_theme_mod( 'mrzaib_video_count', count( $slots ) );
	if ( $ok ) {
		update_option( 'mrzaib_main_videos_v', MRZAIB_MAIN_VIDEOS_V ); // Only when all 3 are done; done ones stay on retry.
	}
	return $n;
}

add_action( 'admin_init', function () {
	if ( ! mrzaib_data_ready() || (int) get_option( 'mrzaib_main_videos_v' ) >= MRZAIB_MAIN_VIDEOS_V || ! current_user_can( 'upload_files' ) || wp_doing_ajax() ) {
		return;
	}
	mrzaib_replace_main_videos();
}, 35 );

/**
 * Hero reset (user, 2026-11-18): all 13 built-in perfume slides in order (collection slide removed),
 * with the current podium banners and prices. Slides that still hold an older theme banner are
 * replaced; the admin's own slide images are kept and moved after the 14 built-in slides.
 * Runs once automatically (MRZAIB_HERO_RESET_V) and from Appearance > MR ZAIB Setup.
 */
define( 'MRZAIB_HERO_RESET_V', 4 ); // v4: 17 perfume slides (Tusqan, Obsession, Elara, Aura added).

function mrzaib_is_theme_hero( $url ) {
	$b = basename( (string) strtok( (string) $url, '?' ) );
	return '' === $url || false !== strpos( (string) $url, '/themes/' ) || preg_match( '/hero-\d+-(desktop|mobile)/', $b );
}

function mrzaib_reset_hero_banners() {
	if ( ! function_exists( 'mrzaib_hero_defaults' ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$hd   = mrzaib_hero_defaults();
	$old  = max( 1, (int) get_theme_mod( 'mrzaib_hero_count', 17 ) );
	$own  = array(); // Admin's visible slides: moved after the built-in ones.
	$hid  = array(); // Admin's slides beyond the slide count: kept, still hidden.
	$last = MRZAIB_HERO_MAX;
	for ( $i = 1; $i <= $last; $i++ ) {
		$img = (string) get_theme_mod( 'mrzaib_hero_img_' . $i, '' );
		if ( $img && ! get_theme_mod( 'mrzaib_hero_rm_' . $i ) && ! mrzaib_is_theme_hero( $img ) ) {
			$mob = (string) get_theme_mod( 'mrzaib_hero_mob_' . $i, '' );
			$sl  = array( $img, mrzaib_is_theme_hero( $mob ) ? '' : $mob, (string) get_theme_mod( 'mrzaib_hero_link_' . $i, '' ) );
			if ( $i <= $old ) {
				$own[] = $sl;
			} else {
				$hid[] = $sl;
			}
		}
	}
	// Capacity first: when 13 built-in + the admin's slides need more than the slide limit,
	// the limit is raised (mrzaib_hero_max, read by MRZAIB_HERO_MAX) so no custom slide is lost.
	$need = count( $hd ) + count( $own ) + count( $hid );
	$cap  = max( MRZAIB_HERO_MAX, $need );
	if ( $cap > MRZAIB_HERO_MAX ) {
		update_option( 'mrzaib_hero_max', $cap );
	}
	for ( $i = 1; $i <= $last; $i++ ) {
		foreach ( array( 'img', 'mob', 'link', 'rm' ) as $f ) {
			remove_theme_mod( 'mrzaib_hero_' . $f . '_' . $i );
		}
	}
	$map = (array) get_option( 'mrzaib_home_media', array() );
	foreach ( $hd as $i => $d ) {
		foreach ( array( 'img' => 'desktop', 'mob' => 'mobile' ) as $f => $kind ) {
			$rel  = 'assets/images/' . basename( strtok( $d[ 'img' === $f ? 0 : 1 ], '?' ) );
			$tag  = $rel . '#p3';
			$file = MRZAIB_DIR . '/' . $rel;
			$id   = mrzaib_home_media_find( $tag );
			if ( ! $id && file_exists( $file ) ) {
				$tmp = wp_tempnam( basename( $file ) );
				copy( $file, $tmp );
				$id = media_handle_sideload( array( 'name' => 'mr-zaib-' . basename( $file ), 'tmp_name' => $tmp ), 0, 'MR ZAIB Hero ' . $i . ' ' . ucfirst( $kind ) );
				if ( is_wp_error( $id ) ) {
					@unlink( $tmp ); // phpcs:ignore
					$id = 0;
				} else {
					update_post_meta( $id, '_mrzaib_src', $tag );
					update_post_meta( $id, '_wp_attachment_image_alt', $d[3] );
				}
			}
			$url = $id ? wp_get_attachment_url( $id ) : $d[ 'img' === $f ? 0 : 1 ];
			set_theme_mod( 'mrzaib_hero_' . $f . '_' . $i, $url );
			if ( $id ) {
				$map[ 'hero_' . $f . '_' . $i ] = (int) $id;
			}
		}
		set_theme_mod( 'mrzaib_hero_link_' . $i, $d[2] );
	}
	$n = count( $hd );
	foreach ( array_merge( $own, $hid ) as $k => $o ) {
		$j = $n + 1 + $k; // Never beyond $cap (computed above).
		set_theme_mod( 'mrzaib_hero_img_' . $j, $o[0] );
		set_theme_mod( 'mrzaib_hero_mob_' . $j, $o[1] );
		set_theme_mod( 'mrzaib_hero_link_' . $j, $o[2] );
	}
	foreach ( array_keys( $map ) as $mk ) {
		if ( preg_match( '/^hero_(img|mob)_(\d+)$/', $mk, $mm ) && (int) $mm[2] > count( $hd ) ) {
			unset( $map[ $mk ] );
		}
	}
	// Remove older theme banner copies (old prices / old style) from the Media Library.
	$used = array_map( 'intval', array_values( $map ) );
	$olds = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => -1, 'fields' => 'ids', 'meta_key' => '_mrzaib_src', 'meta_compare' => 'LIKE', 'meta_value' => 'assets/images/hero-' ) );
	foreach ( $olds as $aid ) {
		if ( ! in_array( (int) $aid, $used, true ) && false === strpos( (string) get_post_meta( $aid, '_mrzaib_src', true ), '#p3' ) ) {
			mrzaib_delete_theme_attachment( $aid ); // Kept when a page, product or setting still uses it.
		}
	}
	set_theme_mod( 'mrzaib_hero_count', min( $cap, $n + count( $own ) ) );
	update_option( 'mrzaib_home_media', $map, false );
	update_option( 'mrzaib_hero_v', '14' );
	update_option( 'mrzaib_hero_reset_v', MRZAIB_HERO_RESET_V );
	return $n;
}

add_action( 'admin_init', function () {
	if ( ! mrzaib_data_ready() ) {
		return;
	}
	if ( (int) get_option( 'mrzaib_hero_reset_v' ) >= MRZAIB_HERO_RESET_V || ! current_user_can( 'upload_files' ) || wp_doing_ajax() ) {
		return;
	}
	mrzaib_reset_hero_banners();
}, 40 );
