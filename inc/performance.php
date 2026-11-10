<?php
/**
 * Front-end performance (PageSpeed / Lighthouse):
 * self-hosted fonts, LCP hero preload, unused plugin assets removed,
 * non-critical CSS loaded async, head scripts moved to the footer,
 * minified theme files, browser caching + gzip rules in .htaccess,
 * WebP sub-sizes for new uploads.
 *
 * @package MR_ZAIB
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------
 * 1. Fonts: self-hosted variable woff2 (no Google Fonts round trips).
 * ------------------------------------------------------------------ */
function mrzaib_fonts_head() {
	$u  = MRZAIB_URI . '/assets/fonts/';
	$ur = 'U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD';
	echo '<link rel="preload" href="' . esc_url( $u . 'jost-latin.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	echo '<style id="mrzaib-fonts">'
		. "@font-face{font-family:'Jost';font-style:normal;font-weight:100 900;font-display:swap;src:url(" . esc_url( $u . 'jost-latin.woff2' ) . ") format('woff2');unicode-range:$ur}"
		. "@font-face{font-family:'Playfair Display';font-style:normal;font-weight:400 900;font-display:swap;src:url(" . esc_url( $u . 'playfair-display-latin.woff2' ) . ") format('woff2');unicode-range:$ur}"
		. '</style>' . "\n";
}
add_action( 'wp_head', 'mrzaib_fonts_head', 2 );

/** The mini-cart plugin's files are removed (theme cart replaces it); keep its leftover inline calls from throwing console errors. */
add_action( 'wp_head', function () {
	echo "<script>window.mca_update=window.mca_update||function(){};</script>\n";
}, 1 );

/* ------------------------------------------------------------------
 * 2. Hero (LCP image): srcset + preload of the first slide.
 * ------------------------------------------------------------------ */
function mrzaib_hero_img_data( $url ) {
	static $cache = array();
	if ( isset( $cache[ $url ] ) ) {
		return $cache[ $url ];
	}
	$d  = array( 'srcset' => '', 'w' => 1920, 'h' => 760 );
	$id = $url ? attachment_url_to_postid( strtok( $url, '?' ) ) : 0;
	if ( $id ) {
		$meta = wp_get_attachment_metadata( $id );
		if ( ! empty( $meta['width'] ) ) {
			$d['w'] = (int) $meta['width'];
			$d['h'] = (int) $meta['height'];
		}
		$ss = wp_get_attachment_image_srcset( $id, 'full' );
		if ( $ss ) {
			$d['srcset'] = $ss;
		}
	} else {
		$p = MRZAIB_DIR . '/assets/images/' . basename( strtok( $url, '?' ) );
		if ( file_exists( $p ) ) {
			$s = @getimagesize( $p ); // phpcs:ignore
			if ( $s ) {
				$d['w'] = $s[0];
				$d['h'] = $s[1];
			}
		}
	}
	return $cache[ $url ] = $d;
}

add_action( 'wp_head', function () {
	if ( ! is_front_page() || ! function_exists( 'mrzaib_hero_slides' ) ) {
		return;
	}
	$sl = mrzaib_hero_slides();
	if ( empty( $sl[0] ) ) {
		return;
	}
	$dk = mrzaib_hero_img_data( $sl[0][0] );
	echo '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url( $sl[0][0] ) . '"' . ( $dk['srcset'] ? ' imagesrcset="' . esc_attr( $dk['srcset'] ) . '" imagesizes="100vw"' : '' ) . ' media="(min-width: 750px)">' . "\n";
	echo '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url( $sl[0][1] ) . '" media="(max-width: 749px)">' . "\n";
}, 3 );

/* ------------------------------------------------------------------
 * 3. Assets: drop what the theme replaced, load the rest without blocking.
 * ------------------------------------------------------------------ */
function mrzaib_is_theme_page() {
	if ( is_front_page() ) {
		return true;
	}
	return function_exists( 'is_woocommerce' ) && ( is_shop() || is_product() || is_product_taxonomy() );
}

function mrzaib_perf_assets() {
	if ( is_admin() ) {
		return;
	}
	// Plugins whose front-end UI the theme replaced (theme cart dropdown, theme predictive search).
	$drop = array( '/plugins/woo-mini-cart-drawer/', '/plugins/advanced-woo-search/' );
	// Icegram's public files: the theme's footer form posts through its own ajax.
	$es_needed = false;
	if ( is_singular() ) {
		$post      = get_post();
		$es_needed = $post && ( has_shortcode( $post->post_content, 'email-subscribers-form' ) || has_shortcode( $post->post_content, 'email-subscribers' ) || has_block( 'email-subscribers/subscription-form', $post ) );
	}
	if ( ! $es_needed ) {
		$drop[] = '/plugins/email-subscribers/lite/public/';
	}
	foreach ( array( wp_styles(), wp_scripts() ) as $reg ) {
		foreach ( (array) $reg->queue as $h ) {
			$src = isset( $reg->registered[ $h ] ) ? (string) $reg->registered[ $h ]->src : '';
			foreach ( $drop as $d ) {
				if ( $src && false !== strpos( $src, $d ) ) {
					$reg instanceof WP_Styles ? wp_dequeue_style( $h ) : wp_dequeue_script( $h );
				}
			}
		}
	}
	// Block styles are not used on the theme's own templates (home, shop, product).
	if ( mrzaib_is_theme_page() ) {
		foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles', 'wc-blocks-style' ) as $h ) {
			wp_dequeue_style( $h );
		}
	}
	// jQuery without the migrate shim, printed in the footer like every other script.
	$s = wp_scripts();
	if ( isset( $s->registered['jquery'] ) ) {
		$s->registered['jquery']->deps = array( 'jquery-core' );
	}
	// Every script (and the scripts it needs) prints at the end of the page: nothing blocks the first paint.
	$todo = (array) $s->queue;
	$seen = array();
	while ( $todo ) {
		$h = array_shift( $todo );
		if ( isset( $seen[ $h ] ) || ! isset( $s->registered[ $h ] ) ) {
			continue;
		}
		$seen[ $h ] = true;
		$s->add_data( $h, 'group', 1 );
		$todo = array_merge( $todo, (array) $s->registered[ $h ]->deps );
	}
}
add_action( 'wp_enqueue_scripts', 'mrzaib_perf_assets', 9999 );
add_action( 'wp_print_styles', 'mrzaib_perf_assets', 1 );

