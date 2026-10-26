<?php
/**
 * MR ZAIB: one-time Yoast SEO configuration.
 *
 * Runs automatically in wp-admin once Yoast SEO is active (and again from the
 * setup page). Global settings are set every run; per product / page / category
 * titles, descriptions and focus keyphrases are only filled when still empty,
 * so anything edited in Yoast is never overwritten.
 *
 * @package mrzaib
 */

defined( 'ABSPATH' ) || exit;

define( 'MRZAIB_YOAST_V', '2' );

add_action( 'admin_init', function () {
	if ( defined( 'WPSEO_VERSION' ) && MRZAIB_YOAST_V !== get_option( 'mrzaib_yoast_v' ) && current_user_can( 'manage_options' ) ) {
		mrzaib_configure_yoast();
	}
}, 30 );

/** Clean product name for keyphrases ("Kahn-vahl (Copy)" → "Kahn-vahl"). */
function mrzaib_seo_clean_name( $name ) {
	return trim( preg_replace( '/\s*\((copy|kopie)\)/i', '', $name ) );
}

/** Logo attachment for Yoast's organisation + default share image. */
function mrzaib_seo_logo_id() {
	$id = (int) get_theme_mod( 'custom_logo' );
	if ( $id ) {
		return $id;
	}
	$id = (int) get_option( 'mrzaib_seo_logo_id' );
	if ( $id && get_post( $id ) ) {
		return $id;
	}
	$src = get_template_directory() . '/assets/images/logo-gold.png';
	if ( ! file_exists( $src ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( 'mr-zaib-logo.png' );
	copy( $src, $tmp );
	$id = media_handle_sideload( array( 'name' => 'mr-zaib-logo.png', 'tmp_name' => $tmp ), 0, 'MR ZAIB Luxury Perfumes' );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	update_post_meta( $id, '_wp_attachment_image_alt', 'MR ZAIB Luxury Perfumes logo' );
	update_option( 'mrzaib_seo_logo_id', $id );
	return (int) $id;
}

/** Set a Yoast post field only when it is empty. */
function mrzaib_seo_fill( $post_id, $key, $value ) {
	if ( '' === (string) get_post_meta( $post_id, '_yoast_wpseo_' . $key, true ) && '' !== (string) $value ) {
		update_post_meta( $post_id, '_yoast_wpseo_' . $key, $value );
		return true;
	}
	return false;
}

/** Trim text to a meta-description length on a word boundary. */
function mrzaib_seo_trim( $text, $len = 155 ) {
	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );
	if ( strlen( $text ) <= $len ) {
		return $text;
	}
	$cut = substr( $text, 0, $len );
	return rtrim( substr( $cut, 0, strrpos( $cut, ' ' ) ), ' ,.;:' ) . '.';
}

function mrzaib_configure_yoast() {
	if ( ! defined( 'WPSEO_VERSION' ) ) {
		return 'Yoast SEO is not active.';
	}
	$brand   = 'MR ZAIB';

	/* ---- Let search engines index the site (fixes Yoast "Huge SEO Issue: You're blocking access to robots") ---- */
	update_option( 'blog_public', '1' );
	if ( class_exists( 'Yoast_Notification_Center' ) ) {
		$nc = Yoast_Notification_Center::get();
		if ( method_exists( $nc, 'remove_notification_by_id' ) ) {
			$nc->remove_notification_by_id( 'wpseo-search-engines-discouraged' );
		}
	}
	delete_option( 'wpseo_search_engines_discouraged_notice' );

	$logo_id = mrzaib_seo_logo_id();
	$logo    = $logo_id ? wp_get_attachment_url( $logo_id ) : '';

	/* ---- Search appearance (titles & descriptions) ---- */
	$t = get_option( 'wpseo_titles', array() );
	$t = is_array( $t ) ? $t : array();
	$t = array_merge( $t, array(
		'separator'                      => 'sc-pipe',
		'website_name'                   => $brand,
		'alternate_website_name'         => 'MR ZAIB Luxury Perfumes',
		'company_or_person'              => 'company',
		'company_name'                   => $brand,
		'company_alternate_name'         => 'MR ZAIB Luxury Perfumes',
		'company_logo'                   => $logo,
		'company_logo_id'                => $logo_id,
		'breadcrumbs-enable'             => true,
		'disable-attachment'             => true,
		'title-home-wpseo'               => '%%sitename%% %%sep%% Luxury Long Lasting Perfumes in Pakistan',
		'metadesc-home-wpseo'            => 'MR ZAIB Luxury Perfumes: long lasting perfumes for men, women and unisex in Pakistan. Cash on Delivery all over Pakistan.',
		'title-page'                     => '%%title%% %%page%% %%sep%% %%sitename%%',
		'title-post'                     => '%%title%% %%page%% %%sep%% %%sitename%%',
		'title-product'                  => '%%title%% Perfume %%sep%% %%sitename%% Pakistan',
		'metadesc-product'               => '%%excerpt%%',
		'noindex-product'                => false,
		'title-ptarchive-product'        => 'Shop All Perfumes Online in Pakistan %%page%% %%sep%% %%sitename%%',
		'metadesc-ptarchive-product'     => 'Shop all MR ZAIB perfumes online: long lasting luxury fragrances for men, women and unisex. Cash on Delivery all over Pakistan.',
		'title-tax-product_cat'          => '%%term_title%% Perfumes in Pakistan %%page%% %%sep%% %%sitename%%',
		'metadesc-tax-product_cat'       => 'Shop %%term_title%% perfumes by MR ZAIB: long lasting luxury fragrances at affordable prices with Cash on Delivery all over Pakistan.',
		'noindex-tax-product_cat'        => false,
		'noindex-tax-product_tag'        => true,
		'noindex-tax-product_shipping_class' => true,
		'noindex-author-wpseo'           => true,
		'disable-author'                 => true,
		'disable-date'                   => true,
		'noindex-tax-post_format'        => true,
		'title-search-wpseo'             => 'Search results for %%searchphrase%% %%page%% %%sep%% %%sitename%%',
		'title-404-wpseo'                => 'Page not found %%sep%% %%sitename%%',
	) );
	update_option( 'wpseo_titles', $t );

	/* ---- Social / Open Graph ---- */
	$s = get_option( 'wpseo_social', array() );
	$s = is_array( $s ) ? $s : array();
	$s['opengraph']           = true;
	$s['twitter']             = true;
	$s['twitter_card_type']   = 'summary_large_image';
	$s['og_default_image']    = $logo;
	$s['og_default_image_id'] = $logo_id;
	$social = array();
	foreach ( array( 'facebook', 'instagram', 'tiktok', 'youtube', 'x', 'snapchat', 'pinterest' ) as $net ) {
		$u = get_theme_mod( 'mrzaib_social_' . $net, '' );
		if ( $u && '#' !== $u ) {
			if ( 'facebook' === $net ) {
				$s['facebook_site'] = $u;
			} else {
				$social[] = $u;
			}
		}
	}
	if ( $social ) {
		$s['other_social_urls'] = array_values( array_unique( array_merge( isset( $s['other_social_urls'] ) && is_array( $s['other_social_urls'] ) ? $s['other_social_urls'] : array(), $social ) ) );
	}
	update_option( 'wpseo_social', $s );

	/* ---- General features ---- */
	$w = get_option( 'wpseo', array() );
	$w = is_array( $w ) ? $w : array();
	$w = array_merge( $w, array(
		'enable_xml_sitemap'                    => true,
		'keyword_analysis_active'               => true,
		'content_analysis_active'               => true,
		'enable_cornerstone_content'            => true,
		'enable_text_link_counter'              => true,
		'enable_admin_bar_menu'                 => true,
		'site_type'                             => 'shop',
		'should_redirect_after_install_free'    => false,
		'show_onboarding_notice'                => false,
		'dismiss_configuration_workout_notice'  => true,
		'remove_shortlinks'                     => true,
		'remove_rsd_wlw_links'                  => true,
		'remove_generator'                      => true,
		'remove_emoji_scripts'                  => true,
		'remove_powered_by_header'              => true,
		'remove_feed_post_comments'             => true,
		'clean_campaign_tracking_urls'          => true,
	) );
	update_option( 'wpseo', $w );

	$touched = array();

	/* ---- Products ---- */
	$known = array();
	if ( function_exists( 'mrzaib_store_products' ) ) {
		foreach ( mrzaib_store_products() as $p ) {
			if ( ! empty( $p['name'] ) && ! empty( $p['seo'] ) ) {
				$known[ sanitize_title( $p['name'] ) ] = $p['seo'];
			}
		}
	}
	$products = get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'numberposts' => -1 ) );
	foreach ( $products as $post ) {
		$name = mrzaib_seo_clean_name( $post->post_title );
		$slug = sanitize_title( $name );
		$seo  = isset( $known[ $slug ] ) ? $known[ $slug ] : null;
		$sub  = (string) get_post_meta( $post->ID, '_mrzaib_subtitle', true );
		$desc = $seo ? $seo['desc'] : mrzaib_seo_trim( 'Buy ' . $name . ' by MR ZAIB' . ( $sub ? ', ' . strtolower( $sub ) : '' ) . '. ' . ( $post->post_excerpt ? $post->post_excerpt : $post->post_content ) . ' Cash on Delivery in Pakistan.' );
		$c    = 0;
		$c   += mrzaib_seo_fill( $post->ID, 'focuskw', $seo ? $seo['focus'] : $name . ' perfume' );
		$c   += mrzaib_seo_fill( $post->ID, 'metadesc', $desc );
		if ( $seo ) {
			$c += mrzaib_seo_fill( $post->ID, 'title', $seo['title'] );
		}
		if ( ! get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true ) && get_post_thumbnail_id( $post->ID ) ) {
			update_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', $name . ' perfume by MR ZAIB' );
		}
		if ( $c ) {
			$touched[] = $post->ID;
		}
	}

	/* ---- Pages ---- */
	$front = (int) get_option( 'page_on_front' );
	$pages = array(
		$front => array( 'MR ZAIB %%sep%% Luxury Long Lasting Perfumes in Pakistan', 'Shop MR ZAIB luxury perfumes: long lasting fragrances for men, women and unisex in Pakistan. Rs. 2,500 per 50ml with Cash on Delivery all over Pakistan.', 'luxury perfumes Pakistan' ),
	);
	if ( function_exists( 'wc_get_page_id' ) ) {
		$pages[ wc_get_page_id( 'shop' ) ] = array( 'Shop Perfumes Online in Pakistan %%sep%% %%sitename%%', 'Shop all MR ZAIB perfumes online: long lasting luxury fragrances for men, women and unisex. Cash on Delivery all over Pakistan.', 'buy perfume online Pakistan' );
		foreach ( array( 'cart', 'checkout', 'myaccount' ) as $wcp ) {
			$pid = wc_get_page_id( $wcp );
			if ( $pid > 0 ) {
				update_post_meta( $pid, '_yoast_wpseo_meta-robots-noindex', '1' );
				$touched[] = $pid;
			}
		}
	}
	$by_slug = array(
		'contact'  => array( 'Contact Us %%sep%% %%sitename%%', 'Contact MR ZAIB Luxury Perfumes for orders, delivery and fragrance questions. We reply quickly and deliver all over Pakistan.', 'MR ZAIB contact' ),
		'faqs'     => array( 'FAQs %%sep%% %%sitename%%', 'Answers to common questions about MR ZAIB perfumes, delivery times, Cash on Delivery, returns and how long our fragrances last.', 'MR ZAIB FAQs' ),
		'returns'  => array( 'Returns & Exchange Policy %%sep%% %%sitename%%', 'MR ZAIB easy returns and exchange policy: how to return or exchange your perfume order anywhere in Pakistan.', 'MR ZAIB returns policy' ),
		'outlets'  => array( 'Our Outlets %%sep%% %%sitename%%', 'Visit MR ZAIB perfume outlets in Pakistan and try every fragrance in person before you buy.', 'MR ZAIB outlets' ),
	);
	foreach ( $by_slug as $slug => $data ) {
		$pg = get_page_by_path( $slug );
		if ( $pg ) {
			$pages[ $pg->ID ] = $data;
		}
	}
	foreach ( $pages as $pid => $d ) {
		if ( $pid <= 0 || ! get_post( $pid ) ) {
			continue;
		}
		$c  = mrzaib_seo_fill( $pid, 'title', $d[0] );
		$c += mrzaib_seo_fill( $pid, 'metadesc', $d[1] );
		$c += mrzaib_seo_fill( $pid, 'focuskw', $d[2] );
		if ( $c ) {
			$touched[] = $pid;
		}
	}

	/* ---- Product categories ---- */
	$cat_text = array(
		'men'    => array( 'Perfumes for Men in Pakistan %%sep%% %%sitename%%', 'Shop long lasting perfumes for men by MR ZAIB. Fresh, aquatic and woody men fragrances with Cash on Delivery all over Pakistan.', 'perfume for men' ),
		'women'  => array( 'Perfumes for Women in Pakistan %%sep%% %%sitename%%', 'Shop long lasting perfumes for women by MR ZAIB. Floral, fruity and sweet fragrances with Cash on Delivery all over Pakistan.', 'perfume for women' ),
		'unisex' => array( 'Unisex Perfumes in Pakistan %%sep%% %%sitename%%', 'Shop unisex perfumes by MR ZAIB for men and women. Long lasting luxury fragrances with Cash on Delivery all over Pakistan.', 'unisex perfume' ),
		'attar'  => array( 'Attar & Perfume Oils in Pakistan %%sep%% %%sitename%%', 'Shop pure attar and concentrated perfume oils by MR ZAIB. Long lasting traditional fragrances with Cash on Delivery all over Pakistan.', 'attar' ),
		'tester' => array( 'Perfume Testers in Pakistan %%sep%% %%sitename%%', 'Try MR ZAIB perfume testers before buying the full bottle. Affordable fragrance samples with Cash on Delivery all over Pakistan.', 'perfume tester' ),
	);
	$tm = get_option( 'wpseo_taxonomy_meta', array() );
	$tm = is_array( $tm ) ? $tm : array();
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
		foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) {
			if ( 'uncategorized' === $term->slug ) {
				continue;
			}
			$m = isset( $tm['product_cat'][ $term->term_id ] ) ? $tm['product_cat'][ $term->term_id ] : array();
			$d = isset( $cat_text[ $term->slug ] ) ? $cat_text[ $term->slug ] : array( '', '', $term->name . ' perfume' );
			foreach ( array( 'wpseo_title' => $d[0], 'wpseo_desc' => $d[1], 'wpseo_focuskw' => $d[2] ) as $k => $v ) {
				if ( empty( $m[ $k ] ) && '' !== $v ) {
					$m[ $k ] = $v;
				}
			}
			$tm['product_cat'][ $term->term_id ] = $m;
		}
		update_option( 'wpseo_taxonomy_meta', $tm );
	}

	/* ---- Let Yoast rebuild its index for what changed ---- */
	foreach ( array_unique( $touched ) as $pid ) {
		clean_post_cache( $pid );
		wp_update_post( array( 'ID' => $pid ) );
	}
	if ( taxonomy_exists( 'product_cat' ) && ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			do_action( 'edited_term', $term->term_id, $term->term_taxonomy_id, 'product_cat', array() );
		}
	}
	if ( class_exists( 'WPSEO_Sitemaps_Cache' ) ) {
		WPSEO_Sitemaps_Cache::clear();
	}
	update_option( 'mrzaib_yoast_v', MRZAIB_YOAST_V );
	flush_rewrite_rules( false );
	return 'Yoast SEO configured: titles, meta descriptions, focus keyphrases, social sharing and sitemap.';
}
