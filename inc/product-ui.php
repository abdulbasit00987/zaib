<?php
/**
 * Product page, Quick View modal and cart drawer UI (reference-style layout).
 *
 * - PDP info column order: title, price, rating, subtitle, size pills, qty stepper,
 *   USP list + tax note, Add to cart, trust badges; accordions below.
 * - Quick View (Reno QuickView plugin) uses the same info order.
 * - Cart drawer (Mini Cart Drawer plugin) gets a free-shipping progress bar fragment.
 * - Gallery: "Product Gallery Slider, Additional Variation Images for WooCommerce" plugin,
 *   thumbnails on the left (configured by mrzaib_configure_gallery_plugin()).
 *
 * @package mrzaib
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/** USP list + tax/shipping note (sits between quantity and Add to cart). */
function mrzaib_usp_block() {
	if ( doing_action( 'wqv_product_content' ) ) {
		// Quick View: short description under the quantity counter instead of the USP list/tax note.
		global $product;
		static $done = array();
		$pid = $product ? $product->get_id() : 0;
		if ( isset( $done[ $pid ] ) ) {
			return; // Never print the description twice.
		}
		$done[ $pid ] = true;
		$desc = $product ? $product->get_short_description() : '';
		if ( $desc ) {
			echo '<div class="mz-qv-desc">' . wp_kses_post( wpautop( $desc ) ) . '</div>';
		}
		return;
	}
	$ship = get_page_by_path( 'shipping-policy' );
	$ship = $ship ? get_permalink( $ship ) : '#';
	echo '<div class="mz-usp">';
	global $product;
	$pid   = $product ? $product->get_id() : get_the_ID();
	$lines = metadata_exists( 'post', $pid, '_mrzaib_usps' ) ? preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $pid, '_mrzaib_usps', true ) ) : mrzaib_default_usps();
	$lines = array_filter( array_map( 'trim', $lines ), 'strlen' );
	$items = array();
	foreach ( $lines as $line ) {
		if ( '{shipping}' === strtolower( $line ) ) {
			$icon = 'truck';
			$line = mrzaib_ship_short();
		} elseif ( preg_match( '/return|exchange|refund/i', $line ) ) {
			$icon = 'return';
		} elseif ( preg_match( '/deliver|shipping|courier/i', $line ) ) {
			$icon = 'truck';
		} elseif ( preg_match( '/cash|cod|pay|secure|safe|original|genuine/i', $line ) ) {
			$icon = 'shield';
		} else {
			$icon = 'star';
		}
		$items[] = '<li>' . mrzaib_icon( $icon ) . esc_html( str_ireplace( '{shipping}', mrzaib_ship_short(), $line ) ) . '</li>';
	}
	if ( $items ) {
		echo '<ul class="mz-usp-list">' . implode( '', $items ) . '</ul>'; // phpcs:ignore
	}
	echo '<p class="mz-tax-note">' . esc_html__( 'Tax included.', 'mrzaib' ) . ' <a href="' . esc_url( $ship ) . '">' . esc_html__( 'Shipping', 'mrzaib' ) . '</a> ' . esc_html__( 'calculated at checkout.', 'mrzaib' ) . '</p>';
	echo '</div>';
}
add_action( 'woocommerce_after_add_to_cart_quantity', 'mrzaib_usp_block', 20 );

function mrzaib_subtitle_line() {
	$sub = get_post_meta( get_the_ID(), '_mrzaib_subtitle', true );
	if ( $sub ) {
		echo '<p class="product-subtitle">' . esc_html( $sub ) . '</p>';
	}
}

/** Trust badges under Add to cart. */
function mrzaib_trust_badges() {
	echo '<ul class="mz-badges">';
	echo '<li>' . mrzaib_icon( 'drop' ) . '<span>' . esc_html__( 'Safe Ingredients', 'mrzaib' ) . '</span></li>';
	echo '<li>' . mrzaib_icon( 'star' ) . '<span>' . esc_html__( 'High EDP Concentration', 'mrzaib' ) . '</span></li>';
	echo '<li>' . mrzaib_icon( 'clock' ) . '<span>' . esc_html__( 'Long Maceration', 'mrzaib' ) . '</span></li>';
	echo '</ul>';
}

