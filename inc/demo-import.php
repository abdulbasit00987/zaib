<?php
/**
 * One-click demo import: Appearance > MR ZAIB Setup
 * Creates categories, dummy products (with images), pages, blog posts, menus,
 * sets the homepage, PKR currency and Cash on Delivery.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	add_theme_page( __( 'MR ZAIB Setup', 'mrzaib' ), __( 'MR ZAIB Setup', 'mrzaib' ), 'manage_options', 'mrzaib-setup', 'mrzaib_setup_page' );
} );

/* Nudge after activation */
add_action( 'after_switch_theme', function () {
	set_transient( 'mrzaib_activated', 1, 300 );
} );
add_action( 'admin_notices', function () {
	if ( get_transient( 'mrzaib_activated' ) && ! get_option( 'mrzaib_demo_imported' ) ) {
		echo '<div class="notice notice-success is-dismissible"><p><strong>MR ZAIB theme activated!</strong> ';
		echo 'Go to <a href="' . esc_url( admin_url( 'themes.php?page=mrzaib-setup' ) ) . '">Appearance &rarr; MR ZAIB Setup</a> to import the demo website in one click.</p></div>';
	}
} );

/**
 * Install + activate the "Advanced Woo Search" plugin (used for the header search).
 */
function mrzaib_install_aws() {
	$file = 'advanced-woo-search/advanced-woo-search.php';
	if ( ! current_user_can( 'install_plugins' ) ) {
		return 'No permission to install plugins.';
	}
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( ! file_exists( WP_PLUGIN_DIR . '/' . $file ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$api = plugins_api( 'plugin_information', array( 'slug' => 'advanced-woo-search', 'fields' => array( 'sections' => false ) ) );
		if ( is_wp_error( $api ) ) {
			return 'Could not reach WordPress.org: ' . $api->get_error_message();
		}
		$up  = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$res = $up->install( $api->download_link );
		if ( ! $res || is_wp_error( $res ) ) {
			return 'Advanced Woo Search could not be installed automatically. Install it from Plugins > Add New.';
		}
	}
	if ( ! is_plugin_active( $file ) ) {
		$act = activate_plugin( $file );
		if ( is_wp_error( $act ) ) {
			return 'Activation failed: ' . $act->get_error_message();
		}
	}
	mrzaib_configure_aws();
	// Build the search index so existing products show up in results.
	if ( class_exists( 'AWS_Table' ) ) {
		delete_transient( 'aws_index_processed' );
		delete_option( 'aws_cron_job' );
		$t = new AWS_Table();
		if ( method_exists( $t, 'reindex_table_job' ) ) {
			$t->reindex_table_job();
		}
	}
	return 'Advanced Woo Search installed and active (header search uses it).';
}

/** Sensible AWS defaults: product images, prices, "View all results". */
function mrzaib_configure_aws() {
	$o = get_option( 'aws_settings' );
	if ( ! is_array( $o ) ) {
		return;
	}
	$o['search_field_text'] = 'Search';
	$o['show_image']        = 'true';
	$o['show_price']        = 'true';
	$o['show_more']         = 'true';
	$o['show_page']         = 'true';
	$o['show_clear']        = 'true';
	$o['results_num']       = '6';
	update_option( 'aws_settings', $o );
}

/**
 * Turn a few demo perfumes into variable products (Size: 50ml / 100ml) so the
 * card cart icon opens the Quick View modal to choose a size, like the reference.
 */
function mrzaib_make_variable_demo() {
	if ( ! class_exists( 'WC_Product_Variable' ) ) {
		return '';
	}
	$names = array( 'Royal Oud', 'Midnight Code', 'Rose Velvet', 'Amber Night' );
	$n     = 0;
	foreach ( $names as $name ) {
		$ids = get_posts( array( 'post_type' => 'product', 'title' => $name, 'numberposts' => 1, 'fields' => 'ids', 'post_status' => 'any' ) );
		if ( ! $ids ) {
			continue;
		}
		$pid = $ids[0];
		$old = wc_get_product( $pid );
		if ( ! $old || $old->is_type( 'variable' ) ) {
			continue;
		}
		$price = (int) $old->get_regular_price();
		$sale  = (int) $old->get_sale_price();
		wp_set_object_terms( $pid, 'variable', 'product_type' );
		wc_delete_product_transients( $pid );
		$prod = new WC_Product_Variable( $pid );
		$att  = new WC_Product_Attribute();
		$att->set_name( 'Size' );
		$att->set_options( array( '50ml', '100ml' ) );
		$att->set_visible( true );
		$att->set_variation( true );
		$prod->set_attributes( array( $att ) );
		$prod->save();
		foreach ( array( '50ml' => 0.6, '100ml' => 1 ) as $size => $f ) {
			$v = new WC_Product_Variation();
			$v->set_parent_id( $pid );
			$v->set_attributes( array( 'size' => $size ) );
			$v->set_regular_price( (string) ( round( $price * $f / 10 ) * 10 ) );
			if ( $sale ) {
				$v->set_sale_price( (string) ( round( $sale * $f / 10 ) * 10 ) );
			}
			$v->set_stock_status( 'instock' );
			$v->save();
		}
		WC_Product_Variable::sync( $pid );
		$n++;
	}
	return $n . ' demo products given sizes (50ml / 100ml variations).';
}

/**
 * Install + activate any wordpress.org plugin by slug.
 */
function mrzaib_install_plugin( $slug, $file, $label ) {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return 'No permission to install plugins.';
	}
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( ! file_exists( WP_PLUGIN_DIR . '/' . $file ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		$api = plugins_api( 'plugin_information', array( 'slug' => $slug, 'fields' => array( 'sections' => false ) ) );
		if ( is_wp_error( $api ) ) {
			return $label . ': could not reach WordPress.org (' . $api->get_error_message() . ').';
		}
		$up  = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$res = $up->install( $api->download_link );
		if ( ! $res || is_wp_error( $res ) ) {
			return $label . ' could not be installed automatically. Install it from Plugins > Add New.';
		}
	}
	if ( ! is_plugin_active( $file ) ) {
		$act = activate_plugin( $file );
		if ( is_wp_error( $act ) ) {
			return $label . ' activation failed: ' . $act->get_error_message();
		}
	}
	return $label . ' installed and active.';
}

/** Quick View + Mini Cart Drawer, configured for the product-card icons. */
function mrzaib_install_card_plugins() {
	$out   = array();
	$out[] = mrzaib_install_plugin( 'woo-quickview', 'woo-quickview/woo-quick-view.php', 'Quick View for WooCommerce (Reno QuickView)' );
	// Header cart is theme-native now; Mini Cart Drawer plugin no longer installed.
	if ( false ) $out[] = mrzaib_install_plugin( 'woo-mini-cart-drawer', 'woo-mini-cart-drawer/woo-mini-cart-drawer.php', 'Mini Cart Drawer for WooCommerce' );
	$out[] = mrzaib_install_plugin( 'wordpress-seo', 'wordpress-seo/wp-seo.php', 'Yoast SEO' );
	$out[] = mrzaib_install_plugin( 'woo-product-gallery-slider', 'woo-product-gallery-slider/woo-product-gallery-slider.php', 'Product Gallery Slider, Additional Variation Images for WooCommerce' );
	if ( function_exists( 'mrzaib_configure_gallery_plugin' ) ) {
		mrzaib_configure_gallery_plugin();
	}
	$o = get_option( '_sp_wqvpro_options' );
	$o = is_array( $o ) ? $o : array();
	$o['wqvpro_enable_quick_view']   = true;
	$o['wqvpro_aj_add_to_cart']      = true;   // add to cart inside the modal via AJAX
	$o['wqvpro_redirect_after_cart'] = 'same_page';
	$o['wqvpro_popup_close_button']  = true;
	$o['wqvpro_view_details_button'] = true;
	update_option( '_sp_wqvpro_options', $o );
	// Mini Cart Drawer: slide in from the right, show Checkout + View Cart, brand text.
	$key = 'woo-mini-cart-drawer_o_mcn_settings';
	$mc  = get_option( $key );
	$mc  = is_array( $mc ) ? $mc : array();
	$mc  = array_merge( array(
		'drawer_type' => 'D', 'skin' => 'DF', 'color' => 'default', 'icon' => 'ap-cart', 'df_type' => 'TA', 'df_amount_type' => 'S',
		'hide_in_cart' => 'N', 'hide_in_checkout' => 'N', 'hide_in_empty_item' => 'N', 'not_hide_in_undo' => 'N', 'dont_wc_add_msg' => 'Y',
		'add_menu_item' => 'N', 'control_size' => 60, 'control_icon_size' => 40, 'border_radius' => 50, 'top_margin' => 30, 'left_margin' => 30,
		'cart_container_size' => 100, 'cart_container_width' => 420, 'hide_corner_circle' => 'N', 'circle_type' => 'Q',
		'is_undo_remove' => 'Y', 'is_show_dis_total' => 'Y', 'is_show_all_fee' => 'Y', 'is_show_cpn' => 'N', 'is_show_acpn' => 'N',
		'shadow_opacity' => 35, 'cart_shadow_opacity' => 35, 'cart_border_radius' => 0, 'empty_cart_icon' => 'ap-empty-cart-2',
	), $mc, array(
		'position'          => 'RM',
		'dr_anim'           => 'ape-slideInRight',
		'dr_anim_out'       => 'ape-slideOutRight',
		'title_text'        => 'Your Cart',
		'empty_txt'         => 'Your cart is empty',
		'is_cart_btn'       => 'Y',
		'cart_btn_text'     => 'View Cart',
		'is_checkout_btn'   => 'Y',
		'checkout_btn_text' => 'Checkout',
	) );
	update_option( $key, $mc );
	return implode( ' ', $out );
}

function mrzaib_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$done = false;
	$log  = array();
	if ( isset( $_POST['mrzaib_woo'] ) && check_admin_referer( 'mrzaib_woo' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore
		echo '<div class="notice notice-info"><p>' . esc_html( mrzaib_install_plugin( 'woocommerce', 'woocommerce/woocommerce.php', 'WooCommerce' ) ) . '</p></div>';
		echo '<script>setTimeout(function(){location.href=' . wp_json_encode( admin_url( 'themes.php?page=mrzaib-setup' ) ) . ';},1500);</script>';
	}
	if ( isset( $_POST['mrzaib_yoast_apply'] ) && check_admin_referer( 'mrzaib_yoast_apply' ) ) {
		echo '<div class="notice notice-success"><p>' . esc_html( mrzaib_configure_yoast() ) . '</p></div>';
	}
	if ( defined( 'WPSEO_VERSION' ) ) {
		echo '<form method="post" style="margin:12px 0">';
		wp_nonce_field( 'mrzaib_yoast_apply' );
		echo '<p><button class="button" name="mrzaib_yoast_apply" value="1">' . esc_html__( 'Apply MR ZAIB Yoast SEO settings', 'mrzaib' ) . '</button> <span class="description">' . esc_html__( 'Fills only empty SEO titles, descriptions and keyphrases; your edits are kept.', 'mrzaib' ) . '</span></p></form>';
	}
	if ( isset( $_POST['mrzaib_cardplugins'] ) && check_admin_referer( 'mrzaib_cardplugins' ) ) {
		echo '<div class="notice notice-info"><p>' . esc_html( mrzaib_install_card_plugins() ) . '</p></div>';
	}
	if ( isset( $_POST['mrzaib_aws'] ) && check_admin_referer( 'mrzaib_aws' ) ) {
		echo '<div class="notice notice-info"><p>' . esc_html( mrzaib_install_aws() ) . '</p></div>';
	}
	if ( isset( $_POST['mrzaib_import'] ) && check_admin_referer( 'mrzaib_import' ) ) {
		@set_time_limit( 600 ); // phpcs:ignore
		$log  = mrzaib_run_import();
		$done = true;
	}
	$woo = class_exists( 'WooCommerce' );
	?>
	<div class="wrap">
		<h1>MR ZAIB &mdash; Store Setup</h1>
		<p style="font-size:15px;max-width:720px">This removes all old dummy data (demo perfumes, demo categories, demo blog posts, demo images and menus) and sets up your real MR ZAIB store: categories <strong>Men, Women, Unisex, Attar, Tester</strong>, your perfumes <strong>Kahn-vahl</strong> (Men) and <strong>Arvon</strong> (Unisex) with photos, notes, descriptions and SEO keywords, the homepage banners, pages and menus. It also sets PKR currency and Cash on Delivery. Products you added yourself are not touched.</p>
		<?php if ( ! $woo ) : ?>
			<div class="notice notice-error inline"><p><strong>WooCommerce is required.</strong> Install &amp; activate it first: <a href="<?php echo esc_url( admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' ) ); ?>">Plugins &rarr; Add New &rarr; WooCommerce</a>. Then come back here.</p></div>
			<form method="post"><?php wp_nonce_field( 'mrzaib_woo' ); ?><p><button class="button button-primary" name="mrzaib_woo" value="1">Install &amp; Activate WooCommerce</button></p></form>
		<?php endif; ?>
		<?php if ( $done ) : ?>
			<div class="notice notice-success inline"><p><strong>Store set up!</strong> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View your website &rarr;</a></p></div>
			<ul style="background:#fff;padding:12px 24px;max-width:720px;list-style:disc"><?php foreach ( $log as $l ) { echo '<li>' . esc_html( $l ) . '</li>'; } ?></ul>
		<?php endif; ?>
		<?php if ( get_option( 'mrzaib_demo_imported' ) && ! $done ) : ?>
			<p><em>Already run once. Running again is safe: it removes any leftover dummy data and refreshes Kahn-vahl and Arvon (prices, notes, descriptions).</em></p>
		<?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'mrzaib_import' ); ?>
			<p><button class="button button-primary button-hero" name="mrzaib_import" value="1" <?php disabled( ! $woo ); ?>>Set Up MR ZAIB Store (remove dummy data)</button></p>
		</form>
		<h2>Header search: Advanced Woo Search</h2>
		<?php if ( function_exists( 'aws_get_search_form' ) ) : ?>
			<p>&#10004; <strong>Advanced Woo Search is active.</strong> The search box left of the logo shows live product results with images and prices. Settings: <a href="<?php echo esc_url( admin_url( 'admin.php?page=aws-options' ) ); ?>">Advanced Woo Search &rarr; Settings</a>.</p>
		<?php else : ?>
			<p>The search box left of the logo uses the free <strong>Advanced Woo Search</strong> plugin for live results with product images. (The demo import installs it too.)</p>
			<form method="post"><?php wp_nonce_field( 'mrzaib_aws' ); ?><p><button class="button button-secondary" name="mrzaib_aws" value="1">Install &amp; Activate Advanced Woo Search</button></p></form>
		<?php endif; ?>
		<h2>Quick View, Cart Drawer &amp; Product Gallery plugins</h2>
		<?php $qv = class_exists( 'SP_Woo_Quick_View' ); $mc = class_exists( '\Minicart_Lite\Modules\MCN_Settings' ) || is_plugin_active( 'woo-mini-cart-drawer/woo-mini-cart-drawer.php' ); ?>
		<p>Hovering a product shows two icons: the top one opens <strong>Quick View for WooCommerce (Reno QuickView)</strong>, the bottom cart icon adds simple products straight to the <strong>Mini Cart Drawer for WooCommerce</strong>; products with sizes open the Quick View to choose a size first, then the cart drawer opens.</p>
		<p>Quick View: <?php echo $qv ? '&#10004; active' : '&#10008; not active'; ?> &nbsp; | &nbsp; Mini Cart Drawer: <?php echo $mc ? '&#10004; active' : '&#10008; not active'; ?> &nbsp; | &nbsp; Product Gallery Slider: <?php echo defined( 'CIPG_VERSION' ) ? '&#10004; active' : '&#10008; not active'; ?></p>
		<form method="post"><?php wp_nonce_field( 'mrzaib_cardplugins' ); ?><p><button class="button button-secondary" name="mrzaib_cardplugins" value="1">Install, Activate &amp; Configure all 3 plugins</button></p></form>
		<h2>After import</h2>
		<ol style="font-size:14px">
			<li>Appearance &rarr; Customize &rarr; Site Identity: upload your logo.</li>
			<li>Appearance &rarr; Customize &rarr; MR ZAIB Options: announcement bar, hero slides, WhatsApp, phone, address, social links.</li>
			<li>Products &rarr; edit each product: real name, price, photos. Field &ldquo;Subtitle&rdquo; is the small line under the name.</li>
			<li>WooCommerce &rarr; Settings &rarr; Payments: Cash on Delivery is on. Add JazzCash / Easypaisa / card plugins if you want.</li>
			<li>WooCommerce &rarr; Settings &rarr; Shipping: zones &ldquo;Karachi&rdquo; and &ldquo;Rest of Pakistan&rdquo; are created automatically; edit their charges there and the site text updates.</li>
		</ol>
	</div>
	<?php
}

