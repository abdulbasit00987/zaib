<?php
/**
 * Appearance > MR ZAIB Setup (user, 2026-11-18).
 *
 * Step 1: one button installs + activates every plugin the MR ZAIB store uses and applies the
 *         same settings as the live store (Wordfence license key is added by the owner).
 * Step 2: after step 1, "Add All Data" adds the whole store: categories, all perfumes with photos,
 *         blogs, pages, menus, policies, homepage banners/videos, logo. The button disappears after.
 * Each part runs as its own small AJAX request so slow hosts (XAMPP) never time out.
 *
 * @package mrzaib
 */

defined( 'ABSPATH' ) || exit;

/** Plugins used by the store: slug => array( main file, label ). WooCommerce first. */
function mrzaib_setup_plugins() {
	return array(
		'woocommerce'                => array( 'woocommerce/woocommerce.php', 'WooCommerce' ),
		'wordpress-seo'              => array( 'wordpress-seo/wp-seo.php', 'Yoast SEO' ),
		'woo-quickview'              => array( 'woo-quickview/woo-quick-view.php', 'Quick View for WooCommerce (Reno QuickView)' ),
		'woo-product-gallery-slider' => array( 'woo-product-gallery-slider/woo-product-gallery-slider.php', 'Product Gallery Slider for WooCommerce' ),
		'advanced-woo-search'        => array( 'advanced-woo-search/advanced-woo-search.php', 'Advanced Woo Search' ),
		'email-subscribers'          => array( 'email-subscribers/email-subscribers.php', 'Icegram Express (Email Subscribers & Newsletters)' ),
		'wp-mail-smtp'               => array( 'wp-mail-smtp/wp_mail_smtp.php', 'WP Mail SMTP' ),
		'wordfence'                  => array( 'wordfence/wordfence.php', 'Wordfence Security' ),
	);
}

function mrzaib_setup_plugin_active( $file ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	return is_plugin_active( $file );
}

/** Every store plugin is active and the MR ZAIB settings were applied (or the store was set up before). */
function mrzaib_setup_plugins_ok() {
	foreach ( mrzaib_setup_plugins() as $p ) {
		if ( ! mrzaib_setup_plugin_active( $p[0] ) ) {
			return false;
		}
	}
	return (bool) ( get_option( 'mrzaib_plugins_done' ) || get_option( 'mrzaib_demo_imported' ) );
}

/** Same settings as the live store, applied after all plugins are active. */
function mrzaib_setup_configure() {
	$log = array();
	// No welcome / onboarding redirects from freshly activated plugins.
	delete_transient( '_wc_activation_redirect' );
	update_option( 'woocommerce_onboarding_profile', array( 'skipped' => true ) );
	update_option( 'wp_mail_smtp_activation_prevent_redirect', true );
	delete_transient( 'wp_mail_smtp_activation_redirect' );
	if ( class_exists( 'WPSEO_Options' ) ) {
		WPSEO_Options::set( 'should_redirect_after_install_free', false );
	}

	if ( class_exists( 'WooCommerce' ) ) {
		mrzaib_wc_base_settings();
		update_option( 'woocommerce_coming_soon', 'no' );
		update_option( 'woocommerce_store_pages_only', 'no' );
		if ( '' === (string) get_option( 'mrzaib_dtime_karachi', '' ) ) {
			update_option( 'mrzaib_dtime_karachi', '2 to 5 working days' );
			update_option( 'mrzaib_dtime_rest', '2 to 5 working days' );
		}
		if ( function_exists( 'mrzaib_setup_shipping_zones' ) ) {
			$log[] = mrzaib_setup_shipping_zones();
		}
		$log[] = 'WooCommerce: PKR, Cash on Delivery, Pakistan only.';
	}
	if ( function_exists( 'mrzaib_configure_aws' ) && mrzaib_setup_plugin_active( 'advanced-woo-search/advanced-woo-search.php' ) ) {
		mrzaib_configure_aws();
	}
	if ( function_exists( 'mrzaib_install_card_plugins' ) ) {
		mrzaib_install_card_plugins(); // Quick View + Gallery Slider options (plugins already installed).
	}
	if ( defined( 'WPSEO_VERSION' ) && function_exists( 'mrzaib_configure_yoast' ) ) {
		$log[] = mrzaib_configure_yoast();
	}
	if ( function_exists( 'mrzaib_es_active' ) && mrzaib_es_active() ) {
		mrzaib_es_setup( true );
		$log[] = 'Icegram Express: subscriber list and new product/blog email set up.';
	}
	if ( function_exists( 'mrzaib_wf_active' ) && mrzaib_wf_active() ) {
		$log[] = mrzaib_wf_configure();
	}
	$imp = mrzaib_setup_import_settings();
	if ( $imp ) {
		$log[] = sprintf( '%d plugin settings copied from the live store.', $imp );
	}
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules();
	update_option( 'mrzaib_plugins_done', time() );
	return implode( ' ', array_filter( $log ) );
}

