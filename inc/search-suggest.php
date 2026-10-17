<?php
/**
 * Predictive search (Suggestions / Products / Collections) like the reference header.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_mrzaib_suggest', 'mrzaib_suggest' );
add_action( 'wp_ajax_nopriv_mrzaib_suggest', 'mrzaib_suggest' );

function mrzaib_suggest_bold( $text, $q ) {
	$pos = stripos( $text, $q );
	if ( false === $pos ) {
		return '<b>' . esc_html( $text ) . '</b>';
	}
	return '<b>' . esc_html( substr( $text, 0, $pos ) ) . '</b>' . esc_html( substr( $text, $pos, strlen( $q ) ) ) . '<b>' . esc_html( substr( $text, $pos + strlen( $q ) ) ) . '</b>';
}

function mrzaib_suggest() {
	$q = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // phpcs:ignore
	$q = trim( $q );
	if ( '' === $q ) {
		wp_send_json( array( 'html' => '' ) );
	}
	$type = post_type_exists( 'product' ) ? 'product' : 'post';

	$products = get_posts( array( 'post_type' => $type, 'post_status' => 'publish', 's' => $q, 'posts_per_page' => 3, 'suppress_filters' => false ) );

	$terms = array();
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'name__like' => $q, 'hide_empty' => false, 'number' => 3, 'exclude' => array( (int) get_option( 'default_product_cat' ) ) ) );
		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}
	}

	$sugs = array( strtolower( $q ) );
	$pool = array();
	foreach ( $products as $p ) {
		$pool[] = $p->post_title;
	}
	foreach ( array( 'product_tag', 'product_cat' ) as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			$tt = get_terms( array( 'taxonomy' => $tax, 'name__like' => $q, 'hide_empty' => false, 'number' => 6 ) );
			if ( ! is_wp_error( $tt ) ) {
				foreach ( $tt as $t ) {
					$pool[] = $t->name;
				}
			}
		}
	}
	foreach ( $pool as $name ) {
		$n = strtolower( wp_strip_all_tags( $name ) );
		if ( false !== stripos( $n, $q ) && ! in_array( $n, $sugs, true ) ) {
			$sugs[] = $n;
		}
		if ( count( $sugs ) >= 3 ) {
			break;
		}
	}

	ob_start();
	echo '<div class="mzp-sec"><h4>' . esc_html__( 'Suggestions', 'mrzaib' ) . '</h4><ul>';
	foreach ( $sugs as $s ) {
		$url = add_query_arg( array( 's' => $s, 'post_type' => $type ), home_url( '/' ) );
		echo '<li class="mzp-item"><a href="' . esc_url( $url ) . '">' . mrzaib_suggest_bold( $s, $q ) . '</a></li>'; // phpcs:ignore
	}
	echo '</ul></div>';

	if ( $products ) {
		echo '<div class="mzp-sec"><h4>' . esc_html__( 'Products', 'mrzaib' ) . '</h4><ul>';
		foreach ( $products as $p ) {
			$img   = get_the_post_thumbnail_url( $p->ID, 'woocommerce_thumbnail' );
			$price = '';
			if ( function_exists( 'wc_get_product' ) ) {
				$prod = wc_get_product( $p->ID );
				if ( $prod ) {
					if ( $prod->is_type( 'variable' ) && $prod->get_variation_price( 'min' ) !== $prod->get_variation_price( 'max' ) ) {
						$price = '<span class="mzp-from">' . esc_html__( 'from', 'mrzaib' ) . '</span> ' . wc_price( $prod->get_variation_price( 'min', true ) );
					} else {
						$price = wc_price( wc_get_price_to_display( $prod ) );
					}
				}
			}
			echo '<li class="mzp-item mzp-item--image"><a href="' . esc_url( get_permalink( $p ) ) . '">';
			echo $img ? '<img src="' . esc_url( $img ) . '" alt="' . esc_attr( $p->post_title ) . '" width="80" height="80" loading="lazy">' : '<span class="mzp-noimg"></span>';
			echo '<span class="mzp-details"><span class="mzp-title">' . esc_html( $p->post_title ) . '</span><span class="mzp-vendor">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
			if ( $price ) {
				echo '<span class="mzp-price">' . wp_kses_post( $price ) . '</span>';
			}
			echo '</span></a></li>';
		}
		echo '</ul></div>';
	}

	if ( $terms ) {
		echo '<div class="mzp-sec"><h4>' . esc_html__( 'Collections', 'mrzaib' ) . '</h4><ul>';
		foreach ( $terms as $t ) {
			echo '<li class="mzp-item"><a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a></li>';
		}
		echo '</ul></div>';
	}

	$all = add_query_arg( array( 's' => $q, 'post_type' => $type ), home_url( '/' ) );
	/* translators: %s search term */
	echo '<a class="mzp-button" href="' . esc_url( $all ) . '">' . esc_html( sprintf( __( 'Show all results for "%s"', 'mrzaib' ), $q ) ) . '</a>';

	wp_send_json( array( 'html' => ob_get_clean() ) );
}
