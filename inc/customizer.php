<?php
/**
 * Customizer: Appearance > Customize > MR ZAIB Options
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mrzaib_customize( $wp_customize ) {
	$wp_customize->add_panel( 'mrzaib_panel', array( 'title' => __( 'MR ZAIB Options', 'mrzaib' ), 'priority' => 20 ) );

	$sections = array(
		'mrzaib_header'  => __( 'Announcement Bar', 'mrzaib' ),
		'mrzaib_hero'    => __( 'Hero Slider', 'mrzaib' ),
		'mrzaib_home'    => __( 'Homepage Texts', 'mrzaib' ),
		'mrzaib_videos'  => __( 'Homepage Videos', 'mrzaib' ),
		'mrzaib_feats'   => __( 'Homepage Features & Icons', 'mrzaib' ),
		'mrzaib_seo'     => __( 'Homepage SEO Text', 'mrzaib' ),
		'mrzaib_contact' => __( 'Contact & Social', 'mrzaib' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section( $id, array( 'title' => $title, 'panel' => 'mrzaib_panel' ) );
	}

	$text = function ( $key, $label, $section, $default = '', $type = 'text' ) use ( $wp_customize ) {
		$wp_customize->add_setting( 'mrzaib_' . $key, array( 'default' => $default, 'sanitize_callback' => 'textarea' === $type ? 'wp_kses_post' : 'sanitize_text_field' ) );
		$wp_customize->add_control( 'mrzaib_' . $key, array( 'label' => $label, 'section' => $section, 'type' => $type ) );
	};

	// Announcement.
	$text( 'announce_1', __( 'Announcement 1', 'mrzaib' ), 'mrzaib_header', 'Luxury Perfumes | Leave Your Presence' );
	$text( 'announce_2', __( 'Announcement 2', 'mrzaib' ), 'mrzaib_header', 'Free Shipping on orders over Rs. 3000' );
	$text( 'announce_3', __( 'Announcement 3', 'mrzaib' ), 'mrzaib_header', 'Cash on Delivery all over Pakistan' );

	// Hero slides: choose how many (1-10); each has desktop image, mobile image, link and alt text.
	$wp_customize->add_setting( 'mrzaib_hero_count', array( 'default' => 3, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'mrzaib_hero_count', array(
		'label'       => __( 'Number of slides', 'mrzaib' ),
		'description' => __( 'Increase to add a new slide (max 10). Save & reload the Customizer to see the new slide fields.', 'mrzaib' ),
		'section'     => 'mrzaib_hero',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 1, 'max' => MRZAIB_HERO_MAX, 'step' => 1 ),
	) );
	$hd = mrzaib_hero_defaults();
	for ( $i = 1; $i <= MRZAIB_HERO_MAX; $i++ ) {
		$d      = isset( $hd[ $i ] ) ? $hd[ $i ] : array( '', '', '', '' );
		$active = function () use ( $i ) {
			return $i <= mrzaib_hero_count();
		};
		$wp_customize->add_setting( 'mrzaib_hero_img_' . $i, array( 'default' => $d[0], 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_hero_img_' . $i, array( 'label' => sprintf( __( 'Slide %d desktop image (1920x760)', 'mrzaib' ), $i ), 'section' => 'mrzaib_hero', 'active_callback' => $active ) ) );
		$wp_customize->add_setting( 'mrzaib_hero_mob_' . $i, array( 'default' => $d[1], 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_hero_mob_' . $i, array( 'label' => sprintf( __( 'Slide %d mobile image (900x1100)', 'mrzaib' ), $i ), 'section' => 'mrzaib_hero', 'active_callback' => $active ) ) );
		$wp_customize->add_setting( 'mrzaib_hero_link_' . $i, array( 'default' => $d[2], 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( 'mrzaib_hero_link_' . $i, array( 'label' => sprintf( __( 'Slide %d link', 'mrzaib' ), $i ), 'section' => 'mrzaib_hero', 'type' => 'url', 'active_callback' => $active ) );
		$wp_customize->add_setting( 'mrzaib_hero_alt_' . $i, array( 'default' => $d[3], 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( 'mrzaib_hero_alt_' . $i, array( 'label' => sprintf( __( 'Slide %d image alt text (SEO)', 'mrzaib' ), $i ), 'section' => 'mrzaib_hero', 'active_callback' => $active ) );
	}

	// Home texts.
	$text( 'bs_title', __( 'Best sellers title', 'mrzaib' ), 'mrzaib_home', 'Our Best Sellers' );
	$text( 'bs_sub', __( 'Best sellers subtitle', 'mrzaib' ), 'mrzaib_home', "Perfumes you won't find anywhere else" );
	$text( 'outlet_text', __( 'Retail outlets text', 'mrzaib' ), 'mrzaib_home', 'We are one of the fastest growing fragrance brands in Pakistan. Visit our outlets in major malls and experience every MR ZAIB scent in person before you buy.', 'textarea' );
	$text( 'seo_title', __( 'SEO block title', 'mrzaib' ), 'mrzaib_home', 'Best Long Lasting Perfumes in Pakistan by MR ZAIB' );
	$text( 'stat_1', __( 'Stat 1 number', 'mrzaib' ), 'mrzaib_home', '1' );
	$text( 'stat_2', __( 'Stat 2 number (%)', 'mrzaib' ), 'mrzaib_home', '98' );
	$text( 'stat_3', __( 'Stat 3 number', 'mrzaib' ), 'mrzaib_home', '100' );
	$text( 'col_title', __( 'Categories section title', 'mrzaib' ), 'mrzaib_home', 'Top Perfume Categories' );
	$text( 'row1_title', __( 'Product row 1 title', 'mrzaib' ), 'mrzaib_home', 'Executive Range' );
	$text( 'row1_cat', __( 'Product row 1 category slug (empty = all)', 'mrzaib' ), 'mrzaib_home', '' );
	$text( 'row2_title', __( 'Product row 2 title', 'mrzaib' ), 'mrzaib_home', 'Poetic Range' );
	$text( 'row2_cat', __( 'Product row 2 category slug (empty = all)', 'mrzaib' ), 'mrzaib_home', '' );
	// Videos (own section): count + per video file, poster and product badge.
	$wp_customize->add_setting( 'mrzaib_video_count', array( 'default' => 4, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'mrzaib_video_count', array( 'label' => __( 'Number of videos (0 = hide section)', 'mrzaib' ), 'description' => __( 'Save & refresh to see fields for new videos.', 'mrzaib' ), 'section' => 'mrzaib_videos', 'type' => 'number', 'input_attrs' => array( 'min' => 0, 'max' => MRZAIB_VIDEO_MAX ) ) );
	for ( $i = 1; $i <= MRZAIB_VIDEO_MAX; $i++ ) {
		$vact = function () use ( $i ) {
			return $i <= (int) get_theme_mod( 'mrzaib_video_count', 4 );
		};
		$wp_customize->add_setting( 'mrzaib_video_' . $i, array( 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, 'mrzaib_video_' . $i, array( 'label' => sprintf( __( 'Video %d (.mp4, vertical 2:3)', 'mrzaib' ), $i ), 'section' => 'mrzaib_videos', 'mime_type' => 'video', 'active_callback' => $vact ) ) );
		$wp_customize->add_setting( 'mrzaib_video_poster_' . $i, array( 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_video_poster_' . $i, array( 'label' => sprintf( __( 'Video %d cover image (optional)', 'mrzaib' ), $i ), 'section' => 'mrzaib_videos', 'active_callback' => $vact ) ) );
		$wp_customize->add_setting( 'mrzaib_video_product_' . $i, array( 'default' => 1 === $i % 2 ? 'kahn-vahl' : 'arvon', 'sanitize_callback' => 'sanitize_title' ) );
		$wp_customize->add_control( 'mrzaib_video_product_' . $i, array( 'label' => sprintf( __( 'Video %d product slug (badge)', 'mrzaib' ), $i ), 'section' => 'mrzaib_videos', 'active_callback' => $vact ) );
	}
	$wp_customize->add_setting( 'mrzaib_promo_img_d', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_promo_img_d', array( 'label' => __( 'Promo banner desktop (1920x384)', 'mrzaib' ), 'section' => 'mrzaib_home' ) ) );
	$wp_customize->add_setting( 'mrzaib_promo_img_m', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_promo_img_m', array( 'label' => __( 'Promo banner mobile (900x544)', 'mrzaib' ), 'section' => 'mrzaib_home' ) ) );
	$wp_customize->add_setting( 'mrzaib_promo_link', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'mrzaib_promo_link', array( 'label' => __( 'Promo banner link', 'mrzaib' ), 'section' => 'mrzaib_home', 'type' => 'url' ) );
	$text( 'feat_1', __( 'Feature 1 text', 'mrzaib' ), 'mrzaib_feats', '1M+ Customers' );
	$wp_customize->add_setting( 'mrzaib_feat_icon_1', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_feat_icon_1', array( 'label' => __( 'Feature 1 icon (PNG/SVG, empty = theme icon)', 'mrzaib' ), 'section' => 'mrzaib_feats' ) ) );
	$text( 'feat_2', __( 'Feature 2 text', 'mrzaib' ), 'mrzaib_feats', 'Long Lasting Fragrances' );
	$wp_customize->add_setting( 'mrzaib_feat_icon_2', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_feat_icon_2', array( 'label' => __( 'Feature 2 icon (PNG/SVG, empty = theme icon)', 'mrzaib' ), 'section' => 'mrzaib_feats' ) ) );
	$text( 'feat_3', __( 'Feature 3 text', 'mrzaib' ), 'mrzaib_feats', '15 Days Easy Returns Policy' );
	$wp_customize->add_setting( 'mrzaib_feat_icon_3', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_feat_icon_3', array( 'label' => __( 'Feature 3 icon (PNG/SVG, empty = theme icon)', 'mrzaib' ), 'section' => 'mrzaib_feats' ) ) );
	$text( 'feat_4', __( 'Feature 4 text', 'mrzaib' ), 'mrzaib_feats', 'Super Macerated Perfumes' );
	$wp_customize->add_setting( 'mrzaib_feat_icon_4', array( 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'mrzaib_feat_icon_4', array( 'label' => __( 'Feature 4 icon (PNG/SVG, empty = theme icon)', 'mrzaib' ), 'section' => 'mrzaib_feats' ) ) );
	$text( 'seo_title2', __( 'SEO section title', 'mrzaib' ), 'mrzaib_seo', 'Best Perfumes in Pakistan by MR ZAIB' );
	$text( 'seo_intro', __( 'SEO intro paragraph (empty = theme text). HTML allowed.', 'mrzaib' ), 'mrzaib_seo', '', 'textarea' );
	$text( 'seo_more', __( 'SEO "Read More" text (empty = theme text). HTML allowed: <h3>, <p>, <a>, <strong>.', 'mrzaib' ), 'mrzaib_seo', '', 'textarea' );
	$text( 'trust_pill', __( 'Trusted section pill', 'mrzaib' ), 'mrzaib_home', 'Trusted by Fragheads' );
	$text( 'trust_title', __( 'Trusted section title', 'mrzaib' ), 'mrzaib_home', "Pakistan's Luxury Perfume Brand." );
	$text( 'trust_sub', __( 'Trusted section text', 'mrzaib' ), 'mrzaib_home', 'Pakistanis choose MR ZAIB for quality they can feel, at prices that make sense.' );
	foreach ( array( 1 => array( '1', 'M+', 'customers served across Pakistan' ), 2 => array( '98', '%', 'satisfaction rate based on customer reviews' ), 3 => array( '15', ' Days', 'easy returns on every order' ) ) as $i => $d ) {
		$text( 'tstat_' . $i, sprintf( __( 'Trusted stat %d number', 'mrzaib' ), $i ), 'mrzaib_home', $d[0] );
		$text( 'tstat_' . $i . 's', sprintf( __( 'Trusted stat %d suffix', 'mrzaib' ), $i ), 'mrzaib_home', $d[1] );
		$text( 'tstat_' . $i . 'l', sprintf( __( 'Trusted stat %d label', 'mrzaib' ), $i ), 'mrzaib_home', $d[2] );
	}
	$text( 'tbadge_1', __( 'Trusted badge 1', 'mrzaib' ), 'mrzaib_home', 'Easy 15-day exchange' );
	$text( 'tbadge_2', __( 'Trusted badge 2', 'mrzaib' ), 'mrzaib_home', 'Cash on Delivery' );
	$text( 'tbadge_3', __( 'Trusted badge 3', 'mrzaib' ), 'mrzaib_home', 'Ships across Pakistan' );
	$text( 'free_ship', __( 'Free shipping over (Rs.)', 'mrzaib' ), 'mrzaib_home', '3000' );

	// Contact & social.
	$text( 'whatsapp', __( 'WhatsApp number (923001234567)', 'mrzaib' ), 'mrzaib_contact', '' );
	$text( 'phone', __( 'Phone', 'mrzaib' ), 'mrzaib_contact', '' );
	$text( 'email', __( 'Email', 'mrzaib' ), 'mrzaib_contact', '' );
	$text( 'address', __( 'Address', 'mrzaib' ), 'mrzaib_contact', '' );
	foreach ( array( 'facebook', 'instagram', 'tiktok', 'youtube', 'x' ) as $s ) {
		$wp_customize->add_setting( 'mrzaib_social_' . $s, array( 'default' => '#', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( 'mrzaib_social_' . $s, array( 'label' => ucfirst( $s ) . ' URL', 'section' => 'mrzaib_contact', 'type' => 'url' ) );
	}
}
add_action( 'customize_register', 'mrzaib_customize' );

if ( ! defined( 'MRZAIB_VIDEO_MAX' ) ) {
	define( 'MRZAIB_VIDEO_MAX', 12 );
}
if ( ! defined( 'MRZAIB_HERO_MAX' ) ) {
	define( 'MRZAIB_HERO_MAX', 10 );
}

/** Built-in hero slides (desktop, mobile, link, alt). */
function mrzaib_hero_defaults() {
	return array(
		1 => array( mrzaib_img( 'hero-1-desktop.jpg' ), mrzaib_img( 'hero-1-mobile.jpg' ), mrzaib_cat_link( 'men' ), 'Kahn-vahl perfume for men by MR ZAIB' ),
		2 => array( mrzaib_img( 'hero-2-desktop.jpg' ), mrzaib_img( 'hero-2-mobile.jpg' ), mrzaib_cat_link( 'unisex' ), 'Arvon unisex perfume by MR ZAIB' ),
		3 => array( mrzaib_img( 'hero-3-desktop.jpg' ), mrzaib_img( 'hero-3-mobile.jpg' ), mrzaib_shop_link(), 'The MR ZAIB luxury perfume collection' ),
	);
}

function mrzaib_hero_count() {
	return max( 1, min( MRZAIB_HERO_MAX, (int) get_theme_mod( 'mrzaib_hero_count', 3 ) ) );
}

/** Slides to render: [desktop, mobile, link, alt]; slides without a desktop image are skipped. */
function mrzaib_hero_slides() {
	$hd  = mrzaib_hero_defaults();
	$out = array();
	for ( $i = 1; $i <= mrzaib_hero_count(); $i++ ) {
		$d   = isset( $hd[ $i ] ) ? $hd[ $i ] : array( '', '', '', '' );
		$dsk = mrzaib_opt( 'hero_img_' . $i, $d[0] );
		if ( ! $dsk ) {
			continue;
		}
		$mob   = mrzaib_opt( 'hero_mob_' . $i, get_theme_mod( 'mrzaib_hero_img_' . $i ) ? $dsk : ( $d[1] ? $d[1] : $dsk ) );
		$out[] = array( $dsk, $mob, mrzaib_opt( 'hero_link_' . $i, $d[2] ? $d[2] : mrzaib_shop_link() ), mrzaib_opt( 'hero_alt_' . $i, $d[3] ? $d[3] : get_bloginfo( 'name' ) ) );
	}
	return $out;
}
