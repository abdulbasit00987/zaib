<?php
/**
 * WooCommerce integration.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WooCommerce' ) ) {
	// Helper functions below are declared at compile time and guard themselves.
	return;
}

/* Remove default wrappers/sidebar, add ours */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
add_action( 'woocommerce_before_main_content', function () { echo '<main id="MainContent" class="site-main wc-main"><div class="container">'; }, 10 );
add_action( 'woocommerce_after_main_content', function () { echo '</div></main>'; }, 10 );

/* Shop grid */
add_filter( 'loop_shop_columns', function () { return 4; } );
add_filter( 'loop_shop_per_page', function () { return 24; } );
add_filter( 'woocommerce_output_related_products_args', function ( $a ) { $a['posts_per_page'] = 4; $a['columns'] = 4; return $a; } );
add_filter( 'woocommerce_sale_flash', function () { return '<span class="badge badge-sale">' . esc_html__( 'Sale', 'mrzaib' ) . '</span>'; } );

/* Currency symbol style "Rs." */
add_filter( 'woocommerce_currency_symbol', function ( $s, $c ) { return 'PKR' === $c ? 'Rs.' : $s; }, 10, 2 );

/* Fragrance notes tab */
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	global $product;
	if ( ! $product ) {
		return $tabs;
	}
	$notes = get_post_meta( $product->get_id(), '_mrzaib_notes', true );
	if ( $notes && false ) { // Fragrance Notes accordion removed (user request).
		$tabs['mrzaib_notes'] = array(
			'title'    => __( 'Fragrance Notes', 'mrzaib' ),
			'priority' => 15,
			'callback' => function () use ( $notes ) {
				echo '<div class="notes-grid">';
				foreach ( (array) $notes as $label => $val ) {
					echo '<div class="note"><span>' . esc_html( $label ) . '</span><strong>' . esc_html( $val ) . '</strong></div>';
				}
				echo '</div>';
			},
		);
	}
	return $tabs;
} );

/* Cart count + mini cart fragments */
add_filter( 'woocommerce_add_to_cart_fragments', function ( $f ) {
	ob_start();
	$c = WC()->cart->get_cart_contents_count();
	echo '<span class="cart-count' . ( $c ? '' : ' is-empty' ) . '">' . esc_html( $c ) . '</span>';
	$f['span.cart-count'] = ob_get_clean();
	return $f;
} );

/**
 * Get products for a section.
 */
function mrzaib_products_query( $args = array() ) {
	$defaults = array( 'limit' => 4, 'status' => 'publish', 'orderby' => 'menu_order', 'order' => 'ASC' );
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$args     = wp_parse_args( $args, $defaults );
	if ( ! empty( $args['category'] ) && ! term_exists( $args['category'][0], 'product_cat' ) ) {
		unset( $args['category'] );
	}
	if ( ! empty( $args['mz_top_rated'] ) ) {
		// Best sellers: highest average rating first, then most ratings, then most sales.
		$limit = (int) $args['limit'];
		unset( $args['mz_top_rated'] );
		$args['limit'] = -1;
		$all = wc_get_products( $args );
		usort( $all, function ( $a, $b ) {
			$r = (float) $b->get_average_rating() <=> (float) $a->get_average_rating();
			if ( 0 === $r ) {
				$r = (int) $b->get_rating_count() <=> (int) $a->get_rating_count();
			}
			if ( 0 === $r ) {
				$r = (int) $b->get_total_sales() <=> (int) $a->get_total_sales();
			}
			return $r;
		} );
		return $limit > 0 ? array_slice( $all, 0, $limit ) : $all;
	}
	return wc_get_products( $args );
}

/** Shop filter for the homepage "Featured Collection" View all link: /shop/?mz_featured=1 */
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() || empty( $_GET['mz_featured'] ) || ! taxonomy_exists( 'product_visibility' ) ) { // phpcs:ignore
		return;
	}
	if ( $q->is_post_type_archive( 'product' ) || $q->is_tax( 'product_cat' ) ) {
		$tax   = (array) $q->get( 'tax_query' );
		$tax[] = array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => array( 'featured' ) );
		$q->set( 'tax_query', $tax );
	}
} );

/**
 * Product card (homepage + shop loop).
 */