/* ------------------------------------------------------------------ */

function mrzaib_post_exists( $title, $type ) {
	$q = get_posts( array( 'post_type' => $type, 'title' => $title, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	return ! empty( $q );
}

function mrzaib_import_image( $file ) {
	$map = get_option( 'mrzaib_demo_media', array() );
	if ( isset( $map[ $file ] ) && get_post( $map[ $file ] ) ) {
		return (int) $map[ $file ];
	}
	$path = MRZAIB_DIR . '/assets/images/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$upload = wp_upload_bits( 'mrzaib-' . $file, null, file_get_contents( $path ) ); // phpcs:ignore
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}
	$type = wp_check_filetype( $upload['file'] );
	$id   = wp_insert_attachment( array(
		'post_mime_type' => $type['type'],
		'post_title'     => sanitize_title( pathinfo( $file, PATHINFO_FILENAME ) ),
		'post_status'    => 'inherit',
	), $upload['file'] );
	if ( $id && ! is_wp_error( $id ) ) {
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
		$map[ $file ] = $id;
		update_option( 'mrzaib_demo_media', $map, false );
		return $id;
	}
	return 0;
}

function mrzaib_demo_categories() {
	// slug => array( name, image ).
	return array(
		'perfumes-for-men'   => array( 'Perfume for Men', 'collection-02.jpg' ),
		'perfumes-for-women' => array( 'Perfume for Women', 'collection-04.jpg' ),
		'unisex-perfumes'    => array( 'Unisex Perfume', 'collection-03.jpg' ),
		'best-sellers-men'   => array( 'Best Sellers Men', 'collection-02.jpg' ),
		'best-sellers-women' => array( 'Best Sellers Women', 'collection-04.jpg' ),
		'signature-range'    => array( 'Signature Range', 'collection-01.jpg' ),
		'executive-range'    => array( 'Executive Range', 'collection-02.jpg' ),
		'royal-range'        => array( 'Royal Range', 'collection-03.jpg' ),
		'new-arrivals'       => array( 'New Arrivals', 'collection-04.jpg' ),
		'gift-box'           => array( 'Gift Box', 'collection-05.jpg' ),
		'gourmand'           => array( 'Gourmand Collection', 'collection-06.jpg' ),
		'attar'              => array( 'Attar', 'collection-07.jpg' ),
		'air-care'           => array( 'Air Care', 'collection-08.jpg' ),
		'oud'                => array( 'Oud', 'collection-09.jpg' ),
		'tester-box'         => array( 'Tester Box', 'collection-10.jpg' ),
		'under-2000'         => array( 'Under 2000', 'collection-01.jpg' ),
		'body-mist'          => array( 'Body Mist', 'banner-3.jpg' ),
		'perfume-wax'        => array( 'Perfume Wax', 'banner-2.jpg' ),
		'pocket-perfumes'    => array( 'Pocket Perfume', 'collection-06.jpg' ),
	);
}

function mrzaib_demo_products() {
	// name, subtitle, regular, sale, image index, categories.
	return array(
		array( 'Royal Oud', 'Executive Range | 100ml Perfume', 2500, 0, 1, array( 'executive-range', 'oud', 'perfumes-for-men', 'best-sellers-men' ) ),
		array( 'Midnight Code', 'Executive Range', 1990, 0, 2, array( 'executive-range', 'perfumes-for-men', 'under-2000', 'best-sellers-men' ) ),
		array( 'Ocean Breeze', 'Executive Range', 2790, 0, 3, array( 'executive-range', 'perfumes-for-men', 'unisex-perfumes' ) ),
		array( 'Rose Velvet', 'Executive Range', 2190, 0, 4, array( 'executive-range', 'perfumes-for-women', 'best-sellers-women' ) ),
		array( 'Green Vetiver', 'Executive Range | 100ml Perfume', 2500, 0, 5, array( 'executive-range', 'perfumes-for-men', 'new-arrivals' ) ),
		array( 'Amber Night', 'Royal Range', 1790, 1610, 6, array( 'royal-range', 'oud', 'perfumes-for-men', 'under-2000' ) ),
		array( 'Golden Saffron', 'Royal Range', 1890, 1700, 7, array( 'royal-range', 'perfumes-for-men', 'under-2000' ) ),
		array( 'Blue Legend', 'Royal Range', 1650, 1490, 8, array( 'royal-range', 'perfumes-for-men', 'under-2000' ) ),
		array( 'Black Musk', 'Royal Range', 2670, 0, 9, array( 'royal-range', 'oud', 'unisex-perfumes' ) ),
		array( 'Desert Rose', 'Signature Range', 1490, 0, 10, array( 'signature-range', 'perfumes-for-women', 'under-2000', 'best-sellers-women' ) ),
		array( 'Citrus Bloom', 'Signature Range', 1350, 0, 11, array( 'signature-range', 'perfumes-for-women', 'under-2000', 'new-arrivals' ) ),
		array( 'Vanilla Dream', 'Signature Range', 1550, 0, 12, array( 'signature-range', 'gourmand', 'perfumes-for-women', 'under-2000' ) ),
		array( 'Leather Noir', 'Signature Range', 1390, 0, 13, array( 'signature-range', 'oud', 'perfumes-for-men', 'under-2000' ) ),
		array( 'Royal Duo', 'Royal Oud + Midnight Code', 4490, 3990, 14, array( 'gift-box' ) ),
		array( 'Floral Duo', 'Fresh floral perfumes for her', 2240, 1990, 4, array( 'gift-box', 'perfumes-for-women' ) ),
		array( 'Two Of A Kind', 'Stylish gift for men', 3340, 2990, 2, array( 'gift-box', 'perfumes-for-men' ) ),
		array( 'Cool Breeze Duo', 'Two scents. One cool vibe.', 2300, 1990, 3, array( 'gift-box' ) ),
		array( 'Oud Supreme - Attar (8ML)', 'Sophisticated Scent Journey', 1950, 0, 15, array( 'attar', 'oud' ) ),
		array( 'Saffron Gold - Attar (8ML)', 'Poetry in Every Drop', 2290, 0, 7, array( 'attar' ) ),
		array( 'White Jasmine - Attar (8ML)', 'Floral with Depth', 1450, 0, 14, array( 'attar', 'perfumes-for-women' ) ),
		array( 'Top 5 Samples', 'Tester Box', 900, 0, 16, array( 'tester-box', 'under-2000' ) ),
		array( 'Top 10 Sample Set', 'Tester Box', 1690, 0, 8, array( 'tester-box', 'under-2000' ) ),
		array( 'Aroma Plug-In Diffuser', 'For small rooms up to 30m²', 14800, 0, 16, array( 'air-care' ) ),
		array( 'Smart Scent Diffuser', 'For small spaces', 25000, 0, 9, array( 'air-care' ) ),
		array( 'Silver Mist Body Mist', 'Body Mist | 250ml', 1200, 0, 16, array( 'body-mist', 'under-2000' ) ),
		array( 'Pocket Oud 20ml', 'Pocket Perfume', 690, 0, 15, array( 'pocket-perfumes', 'under-2000' ) ),
		array( 'Amber Perfume Wax', 'Solid Perfume', 850, 0, 6, array( 'perfume-wax', 'under-2000' ) ),
	);
}

function mrzaib_lorem_desc( $name ) {
	return '<p><strong>' . esc_html( $name ) . '</strong> is a long lasting, super-macerated fragrance by MR ZAIB. This is dummy description text &mdash; replace it with your own story of the scent, its mood and the best occasions to wear it.</p>'
		. '<ul><li>Concentration: Eau de Parfum</li><li>Lasting: 8&ndash;12 hours</li><li>Projection: Strong</li><li>Season: All year</li></ul>';
}

function mrzaib_run_import() {
	$log = array();

	/* --- WooCommerce settings --- */
	update_option( 'woocommerce_currency', 'PKR' );
	update_option( 'woocommerce_price_num_decimals', 0 );
	update_option( 'woocommerce_default_country', 'PK:SD' );
	update_option( 'woocommerce_allowed_countries', 'specific' );
	update_option( 'woocommerce_specific_allowed_countries', array( 'PK' ) );
	update_option( 'woocommerce_ship_to_countries', '' );
	update_option( 'woocommerce_enable_ajax_add_to_cart', 'yes' );
	update_option( 'woocommerce_cod_settings', array(
		'enabled'            => 'yes',
		'title'              => 'Cash on Delivery',
		'description'        => 'Pay with cash when your parcel arrives.',
		'instructions'       => 'Pay with cash upon delivery.',
		'enable_for_methods' => array(),
		'enable_for_virtual' => 'yes',
	) );
	$log[] = 'Currency set to PKR (Rs.), Cash on Delivery enabled, AJAX add-to-cart on.';

	/* --- Shipping zones: Karachi + Rest of Pakistan --- */
	if ( function_exists( 'mrzaib_setup_shipping_zones' ) && MRZAIB_SHIP_V !== get_option( 'mrzaib_ship_v' ) ) {
		$log[] = mrzaib_setup_shipping_zones();
	}

	/* --- Remove old dummy data, then add the real MR ZAIB catalogue --- */
	$log[]   = mrzaib_remove_dummy();
	$cat_ids = mrzaib_install_categories();
	$log[]   = count( $cat_ids ) . ' categories ready: Men, Women, Unisex, Attar, Tester.';
	$log[]   = mrzaib_install_products( $cat_ids );

	/* --- Pages --- */
	$pages = array(
		'home'              => array( 'Home', '', '' ),
		'blog'              => array( 'Blogs', '', '' ),
		'contact-us'        => array( 'Contact Us', '', 'page-templates/contact.php' ),
		'faqs'              => array( 'FAQs', '', 'page-templates/faqs.php' ),
		'track-order'       => array( 'Track Your Order', '<!-- wp:shortcode -->[woocommerce_order_tracking]<!-- /wp:shortcode -->', '' ),
		'returns'           => array( 'Return Form', '', 'page-templates/returns.php' ),
		'shipping-policy'   => array( 'Shipping Policy', mrzaib_page_generic( 'Shipping Policy', '[mrzaib_delivery_time] [mrzaib_shipping] Cash on Delivery available on all orders.' ), '' ),
		'privacy-policy-2'  => array( 'Privacy Policy', mrzaib_page_generic( 'Privacy Policy', 'Your privacy matters to us. We only use your details to process orders and never sell your data. (Replace with your full policy.)' ), '' ),
		'terms-of-service'  => array( 'Terms of Service', mrzaib_page_generic( 'Terms of Service', 'By using this website you agree to our terms. (Replace with your full terms.)' ), '' ),
		'refund-policy'     => array( 'Return & Refund Policy', mrzaib_page_generic( 'Return & Refund Policy', 'Not happy with a fragrance? Return it within 15 days of delivery for an exchange or refund. Product must be at least 80% full with original packaging.' ), '' ),
	);
	$page_ids = array();
	foreach ( $pages as $slug => $pg ) {
		$ex = get_page_by_path( $slug );
		if ( $ex ) {
			$page_ids[ $slug ] = $ex->ID;
			continue;
		}
		$pid = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $pg[0],
			'post_name'    => $slug,
			'post_content' => $pg[1],
		) );
		if ( $pid && $pg[2] ) {
			update_post_meta( $pid, '_wp_page_template', $pg[2] );
		}
		$page_ids[ $slug ] = $pid;
	}
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_ids['home'] );
	update_option( 'page_for_posts', $page_ids['blog'] );
	$log[] = count( $page_ids ) . ' pages ready. Home set as front page.';

	/* --- Menus --- */
	mrzaib_build_menus();
	$log[] = 'Main menu (Shop, Men, Women, Unisex, Attar, Tester, Contact) and footer menus created.';

	/* --- Logo --- */
	if ( ! get_theme_mod( 'custom_logo' ) || ! get_post( (int) get_theme_mod( 'custom_logo' ) ) ) {
		$logo = mrzaib_import_image( 'logo-gold.png' ); // MR ZAIB gold logo for the dark header
		if ( $logo ) {
			set_theme_mod( 'custom_logo', $logo );
		}
	}

	$log[] = mrzaib_install_aws();
	$log[] = mrzaib_install_card_plugins();
	if ( function_exists( 'mrzaib_configure_yoast' ) && defined( 'WPSEO_VERSION' ) ) {
		$log[] = mrzaib_configure_yoast();
	}
	// Make the store public (WooCommerce 9.x+ starts new stores in Coming Soon mode).
	update_option( 'woocommerce_coming_soon', 'no' );
	update_option( 'woocommerce_store_pages_only', 'no' );
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules();
	update_option( 'mrzaib_demo_imported', time() );
	$log[] = 'Permalinks set to /post-name/. Done!';
	return $log;
}