/* ---------- Plugin settings: export from the live store, bundled into the theme ---------- */

/** Option names that must never travel between sites (IDs, versions, secrets, internal state). */
function mrzaib_settings_skip( $name ) {
	return (bool) preg_match( '/(version|_page_id|_db_|install|notice|admin_|onboarding|task_list|tracker|tracking|queue|_id$|_ids$|secret|token|key|pass|cache|transient|last_|timestamp|maxmind|remote|marketplace|inbox|navigation|feature|schedule|cron|guid|lock|migration|usage|telemetry|activated|activation|redirect|_hash|nonce|stats|count|custom_orders_table|hpos|placeholder_image|taxonomy_meta|sent_data|imported|_ver$|email_improvements_first|^wpseo$)/i', $name );
}

function mrzaib_settings_strip( $v ) {
	if ( is_array( $v ) ) {
		foreach ( $v as $k => $x ) {
			if ( is_string( $k ) && preg_match( '/(pass|secret|token|api_key|apikey|access|refresh|client_id|client_secret|license)/i', $k ) ) {
				unset( $v[ $k ] );
			} else {
				$v[ $k ] = mrzaib_settings_strip( $x );
			}
		}
	}
	return $v;
}

/** The store's plugin + WooCommerce settings (no passwords, keys or page IDs). */
function mrzaib_settings_collect() {
	global $wpdb;
	$out   = array();
	$names = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'woocommerce\_%' OR option_name LIKE 'wpseo%' OR option_name LIKE 'ig\_es\_%' OR option_name LIKE 'aws\_%' OR option_name IN ('_sp_wqvpro_options','wpgs_form','wp_mail_smtp','mrzaib_forms','mrzaib_dtime_karachi','mrzaib_dtime_rest','mrzaib_newsletter')" ); // phpcs:ignore
	foreach ( $names as $n ) {
		if ( 0 !== strpos( $n, '_sp_' ) && 0 !== strpos( $n, 'mrzaib_' ) && mrzaib_settings_skip( $n ) ) {
			continue;
		}
		$v = get_option( $n );
		if ( is_object( $v ) ) {
			continue;
		}
		$out[ $n ] = mrzaib_settings_strip( $v );
	}
	return $out;
}

/** Copy the bundled settings (assets/data/plugin-settings.json) into this site. */
function mrzaib_setup_import_settings() {
	$file = MRZAIB_DIR . '/assets/data/plugin-settings.json';
	if ( ! file_exists( $file ) ) {
		return 0;
	}
	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore
	if ( ! is_array( $data ) || empty( $data['options'] ) ) {
		return 0;
	}
	$n = 0;
	foreach ( $data['options'] as $name => $v ) {
		if ( ! is_string( $name ) || ( 0 !== strpos( $name, '_sp_' ) && 0 !== strpos( $name, 'mrzaib_' ) && mrzaib_settings_skip( $name ) ) ) {
			continue;
		}
		$old = get_option( $name );
		if ( is_array( $old ) && is_array( $v ) ) {
			$v = array_replace_recursive( $old, $v ); // keeps this site's own passwords / IDs.
		}
		update_option( $name, $v );
		$n++;
	}
	return $n;
}

add_action( 'admin_post_mrzaib_export_settings', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'mrzaib_export_settings' ) ) {
		wp_die( 'No permission.' );
	}
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=mrzaib-plugin-settings.json' );
	echo wp_json_encode( array( 'site' => home_url(), 'date' => gmdate( 'c' ), 'options' => mrzaib_settings_collect() ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
} );

