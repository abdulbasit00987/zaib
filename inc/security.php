<?php
/**
 * Security: Wordfence install button + recommended settings, and small theme-level hardening.
 *
 * - Admin notice with "Install & Activate" for the free Wordfence Security plugin.
 * - Once Wordfence is active, MR ZAIB applies recommended settings one time
 *   (option mrzaib_wf_v, bump MRZAIB_WF_V to re-apply). Admin can change them later in Wordfence.
 * - Hardening that works without any plugin: no file editor, no XML-RPC, hidden WP version, safe headers.
 *
 * @package mrzaib
 */

defined( 'ABSPATH' ) || exit;

define( 'MRZAIB_WF_V', 2 );
define( 'MRZAIB_WF_SLUG', 'wordfence' );
define( 'MRZAIB_WF_FILE', 'wordfence/wordfence.php' );

/* ---------- theme-level hardening ---------- */
// Hackers who get into wp-admin often use the built-in file editor to plant malware. Turn it off.
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true );
}
// XML-RPC is a common brute-force target and the store does not need it.
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'wp_headers', function ( $h ) {
	unset( $h['X-Pingback'] );
	return $h;
} );
// Hide the WordPress version.
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );
// Safe response headers.
add_action( 'send_headers', function () {
	if ( headers_sent() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
} );
// Do not reveal usernames through ?author=N.
add_action( 'template_redirect', function () {
	if ( ! is_admin() && isset( $_GET['author'] ) && ! current_user_can( 'list_users' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );
// Hide users from the public REST API (usernames are half of a login).
add_filter( 'rest_endpoints', function ( $e ) {
	if ( ! is_user_logged_in() ) {
		unset( $e['/wp/v2/users'], $e['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $e;
} );

/* ---------- Wordfence ---------- */
function mrzaib_wf_active() {
	return class_exists( 'wfConfig' );
}

/** Recommended Wordfence settings for the MR ZAIB store. */
function mrzaib_wf_settings() {
	$s = array(
		// Firewall + brute force protection.
		'firewallEnabled'               => 1,
		'loginSecurityEnabled'          => 1,
		'loginSec_maxFailures'          => 5,
		'loginSec_maxForgotPasswd'      => 5,
		'loginSec_countFailMins'        => 240,
		'loginSec_lockoutMins'          => 240,
		'loginSec_lockInvalidUsers'     => 1,
		'loginSec_maskLoginErrors'      => 1,
		'loginSec_blockAdminReg'        => 1,
		'loginSec_disableAuthorScan'    => 1,
		'loginSec_disableOEmbedAuthor'  => 1,
		'loginSec_strongPasswds_enabled' => 1,
		'loginSec_strongPasswds'        => 'pubs',
		'loginSec_breachPasswds_enabled' => 1,
		'loginSec_breachPasswds'        => 'admins',
		'other_blockBadPOST'            => 1,
		'other_pwStrengthOnUpdate'      => 1,
		'other_WFNet'                   => 1,
		'other_hideWPVersion'           => 1,
		// Stop PHP files running from the uploads folder (blocks uploaded malware). Apache/LiteSpeed.
		'disableCodeExecutionUploads'   => 1,
		// Keep Wordfence itself updated.
		'autoUpdate'                    => 1,
		// Rate limiting: generous, so real customers on mobile networks are never blocked.
		'maxGlobalRequests'             => 'DISABLED',
		'maxRequestsCrawlers'           => '120',
		'maxRequestsCrawlers_action'    => 'throttle',
		'maxRequestsHumans'             => '240',
		'maxRequestsHumans_action'      => 'throttle',
		'max404Crawlers'                => '60',
		'max404Crawlers_action'         => 'throttle',
		'max404Humans'                  => '120',
		'max404Humans_action'           => 'throttle',
		'blockedTime'                   => 3600,
		'neverBlockBG'                  => 'neverBlockVerified',
		// Malware scans: scheduled, covering core, themes, plugins, files, posts and comments.
		'scheduledScansEnabled'         => 1,
		'scansEnabled_core'             => 1,
		'scansEnabled_themes'           => 1,
		'scansEnabled_plugins'          => 1,
		'scansEnabled_coreUnknown'      => 1,
		'scansEnabled_malware'          => 1,
		'scansEnabled_fileContents'     => 1,
		'scansEnabled_fileContentsGSB'  => 1,
		'scansEnabled_checkGSB'         => 1,
		'scansEnabled_checkReadableConfig' => 1,
		'scansEnabled_suspectedFiles'   => 1,
		'scansEnabled_posts'            => 1,
		'scansEnabled_comments'         => 1,
		'scansEnabled_suspiciousOptions' => 1,
		'scansEnabled_oldVersions'      => 1,
		'scansEnabled_suspiciousAdminUsers' => 1,
		'scansEnabled_passwds'          => 1,
		'scansEnabled_wafStatus'        => 1,
		'scansEnabled_highSense'        => 0,
		// Email alerts that matter, without spam.
		'alertEmails'                   => get_option( 'admin_email' ),
		'alertOn_critical'              => 1,
		'alertOn_warnings'              => 0,
		'alertOn_update'                => 1,
		'alertOn_wordfenceDeactivated'  => 1,
		'alertOn_adminLogin'            => 1,
		'alertOn_firstAdminLoginOnly'   => 1,
		'alertOn_nonAdminLogin'         => 0,
		'alertOn_loginLockout'          => 0,
		'alertOn_block'                 => 0,
		'alertOn_throttle'              => 0,
		'alertOn_breachLogin'           => 1,
		'wafAlertOnAttacks'             => 1,
		'notification_promotions'       => 0,
		'notification_blogHighlights'   => 0,
		// Live traffic logging slows small hosting down.
		'liveTrafficEnabled'            => 0,
	);
	// Instantly block common hacker usernames, but never one that a real user of this site has.
	$names = array();
	foreach ( array( 'admin', 'administrator', 'root', 'test', 'user', 'demo', 'webmaster' ) as $n ) {
		if ( ! username_exists( $n ) ) {
			$names[] = $n;
		}
	}
	if ( $names ) {
		$s['loginSec_userBlacklist'] = implode( "\n", $names );
	}
	return $s;
}

/** Apply the settings. Returns a short status message. */
function mrzaib_wf_configure() {
	if ( ! mrzaib_wf_active() ) {
		return 'Wordfence is not active.';
	}
	$done = 0;
	foreach ( mrzaib_wf_settings() as $k => $v ) {
		try {
			if ( 'autoUpdate' === $k && method_exists( 'wfConfig', 'enableAutoUpdate' ) ) {
				wfConfig::enableAutoUpdate();
			} elseif ( 'disableCodeExecutionUploads' === $k && method_exists( 'wfConfig', 'disableCodeExecutionForUploads' ) ) {
				wfConfig::set( $k, 1 );
				wfConfig::disableCodeExecutionForUploads();
			} else {
				wfConfig::set( $k, $v );
			}
			$done++;
		} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
			// One setting failing (e.g. .htaccess not writable) must not stop the rest.
		}
	}
	// Wordfence Login Security: protect the WooCommerce login / register forms too.
	if ( class_exists( '\\WordfenceLS\\Controller_Settings' ) ) {
		try {
			\WordfenceLS\Controller_Settings::shared()->set( \WordfenceLS\Controller_Settings::OPTION_ENABLE_WOOCOMMERCE_INTEGRATION, true );
			$done++;
		} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
		}
	}
	update_option( 'mrzaib_wf_v', MRZAIB_WF_V );
	return sprintf( 'Wordfence configured with MR ZAIB recommended settings (%d settings).', $done );
}

add_action( 'admin_init', function () {
	if ( mrzaib_wf_active() && current_user_can( 'manage_options' ) && (int) get_option( 'mrzaib_wf_v' ) < MRZAIB_WF_V ) {
		mrzaib_wf_configure();
		set_transient( 'mrzaib_wf_done', 1, HOUR_IN_SECONDS );
	}
}, 40 );

add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	if ( mrzaib_wf_active() ) {
		if ( isset( $_GET['mrzaib_wf_apply'] ) && check_admin_referer( 'mrzaib_wf_apply' ) ) {
			echo '<div class="notice notice-success"><p><strong>MR ZAIB:</strong> ' . esc_html( mrzaib_wf_configure() ) . '</p></div>';
			return;
		}
		if ( get_transient( 'mrzaib_wf_done' ) ) {
			delete_transient( 'mrzaib_wf_done' );
			echo '<div class="notice notice-success is-dismissible"><p><strong>MR ZAIB:</strong> ' . esc_html__( 'Wordfence is set up with the recommended settings. Finish the Wordfence installation (free license), then open Wordfence > Firewall and click "Optimize the Wordfence Firewall". The firewall learns your site for 7 days, then blocks attacks on its own.', 'mrzaib' ) . '</p></div>';
		}
		return;
	}
	if ( isset( $_GET['mrzaib_wf_install'] ) && check_admin_referer( 'mrzaib_wf_install' ) && function_exists( 'mrzaib_install_plugin' ) ) {
		$msg = mrzaib_install_plugin( MRZAIB_WF_SLUG, MRZAIB_WF_FILE, 'Wordfence Security' );
		echo '<div class="notice notice-info"><p>' . esc_html( $msg ) . ' <a href="' . esc_url( admin_url( 'index.php' ) ) . '">' . esc_html__( 'Continue', 'mrzaib' ) . '</a></p></div>';
		return;
	}
	$url = wp_nonce_url( add_query_arg( 'mrzaib_wf_install', 1, admin_url( 'index.php' ) ), 'mrzaib_wf_install' );
	echo '<div class="notice notice-warning"><p><strong>MR ZAIB:</strong> ' . esc_html__( 'Protect your store from hackers and malware with the free Wordfence Security plugin. The theme sets it up with the recommended settings automatically.', 'mrzaib' ) . ' <a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Install & Activate Wordfence', 'mrzaib' ) . '</a></p></div>';
} );

/* "Re-apply recommended settings" link on the Plugins screen next to Wordfence. */
add_filter( 'plugin_action_links_' . MRZAIB_WF_FILE, function ( $links ) {
	if ( mrzaib_wf_active() && current_user_can( 'manage_options' ) ) {
		$links[] = '<a href="' . esc_url( wp_nonce_url( add_query_arg( 'mrzaib_wf_apply', 1, admin_url( 'index.php' ) ), 'mrzaib_wf_apply' ) ) . '">' . esc_html__( 'MR ZAIB settings', 'mrzaib' ) . '</a>';
	}
	return $links;
} );
