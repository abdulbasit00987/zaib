<?php
/**
 * Newsletter via the free "Icegram Express - Email Subscribers & Newsletters" plugin
 * (wordpress.org/plugins/email-subscribers): unlimited subscribers and emails.
 *
 * - The theme's footer form saves subscribers into the plugin's "Main" list.
 * - One-time setup creates a Post Notification campaign for new blog posts AND new
 *   WooCommerce products, a welcome email and single opt-in.
 * - Subscribers, emails, unsubscribes and reports are all managed in the plugin's menu.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MRZAIB_ES_SLUG', 'email-subscribers' );
define( 'MRZAIB_ES_FILE', 'email-subscribers/email-subscribers.php' );
define( 'MRZAIB_ES_V', 1 );

function mrzaib_es_active() {
	return function_exists( 'ES' ) && class_exists( 'ES_Handle_Subscription' );
}

/* ------------------------------------------------------------------
 * Plugin setup (runs once in admin when the plugin is active)
 * ------------------------------------------------------------------ */
function mrzaib_es_main_list() {
	if ( ! mrzaib_es_active() || ! isset( ES()->lists_db ) ) {
		return array();
	}
	$name = defined( 'IG_MAIN_LIST' ) ? IG_MAIN_LIST : 'Main';
	$list = ES()->lists_db->get_list_by_name( $name );
	if ( empty( $list ) ) {
		ES()->lists_db->add_list( $name );
		$list = ES()->lists_db->get_list_by_name( $name );
	}
	return (array) $list;
}

function mrzaib_es_notification_body() {
	$gold = '#deba68';
	$f    = "'Jost','Helvetica Neue',Helvetica,Arial,sans-serif";
	return '<div style="max-width:600px;margin:0 auto;background:#ffffff;font-family:' . $f . ';color:#333">'
		. '<div style="background:#000;padding:22px 20px;text-align:center"><img src="' . esc_url( function_exists( 'mrzaib_email_logo_url' ) ? mrzaib_email_logo_url() : '' ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" width="150" style="width:150px;max-width:60%;height:auto"></div>'
		. '<div style="height:4px;background:' . $gold . '"></div>'
		. '<div style="padding:30px 32px 10px;text-align:center">'
		. '<p style="margin:0 0 8px;font-size:12px;letter-spacing:3px;text-transform:uppercase;color:#a8843c;font-weight:600">New at ' . esc_html( get_bloginfo( 'name' ) ) . '</p>'
		. '<h1 style="margin:0 0 18px;font-family:Georgia,serif;font-size:26px;color:#111">{{POSTTITLE}}</h1>'
		. '<a href="{{POSTLINK-ONLY}}"><img src="{{POSTIMAGE-URL}}" alt="{{POSTTITLE}}" style="display:block;width:100%;max-width:520px;height:auto;margin:0 auto;border-radius:10px"></a>'
		. '<p style="margin:20px 0 0;font-size:15px;line-height:1.7;text-align:left">{{POSTEXCERPT}}</p>'
		. '<p style="margin:26px 0 10px"><a href="{{POSTLINK-ONLY}}" style="display:inline-block;background:' . $gold . ';color:#111;text-decoration:none;font-size:14px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;padding:15px 34px;border-radius:4px">View Now</a></p>'
		. '</div>'
		. '<div style="background:#000;color:#bbb;padding:20px;text-align:center;font-size:12.5px"><a href="' . esc_url( home_url( '/' ) ) . '" style="color:' . $gold . ';text-decoration:none">' . esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ) . '</a></div>'
		. '</div>';
}