function mrzaib_product_card( $product ) {
	if ( ! $product || ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	$GLOBALS['product'] = $product;
	$id      = $product->get_id();
	$link    = get_permalink( $id );
	$sub     = get_post_meta( $id, '_mrzaib_subtitle', true );
	$gallery = $product->get_gallery_image_ids();
	$img1    = $product->get_image_id() ? wp_get_attachment_image( $product->get_image_id(), 'woocommerce_thumbnail', false, array( 'class' => 'img-main', 'loading' => 'lazy' ) ) : wc_placeholder_img( 'woocommerce_thumbnail' );
	$img2    = $gallery ? wp_get_attachment_image( $gallery[0], 'woocommerce_thumbnail', false, array( 'class' => 'img-hover', 'loading' => 'lazy' ) ) : '';
	?>
	<div class="product-card<?php echo $img2 ? ' has-hover' : ''; ?>">
		<div class="card-media-wrap">
		<a class="card-media" href="<?php echo esc_url( $link ); ?>">
			<?php echo $img1; // phpcs:ignore ?>
			<?php echo $img2; // phpcs:ignore ?>
			<?php if ( $product->is_on_sale() ) : ?><span class="badge badge-sale"><?php esc_html_e( 'Sale', 'mrzaib' ); ?></span><?php endif; ?>
			<?php if ( ! $product->is_in_stock() ) : ?><span class="badge badge-sold"><?php esc_html_e( 'Sold out', 'mrzaib' ); ?></span><?php endif; ?>
		</a>
			<?php mrzaib_card_icons( $product ); ?>
		</div>
		<div class="card-info">
			<h3 class="card-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
			<?php if ( $sub ) : ?><p class="card-sub"><?php echo esc_html( $sub ); ?></p><?php endif; ?>
			<?php
			$rc = (int) $product->get_review_count();
			if ( $rc > 0 ) :
				$avg = (float) $product->get_average_rating();
				?>
				<div class="card-rating" aria-label="<?php echo esc_attr( sprintf( __( 'Rated %s out of 5', 'mrzaib' ), $avg ) ); ?>">
					<span class="card-stars"><?php for ( $k = 1; $k <= 5; $k++ ) { echo '<span class="' . ( $k <= round( $avg ) ? 'on' : 'off' ) . '">' . mrzaib_icon( 'starf' ) . '</span>'; } // phpcs:ignore ?></span>
					<span class="card-reviews"><?php echo esc_html( sprintf( _n( '%d Review', '%d Reviews', $rc, 'mrzaib' ), $rc ) ); ?></span>
				</div>
			<?php endif; ?>
			<div class="card-price"><?php echo $product->get_price_html(); // phpcs:ignore ?></div>
		</div>
	</div>
	<?php
}

/**
 * Hover icons on product cards (like the reference):
 *  - top: Quick View (Reno QuickView plugin modal)
 *  - bottom: cart. Simple product -> AJAX add + cart drawer.
 *            Variable product -> Quick View modal to choose size, then drawer.
 */
function mrzaib_card_icons( $product ) {
	$id   = $product->get_id();
	$qv   = class_exists( 'SP_Woo_Quick_View' ) && function_exists( 'sp_wqv_get_option' );
	$link = get_permalink( $id );
	echo '<div class="card-icons">';
	if ( $qv ) {
		echo '<button type="button" class="card-icon mz-qv" data-id="' . esc_attr( $id ) . '" aria-label="' . esc_attr__( 'Quick shop', 'mrzaib' ) . '" title="' . esc_attr__( 'Quick shop', 'mrzaib' ) . '">' . mrzaib_icon( 'eye' ) . '</button>'; // phpcs:ignore
		echo '<span class="mz-qv-src" hidden>' . SP_Woo_Quick_View::instance()->sp_wqv_view_button( $id ) . '</span>'; // phpcs:ignore
	} else {
		echo '<a class="card-icon" href="' . esc_url( $link ) . '" aria-label="' . esc_attr__( 'View product', 'mrzaib' ) . '">' . mrzaib_icon( 'eye' ) . '</a>'; // phpcs:ignore
	}
	$cart_icon = mrzaib_icon( 'qcart' );
	if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		echo '<a class="card-icon is-disabled" href="' . esc_url( $link ) . '" aria-label="' . esc_attr__( 'Sold out', 'mrzaib' ) . '">' . $cart_icon . '</a>'; // phpcs:ignore
	} elseif ( $product->is_type( 'simple' ) ) {
		printf(
			'<a href="%s" data-quantity="1" data-product_id="%d" data-product_sku="%s" class="card-icon add_to_cart_button ajax_add_to_cart product_type_simple" rel="nofollow" aria-label="%s" title="%s">%s</a>',
			esc_url( $product->add_to_cart_url() ),
			(int) $id,
			esc_attr( $product->get_sku() ),
			esc_attr__( 'Add to cart', 'mrzaib' ),
			esc_attr__( 'Add to cart', 'mrzaib' ),
			$cart_icon // phpcs:ignore
		);
	} elseif ( $qv && $product->is_type( 'variable' ) ) {
		echo '<button type="button" class="card-icon mz-qv" data-id="' . esc_attr( $id ) . '" aria-label="' . esc_attr__( 'Choose options', 'mrzaib' ) . '" title="' . esc_attr__( 'Choose options', 'mrzaib' ) . '">' . $cart_icon . '</button>'; // phpcs:ignore
	} else {
		echo '<a class="card-icon" href="' . esc_url( $link ) . '" aria-label="' . esc_attr__( 'Choose options', 'mrzaib' ) . '">' . $cart_icon . '</a>'; // phpcs:ignore
	}
	echo '</div>';
}