/* --- Single product summary order --- */
add_action( 'init', function () {
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 7 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 8 );
	add_action( 'woocommerce_single_product_summary', 'mrzaib_subtitle_line', 9 );

	/* Tabs -> accordions */
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	add_action( 'woocommerce_after_single_product_summary', 'mrzaib_product_accordions', 10 );
}, 20 );

/** Accordions (Description, Fragrance Notes, Shipping Information, Reviews). */
function mrzaib_product_accordions() {
	$tabs = apply_filters( 'woocommerce_product_tabs', array() );
	unset( $tabs['additional_information'] );
	$tabs['mrzaib_shipping'] = array(
		'title'    => __( 'Shipping Information', 'mrzaib' ),
		'priority' => 40,
		'callback' => function () {
			echo '<p>' . esc_html__( 'We deliver all over Pakistan in 2 to 5 working days.', 'mrzaib' ) . ' ' . esc_html( mrzaib_ship_long() ) . ' ' . esc_html__( 'Cash on Delivery is available on every order.', 'mrzaib' ) . '</p>';
		},
	);
	uasort( $tabs, function ( $a, $b ) { return ( $a['priority'] ?? 50 ) <=> ( $b['priority'] ?? 50 ); } );
	if ( ! $tabs ) {
		return;
	}
	echo '<div class="mz-accordions">';
	$first = true;
	foreach ( $tabs as $key => $tab ) {
		if ( empty( $tab['callback'] ) ) {
			continue;
		}
		$title = wp_strip_all_tags( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) );
		echo '<details class="mz-acc mz-acc-' . esc_attr( $key ) . '"' . ( $first ? ' open' : '' ) . '><summary>' . esc_html( $title ) . '<span class="mz-acc-ico"></span></summary><div class="mz-acc-body">';
		call_user_func( $tab['callback'], $key, $tab );
		echo '</div></details>';
		$first = false;
	}
	echo '</div>';
}
/* Description tab: no duplicate "Description" heading inside the accordion. */
add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
add_filter( 'woocommerce_product_reviews_heading', '__return_empty_string' );

/* --- Quick View modal content (Reno QuickView) --- */
add_action( 'init', function () {
	if ( ! class_exists( 'SP_WQV_Popup' ) && ! has_action( 'wqv_product_content' ) ) {
		return;
	}
	remove_all_actions( 'wqv_product_content' );
	add_action( 'wqv_product_content', 'woocommerce_template_single_title', 5 );
	add_action( 'wqv_product_content', 'woocommerce_template_single_price', 10 );
	add_action( 'wqv_product_content', 'mrzaib_subtitle_line', 15 );
	add_action( 'wqv_product_content', 'woocommerce_template_single_add_to_cart', 25 );
	add_action( 'wqv_product_content', 'mrzaib_qv_gallery_data', 1 );
}, 30 );

/* Slick (shipped with the gallery plugin) on every page so Quick View can use the plugin slider. */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! defined( 'WPGS_ROOT_URL' ) ) {
		return;
	}
	if ( ! wp_script_is( 'slick', 'enqueued' ) ) {
		wp_enqueue_script( 'slick', WPGS_ROOT_URL . 'assets/js/slick.min.js', array( 'jquery' ), defined( 'CIPG_VERSION' ) ? CIPG_VERSION : null, true );
	}
	if ( ! wp_style_is( 'slick', 'enqueued' ) ) {
		wp_enqueue_style( 'slick', WPGS_ROOT_URL . 'assets/css/slick.css', array(), defined( 'CIPG_VERSION' ) ? CIPG_VERSION : null );
	}
}, 95 );

/**
 * Theme gallery (product page + Quick View): main image slider with a vertical thumbnail strip on the left
 * (horizontal under the image on phones). Pure theme code, no gallery plugin needed.
 */
