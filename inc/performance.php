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
	$d  = array( 'srcset' => '', 'w' => 1920, 'h' => 760, 'src' => $url );
	$id = $url ? attachment_url_to_postid( strtok( $url, '?' ) ) : 0;
	if ( $id ) {
		$GLOBALS['mrzaib_webp_force'] = true;
		$wu = function_exists( 'mrzaib_webp_url' ) ? mrzaib_webp_url( $url ) : '';
		if ( $wu ) {
			$d['src'] = $wu;
		}
		$meta = wp_get_attachment_metadata( $id );
		if ( ! empty( $meta['width'] ) ) {
			$d['w'] = (int) $meta['width'];
			$d['h'] = (int) $meta['height'];
		}
		$ss = wp_get_attachment_image_srcset( $id, 'full' );
		$GLOBALS['mrzaib_webp_force'] = false;
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
	echo '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url( $dk['src'] ) . '"' . ( $dk['srcset'] ? ' imagesrcset="' . esc_attr( $dk['srcset'] ) . '" imagesizes="100vw"' : '' ) . ' media="(min-width: 750px)">' . "\n";
	$ms = mrzaib_hero_mobile_srcset( $sl[0][1] );
	echo '<link rel="preload" as="image" fetchpriority="high" href="' . esc_url( $sl[0][1] ) . '"' . ( $ms ? ' imagesrcset="' . esc_attr( $ms ) . '" imagesizes="100vw"' : '' ) . ' media="(max-width: 749px)">' . "\n";
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

/* ------------------------------------------------------------------
 * 8. WebP copies of uploaded JPEG/PNG images, made once on first use
 *    (file.jpg -> file.jpg.webp, plus a few right-sized widths),
 *    swapped into the page's <img> src/srcset. Originals untouched.
 * ------------------------------------------------------------------ */
function mrzaib_webp_on() {
	static $ok = null;
	if ( null === $ok ) {
		$ok = function_exists( 'wp_image_editor_supports' ) && wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
	}
	return $ok;
}

/** Page-body flag: true after <head> is printed (keeps OG/schema images as JPEG for social apps). */
$GLOBALS['mrzaib_webp_live'] = false;
add_action( 'wp_head', function () {
	$GLOBALS['mrzaib_webp_live'] = true;
}, PHP_INT_MAX );

function mrzaib_url_to_path( $url ) {
	static $up = null;
	if ( null === $up ) {
		$up = wp_get_upload_dir();
	}
	$url  = strtok( (string) $url, '?' );
	$base = preg_replace( '#^https?:#', '', $up['baseurl'] );
	$u    = preg_replace( '#^https?:#', '', $url );
	if ( 0 === strpos( $u, $base . '/' ) ) {
		return array( $up['basedir'] . substr( $u, strlen( $base ) ), $up['baseurl'] . substr( $u, strlen( $base ) ) );
	}
	return false;
}

/**
 * WebP version of an uploaded image, optionally resized to $w px wide.
 * Returns its URL, or '' when not possible (it is then made on a later request).
 */
function mrzaib_webp_url( $url, $w = 0 ) {
	if ( ! mrzaib_webp_on() ) {
		return '';
	}
	$m = mrzaib_url_to_path( $url );
	if ( ! $m || ! preg_match( '/\.(jpe?g|png)$/i', $m[0] ) ) {
		return '';
	}
	list( $src, $srcurl ) = $m;
	$suffix = $w ? '-mz2w' . (int) $w . '.webp' : '.webp';
	$dest   = $src . $suffix;
	if ( ! file_exists( $dest ) ) {
		static $start = null, $made = 0;
		if ( null === $start ) {
			$start = microtime( true );
		}
		if ( ! file_exists( $src ) || $made >= 40 || microtime( true ) - $start > 8 || ! wp_is_writable( dirname( $src ) ) ) {
			return '';
		}
		$ed = wp_get_image_editor( $src );
		if ( is_wp_error( $ed ) ) {
			return '';
		}
		if ( $w ) {
			$sz = $ed->get_size();
			if ( empty( $sz['width'] ) || $sz['width'] <= $w ) {
				return '';
			}
			$ed->resize( $w, null );
		}
		$ed->set_quality( $w ? 68 : 75 );
		$r = $ed->save( $dest, 'image/webp' );
		$made++;
		if ( is_wp_error( $r ) || ! file_exists( $dest ) ) {
			return '';
		}
		// Keep the WebP only when it is actually smaller.
		if ( ! $w && filesize( $dest ) >= filesize( $src ) ) {
			@unlink( $dest ); // phpcs:ignore
			@copy( $src, $dest . '.skip' ); // phpcs:ignore
			return '';
		}
	}
	return $srcurl . $suffix;
}

function mrzaib_webp_active() {
	return ! is_admin() && ! wp_doing_ajax() && ! is_feed() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) && ! empty( $GLOBALS['mrzaib_webp_live'] ) && mrzaib_webp_on();
}

/** srcset: WebP for every width, plus in-between widths so phones never get a far bigger file. */
function mrzaib_webp_srcset_sources( $sources, $full_url, $full_w ) {
	foreach ( $sources as $k => $s ) {
		$wu = mrzaib_webp_url( $s['url'] );
		if ( $wu ) {
			$sources[ $k ]['url'] = $wu;
		}
	}
	if ( $full_url && $full_w ) {
		foreach ( array( 360, 450, 720, 1080 ) as $want ) {
			if ( $want >= $full_w * 0.9 ) {
				continue;
			}
			$near = false;
			foreach ( $sources as $s ) {
				if ( abs( (int) $s['value'] - $want ) <= $want * 0.15 ) {
					$near = true;
				}
			}
			if ( ! $near ) {
				$wu = mrzaib_webp_url( $full_url, $want );
				if ( $wu ) {
					$sources[ $want ] = array( 'url' => $wu, 'descriptor' => 'w', 'value' => $want );
				}
			}
		}
		ksort( $sources );
	}
	return $sources;
}