function mrzaib_menu_add( $menu_id, $title, $target, $parent = 0 ) {
	$args = array( 'menu-item-title' => $title, 'menu-item-status' => 'publish', 'menu-item-parent-id' => $parent );
	if ( 'shop' === $target && function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'shop' ) > 0 ) {
		$args['menu-item-type']      = 'post_type';
		$args['menu-item-object']    = 'page';
		$args['menu-item-object-id'] = wc_get_page_id( 'shop' );
	} elseif ( 0 === strpos( $target, 'page:' ) ) {
		$slug = substr( $target, 5 );
		$p    = get_page_by_path( $slug );
		if ( 'my-account' === $slug && function_exists( 'wc_get_page_id' ) ) {
			$p = get_post( wc_get_page_id( 'myaccount' ) );
		}
		if ( $p ) {
			$args['menu-item-type']      = 'post_type';
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = $p->ID;
		} else {
			$args['menu-item-type'] = 'custom';
			$args['menu-item-url']  = home_url( '/' . $slug . '/' );
		}
	} else {
		$t = get_term_by( 'slug', $target, 'product_cat' );
		if ( ! $t && 'shop' !== $target ) {
			$new = wp_insert_term( $title, 'product_cat', array( 'slug' => $target ) );
			if ( ! is_wp_error( $new ) ) {
				$t = get_term( $new['term_id'], 'product_cat' );
			}
		}
		if ( $t ) {
			$args['menu-item-type']      = 'taxonomy';
			$args['menu-item-object']    = 'product_cat';
			$args['menu-item-object-id'] = $t->term_id;
		} else {
			$args['menu-item-type'] = 'custom';
			$args['menu-item-url']  = mrzaib_shop_link();
		}
	}
	return wp_update_nav_menu_item( $menu_id, 0, $args );
}

