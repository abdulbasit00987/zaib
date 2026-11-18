<?php
/**
 * Shop / category archive UI: sidebar (categories, price filter, top rated, tags),
 * toolbar (count, styled sorting, Large / Small / List view switcher like the reference).
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/** Shop-type archive (shop, category, tag, product attribute). */
function mrzaib_is_shop_archive() {
	return ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() );
}

/* Replace default count/order with our toolbar. */
add_action( 'wp', function () {
	if ( ! mrzaib_is_shop_archive() ) {
		return;
	}
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	add_action( 'woocommerce_before_shop_loop', 'mrzaib_shop_open', 1 );
	add_action( 'woocommerce_before_shop_loop', 'mrzaib_shop_toolbar', 25 );
	add_action( 'woocommerce_after_shop_loop', 'mrzaib_shop_close', 99 );
	add_action( 'woocommerce_no_products_found', 'mrzaib_shop_open', 1 );
	add_action( 'woocommerce_no_products_found', 'mrzaib_shop_close', 99 );
} );


/* Body class + saved view applied before paint. */
add_filter( 'body_class', function ( $c ) {
	if ( mrzaib_is_shop_archive() ) {
		$c[] = 'mz-shop-page';
	}
	return $c;
} );

function mrzaib_shop_open() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	echo '<div class="mzshop">';
	echo '<div class="mzshop-overlay" data-mzshop-close></div>';
	echo '<aside class="mzshop-side" id="mzshop-side" aria-label="' . esc_attr__( 'Filters', 'mrzaib' ) . '">';
	echo '<div class="mzshop-side-head"><span>' . esc_html__( 'Filter', 'mrzaib' ) . '</span><button type="button" class="mzshop-x" data-mzshop-close aria-label="' . esc_attr__( 'Close filters', 'mrzaib' ) . '">' . mrzaib_icon( 'close' ) . '</button></div>'; // phpcs:ignore
	echo '<div class="mzshop-side-inner">';
	// Always the theme's own sidebar (WP auto-assigns default blog widgets to widget areas, which don't belong here).
	mrzaib_shop_widget_categories();
	mrzaib_shop_widget_price();
	mrzaib_shop_widget_top_rated();
	mrzaib_shop_widget_tags();
	echo '</div></aside>';
	echo '<div class="mzshop-main">';
	if ( did_action( 'woocommerce_no_products_found' ) ) {
		mrzaib_shop_toolbar();
	}
}

function mrzaib_shop_close() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	echo '</div></div>';
}

/* ---------- Sidebar blocks ---------- */
function mrzaib_shop_widget_categories() {
	$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => 0, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
	if ( is_wp_error( $cats ) || ! $cats ) {
		return;
	}
	$current = is_product_category() ? get_queried_object_id() : 0;
	echo '<div class="mzw mzw-cats"><h4 class="mzw-title">' . esc_html__( 'Categories', 'mrzaib' ) . '</h4><ul class="mzw-catlist">';
	foreach ( $cats as $c ) {
		mrzaib_shop_cat_item( $c, $current );
	}
	echo '</ul></div>';
}

function mrzaib_shop_cat_item( $c, $current ) {
	$kids = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'parent' => $c->term_id ) );
	$kids = is_wp_error( $kids ) ? array() : $kids;
	$open = $current && ( $current === $c->term_id || term_is_ancestor_of( $c->term_id, $current, 'product_cat' ) );
	$cls  = ( $current === $c->term_id ? 'is-current ' : '' ) . ( $kids ? 'has-kids ' : '' ) . ( $open ? 'is-open' : '' );
	echo '<li class="' . esc_attr( trim( $cls ) ) . '"><a href="' . esc_url( get_term_link( $c ) ) . '"><span class="mzw-cname">' . esc_html( $c->name ) . '</span><span class="mzw-count">' . (int) $c->count . '</span></a>';
	if ( $kids ) {
		echo '<button type="button" class="mzw-tog" aria-label="' . esc_attr__( 'Show sub categories', 'mrzaib' ) . '"></button><ul class="mzw-sub">';
		foreach ( $kids as $k ) {
			mrzaib_shop_cat_item( $k, $current );
		}
		echo '</ul>';
	}
	echo '</li>';
}

