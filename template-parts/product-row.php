<?php
/**
 * Homepage product row: title + View all + products (carousel on mobile).
 *
 * @package mrzaib
 */
$title = isset( $args['title'] ) ? $args['title'] : '';
$slug  = isset( $args['slug'] ) ? $args['slug'] : '';
$limit = isset( $args['limit'] ) ? (int) $args['limit'] : 5;
$q     = array( 'limit' => $limit, 'category' => array( $slug ) );
if ( ! empty( $args['offset_order'] ) ) {
	$q['orderby'] = 'date';
	$q['order']   = 'ASC';
}
$items = mrzaib_products_query( $q );
if ( ! $items ) {
	return;
}
?>
<section class="section product-row">
	<div class="container">
		<div class="row-head">
			<h2 class="section-title"><?php echo esc_html( $title ); ?></h2>
			<a class="view-all" href="<?php echo esc_url( ( $slug ? mrzaib_cat_link( $slug ) : mrzaib_shop_link() ) ); ?>"><?php esc_html_e( 'View all', 'mrzaib' ); ?></a>
		</div>
		<div class="product-grid grid-5 scroll-x<?php echo count( $items ) < 4 ? ' is-few' : ''; ?>">
			<?php foreach ( $items as $p ) { mrzaib_product_card( $p ); } ?>
		</div>
	</div>
</section>
