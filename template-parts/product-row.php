<?php
/**
 * Homepage product row: title + View all + products (carousel on mobile).
 * mode: 'new' = newest products first, 'featured' = products marked Featured.
 *
 * @package mrzaib
 */
$title = isset( $args['title'] ) ? $args['title'] : '';
$mode  = isset( $args['mode'] ) ? $args['mode'] : 'new';
$limit = isset( $args['limit'] ) ? (int) $args['limit'] : 5;
$q     = array( 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC' );
$shop  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
if ( 'featured' === $mode ) {
	$q['featured'] = true;
	$link          = add_query_arg( 'mz_featured', '1', $shop );
} else {
	$link = add_query_arg( 'orderby', 'date', $shop );
}
$items = mrzaib_products_query( $q );
if ( ! $items ) {
	return; // e.g. no product marked Featured yet.
}
?>
<section class="section product-row product-row-<?php echo esc_attr( $mode ); ?>">
	<div class="container">
		<div class="row-head">
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<a class="view-all" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'View all', 'mrzaib' ); ?></a>
		</div>
		<div class="product-grid grid-5 scroll-x<?php echo count( $items ) < 4 ? ' is-few' : ''; ?>">
			<?php foreach ( $items as $p ) { mrzaib_product_card( $p ); } ?>
		</div>
	</div>
</section>