function mrzaib_gallery_html( $product, $extra_class = '' ) {
	if ( ! $product ) {
		return '';
	}
	$ids = array_values( array_unique( array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) ) ) );
	$title = $product->get_name();
	if ( ! $ids ) {
		return '<div class="mzgal is-single ' . esc_attr( $extra_class ) . '"><div class="mzgal-main"><div class="mzgal-track"><div class="mzgal-slide">' . wc_placeholder_img( 'woocommerce_single' ) . '</div></div></div></div>';
	}
	$chev = function ( $d ) {
		return '<button type="button" class="mzgal-arr mzgal-' . $d . '" aria-label="' . ( 'up' === $d ? esc_attr__( 'Previous', 'mrzaib' ) : esc_attr__( 'Next', 'mrzaib' ) ) . '"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="' . ( 'up' === $d ? 'M6 15l6-6 6 6' : 'M6 9l6 6 6-6' ) . '"/></svg></button>';
	};
	$slides = '';
	$thumbs = '';
	foreach ( $ids as $n => $id ) {
		$alt     = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$alt     = $alt ? $alt : $title;
		$slides .= '<div class="mzgal-slide">' . wp_get_attachment_image( $id, 'woocommerce_single', false, array( 'alt' => $alt, 'loading' => $n ? 'lazy' : 'eager', 'class' => 'mzgal-img' ) ) . '</div>';
		$thumbs .= '<button type="button" class="mzgal-t' . ( $n ? '' : ' is-active' ) . '" aria-label="' . esc_attr( sprintf( __( 'Image %d', 'mrzaib' ), $n + 1 ) ) . '">' . wp_get_attachment_image( $id, 'woocommerce_gallery_thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ) . '</button>';
	}
	$multi = count( $ids ) > 1;
	$html  = '<div class="mzgal ' . ( $multi ? '' : 'is-single ' ) . esc_attr( $extra_class ) . '">';
	if ( $multi ) {
		$html .= '<div class="mzgal-thumbs">' . $chev( 'up' ) . '<div class="mzgal-tlist">' . $thumbs . '</div>' . $chev( 'down' ) . '</div>';
	}
	$html .= '<div class="mzgal-main"><div class="mzgal-track">' . $slides . '</div></div></div>';
	return $html;
}

/* Product page: our gallery replaces the WooCommerce / plugin gallery. */
add_filter( 'wpgs_default_gallery_hook', '__return_false' );
add_action( 'wp', function () {
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
	add_action( 'woocommerce_before_single_product_summary', 'mrzaib_pdp_gallery', 20 );
}, 99 );
function mrzaib_pdp_gallery() {
	global $product;
	echo '<div class="mz-pdp-gallery">' . mrzaib_gallery_html( $product, 'mzgal-pdp' ) . '</div>'; // phpcs:ignore
}

/* Missing gallery-plugin option keys caused PHP warnings ("Undefined array key lightbox_img_count / thumbnail_image_size"). */
add_filter( 'option_wpgs_form', function ( $o ) {
	$o = is_array( $o ) ? $o : array();
	return $o + array( 'lightbox_img_count' => '0', 'thumbnail_image_size' => 'woocommerce_gallery_thumbnail' );
} );

/** Quick View: same gallery, placed into the popup's image column by main.js. */
function mrzaib_qv_gallery_data() {
	global $product;
	if ( ! $product ) {
		return;
	}
	echo '<template class="mz-qv-gal">' . mrzaib_gallery_html( $product, 'mzgal-qv' ) . '</template>'; // phpcs:ignore
}

function mrzaib_quickview_details() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$notes = get_post_meta( $product->get_id(), '_mrzaib_notes', true );
	if ( $notes ) {
		echo '<details class="mz-acc" open><summary>' . esc_html__( 'Fragrance Notes', 'mrzaib' ) . '<span class="mz-acc-ico"></span></summary><div class="mz-acc-body"><ul class="mz-notes-list">';
		foreach ( (array) $notes as $label => $val ) {
			echo '<li><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $val ) . '</li>';
		}
		echo '</ul></div></details>';
	}
	$desc = $product->get_short_description();
	if ( $desc ) {
		echo '<details class="mz-acc"><summary>' . esc_html__( 'Description', 'mrzaib' ) . '<span class="mz-acc-ico"></span></summary><div class="mz-acc-body">' . wp_kses_post( wpautop( $desc ) ) . '</div></details>';
	}
}