function mrzaib_shop_widget_price() {
	global $wpdb;
	$row = $wpdb->get_row( "SELECT MIN( l.min_price ) AS lo, MAX( l.max_price ) AS hi FROM {$wpdb->wc_product_meta_lookup} l INNER JOIN {$wpdb->posts} p ON p.ID = l.product_id WHERE p.post_type = 'product' AND p.post_status = 'publish'" ); // phpcs:ignore
	if ( ! $row || null === $row->hi ) {
		return;
	}
	$step = 100;
	$lo   = (int) floor( (float) $row->lo / $step ) * $step;
	$hi   = (int) ceil( (float) $row->hi / $step ) * $step;
	if ( $hi <= $lo ) {
		$lo = max( 0, $lo - 500 );
		$hi = $hi + 500;
	}
	$cur_lo = isset( $_GET['min_price'] ) ? max( $lo, (int) $_GET['min_price'] ) : $lo; // phpcs:ignore
	$cur_hi = isset( $_GET['max_price'] ) ? min( $hi, (int) $_GET['max_price'] ) : $hi; // phpcs:ignore
	$sym    = html_entity_decode( get_woocommerce_currency_symbol() );
	$action = is_product_taxonomy() ? get_term_link( get_queried_object() ) : wc_get_page_permalink( 'shop' );
	echo '<div class="mzw mzw-price"><h4 class="mzw-title">' . esc_html__( 'Filter by price', 'mrzaib' ) . '</h4>';
	echo '<form class="mzp-range" method="get" action="' . esc_url( $action ) . '" data-sym="' . esc_attr( $sym ) . '">';
	echo '<div class="mzp-track"><div class="mzp-fill"></div>';
	echo '<input type="range" class="mzp-lo" name="min_price" min="' . (int) $lo . '" max="' . (int) $hi . '" step="' . (int) $step . '" value="' . (int) $cur_lo . '" aria-label="' . esc_attr__( 'Minimum price', 'mrzaib' ) . '">';
	echo '<input type="range" class="mzp-hi" name="max_price" min="' . (int) $lo . '" max="' . (int) $hi . '" step="' . (int) $step . '" value="' . (int) $cur_hi . '" aria-label="' . esc_attr__( 'Maximum price', 'mrzaib' ) . '">';
	echo '</div><div class="mzp-row"><span class="mzp-label">' . esc_html__( 'Price:', 'mrzaib' ) . ' <b class="mzp-v-lo">' . esc_html( $sym . ' ' . number_format( $cur_lo ) ) . '</b> — <b class="mzp-v-hi">' . esc_html( $sym . ' ' . number_format( $cur_hi ) ) . '</b></span>';
	foreach ( array( 'orderby', 'product_tag', 's', 'post_type' ) as $keep ) {
		if ( isset( $_GET[ $keep ] ) ) { // phpcs:ignore
			echo '<input type="hidden" name="' . esc_attr( $keep ) . '" value="' . esc_attr( sanitize_text_field( wp_unslash( $_GET[ $keep ] ) ) ) . '">'; // phpcs:ignore
		}
	}
	echo '<button type="submit" class="mzp-go">' . esc_html__( 'Filter', 'mrzaib' ) . '</button></div></form></div>';
}

function mrzaib_shop_widget_top_rated() {
	$q = new WP_Query( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'no_found_rows'  => true,
		'meta_key'       => '_wc_average_rating', // phpcs:ignore
		'orderby'        => array( 'meta_value_num' => 'DESC', 'date' => 'DESC' ),
		'tax_query'      => WC()->query->get_tax_query(), // phpcs:ignore
	) );
	if ( ! $q->have_posts() ) {
		return;
	}
	echo '<div class="mzw mzw-top"><h4 class="mzw-title">' . esc_html__( 'Top Rated Perfumes', 'mrzaib' ) . '</h4><ul class="mzw-plist">';
	foreach ( $q->posts as $p ) {
		$prod = wc_get_product( $p );
		if ( ! $prod ) {
			continue;
		}
		$avg  = (float) $prod->get_average_rating();
		$link = get_permalink( $p );
		echo '<li><a class="mzw-pimg" href="' . esc_url( $link ) . '">' . $prod->get_image( 'woocommerce_gallery_thumbnail' ) . '</a><div class="mzw-pinfo">'; // phpcs:ignore
		echo '<a class="mzw-pname" href="' . esc_url( $link ) . '">' . esc_html( $prod->get_name() ) . '</a>';
		echo '<span class="mzw-stars" aria-label="' . esc_attr( sprintf( __( 'Rated %s out of 5', 'mrzaib' ), $avg ) ) . '">';
		for ( $k = 1; $k <= 5; $k++ ) {
			echo '<span class="' . ( $k <= round( $avg ) ? 'on' : 'off' ) . '">' . mrzaib_icon( 'starf' ) . '</span>'; // phpcs:ignore
		}
		echo '</span><span class="mzw-pprice">' . $prod->get_price_html() . '</span></div></li>'; // phpcs:ignore
	}
	echo '</ul></div>';
	wp_reset_postdata();
}