function mrzaib_build_menus() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( ! wp_get_nav_menu_object( 'MR ZAIB Main' ) ) {
		$mid = wp_create_nav_menu( 'MR ZAIB Main' );
		foreach ( mrzaib_menu_structure() as $top ) {
			$tid = mrzaib_menu_add( $mid, $top[0], $top[1] );
			foreach ( $top[2] as $c ) {
				$cid = mrzaib_menu_add( $mid, $c[0], $c[1], $tid );
				if ( ! empty( $c[2] ) ) {
					foreach ( $c[2] as $g ) {
						mrzaib_menu_add( $mid, $g[0], $g[1], $cid );
					}
				}
			}
		}
		$locations['primary'] = $mid;
	}

	$footers = array(
		'footer-about'   => array( 'Footer About', array( 'Men' => 'men', 'Women' => 'women', 'Unisex' => 'unisex', 'Attar' => 'attar', 'Tester' => 'tester' ) ),
		'footer-support' => array( 'Footer Support', array( 'My Account' => 'page:my-account', 'Return Form' => 'page:returns', 'Privacy Policy' => 'page:privacy-policy-2', 'Shipping Policy' => 'page:shipping-policy', 'Terms of Service' => 'page:terms-of-service', 'Track Your Order' => 'page:track-order' ) ),
		'footer-quick'   => array( 'Footer Quick Links', array( 'FAQs' => 'page:faqs', 'Contact Us' => 'page:contact-us', 'Return & Refund Policy' => 'page:refund-policy', 'Blogs' => 'page:blog' ) ),
	);
	foreach ( $footers as $loc => $f ) {
		if ( wp_get_nav_menu_object( $f[0] ) ) {
			continue;
		}
		$mid = wp_create_nav_menu( $f[0] );
		foreach ( $f[1] as $label => $target ) {
			mrzaib_menu_add( $mid, $label, $target );
		}
		$locations[ $loc ] = $mid;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
}