/**
 * Plugin stylesheets that only matter after a click (quick view popup, gallery slider)
 * load without blocking the first paint.
 */
add_filter( 'style_loader_tag', function ( $tag, $handle, $href ) {
	if ( is_admin() ) {
		return $tag;
	}
	$async = false;
	foreach ( array( '/plugins/woo-quickview/', '/plugins/woo-product-gallery-slider/', '/plugins/email-subscribers/', 'woocommerce-smallscreen' ) as $p ) {
		if ( false !== strpos( $href, $p ) || $handle === $p ) {
			$async = true;
		}
	}
	if ( 'woocommerce-smallscreen' === $handle ) {
		$async = true;
	}
	if ( ! $async || false !== strpos( $tag, 'onload=' ) ) {
		return $tag;
	}
	$noscript = '<noscript>' . $tag . '</noscript>';
	$tag      = preg_replace( "/media=(['\"])([^'\"]*)\\1/", 'media="print" data-media="$2" onload="this.media=this.dataset.media||\'all\';this.onload=null"', $tag, 1 );
	return $tag . $noscript;
}, 10, 3 );

/* ------------------------------------------------------------------
 * 4. Minified theme CSS/JS (assets/*.min.*, built from the sources).
 * ------------------------------------------------------------------ */
function mrzaib_asset( $rel ) {
	$min = preg_replace( '/\.(css|js)$/', '.min.$1', $rel );
	if ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) && file_exists( MRZAIB_DIR . $min ) && filemtime( MRZAIB_DIR . $min ) >= filemtime( MRZAIB_DIR . $rel ) ) {
		$rel = $min;
	}
	return array( MRZAIB_URI . $rel, MRZAIB_VERSION . '.' . filemtime( MRZAIB_DIR . $rel ) );
}

/* ------------------------------------------------------------------
 * 5. Browser caching + compression (.htaccess, Apache / LiteSpeed).
 * ------------------------------------------------------------------ */
define( 'MRZAIB_HTACCESS_V', 1 );
add_action( 'admin_init', function () {
	if ( (int) get_option( 'mrzaib_htaccess_v' ) >= MRZAIB_HTACCESS_V ) {
		return;
	}
	if ( ! function_exists( 'insert_with_markers' ) ) {
		require_once ABSPATH . 'wp-admin/includes/misc.php';
	}
	$file = ABSPATH . '.htaccess';
	if ( ( file_exists( $file ) && ! is_writable( $file ) ) || ( ! file_exists( $file ) && ! is_writable( ABSPATH ) ) ) {
		return;
	}
	$rules = array(
		'<IfModule mod_expires.c>',
		'ExpiresActive On',
		'ExpiresByType image/jpeg "access plus 1 year"',
		'ExpiresByType image/png "access plus 1 year"',
		'ExpiresByType image/webp "access plus 1 year"',
		'ExpiresByType image/avif "access plus 1 year"',
		'ExpiresByType image/svg+xml "access plus 1 year"',
		'ExpiresByType image/gif "access plus 1 year"',
		'ExpiresByType image/x-icon "access plus 1 year"',
		'ExpiresByType video/mp4 "access plus 1 year"',
		'ExpiresByType font/woff2 "access plus 1 year"',
		'ExpiresByType application/font-woff2 "access plus 1 year"',
		'ExpiresByType text/css "access plus 1 year"',
		'ExpiresByType application/javascript "access plus 1 year"',
		'ExpiresByType text/javascript "access plus 1 year"',
		'</IfModule>',
		'<IfModule mod_headers.c>',
		'<FilesMatch "\.(css|js|jpe?g|png|gif|webp|avif|svg|ico|woff2?|mp4)$">',
		'Header set Cache-Control "public, max-age=31536000"',
		'</FilesMatch>',
		'</IfModule>',
		'<IfModule mod_deflate.c>',
		'AddOutputFilterByType DEFLATE text/html text/plain text/css text/xml application/xml application/javascript text/javascript application/json image/svg+xml',
		'</IfModule>',
	);
	insert_with_markers( $file, 'MR ZAIB Performance', $rules );
	update_option( 'mrzaib_htaccess_v', MRZAIB_HTACCESS_V );
} );

/* ------------------------------------------------------------------
 * 6. New JPEG/PNG uploads also get WebP sub-sizes (smaller files).
 * ------------------------------------------------------------------ */
add_filter( 'image_editor_output_format', function ( $formats ) {
	if ( wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) ) ) {
		$formats['image/jpeg'] = 'image/webp';
	}
	return $formats;
} );

/* ------------------------------------------------------------------
 * 7. Custom logo: right-sized srcset choice (shown ~168px wide).
 * ------------------------------------------------------------------ */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
	if ( isset( $attr['class'] ) && false !== strpos( $attr['class'], 'logo-img' ) ) {
		$attr['sizes'] = '(max-width: 768px) 130px, 170px';
		unset( $attr['loading'] );
	}
	return $attr;
} );
