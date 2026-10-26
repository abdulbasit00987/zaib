<?php
/**
 * Shipping: Karachi vs rest of Pakistan.
 *
 * Rates live in WooCommerce > Settings > Shipping (zones "Karachi" and "Rest of Pakistan").
 * Every shipping text on the site is built from those zones, so editing a zone there
 * updates the announcement bar, product page, cart bar, FAQ and Shipping Policy automatically.
 *
 * @package mrzaib
 */

define( 'MRZAIB_SHIP_V', '2' );
define( 'MRZAIB_KHI_POSTCODES', '74000...75999' );

/** Is this city Karachi? */
function mrzaib_is_karachi( $city ) {
	return false !== stripos( (string) $city, 'karachi' );
}

/** Karachi customers usually leave the postcode empty: give the package a Karachi postcode so the Karachi zone matches. */
function mrzaib_ship_destination_fix( $dest ) {
	$country = isset( $dest['country'] ) ? $dest['country'] : '';
	if ( ( '' === $country || 'PK' === $country ) && ! empty( $dest['city'] ) && mrzaib_is_karachi( $dest['city'] ) && empty( $dest['postcode'] ) ) {
		$dest['postcode'] = '74000';
		$dest['country']  = 'PK';
	}
	return $dest;
}
add_filter( 'woocommerce_cart_shipping_packages', function ( $packages ) {
	foreach ( $packages as $k => $p ) {
		if ( isset( $p['destination'] ) ) {
			$packages[ $k ]['destination'] = mrzaib_ship_destination_fix( $p['destination'] );
		}
	}
	return $packages;
} );

/** Price as plain text, e.g. "Rs. 7,500". */
function mrzaib_ship_money( $n ) {
	if ( function_exists( 'wc_price' ) ) {
		$t = trim( str_replace( "\xC2\xA0", ' ', html_entity_decode( wp_strip_all_tags( wc_price( $n, array( 'decimals' => 0 ) ) ), ENT_QUOTES, 'UTF-8' ) ) );
		return preg_replace( '/^(Rs\.?)(?=\d)/', '$1 ', $t );
	}
	return 'Rs. ' . number_format( (float) $n );
}

/** Zones with their delivery charge and free-shipping minimum, read live from WooCommerce. */
function mrzaib_ship_rules() {
	static $rules = null;
	if ( null !== $rules ) {
		return $rules;
	}
	$rules = array();
	if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
		return $rules;
	}
	foreach ( WC_Shipping_Zones::get_zones() as $z ) {
		$zone = new WC_Shipping_Zone( $z['id'] );
		$r    = array( 'id' => $z['id'], 'name' => $zone->get_zone_name(), 'flat' => null, 'free' => null );
		foreach ( $zone->get_shipping_methods( true ) as $m ) {
			if ( 'flat_rate' === $m->id && null === $r['flat'] ) {
				$r['flat'] = (float) $m->get_option( 'cost', 0 );
			}
			if ( 'free_shipping' === $m->id && in_array( $m->get_option( 'requires' ), array( 'min_amount', 'either' ), true ) && (float) $m->get_option( 'min_amount' ) > 0 ) {
				$r['free'] = (float) $m->get_option( 'min_amount' );
			}
		}
		if ( null !== $r['flat'] || null !== $r['free'] ) {
			$rules[] = $r;
		}
	}
	return $rules;
}

/** Short line for the announcement bar / product USP. */
function mrzaib_ship_short() {
	$parts = array();
	foreach ( mrzaib_ship_rules() as $r ) {
		if ( $r['free'] ) {
			$parts[] = sprintf( '%s on orders of %s+', $r['name'], mrzaib_ship_money( $r['free'] ) );
		}
	}
	return $parts ? 'Free Delivery: ' . implode( ' | ', $parts ) : __( 'Cash on Delivery all over Pakistan', 'mrzaib' );
}

/** Announcement 2: Customizer text, or automatic from WooCommerce when empty / still the old Rs. 3000 text. */
function mrzaib_announce_shipping() {
	$a = trim( (string) get_theme_mod( 'mrzaib_announce_2', '' ) );
	if ( '' === $a || preg_match( '/Rs\.?\s*3,?000/i', $a ) ) {
		return mrzaib_ship_short();
	}
	return $a;
}

/** Full sentence: charge + free minimum per zone. */
function mrzaib_ship_long() {
	$out = array();
	foreach ( mrzaib_ship_rules() as $r ) {
		$s = $r['name'] . ': ';
		if ( null !== $r['flat'] ) {
			$s .= $r['flat'] > 0 ? sprintf( '%s delivery charges', mrzaib_ship_money( $r['flat'] ) ) : 'free delivery';
		}
		if ( $r['free'] ) {
			$s .= ( null !== $r['flat'] ? ', ' : '' ) . sprintf( 'free delivery on orders of %s or more', mrzaib_ship_money( $r['free'] ) );
		}
		$out[] = $s . '.';
	}
	return implode( ' ', $out );
}
add_shortcode( 'mrzaib_shipping', function () {
	return esc_html( mrzaib_ship_long() );
} );

