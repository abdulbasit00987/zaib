<?php
/**
 * Shop loop product card (override).
 *
 * @package mrzaib
 */
defined( 'ABSPATH' ) || exit;
global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<li <?php wc_product_class( 'product-li', $product ); ?>>
	<?php mrzaib_product_card( $product ); ?>
</li>