/* --- Free-shipping progress bar (cart drawer) --- */
function mrzaib_freeship_html() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '<div class="mz-freeship"></div>';
	}
	$rule  = mrzaib_ship_customer_rule();
	$min   = $rule && $rule['free'] ? (float) $rule['free'] : 0;
	$where = $rule ? ' ' . sprintf( __( 'in %s', 'mrzaib' ), $rule['name'] ) : '';
	$total = (float) WC()->cart->get_displayed_subtotal();
	$count = WC()->cart->get_cart_contents_count();
	if ( $min <= 0 || ! $count ) {
		return '<div class="mz-freeship is-empty"></div>';
	}
	$left = max( 0, $min - $total );
	$pct  = min( 100, round( $total / $min * 100 ) );
	$msg  = $left > 0
		/* translators: %s amount left */
		? sprintf( __( 'Add %1$s more for Free Delivery%2$s.', 'mrzaib' ), '<strong>' . wc_price( $left ) . '</strong>', esc_html( $where ) )
		: sprintf( __( 'Congratulations! You have unlocked Free Delivery%s.', 'mrzaib' ), esc_html( $where ) );
	return '<div class="mz-freeship' . ( $left > 0 ? '' : ' is-done' ) . '"><p>' . wp_kses_post( $msg ) . '</p><div class="mz-freeship-track"><span style="width:' . esc_attr( $pct ) . '%"></span></div></div>';
}
add_filter( 'woocommerce_add_to_cart_fragments', function ( $f ) {
	$f['#mz-freeship-holder .mz-freeship'] = mrzaib_freeship_html();
	return $f;
} );
add_action( 'wp_footer', function () {
	echo '<div id="mz-freeship-holder" hidden>' . mrzaib_freeship_html() . '</div>'; // phpcs:ignore
} );

/* --- Gallery plugin: thumbnails left, lightbox on, no zoom/arrows/dots (like the reference) --- */
function mrzaib_configure_gallery_plugin() {
	$o = get_option( 'wpgs_form' );
	$o = is_array( $o ) ? $o : array();
	$o = array_merge( $o, array(
		'slider_animation'                => 'false',
		'slider_lazy_laod'                => 'disable',
		'slider_infinity'                 => '0',
		'slider_adaptiveHeight'           => '1',
		'slider_dragging'                 => '1',
		'slider_autoplay'                 => '0',
		'dots'                            => '0',
		'slider_nav'                      => '0',
		'lightbox_picker'                 => '1',
		'image_zoom'                      => '0',
		'thumb_position'                  => 'left',
		'thumbnails_tabs_thumb_position'  => 'left',
		'thumbnails_mobile_thumb_position' => 'bottom',
		'thumb_to_show'                   => 6,
		'thumb_scroll_by'                 => 1,
		'thumb_nav'                       => '1',
		'thumbnails_layout'               => 'border',
		'thumb_border_non_active_color'   => '#e8e5e0',
		'thumb_border_active_color'       => '#111111',
		'thumbnails_tabs_thumb_to_show'   => 5,
		'thumbnails_tabs_thumb_scroll_by' => 1,
		'thumbnails_mobile_thumb_to_show' => 5,
		'thumbnails_mobile_thumb_scroll_by' => 1,
		'thumbnails_lightbox'             => '0',
		'variation_slide'                 => '1',
		'additional_gallery_load'         => 'if_have_gallery',
	) );
	update_option( 'wpgs_form', $o );
}

/* When free shipping qualifies, hide the paid flat rate so totals match the free-delivery bar. */
add_filter( 'woocommerce_package_rates', function ( $rates ) {
	$free = array();
	foreach ( $rates as $id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $id ] = $rate;
		}
	}
	return $free ? $free : $rates;
}, 100 );
