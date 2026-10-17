<?php
/**
 * Menu helpers: mega menu detection + fallback menus that mirror the demo structure.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Structure used for fallback menu and demo import.
 */
function mrzaib_menu_structure() {
	return array(
		array( 'Shop All', 'shop', array() ),
		array( 'Men', 'men', array() ),
		array( 'Women', 'women', array() ),
		array( 'Unisex', 'unisex', array() ),
		array( 'Ethereal', 'ethereal', array() ),
		array( 'Tester', 'tester', array() ),
		array( 'Contact', 'page:contact-us', array(
			array( 'Contact Us', 'page:contact-us' ),
			array( 'Track Your Order', 'page:track-order' ),
			array( 'Return Form', 'page:returns' ),
			array( 'FAQs', 'page:faqs' ),
		) ),
	);
}

function mrzaib_resolve_link( $target ) {
	if ( 'shop' === $target ) {
		return mrzaib_shop_link();
	}
	if ( 0 === strpos( $target, 'page:' ) ) {
		return mrzaib_page_link( substr( $target, 5 ) );
	}
	return mrzaib_cat_link( $target );
}

/**
 * Fallback menu (used before a menu is assigned).
 */
function mrzaib_fallback_menu() {
	echo '<ul id="primary-menu" class="menu">';
	foreach ( mrzaib_menu_structure() as $top ) {
		$has_mega = false;
		foreach ( $top[2] as $c ) {
			if ( ! empty( $c[2] ) ) {
				$has_mega = true;
			}
		}
		$cls = 'menu-item' . ( $top[2] ? ' menu-item-has-children' : '' ) . ( $has_mega ? ' mega-menu' : '' );
		echo '<li class="' . esc_attr( $cls ) . '"><a href="' . esc_url( mrzaib_resolve_link( $top[1] ) ) . '">' . esc_html( $top[0] ) . '</a>';
		if ( $top[2] ) {
			echo '<ul class="sub-menu">';
			foreach ( $top[2] as $c ) {
				$ccls = 'menu-item' . ( ! empty( $c[2] ) ? ' menu-item-has-children' : '' );
				echo '<li class="' . esc_attr( $ccls ) . '"><a href="' . esc_url( mrzaib_resolve_link( $c[1] ) ) . '">' . esc_html( $c[0] ) . '</a>';
				if ( ! empty( $c[2] ) ) {
					echo '<ul class="sub-menu">';
					foreach ( $c[2] as $g ) {
						echo '<li class="menu-item"><a href="' . esc_url( mrzaib_resolve_link( $g[1] ) ) . '">' . esc_html( $g[0] ) . '</a></li>';
					}
					echo '</ul>';
				}
				echo '</li>';
			}
			echo '</ul>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Add .mega-menu to top-level items that have grandchildren.
 */
function mrzaib_mega_class( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}
	$parents = array();
	$by_id   = array();
	foreach ( $items as $it ) {
		$by_id[ $it->ID ] = $it;
	}
	foreach ( $items as $it ) {
		if ( $it->menu_item_parent && isset( $by_id[ $it->menu_item_parent ] ) ) {
			$parent = $by_id[ $it->menu_item_parent ];
			if ( $parent->menu_item_parent ) {
				$parents[ (int) $parent->menu_item_parent ] = true;
			}
		}
	}
	foreach ( $items as $it ) {
		if ( isset( $parents[ $it->ID ] ) ) {
			$it->classes[] = 'mega-menu';
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'mrzaib_mega_class', 10, 2 );

/**
 * Simple footer list fallback.
 */
function mrzaib_footer_menu( $location, $fallback ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu( array( 'theme_location' => $location, 'container' => false, 'menu_class' => 'footer-links', 'depth' => 1 ) );
		return;
	}
	echo '<ul class="footer-links">';
	foreach ( $fallback as $label => $target ) {
		echo '<li><a href="' . esc_url( mrzaib_resolve_link( $target ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Desktop: category items without children show a product preview dropdown
 * ("Explore Gift Box Collection →" + 4 products), like the reference store.
 */
function mrzaib_menu_product_preview( $output, $item, $depth, $args ) {
	if ( 0 !== $depth || empty( $args->menu_id ) || 'primary-menu' !== $args->menu_id ) {
		return $output;
	}
	if ( 'taxonomy' !== $item->type || 'product_cat' !== $item->object || ! function_exists( 'wc_get_products' ) ) {
		return $output;
	}
	if ( in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		return $output;
	}
	$term = get_term( (int) $item->object_id, 'product_cat' );
	if ( ! $term || is_wp_error( $term ) ) {
		return $output;
	}
	$products = wc_get_products( array( 'limit' => 4, 'status' => 'publish', 'category' => array( $term->slug ), 'orderby' => 'menu_order', 'order' => 'ASC' ) );
	if ( ! $products ) {
		return $output;
	}
	$html  = '<div class="menu-preview"><div class="container"><div class="mp-head"><a href="' . esc_url( get_term_link( $term ) ) . '">' . sprintf( esc_html__( 'Explore %s Collection →', 'mrzaib' ), esc_html( $term->name ) ) . '</a><span>' . esc_html( $term->name ) . '</span></div><div class="mp-grid">';
	foreach ( $products as $p ) {
		$html .= '<a class="mp-item" href="' . esc_url( get_permalink( $p->get_id() ) ) . '">' . $p->get_image( 'woocommerce_thumbnail' ) . ( $p->is_on_sale() ? '<span class="badge badge-sale">Sale</span>' : '' ) . '<span class="mp-name">' . esc_html( $p->get_name() ) . '</span><span class="mp-price">' . $p->get_price_html() . '</span></a>';
	}
	$html .= '</div></div></div>';
	return $output . $html;
}
add_filter( 'walker_nav_menu_start_el', 'mrzaib_menu_product_preview', 10, 4 );

/* Mark those items so CSS can position the preview */
add_filter( 'nav_menu_css_class', function ( $classes, $item, $args, $depth ) {
	if ( 0 === $depth && ! empty( $args->menu_id ) && 'primary-menu' === $args->menu_id && 'taxonomy' === $item->type && 'product_cat' === $item->object && ! in_array( 'menu-item-has-children', $classes, true ) ) {
		$classes[] = 'has-preview';
	}
	return $classes;
}, 10, 4 );