function mrzaib_es_setup( $force = false ) {
	if ( ! mrzaib_es_active() ) {
		return false;
	}
	if ( ! $force && (int) get_option( 'mrzaib_es_v' ) >= MRZAIB_ES_V ) {
		return true;
	}
	$list = mrzaib_es_main_list();
	if ( empty( $list['id'] ) ) {
		return false;
	}
	$site = get_bloginfo( 'name' );
	$from = function_exists( 'mrzaib_forms_opt' ) && is_email( mrzaib_forms_opt( 'from_email' ) ) ? mrzaib_forms_opt( 'from_email' ) : get_option( 'admin_email' );

	if ( ! get_option( 'ig_es_from_name' ) ) {
		update_option( 'ig_es_from_name', $site );
	}
	if ( ! get_option( 'ig_es_from_email' ) ) {
		update_option( 'ig_es_from_email', $from );
	}
	update_option( 'ig_es_optin_type', 'single_opt_in' );
	update_option( 'ig_es_enable_welcome_email', 'yes' );
	update_option( 'ig_es_welcome_email_subject', sprintf( 'Welcome to %s!', $site ) );
	update_option( 'ig_es_welcome_email_content', "Hi {{NAME}},\r\n\r\nThank you for subscribing to " . $site . "! You will be the first to know about our new perfumes, exclusive offers and latest news.\r\n\r\nShop now: " . home_url( '/' ) . "\r\n\r\nWarm regards,\r\nTeam " . $site );
	update_option( 'ig_es_onboarding_complete', 'yes' );

	// Post Notification campaign: new blog posts + new products.
	if ( isset( ES()->campaigns_db ) ) {
		$cid = (int) get_option( 'mrzaib_es_campaign' );
		$row = $cid ? ES()->campaigns_db->get( $cid ) : null;
		if ( ! $row ) {
			$cid = ES()->campaigns_db->insert( array(
				'name'       => 'MR ZAIB: New Product & Blog Post',
				'subject'    => 'New at ' . $site . ': {{POSTTITLE}}',
				'body'       => mrzaib_es_notification_body(),
				'from_name'  => get_option( 'ig_es_from_name', $site ),
				'from_email' => get_option( 'ig_es_from_email', $from ),
				'type'       => 'post_notification',
				'status'     => 1,
				'list_ids'   => (string) $list['id'],
				'categories' => '##post:all|product:all##',
				'meta'       => maybe_serialize( array() ),
			) );
			if ( $cid ) {
				update_option( 'mrzaib_es_campaign', (int) $cid );
				if ( class_exists( 'ES_Campaign_Controller' ) ) {
					ES_Campaign_Controller::add_to_new_category_format_campaign_ids( (int) $cid );
				} else {
					$ids   = (array) get_option( 'ig_es_new_category_format_campaign_ids', array() );
					$ids[] = (int) $cid;
					update_option( 'ig_es_new_category_format_campaign_ids', $ids, false );
				}
			}
		}
	}
	update_option( 'mrzaib_es_v', MRZAIB_ES_V );
	return true;
}

add_action( 'admin_init', function () {
	if ( mrzaib_es_active() && current_user_can( 'manage_options' ) ) {
		mrzaib_es_setup();
	}
}, 30 );

/* Install / activate button when the plugin is missing. */
add_action( 'admin_notices', function () {
	if ( mrzaib_es_active() || ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	if ( isset( $_GET['mrzaib_es_install'] ) && check_admin_referer( 'mrzaib_es_install' ) && function_exists( 'mrzaib_install_plugin' ) ) {
		$msg = mrzaib_install_plugin( MRZAIB_ES_SLUG, MRZAIB_ES_FILE, 'Icegram Express (Email Subscribers & Newsletters)' );
		echo '<div class="notice notice-info"><p>' . esc_html( $msg ) . ' <a href="' . esc_url( admin_url( 'index.php' ) ) . '">' . esc_html__( 'Continue', 'mrzaib' ) . '</a></p></div>';
		return;
	}
	$url = wp_nonce_url( add_query_arg( 'mrzaib_es_install', 1, admin_url( 'index.php' ) ), 'mrzaib_es_install' );
	echo '<div class="notice notice-warning"><p><strong>MR ZAIB:</strong> ' . esc_html__( 'The footer Subscribe form needs the free Icegram Express (Email Subscribers & Newsletters) plugin: unlimited subscribers, and an automatic email to them for every new product or blog post.', 'mrzaib' ) . ' <a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Install & Activate', 'mrzaib' ) . '</a></p></div>';
} );

/* ------------------------------------------------------------------
 * Footer form -> plugin subscription
 * ------------------------------------------------------------------ */

/**
 * @return array ok(bool), message(string)
 */