/* Quick View plugin: we place its trigger in our own card icons, so drop its default button. */
add_action( 'init', function () {
	if ( class_exists( 'SP_Woo_Quick_View' ) ) {
		remove_filter( 'woocommerce_loop_add_to_cart_link', array( SP_Woo_Quick_View::instance(), 'sp_wqv_quick_view_button' ), 15 );
		remove_action( 'woocommerce_after_shop_loop_item', array( SP_Woo_Quick_View::instance(), 'wqvpro_quick_view_button_after_product_price' ), 99 );
	}
}, 20 );

/* Button labels */
add_filter( 'woocommerce_product_add_to_cart_text', function ( $t, $p ) {
	return $p->is_type( 'simple' ) && $p->is_in_stock() ? __( 'Add to cart', 'mrzaib' ) : $t;
}, 10, 2 );

/* Checkout: Pakistan defaults */
add_filter( 'default_checkout_billing_country', function () { return 'PK'; } );

/**
 * Admin: "Subtitle" field in Product data > General (shown under the title on cards, product page, Quick View and cart).
 */
add_action( 'woocommerce_product_options_general_product_data', 'mrzaib_subtitle_field' );
function mrzaib_subtitle_field() {
	if ( ! function_exists( 'woocommerce_wp_text_input' ) ) {
		return;
	}
	echo '<div class="options_group">';
	woocommerce_wp_text_input(
		array(
			'id'          => '_mrzaib_subtitle',
			'label'       => __( 'Subtitle', 'mrzaib' ),
			'placeholder' => __( 'e.g. Perfume for Men | 50ml', 'mrzaib' ),
			'desc_tip'    => true,
			'description' => __( 'Short line shown under the product title. Leave empty to hide it.', 'mrzaib' ),
		)
	);
	global $post;
	$pid  = $post ? $post->ID : 0;
	$usps = ( $pid && metadata_exists( 'post', $pid, '_mrzaib_usps' ) ) ? get_post_meta( $pid, '_mrzaib_usps', true ) : implode( "\n", mrzaib_default_usps() );
	woocommerce_wp_textarea_input(
		array(
			'id'          => '_mrzaib_usps',
			'label'       => __( 'Points under quantity', 'mrzaib' ),
			'value'       => $usps,
			'rows'        => 5,
			'style'       => 'height:110px',
			'desc_tip'    => true,
			'description' => __( 'One point per line, shown with an icon under the quantity counter. Empty = no points.', 'mrzaib' ),
		)
	);
	echo '</div>';
}
add_action( 'woocommerce_admin_process_product_object', 'mrzaib_subtitle_save' );
function mrzaib_subtitle_save( $product ) {
	if ( isset( $_POST['_mrzaib_subtitle'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$product->update_meta_data( '_mrzaib_subtitle', sanitize_text_field( wp_unslash( $_POST['_mrzaib_subtitle'] ) ) ); // phpcs:ignore
	}
	if ( isset( $_POST['_mrzaib_usps'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$product->update_meta_data( '_mrzaib_usps', sanitize_textarea_field( wp_unslash( $_POST['_mrzaib_usps'] ) ) ); // phpcs:ignore
	}
}

/** Default points under the quantity counter. */
function mrzaib_default_usps() {
	return array( '15 Days Easy Returns', '100% Original & Authentic Fragrance', 'Long Lasting Premium Fragrance', 'Cash on Delivery All Over Pakistan' );
}

/* One-time: replace the old '{shipping}' point saved in products with the new default point. */
add_action( 'admin_init', function () {
	if ( get_option( 'mrzaib_usps_v' ) >= 1 ) {
		return;
	}
	global $wpdb;
	$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_mrzaib_usps' AND meta_value LIKE '%{shipping}%'" ); // phpcs:ignore
	foreach ( $rows as $r ) {
		update_post_meta( $r->post_id, '_mrzaib_usps', str_ireplace( '{shipping}', mrzaib_default_usps()[1], $r->meta_value ) );
	}
	update_option( 'mrzaib_usps_v', 1 );
} );