function mrzaib_shop_widget_tags() {
	$tags = get_terms( array( 'taxonomy' => 'product_tag', 'hide_empty' => true, 'number' => 20, 'orderby' => 'count', 'order' => 'DESC' ) );
	if ( is_wp_error( $tags ) || ! $tags ) {
		return;
	}
	$current = is_product_tag() ? get_queried_object_id() : 0;
	echo '<div class="mzw mzw-tags"><h4 class="mzw-title">' . esc_html__( 'Product Tags', 'mrzaib' ) . '</h4><div class="mzw-taglist">';
	foreach ( $tags as $t ) {
		echo '<a class="' . ( $current === $t->term_id ? 'is-current' : '' ) . '" href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a>';
	}
	echo '</div></div>';
}

/* ---------- Toolbar ---------- */
function mrzaib_shop_toolbar() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done  = true;
	$total = (int) wc_get_loop_prop( 'total' );
	$icons = array(
		'large' => array( __( 'Large', 'mrzaib' ), '<svg viewBox="0 0 18 18" width="18" height="18" fill="currentColor"><rect x="0" y="0" width="8" height="8"/><rect x="10" y="0" width="8" height="8"/><rect x="0" y="10" width="8" height="8"/><rect x="10" y="10" width="8" height="8"/></svg>' ),
		'small' => array( __( 'Small', 'mrzaib' ), '<svg viewBox="0 0 18 18" width="18" height="18" fill="currentColor"><rect x="0" y="0" width="4.6" height="4.6"/><rect x="6.7" y="0" width="4.6" height="4.6"/><rect x="13.4" y="0" width="4.6" height="4.6"/><rect x="0" y="6.7" width="4.6" height="4.6"/><rect x="6.7" y="6.7" width="4.6" height="4.6"/><rect x="13.4" y="6.7" width="4.6" height="4.6"/><rect x="0" y="13.4" width="4.6" height="4.6"/><rect x="6.7" y="13.4" width="4.6" height="4.6"/><rect x="13.4" y="13.4" width="4.6" height="4.6"/></svg>' ),
		'list'  => array( __( 'List', 'mrzaib' ), '<svg viewBox="0 0 18 18" width="18" height="18" fill="currentColor"><rect x="0" y="0" width="18" height="4"/><rect x="0" y="7" width="18" height="4"/><rect x="0" y="14" width="18" height="4"/></svg>' ),
	);
	echo '<div class="mzshop-bar">';
	echo '<button type="button" class="mzshop-filter-btn" data-mzshop-open aria-controls="mzshop-side"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 7h10M18 7h2M4 17h4M12 17h8"/><circle cx="16" cy="7" r="2"/><circle cx="10" cy="17" r="2"/></svg>' . esc_html__( 'Filter', 'mrzaib' ) . '</button>';
	echo '<p class="mzshop-count">' . esc_html( sprintf( _n( '%d product', '%d products', $total, 'mrzaib' ), $total ) ) . '</p>';
	echo '<div class="mzshop-right"><div class="mzshop-sort"><span class="mzshop-sort-lbl">' . esc_html__( 'Sort', 'mrzaib' ) . '</span>';
	woocommerce_catalog_ordering();
	echo '</div><ul class="mzshop-views" role="group" aria-label="' . esc_attr__( 'Product view', 'mrzaib' ) . '">';
	foreach ( $icons as $k => $v ) {
		echo '<li><button type="button" class="grid-view-btn" data-view="' . esc_attr( $k ) . '" title="' . esc_attr( $v[0] ) . '" aria-label="' . esc_attr( $v[0] ) . '">' . $v[1] . '</button></li>'; // phpcs:ignore
	}
	echo '</ul></div></div>';

	/* Active filters chips */
	$chips = array();
	if ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) { // phpcs:ignore
		$min     = isset( $_GET['min_price'] ) ? wc_price( (float) wp_unslash( $_GET['min_price'] ), array( 'decimals' => 0 ) ) : ''; // phpcs:ignore
		$max     = isset( $_GET['max_price'] ) ? wc_price( (float) wp_unslash( $_GET['max_price'] ), array( 'decimals' => 0 ) ) : ''; // phpcs:ignore
		$chips[] = '<a href="' . esc_url( remove_query_arg( array( 'min_price', 'max_price' ) ) ) . '">' . wp_strip_all_tags( $min . ' – ' . $max ) . ' <span aria-hidden="true">×</span></a>';
	}
	if ( $chips ) {
		echo '<div class="mzshop-chips">' . implode( '', $chips ) . '<a class="mzshop-clear" href="' . esc_url( remove_query_arg( array( 'min_price', 'max_price' ) ) ) . '">' . esc_html__( 'Clear all', 'mrzaib' ) . '</a></div>'; // phpcs:ignore
	}
	echo "<script>(function(){try{var v=localStorage.getItem('mzShopView')||'small';document.documentElement.setAttribute('data-mzview',v);}catch(e){}})();</script>";
}