/** The parts of "Add All Data", in order. */
function mrzaib_setup_data_steps() {
	$s = array( array( 'data:base', 'Store settings, categories and pages' ) );
	if ( function_exists( 'mrzaib_store_products' ) ) {
		foreach ( mrzaib_store_products() as $d ) {
			$s[] = array( 'product:' . $d['slug'], 'Product: ' . $d['name'] );
		}
	}
	if ( function_exists( 'mrzaib_blog_posts' ) ) {
		foreach ( mrzaib_blog_posts() as $d ) {
			$s[] = array( 'blog:' . $d['slug'], 'Blog: ' . $d['title'] );
		}
	}
	$s[] = array( 'data:menus', 'Menus, logo, site icon and policies' );
	$s[] = array( 'data:media', 'Homepage banners, promo, icons and videos' );
	$s[] = array( 'data:videos', 'Product videos' );
	$s[] = array( 'data:finish', 'Prices, SEO and final checks' );
	return $s;
}

function mrzaib_setup_run_step( $step ) {
	@set_time_limit( 300 ); // phpcs:ignore
	wp_raise_memory_limit( 'admin' );
	$plugins = mrzaib_setup_plugins();
	if ( 0 === strpos( $step, 'plugin:' ) ) {
		$slug = substr( $step, 7 );
		if ( ! isset( $plugins[ $slug ] ) ) {
			return new WP_Error( 'x', 'Unknown plugin.' );
		}
		$msg = mrzaib_install_plugin( $slug, $plugins[ $slug ][0], $plugins[ $slug ][1] );
		if ( 'woocommerce' === $slug ) {
			delete_transient( '_wc_activation_redirect' );
		}
		return mrzaib_setup_plugin_active( $plugins[ $slug ][0] ) ? $plugins[ $slug ][1] . ': installed and active.' : new WP_Error( 'x', $msg );
	}
	if ( 'configure' === $step ) {
		return 'Settings applied. ' . mrzaib_setup_configure();
	}
	if ( ! class_exists( 'WooCommerce' ) ) {
		return new WP_Error( 'x', 'WooCommerce is not active. Run step 1 first.' );
	}
	if ( 0 === strpos( $step, 'product:' ) ) {
		$cats = mrzaib_install_categories();
		return mrzaib_install_products( $cats, substr( $step, 8 ) );
	}
	if ( 0 === strpos( $step, 'blog:' ) ) {
		return mrzaib_install_blogs( substr( $step, 5 ) );
	}
	switch ( $step ) {
		case 'data:base':
			mrzaib_wc_base_settings();
			if ( function_exists( 'mrzaib_setup_shipping_zones' ) && ! get_option( 'mrzaib_ship_v' ) ) {
				mrzaib_setup_shipping_zones();
			}
			$out  = mrzaib_remove_dummy();
			$cats = mrzaib_install_categories();
			$pg   = mrzaib_install_pages();
			return sprintf( '%d categories and %d pages ready. %s', count( $cats ), count( $pg ), $out );
		case 'data:menus':
			mrzaib_build_menus();
			if ( ! get_theme_mod( 'custom_logo' ) || ! get_post( (int) get_theme_mod( 'custom_logo' ) ) ) {
				$logo = mrzaib_import_image( 'logo-gold.png' );
				if ( $logo ) {
					set_theme_mod( 'custom_logo', $logo );
				}
			}
			if ( function_exists( 'mrzaib_brand_media' ) && ! get_option( 'site_icon' ) ) {
				$icon = mrzaib_brand_media( 'assets/images/favicon-512.png', 'MR ZAIB Site Icon', 'MR ZAIB crown logo' );
				if ( $icon ) {
					update_option( 'site_icon', $icon );
				}
			}
			if ( function_exists( 'mrzaib_og_image_id' ) ) {
				mrzaib_og_image_id();
			}
			if ( function_exists( 'mrzaib_fill_policy_pages' ) ) {
				mrzaib_fill_policy_pages();
				update_option( 'mrzaib_policy_v', MRZAIB_POLICY_V );
			}
			return 'Main + footer menus, logo, site icon and policy pages ready.';
		case 'data:media':
			$n = mrzaib_import_home_media( true );
			$h = mrzaib_reset_hero_banners();
			return sprintf( 'Homepage media added (%d files), %d hero banners in order.', (int) $n, (int) $h );
		case 'data:videos':
			$a = (int) mrzaib_add_product_videos();
			$b = (int) mrzaib_replace_main_videos();
			return sprintf( '%d product videos added to the homepage.', $a + $b );
		case 'data:finish':
			update_option( 'mrzaib_data_done', time() );
			update_option( 'mrzaib_demo_imported', time() );
			if ( function_exists( 'mrzaib_migrate_categories_v2' ) ) {
				mrzaib_migrate_categories_v2();
			}
			if ( function_exists( 'mrzaib_apply_prices_v2' ) ) {
				mrzaib_apply_prices_v2();
			}
			if ( defined( 'MRZAIB_BLOG_V' ) ) {
				update_option( 'mrzaib_blog_v', MRZAIB_BLOG_V, false );
			}
			if ( defined( 'MRZAIB_BRAND_V' ) ) {
				update_option( 'mrzaib_brand_v', MRZAIB_BRAND_V );
			}
			if ( defined( 'WPSEO_VERSION' ) && function_exists( 'mrzaib_configure_yoast' ) ) {
				mrzaib_configure_yoast();
			}
			update_option( 'woocommerce_coming_soon', 'no' );
			update_option( 'permalink_structure', '/%postname%/' );
			flush_rewrite_rules();
			return 'All data added. Your website is ready.';
	}
	return new WP_Error( 'x', 'Unknown step.' );
}

