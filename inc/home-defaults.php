<?php
/**
 * Default homepage texts/images shown in the Customizer (so admins can see and edit them).
 *
 * @package mrzaib
 */

function mrzaib_seo_default_intro() {
	return '<strong>MR ZAIB Luxury Perfumes</strong> is a Pakistani perfume brand creating long lasting fragrances for men, women and unisex lovers. Our promise is simple: <em>Leave Your Presence</em>. Every 50ml bottle has a gold crown cap, rich layered notes and elegant black-and-gold packaging, and every order can be paid with Cash on Delivery all over Pakistan. Whether you want a fresh everyday signature scent, a bold evening fragrance or a thoughtful gift, MR ZAIB has a scent for you.';
}

function mrzaib_seo_default_more() {
	return '<p>At <strong>MR ZAIB</strong>, we put most of our cost into the perfume oil itself, so you get a high concentration, <strong>super macerated perfume</strong> at an affordable price of Rs. 2,500. We share the full top, middle and base notes of every fragrance, so you can choose a scent that matches your personality before you buy.</p>' .
		'<h3>Kahn-vahl: Long Lasting Perfume for Men</h3>' .
		'<p><a href="' . esc_url( mrzaib_product_link( 'kahn-vahl' ) ) . '">Kahn-vahl</a> is a fresh aquatic perfume for men in a deep blue bottle. Top notes of coconut water, a heart of jasmine and iris, and a warm base of vanilla, sandalwood and amber make it a versatile scent for the office, outings and summer evenings.</p>' .
		'<h3>Arvon: Unisex Perfume for Men and Women</h3>' .
		'<p><a href="' . esc_url( mrzaib_product_link( 'arvon' ) ) . '">Arvon</a> is a bold unisex fragrance in a black bottle. Pineapple, red apple, plum, nutmeg, cinnamon and bitter orange open into magnolia, orchid, lily-of-the-valley, jasmine, Moroccan rose and iris, finishing on cacao, vanilla, white musk, amber, sandalwood and cedar.</p>' .
		'<h3>Shop Perfumes by Category</h3>' .
		'<p>Explore perfumes for <a href="' . esc_url( mrzaib_cat_link( 'men' ) ) . '">Men</a>, <a href="' . esc_url( mrzaib_cat_link( 'women' ) ) . '">Women</a> and <a href="' . esc_url( mrzaib_cat_link( 'unisex' ) ) . '">Unisex</a>, traditional <a href="' . esc_url( mrzaib_cat_link( 'attar' ) ) . '">Attar</a>, and perfume <a href="' . esc_url( mrzaib_cat_link( 'tester' ) ) . '">Testers</a> to try a scent before buying the full bottle.</p>' .
		'<h3>Easy Returns and Cash on Delivery</h3>' .
		'<p>Not sure about a scent? Our 15 days easy returns policy has you covered, and you can pay cash when your order arrives anywhere in Pakistan.</p>' .
		'<p><strong>Buy high quality, long lasting perfumes online in Pakistan at affordable prices.</strong></p>';
}

/** Theme icon file for feature N (1-4). */
function mrzaib_feat_default_icon( $n ) {
	$k = array( 1 => 'fcart', 2 => 'award', 3 => 'box', 4 => 'thumb' );
	return isset( $k[ $n ] ) ? MRZAIB_URI . '/assets/images/icons/feat-' . $k[ $n ] . '.svg' : '';
}

/** Theme demo video / cover for slot N (1-4). */
function mrzaib_video_default( $n, $ext = 'mp4' ) {
	$v = array( 1 => 'kahn-vahl', 2 => 'arvon', 3 => 'mr-zaib-collection', 4 => 'mr-zaib-gift' );
	return isset( $v[ $n ] ) ? MRZAIB_URI . '/assets/video/' . $v[ $n ] . '.' . $ext : '';
}
