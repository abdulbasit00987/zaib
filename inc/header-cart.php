<?php
/**
 * Header cart dropdown (copied from the reference store's header cart):
 * slides in from the right just below the header, white panel ~450px, no overlay,
 * item rows (image, title, variant, qty stepper, price), free-delivery bar,
 * subtotal, full-width Check out button, small taxes note. No gift options.
 *
 * @package MRZAIB
 */

defined( 'ABSPATH' ) || exit;

function mrzaib_hcart_inner() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '<div class="mz-hcart-inner"></div>';
	}
	ob_start();
	$cart = WC()->cart;
	echo '<div class="mz-hcart-inner">';
	if ( $cart->is_empty() ) {
		echo '<div class="mzc-empty"><p>' . esc_html__( 'Your cart is currently empty.', 'mrzaib' ) . '</p><a class="btn mzc-btn" href="' . esc_url( mrzaib_shop_link() ) . '">' . esc_html__( 'Continue shopping', 'mrzaib' ) . '</a></div>';
	} else {
		echo '<div class="mzc-items">';
		foreach ( $cart->get_cart() as $key => $item ) {
			$p = $item['data'];
			if ( ! $p || ! $p->exists() || $item['quantity'] <= 0 ) {
				continue;
			}
			$link  = $p->is_visible() ? $p->get_permalink( $item ) : '';
			$name  = $p->get_parent_id() ? get_the_title( $p->get_parent_id() ) : $p->get_name();
			$meta  = '';
			$subt  = get_post_meta( $p->get_parent_id() ? $p->get_parent_id() : $p->get_id(), '_mrzaib_subtitle', true );
			if ( $subt ) {
				$meta .= '<p class="mzc-subtitle">' . esc_html( $subt ) . '</p>';
			}
			if ( ! empty( $item['variation'] ) ) {
				foreach ( $item['variation'] as $attr => $val ) {
					if ( '' === $val ) {
						continue;
					}
					$tax   = str_replace( 'attribute_', '', $attr );
					$label = wc_attribute_label( $tax, $p );
					if ( taxonomy_exists( $tax ) ) {
						$t   = get_term_by( 'slug', $val, $tax );
						$val = $t ? $t->name : $val;
					}
					$meta .= '<p class="mzc-meta"><strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $val ) . '</p>';
				}
			}
			echo '<div class="mzc-item" data-key="' . esc_attr( $key ) . '">';
			echo '<a class="mzc-img" href="' . esc_url( $link ) . '">' . $p->get_image( 'woocommerce_thumbnail' ) . '</a>'; // phpcs:ignore
			echo '<div class="mzc-info"><a class="mzc-title" href="' . esc_url( $link ) . '">' . esc_html( $name ) . '</a>' . $meta; // phpcs:ignore
			echo '<div class="mzc-row"><div class="mzc-qty"><button type="button" class="mzc-q" data-d="-1" aria-label="' . esc_attr__( 'Decrease quantity', 'mrzaib' ) . '"><svg viewBox="0 0 20 20" width="12" height="12"><path d="M3 10h14" stroke="currentColor" stroke-width="1.5"/></svg></button><input type="number" min="0" value="' . esc_attr( $item['quantity'] ) . '" aria-label="' . esc_attr__( 'Quantity', 'mrzaib' ) . '"><button type="button" class="mzc-q" data-d="1" aria-label="' . esc_attr__( 'Increase quantity', 'mrzaib' ) . '"><svg viewBox="0 0 20 20" width="12" height="12"><path d="M3 10h14M10 3v14" stroke="currentColor" stroke-width="1.5"/></svg></button></div>';
			echo '<span class="mzc-price">' . $cart->get_product_subtotal( $p, $item['quantity'] ) . '</span></div>'; // phpcs:ignore
			echo '</div></div>';
		}
		echo '</div><div class="mzc-foot">';
		echo function_exists( 'mrzaib_freeship_html' ) ? mrzaib_freeship_html() : ''; // phpcs:ignore
		echo '<div class="mzc-sub"><span>' . esc_html__( 'Subtotal', 'mrzaib' ) . '</span><span class="mzc-price">' . $cart->get_cart_subtotal() . '</span></div>'; // phpcs:ignore
		echo '<a class="btn mzc-btn mzc-checkout" href="' . esc_url( wc_get_checkout_url() ) . '">' . esc_html__( 'Check out', 'mrzaib' ) . '</a>';
		echo '<p class="mzc-note">' . esc_html__( 'Taxes included. Shipping and discount codes calculated at checkout.', 'mrzaib' ) . '</p>';
		echo '</div>';
	}
	echo '</div>';
	return ob_get_clean();
}

add_filter( 'woocommerce_add_to_cart_fragments', function ( $f ) {
	$f['div.mz-hcart-inner'] = mrzaib_hcart_inner();
	return $f;
} );

/* Quantity change from the dropdown (0 removes) -> refreshed fragments. */
function mrzaib_hcart_qty() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		wp_send_json_error();
	}
	$key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$qty = isset( $_POST['qty'] ) ? max( 0, (int) $_POST['qty'] ) : 0;
	if ( $key && WC()->cart->get_cart_item( $key ) ) {
		if ( $qty ) {
			WC()->cart->set_quantity( $key, $qty, true );
		} else {
			WC()->cart->remove_cart_item( $key );
		}
	}
	WC_AJAX::get_refreshed_fragments();
}
add_action( 'wp_ajax_mrzaib_hcart_qty', 'mrzaib_hcart_qty' );
add_action( 'wp_ajax_nopriv_mrzaib_hcart_qty', 'mrzaib_hcart_qty' );

/* Non-AJAX add to cart (page reload): open the header cart on the next page view. */
add_action( 'woocommerce_add_to_cart', function () {
	if ( function_exists( 'WC' ) && WC()->session && ! wp_doing_ajax() ) {
		WC()->session->set( 'mrzaib_open_cart', 1 );
	}
} );
add_action( 'wp_footer', function () {
	if ( function_exists( 'WC' ) && WC()->session && WC()->session->get( 'mrzaib_open_cart' ) ) {
		WC()->session->set( 'mrzaib_open_cart', null );
		echo '<script>window.addEventListener("load",function(){setTimeout(function(){if(window.mrzaibOpenCart)window.mrzaibOpenCart();},300);});</script>';
	}
}, 99 );

/* Bust WooCommerce's sessionStorage fragment cache whenever the header cart markup changes,
   otherwise old cart HTML is restored on page load until the cart changes. */
function mrzaib_hcart_cache_ver() {
	return substr( md5( (string) filemtime( __FILE__ ) . MRZAIB_VERSION ), 0, 8 );
}
add_filter( 'woocommerce_cart_fragment_name', function ( $n ) { return $n . '_' . mrzaib_hcart_cache_ver(); } );
add_filter( 'woocommerce_cart_hash_key', function ( $n ) { return $n . '_' . mrzaib_hcart_cache_ver(); } );

/* Product page: no "has been added to your cart" notice above the gallery (the header cart opens instead). */
add_action( 'woocommerce_before_single_product', function () {
	if ( ! function_exists( 'wc_get_notices' ) || ! WC()->session ) {
		return;
	}
	$all = wc_get_notices();
	if ( empty( $all['success'] ) ) {
		return;
	}
	$all['success'] = array_values( array_filter( $all['success'], function ( $n ) {
		$msg = is_array( $n ) ? ( $n['notice'] ?? '' ) : (string) $n;
		return false === strpos( $msg, 'added_to_cart' ) && false === stripos( wp_strip_all_tags( $msg ), 'added to your cart' );
	} ) );
	wc_set_notices( $all );
}, 1 );
