<?php
/**
 * Star rating under the product title, links to the reviews widget.
 *
 * @package mrzaib
 */
defined( 'ABSPATH' ) || exit;
global $product;
if ( ! wc_review_ratings_enabled() ) {
	return;
}
$count = $product->get_review_count();
$avg   = (float) $product->get_average_rating();
?>
<div class="woocommerce-product-rating mz-rating-link">
	<a href="#reviews" class="mz-rating-trigger" aria-label="<?php echo esc_attr( sprintf( __( '%1$s out of 5 stars based on %2$d reviews. Go to the reviews', 'mrzaib' ), number_format( $avg, 1 ), $count ) ); ?>">
		<?php echo function_exists( 'mrzaib_stars' ) ? mrzaib_stars( $avg, 16 ) : wc_get_rating_html( $avg, $count ); // phpcs:ignore ?>
		<span><?php echo $count ? esc_html( sprintf( _n( '%s Review', '%s Reviews', $count, 'mrzaib' ), number_format_i18n( $count ) ) ) : esc_html__( 'Be the first to review', 'mrzaib' ); ?></span>
	</a>
</div>