function mrzaib_es_subscribe( $email, $hp = '' ) {
	if ( '' !== trim( (string) $hp ) ) {
		return array( 'ok' => true, 'message' => __( 'Thanks for subscribing!', 'mrzaib' ) );
	}
	$email = strtolower( sanitize_email( $email ) );
	if ( ! is_email( $email ) ) {
		return array( 'ok' => false, 'message' => __( 'Please enter a valid email address.', 'mrzaib' ) );
	}
	if ( ! mrzaib_es_active() ) {
		return array( 'ok' => false, 'message' => __( 'Subscription is not available right now. Please try again later.', 'mrzaib' ) );
	}
	$list = mrzaib_es_main_list();
	if ( empty( $list['hash'] ) ) {
		return array( 'ok' => false, 'message' => __( 'Subscription is not available right now. Please try again later.', 'mrzaib' ) );
	}
	$hp_key = class_exists( 'ES_Common' ) && method_exists( 'ES_Common', 'get_honeypot_field_name' ) ? ES_Common::get_honeypot_field_name( 0 ) : 'esfpx_es_hp_email';
	$data   = array(
		'esfpx_name'          => '',
		'esfpx_email'         => $email,
		'esfpx_lists'         => array( $list['hash'] ),
		'esfpx_form_id'       => 0,
		'esfpx_ip_address'    => function_exists( 'ig_es_get_ip' ) ? ig_es_get_ip() : '',
		'esfpx_reference_site' => home_url( '/' ),
		$hp_key               => '',
	);
	defined( 'IG_ES_RETURN_HANDLE_RESPONSE' ) || define( 'IG_ES_RETURN_HANDLE_RESPONSE', true );
	$handler = new ES_Handle_Subscription();
	$res     = (array) $handler->process_request( $data );
	$code    = isset( $res['message'] ) ? $res['message'] : '';
	if ( 'es_email_exists_notice' === $code ) {
		return array( 'ok' => true, 'message' => __( 'You are already subscribed. Thank you!', 'mrzaib' ) );
	}
	if ( isset( $res['status'] ) && 'SUCCESS' === $res['status'] ) {
		return array( 'ok' => true, 'message' => __( 'Thanks for subscribing! Please check your inbox.', 'mrzaib' ) );
	}
	if ( 'es_rate_limit_notice' === $code ) {
		return array( 'ok' => false, 'message' => __( 'Please wait a moment and try again.', 'mrzaib' ) );
	}
	return array( 'ok' => false, 'message' => ! empty( $res['message_text'] ) ? wp_strip_all_tags( $res['message_text'] ) : __( 'Something went wrong. Please try again.', 'mrzaib' ) );
}

function mrzaib_nl_ajax() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public form; cached pages carry stale nonces, honeypot stops bots.
	$r = mrzaib_es_subscribe( isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '', isset( $_POST['mz_website'] ) ? wp_unslash( $_POST['mz_website'] ) : '' );
	// phpcs:enable
	wp_send_json( $r );
}
add_action( 'wp_ajax_mrzaib_subscribe', 'mrzaib_nl_ajax' );
add_action( 'wp_ajax_nopriv_mrzaib_subscribe', 'mrzaib_nl_ajax' );

/* No-JS fallback. */
add_action( 'template_redirect', function () {
	if ( ! isset( $_POST['mz_nl_submit'] ) ) { // phpcs:ignore
		return;
	}
	$r = mrzaib_es_subscribe( isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '', isset( $_POST['mz_website'] ) ? wp_unslash( $_POST['mz_website'] ) : '' ); // phpcs:ignore
	set_transient( 'mrzaib_nl_msg_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) ), $r, 60 ); // phpcs:ignore
	wp_safe_redirect( ( wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) . '#mz-newsletter' );
	exit;
} );

/** Footer form markup. */
function mrzaib_newsletter_form() {
	$key = 'mrzaib_nl_msg_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) ); // phpcs:ignore
	$r   = get_transient( $key );
	if ( $r ) {
		delete_transient( $key );
	}
	?>
	<form class="nl-form" action="" method="post" data-newsletter id="mz-newsletter" novalidate>
		<input type="email" name="email" required placeholder="<?php esc_attr_e( 'Enter your email', 'mrzaib' ); ?>" aria-label="<?php esc_attr_e( 'Email address', 'mrzaib' ); ?>">
		<span class="mz-hp" aria-hidden="true" style="position:absolute;left:-9999px"><input type="text" name="mz_website" tabindex="-1" autocomplete="off"></span>
		<button type="submit" name="mz_nl_submit" value="1" class="btn btn-dark"><?php esc_html_e( 'Subscribe', 'mrzaib' ); ?></button>
		<p class="nl-msg<?php echo ( $r && empty( $r['ok'] ) ) ? ' is-error' : ''; ?>" role="status" <?php echo $r ? '' : 'hidden'; ?>><?php echo $r ? esc_html( $r['message'] ) : ''; ?></p>
	</form>
	<?php
}