add_filter( 'wp_calculate_image_srcset', function ( $sources, $size_array, $image_src, $image_meta, $attachment_id ) {
	if ( ! mrzaib_webp_active() && empty( $GLOBALS['mrzaib_webp_force'] ) ) {
		return $sources;
	}
	$full = $attachment_id ? wp_get_attachment_url( $attachment_id ) : '';
	$fw   = ! empty( $image_meta['width'] ) ? (int) $image_meta['width'] : 0;
	return mrzaib_webp_srcset_sources( (array) $sources, $full, $fw );
}, 20, 5 );

add_filter( 'wp_get_attachment_image_src', function ( $image ) {
	if ( $image && ( mrzaib_webp_active() || ! empty( $GLOBALS['mrzaib_webp_force'] ) ) ) {
		$wu = mrzaib_webp_url( $image[0] );
		if ( $wu ) {
			$image[0] = $wu;
		}
	}
	return $image;
}, 20 );

/** Hero mobile picture: right-sized WebP widths for phones (shown full width, ~720px on most phones). */
function mrzaib_hero_mobile_srcset( $url ) {
	$GLOBALS['mrzaib_webp_force'] = true;
	$out = '';
	$m   = mrzaib_url_to_path( $url );
	if ( $m && file_exists( $m[0] ) ) {
		$sz    = @getimagesize( $m[0] ); // phpcs:ignore
		$parts = array();
		foreach ( array( 480, 768 ) as $w ) {
			$wu = mrzaib_webp_url( $url, $w );
			if ( $wu ) {
				$parts[] = $wu . ' ' . $w . 'w';
			}
		}
		$full = mrzaib_webp_url( $url );
		if ( $sz ) {
			$parts[] = ( $full ? $full : $url ) . ' ' . (int) $sz[0] . 'w';
			$out     = implode( ', ', $parts );
		}
	}
	$GLOBALS['mrzaib_webp_force'] = false;
	return $out;
}

/* ------------------------------------------------------------------
 * 9. Home page: its stylesheets are inlined (no render-blocking
 *    CSS requests before the first paint).
 * ------------------------------------------------------------------ */
add_filter( 'style_loader_tag', function ( $tag, $handle, $href ) {
	if ( is_admin() || ! is_front_page() || ! in_array( $handle, array( 'woocommerce-layout', 'woocommerce-general', 'mrzaib-main' ), true ) ) {
		return $tag;
	}
	$u    = strtok( $href, '?' );
	$cu   = preg_replace( '#^https?:#', '', content_url() );
	$uu   = preg_replace( '#^https?:#', '', $u );
	if ( 0 !== strpos( $uu, $cu . '/' ) ) {
		return $tag;
	}
	$path = WP_CONTENT_DIR . substr( $uu, strlen( $cu ) );
	if ( ! is_readable( $path ) || filesize( $path ) > 300000 ) {
		return $tag;
	}
	$css  = (string) file_get_contents( $path ); // phpcs:ignore
	$base = trailingslashit( dirname( $u ) );
	$css  = preg_replace_callback( '/url\(\s*([\'"]?)(?!data:|https?:|\/\/|\/|#)([^\'")]+)\1\s*\)/i', function ( $m ) use ( $base ) {
		return 'url(' . $m[1] . $base . $m[2] . $m[1] . ')';
	}, $css );
	$css   = str_replace( '</style', '<\/style', $css );
	$media = preg_match( "/media=['\"]([^'\"]+)['\"]/", $tag, $mm ) ? $mm[1] : 'all';
	return '<style id="' . esc_attr( $handle ) . '-inline-css"' . ( 'all' !== $media ? ' media="' . esc_attr( $media ) . '"' : '' ) . '>' . $css . "</style>\n";
}, 20, 3 );

/* ------------------------------------------------------------------
 * 10. WooCommerce cart-fragments refresh waits for the first
 *     interaction (the header cart is already printed by PHP; ajax
 *     add-to-cart returns its own fragments).
 * ------------------------------------------------------------------ */
add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
	if ( is_admin() || 'wc-cart-fragments' !== $handle ) {
		return $tag;
	}
	return str_replace( array( '<script ', ' src=' ), array( '<script type="text/mzlazy" ', ' data-src=' ), $tag );
}, 20, 3 );

add_action( 'wp_footer', function () {
	echo "<script>(function(){var d=0,E=['scroll','pointerdown','keydown','touchstart','wheel','mousemove'];function g(){if(d)return;d=1;E.forEach(function(e){removeEventListener(e,g)});document.querySelectorAll('script[type=\"text/mzlazy\"]').forEach(function(o){var s=document.createElement('script');s.src=o.getAttribute('data-src');document.body.appendChild(s)})}E.forEach(function(e){addEventListener(e,g,{passive:true,once:true})});addEventListener('pageshow',function(e){if(e.persisted)g()})})();</script>\n";
}, 100 );

/** WebP copy of an uploaded image URL when available, else the URL itself. */
function mrzaib_webp_or( $url, $w = 0 ) {
	if ( ! function_exists( 'mrzaib_webp_url' ) || ! $url ) {
		return $url;
	}
	$wu = $w ? mrzaib_webp_url( $url, $w ) : '';
	if ( ! $wu ) {
		$wu = mrzaib_webp_url( $url );
	}
	return $wu ? $wu : $url;
}
