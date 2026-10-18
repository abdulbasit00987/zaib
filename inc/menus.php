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
		$auto = '';
		if ( ! $top[2] && 'shop' !== $top[1] && 0 !== strpos( $top[1], 'page:' ) && taxonomy_exists( 'product_cat' ) ) {
			$t = get_term_by( 'slug', $top[1], 'product_cat' );
			if ( $t ) {
				$auto = mrzaib_cat_children_menu( $t->term_id );
			}
		}
		$cls = 'menu-item' . ( ( $top[2] || $auto ) ? ' menu-item-has-children' : '' ) . ( $has_mega ? ' mega-menu' : '' );
		echo '<li class="' . esc_attr( $cls ) . '"><a href="' . esc_url( mrzaib_resolve_link( $top[1] ) ) . '">' . esc_html( $top[0] ) . '</a>' . $auto;
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
 * Desktop: a product-category menu item with no manual children shows its
 * WooCommerce sub-categories (if any) as a normal dropdown. No product preview.
 */
function mrzaib_cat_children_menu( $term_id ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return '';
	}
	$kids = get_terms( array( 'taxonomy' => 'product_cat', 'parent' => (int) $term_id, 'hide_empty' => false, 'orderby' => 'menu_order' ) );
	if ( ! $kids || is_wp_error( $kids ) ) {
		return '';
	}
	$html = '<ul class="sub-menu">';
	foreach ( $kids as $k ) {
		$html .= '<li class="menu-item"><a href="' . esc_url( get_term_link( $k ) ) . '">' . esc_html( $k->name ) . '</a></li>';
	}
	return $html . '</ul>';
}

function mrzaib_menu_item_is_auto_cat( $item, $args, $depth, $classes ) {
	return 0 === $depth && ! empty( $args->menu_id ) && 'primary-menu' === $args->menu_id && 'taxonomy' === $item->type && 'product_cat' === $item->object && ! in_array( 'menu-item-has-children', (array) $classes, true );
}

add_filter( 'walker_nav_menu_start_el', function ( $output, $item, $depth, $args ) {
	if ( mrzaib_menu_item_is_auto_cat( $item, $args, $depth, $item->classes ) ) {
		$output .= mrzaib_cat_children_menu( $item->object_id );
	}
	return $output;
}, 10, 4 );

add_filter( 'nav_menu_css_class', function ( $classes, $item, $args, $depth ) {
	if ( mrzaib_menu_item_is_auto_cat( $item, $args, $depth, $classes ) && mrzaib_cat_children_menu( $item->object_id ) ) {
		$classes[] = 'menu-item-has-children';
	}
	return $classes;
}, 10, 4 );