add_action( 'wp_ajax_mrzaib_setup_step', function () {
	if ( ! current_user_can( 'install_plugins' ) || ! check_ajax_referer( 'mrzaib_setup', 'nonce', false ) ) {
		wp_send_json_error( 'No permission.' );
	}
	$step = sanitize_text_field( wp_unslash( $_POST['step'] ?? '' ) ); // phpcs:ignore
	ob_start();
	try {
		$r = mrzaib_setup_run_step( $step );
	} catch ( Throwable $e ) {
		$r = new WP_Error( 'x', $e->getMessage() );
	}
	ob_end_clean();
	if ( is_wp_error( $r ) ) {
		wp_send_json_error( $r->get_error_message() );
	}
	wp_send_json_success( is_string( $r ) ? $r : 'Done.' );
} );

function mrzaib_setup_wizard_page() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	$plugins = mrzaib_setup_plugins();
	$ok      = mrzaib_setup_plugins_ok();
	$data    = mrzaib_data_ready();
	$psteps  = array();
	foreach ( $plugins as $slug => $p ) {
		$psteps[] = array( 'plugin:' . $slug, $p[1] );
	}
	$psteps[] = array( 'configure', 'MR ZAIB settings for all plugins' );
	?>
	<div class="wrap mz-setup">
		<h1>MR ZAIB Setup</h1>
		<style>
			.mz-setup .mz-box{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:22px 26px;max-width:760px;margin:18px 0}
			.mz-setup .mz-box h2{margin:0 0 6px;font-size:18px}
			.mz-setup .mz-list{margin:14px 0;padding:0;list-style:none}
			.mz-setup .mz-list li{padding:5px 0;border-bottom:1px solid #f0f0f1;display:flex;gap:10px}
			.mz-setup .mz-st{width:20px;text-align:center;font-weight:700}
			.mz-setup .ok{color:#008a20}.mz-setup .no{color:#a7aaad}.mz-setup .err{color:#d63638}.mz-setup .run{color:#2271b1}
			.mz-setup .mz-msg{color:#50575e;font-size:12px}
			.mz-setup .mz-done{font-size:15px;color:#008a20;font-weight:600}
		</style>

		<div class="mz-box">
			<h2>1. Plugins</h2>
			<ul class="mz-list" id="mz-plist">
				<?php foreach ( $psteps as $s ) :
					$is = 'configure' === $s[0] ? $ok : mrzaib_setup_plugin_active( $plugins[ substr( $s[0], 7 ) ][0] ); ?>
					<li data-step="<?php echo esc_attr( $s[0] ); ?>"><span class="mz-st <?php echo $is ? 'ok' : 'no'; ?>"><?php echo $is ? '&#10004;' : '&#9675;'; ?></span><span><?php echo esc_html( $s[1] ); ?> <span class="mz-msg"></span></span></li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $ok ) : ?>
				<p class="mz-done">&#10004; All plugins are installed, active and set up.</p>
			<?php else : ?>
				<p><button type="button" class="button button-primary button-hero" id="mz-run-plugins">Install &amp; Activate All Plugins</button></p>
			<?php endif; ?>
			<p class="description">Wordfence license (API key): open Wordfence &rarr; Resume Installation and get the free key with your own account.</p>
			<?php if ( $ok ) : ?>
				<p><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=mrzaib_export_settings' ), 'mrzaib_export_settings' ) ); ?>">Export plugin settings (.json)</a> <span class="description">Downloads this store's WooCommerce and plugin settings (no passwords).</span></p>
			<?php endif; ?>
		</div>

		<?php if ( $ok && ! $data ) : ?>
			<div class="mz-box">
				<h2>2. Website data</h2>
				<p>Adds everything: categories, all MR ZAIB perfumes with photos, prices and notes, blog articles, pages, menus, policies, homepage banners and videos, logo and SEO.</p>
				<ul class="mz-list" id="mz-dlist">
					<?php foreach ( mrzaib_setup_data_steps() as $s ) : ?>
						<li data-step="<?php echo esc_attr( $s[0] ); ?>"><span class="mz-st no">&#9675;</span><span><?php echo esc_html( $s[1] ); ?> <span class="mz-msg"></span></span></li>
					<?php endforeach; ?>
				</ul>
				<p><button type="button" class="button button-primary button-hero" id="mz-run-data">Add All Data</button></p>
			</div>
		<?php elseif ( $ok && $data ) : ?>
			<div class="mz-box">
				<h2>2. Website data</h2>
				<p class="mz-done">&#10004; All website data is added. <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank">View your website &rarr;</a></p>
			</div>
		<?php endif; ?>
	</div>
	<script>
	(function(){
		var ajax=<?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>, nonce=<?php echo wp_json_encode( wp_create_nonce( 'mrzaib_setup' ) ); ?>;
		function run(listId,btn){
			var rows=[].slice.call(document.querySelectorAll('#'+listId+' li')).filter(function(li){return !li.querySelector('.mz-st.ok');}), i=0;
			btn.disabled=true; btn.textContent='Working… please keep this page open';
			window.onbeforeunload=function(){return 'Setup is running';};
			(function next(){
				if(i>=rows.length){ window.onbeforeunload=null; btn.textContent='Done!'; setTimeout(function(){location.reload();},1200); return; }
				var li=rows[i], st=li.querySelector('.mz-st'), msg=li.querySelector('.mz-msg');
				st.className='mz-st run'; st.innerHTML='&#8635;';
				var fd=new FormData(); fd.append('action','mrzaib_setup_step'); fd.append('nonce',nonce); fd.append('step',li.getAttribute('data-step'));
				fetch(ajax,{method:'POST',credentials:'same-origin',body:fd}).then(function(r){return r.text();}).then(function(t){
					var j; try{ j=JSON.parse(t.slice(t.indexOf('{'))); }catch(e){ j={success:false,data:'Server error. Click the button again to continue.'}; }
					if(j.success){ st.className='mz-st ok'; st.innerHTML='&#10004;'; msg.textContent=' '+(j.data||''); i++; next(); }
					else { st.className='mz-st err'; st.innerHTML='&#10008;'; msg.textContent=' '+(j.data||'Failed'); window.onbeforeunload=null; btn.disabled=false; btn.textContent='Try Again'; }
				}).catch(function(){ st.className='mz-st err'; st.innerHTML='&#10008;'; msg.textContent=' Connection error.'; window.onbeforeunload=null; btn.disabled=false; btn.textContent='Try Again'; });
			})();
		}
		var p=document.getElementById('mz-run-plugins'); if(p){ p.addEventListener('click',function(){ run('mz-plist',p); }); }
		var d=document.getElementById('mz-run-data'); if(d){ d.addEventListener('click',function(){ run('mz-dlist',d); }); }
	})();
	</script>
	<?php
}