function mrzaib_page_generic( $title, $text ) {
	return '<!-- wp:heading --><h2>' . esc_html( $title ) . '</h2><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html( $text ) . '</p><!-- /wp:paragraph -->';
}

function mrzaib_page_story() {
	return '<!-- wp:heading --><h2>The Story of MR ZAIB</h2><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>MR ZAIB started with one simple idea: luxury fragrances should not cost a fortune. We create high-concentration, long lasting perfumes in Pakistan and put most of our cost into the juice inside the bottle. (Dummy text: write your own brand story here.)</p><!-- /wp:paragraph -->'
		. '<!-- wp:paragraph --><p>Every scent comes with honest notes, lasting and projection details, so you can choose with confidence. And if it is not right for you, our 15-day easy return policy has you covered.</p><!-- /wp:paragraph -->';
}

/** Ask the admin to install Yoast SEO (the theme leaves all SEO meta to it). */
add_action( 'admin_notices', function () {
	if ( defined( 'WPSEO_VERSION' ) || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	if ( isset( $_GET['mrzaib_yoast'] ) && check_admin_referer( 'mrzaib_yoast' ) ) { // phpcs:ignore
		echo '<div class="notice notice-info"><p>' . esc_html( mrzaib_install_plugin( 'wordpress-seo', 'wordpress-seo/wp-seo.php', 'Yoast SEO' ) ) . '</p></div>';
		return;
	}
	$url = wp_nonce_url( add_query_arg( 'mrzaib_yoast', '1', admin_url( 'index.php' ) ), 'mrzaib_yoast' );
	echo '<div class="notice notice-warning"><p><strong>MR ZAIB:</strong> ' . esc_html__( 'SEO titles, meta descriptions and sitemaps are handled by Yoast SEO.', 'mrzaib' ) . ' <a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Install & Activate Yoast SEO', 'mrzaib' ) . '</a></p></div>';
} );