/** The zone that applies to the current customer (by city), or the first zone (Karachi) when the city is not known yet. */
function mrzaib_ship_customer_rule() {
	$rules = mrzaib_ship_rules();
	if ( ! $rules ) {
		return null;
	}
	if ( function_exists( 'WC' ) && WC()->customer && class_exists( 'WC_Shipping_Zones' ) ) {
		$c    = WC()->customer;
		$city = $c->get_shipping_city() ? $c->get_shipping_city() : $c->get_billing_city();
		if ( $city ) {
			$dest = mrzaib_ship_destination_fix( array(
				'country'  => $c->get_shipping_country() ? $c->get_shipping_country() : 'PK',
				'state'    => $c->get_shipping_state(),
				'postcode' => $c->get_shipping_postcode(),
				'city'     => $city,
			) );
			$zone = WC_Shipping_Zones::get_zone_matching_package( array( 'destination' => $dest ) );
			foreach ( $rules as $r ) {
				if ( (int) $r['id'] === (int) $zone->get_id() ) {
					return $r;
				}
			}
		}
	}
	return $rules[0];
}

/** Create / update the two zones once (Karachi Rs.300, free 7500; Rest of Pakistan Rs.500, free 12000). */
function mrzaib_setup_shipping_zones() {
	if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
		return '';
	}
	$khi = null;
	$pk  = null;
	foreach ( WC_Shipping_Zones::get_zones() as $z ) {
		$n = strtolower( $z['zone_name'] );
		if ( false !== strpos( $n, 'karachi' ) ) {
			$khi = new WC_Shipping_Zone( $z['id'] );
		} elseif ( 'pakistan' === $n || false !== strpos( $n, 'rest of pakistan' ) ) {
			$pk = new WC_Shipping_Zone( $z['id'] );
		}
	}
	if ( ! $khi ) {
		$khi = new WC_Shipping_Zone();
		$khi->set_zone_name( 'Karachi' );
		$khi->add_location( 'PK', 'country' );
		$khi->add_location( MRZAIB_KHI_POSTCODES, 'postcode' );
	}
	$khi->set_zone_order( 0 );
	$khi->save();
	if ( ! $pk ) {
		$pk = new WC_Shipping_Zone();
		$pk->add_location( 'PK', 'country' );
	}
	$pk->set_zone_name( 'Rest of Pakistan' );
	$pk->set_zone_order( 1 );
	$pk->save();

	foreach ( array( array( $khi, 300, 7500 ), array( $pk, 500, 12000 ) ) as $cfg ) {
		list( $zone, $cost, $min ) = $cfg;
		$flat = null;
		$free = null;
		foreach ( $zone->get_shipping_methods() as $m ) {
			if ( 'flat_rate' === $m->id && ! $flat ) {
				$flat = $m->instance_id;
			}
			if ( 'free_shipping' === $m->id && ! $free ) {
				$free = $m->instance_id;
			}
		}
		if ( ! $flat ) {
			$flat = $zone->add_shipping_method( 'flat_rate' );
		}
		if ( ! $free ) {
			$free = $zone->add_shipping_method( 'free_shipping' );
		}
		$fo = get_option( 'woocommerce_flat_rate_' . $flat . '_settings', array() );
		update_option( 'woocommerce_flat_rate_' . $flat . '_settings', array_merge( is_array( $fo ) ? $fo : array(), array( 'title' => 'Standard Delivery', 'cost' => (string) $cost, 'tax_status' => 'none' ) ) );
		$so = get_option( 'woocommerce_free_shipping_' . $free . '_settings', array() );
		update_option( 'woocommerce_free_shipping_' . $free . '_settings', array_merge( is_array( $so ) ? $so : array(), array( 'title' => 'Free Delivery', 'requires' => 'min_amount', 'min_amount' => (string) $min, 'ignore_discounts' => 'no' ) ) );
	}
	if ( class_exists( 'WC_Cache_Helper' ) ) {
		WC_Cache_Helper::get_transient_version( 'shipping', true );
	}

	/* Shipping Policy page: replace the old fixed text with the live shortcode. */
	$page = get_page_by_path( 'shipping-policy' );
	if ( $page && false === strpos( $page->post_content, '[mrzaib_shipping]' ) ) {
		$c = preg_replace( '/Free shipping on orders over Rs\.?\s*3,?000\.?/i', '[mrzaib_shipping]', $page->post_content, -1, $cnt );
		if ( $cnt ) {
			wp_update_post( array( 'ID' => $page->ID, 'post_content' => $c ) );
		}
	}
	update_option( 'mrzaib_zone_done', 1 );
	update_option( 'mrzaib_ship_v', MRZAIB_SHIP_V );
	return 'Shipping zones set: Karachi Rs. 300 (free over Rs. 7,500), Rest of Pakistan Rs. 500 (free over Rs. 12,000).';
}
add_action( 'admin_init', function () {
	if ( class_exists( 'WC_Shipping_Zones' ) && MRZAIB_SHIP_V !== get_option( 'mrzaib_ship_v' ) && current_user_can( 'manage_woocommerce' ) ) {
		mrzaib_setup_shipping_zones();
	}
} );

/* Karachi: let the cart's "Calculate shipping" show the city field (default WC already does) and keep PK as the default country. */
add_filter( 'default_checkout_billing_country', function ( $c ) {
	return $c ? $c : 'PK';
} );
