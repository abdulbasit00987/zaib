<?php
/**
 * Newsletter: footer subscribe form, subscriber list, and automatic emails to all
 * subscribers when a new product or blog post is published.
 *
 * Dashboard: Subscribers (list, export CSV) and Subscribers > Newsletter Settings.
 * Emails go through wp_mail(), so they use the SMTP set in Form Submissions > Email Settings.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MRZAIB_NL_BATCH', 40 );

function mrzaib_nl_defaults() {
	return array(
		'products'      => 1,
		'posts'         => 1,
		'welcome'       => 1,
		'admin_notify'  => 0,
		'subj_product'  => 'New arrival: {title} is here!',
		'subj_post'     => 'New on our blog: {title}',
		'subj_welcome'  => 'Welcome to {site}!',
	);
}

function mrzaib_nl_opt( $key ) {
	$o = wp_parse_args( (array) get_option( 'mrzaib_newsletter', array() ), mrzaib_nl_defaults() );
	return isset( $o[ $key ] ) ? $o[ $key ] : '';
}

/* ------------------------------------------------------------------
 * Subscriber list (private post type, title = email)
 * ------------------------------------------------------------------ */
add_action( 'init', function () {
	register_post_type( 'mz_subscriber', array(
		'labels'          => array(
			'name'          => __( 'Subscribers', 'mrzaib' ),
			'singular_name' => __( 'Subscriber', 'mrzaib' ),
			'menu_name'     => __( 'Subscribers', 'mrzaib' ),
			'all_items'     => __( 'All Subscribers', 'mrzaib' ),
			'add_new_item'  => __( 'Add Subscriber', 'mrzaib' ),
			'edit_item'     => __( 'Edit Subscriber', 'mrzaib' ),
			'search_items'  => __( 'Search Subscribers', 'mrzaib' ),
			'not_found'     => __( 'No subscribers yet.', 'mrzaib' ),
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_position'   => 26,
		'menu_icon'       => 'dashicons-email-alt2',
		'supports'        => array( 'title' ),
		'capability_type' => 'post',
		'map_meta_cap'    => true,
		'capabilities'    => array( 'create_posts' => 'manage_options' ),
	) );
} );

function mrzaib_nl_find( $email ) {
	$q = get_posts( array( 'post_type' => 'mz_subscriber', 'post_status' => array( 'publish', 'draft', 'trash' ), 'title' => $email, 'numberposts' => 1, 'fields' => 'ids' ) );
	return $q ? (int) $q[0] : 0;
}

function mrzaib_nl_token( $id ) {
	$t = get_post_meta( $id, '_mz_token', true );
	if ( ! $t ) {
		$t = wp_generate_password( 32, false );
		update_post_meta( $id, '_mz_token', $t );
	}
	return $t;
}

function mrzaib_nl_unsub_url( $id ) {
	return add_query_arg( array( 'mz_unsub' => (int) $id, 't' => mrzaib_nl_token( $id ) ), home_url( '/' ) );
}

/**
 * Add (or re-activate) a subscriber.
 *
 * @return string added|exists|invalid
 */
function mrzaib_nl_add( $email, $source = 'footer' ) {
	$email = strtolower( sanitize_email( $email ) );
	if ( ! is_email( $email ) ) {
		return 'invalid';
	}
	$id = mrzaib_nl_find( $email );
	if ( $id && 'publish' === get_post_status( $id ) ) {
		return 'exists';
	}
	if ( $id ) {
		wp_untrash_post( $id );
		wp_update_post( array( 'ID' => $id, 'post_status' => 'publish' ) );
	} else {
		$id = wp_insert_post( array( 'post_type' => 'mz_subscriber', 'post_status' => 'publish', 'post_title' => $email ) );
		if ( ! $id || is_wp_error( $id ) ) {
			return 'invalid';
		}
	}
	update_post_meta( $id, '_mz_source', $source );
	mrzaib_nl_token( $id );
	if ( mrzaib_nl_opt( 'welcome' ) ) {
		mrzaib_nl_send_welcome( $id, $email );
	}
	if ( mrzaib_nl_opt( 'admin_notify' ) && function_exists( 'mrzaib_forms_notify_to' ) ) {
		wp_mail( mrzaib_forms_notify_to(), sprintf( __( 'New subscriber: %s', 'mrzaib' ), $email ), sprintf( __( '%s just subscribed to your newsletter.', 'mrzaib' ), $email ) );
	}
	return 'added';
}

function mrzaib_nl_subject( $tpl, $title = '' ) {
	return strtr( $tpl, array( '{title}' => $title, '{site}' => get_bloginfo( 'name' ) ) );
}

function mrzaib_nl_mail( $id, $email, $subject, $args ) {
	$unsub         = mrzaib_nl_unsub_url( $id );
	$args['unsub'] = $unsub;
	$html          = mrzaib_email_html( $args );
	return mrzaib_send_html_mail( $email, $subject, $html, array( 'List-Unsubscribe: <' . esc_url_raw( $unsub ) . '>' ) );
}

function mrzaib_nl_send_welcome( $id, $email ) {
	$site = get_bloginfo( 'name' );
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	mrzaib_nl_mail( $id, $email, mrzaib_nl_subject( mrzaib_nl_opt( 'subj_welcome' ) ), array(
		'eyebrow'   => __( 'Newsletter', 'mrzaib' ),
		'title'     => sprintf( __( 'Welcome to %s', 'mrzaib' ), $site ),
		'preheader' => __( 'You are on the list for new arrivals, offers and news.', 'mrzaib' ),
		'intro'     => '<p style="margin:0 0 10px">' . esc_html__( 'Thank you for subscribing! You will be the first to know about our new perfumes, exclusive offers and latest news.', 'mrzaib' ) . '</p>',
		'button'    => array( __( 'Shop Now', 'mrzaib' ), $shop ),
	) );
}

/* ------------------------------------------------------------------
 * Front end: ajax + non-js fallback + unsubscribe
 * ------------------------------------------------------------------ */
function mrzaib_nl_handle() {
	if ( ! empty( $_POST['mz_website'] ) ) { // phpcs:ignore -- honeypot.
		return 'added';
	}
	/* No nonce check on purpose: cached pages would carry stale nonces and block real visitors.
	   Subscribing is low risk; the honeypot above stops bots. */
	return mrzaib_nl_add( isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '' ); // phpcs:ignore
}

function mrzaib_nl_message( $code ) {
	$m = array(
		'added'   => __( 'Thanks for subscribing! Please check your inbox.', 'mrzaib' ),
		'exists'  => __( 'You are already subscribed. Thank you!', 'mrzaib' ),
		'invalid' => __( 'Please enter a valid email address.', 'mrzaib' ),
		'expired' => __( 'Session expired. Please refresh the page and try again.', 'mrzaib' ),
	);
	return isset( $m[ $code ] ) ? $m[ $code ] : $m['invalid'];
}

function mrzaib_nl_ajax() {
	$code = mrzaib_nl_handle();
	wp_send_json( array( 'ok' => in_array( $code, array( 'added', 'exists' ), true ), 'code' => $code, 'message' => mrzaib_nl_message( $code ) ) );
}
add_action( 'wp_ajax_mrzaib_subscribe', 'mrzaib_nl_ajax' );
add_action( 'wp_ajax_nopriv_mrzaib_subscribe', 'mrzaib_nl_ajax' );

add_action( 'template_redirect', function () {
	if ( isset( $_POST['mz_nl_submit'] ) ) { // phpcs:ignore
		$code = mrzaib_nl_handle();
		wp_safe_redirect( add_query_arg( 'mz_nl', $code, wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) . '#mz-newsletter' );
		exit;
	}
	if ( isset( $_GET['mz_unsub'], $_GET['t'] ) ) { // phpcs:ignore
		$id  = absint( $_GET['mz_unsub'] ); // phpcs:ignore
		$tok = sanitize_text_field( wp_unslash( $_GET['t'] ) ); // phpcs:ignore
		$ok  = $id && 'mz_subscriber' === get_post_type( $id ) && hash_equals( (string) get_post_meta( $id, '_mz_token', true ), $tok );
		if ( $ok ) {
			wp_trash_post( $id );
		}
		wp_die(
			'<h2 style="font-family:Georgia,serif">' . ( $ok ? esc_html__( 'You have been unsubscribed.', 'mrzaib' ) : esc_html__( 'This unsubscribe link is invalid or already used.', 'mrzaib' ) ) . '</h2><p>' . esc_html__( 'You will no longer receive newsletter emails from us.', 'mrzaib' ) . '</p><p><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Back to the store', 'mrzaib' ) . '</a></p>',
			esc_html( get_bloginfo( 'name' ) ),
			array( 'response' => 200 )
		);
	}
} );

/** Footer form markup. */
function mrzaib_newsletter_form() {
	$code = isset( $_GET['mz_nl'] ) ? sanitize_key( $_GET['mz_nl'] ) : ''; // phpcs:ignore
	?>
	<form class="nl-form" action="" method="post" data-newsletter id="mz-newsletter" novalidate>
		<input type="email" name="email" required placeholder="<?php esc_attr_e( 'Enter your email', 'mrzaib' ); ?>" aria-label="<?php esc_attr_e( 'Email address', 'mrzaib' ); ?>">
		<span class="mz-hp" aria-hidden="true" style="position:absolute;left:-9999px"><input type="text" name="mz_website" tabindex="-1" autocomplete="off"></span>
		<button type="submit" name="mz_nl_submit" value="1" class="btn btn-dark"><?php esc_html_e( 'Subscribe', 'mrzaib' ); ?></button>
		<p class="nl-msg<?php echo ( $code && ! in_array( $code, array( 'added', 'exists' ), true ) ) ? ' is-error' : ''; ?>" role="status" <?php echo $code ? '' : 'hidden'; ?>><?php echo $code ? esc_html( mrzaib_nl_message( $code ) ) : ''; ?></p>
	</form>
	<?php
}

/* ------------------------------------------------------------------
 * Automatic emails on new product / blog post
 * ------------------------------------------------------------------ */
add_action( 'transition_post_status', function ( $new, $old, $post ) {
	if ( 'publish' !== $new || 'publish' === $old || ! in_array( $post->post_type, array( 'post', 'product' ), true ) ) {
		return;
	}
	if ( ! mrzaib_nl_opt( 'product' === $post->post_type ? 'products' : 'posts' ) || get_post_meta( $post->ID, '_mrzaib_nl_sent', true ) ) {
		return;
	}
	if ( apply_filters( 'mrzaib_nl_skip', false, $post ) ) {
		return;
	}
	mrzaib_nl_queue( $post->ID );
}, 10, 3 );

function mrzaib_nl_queue( $post_id ) {
	update_post_meta( $post_id, '_mrzaib_nl_sent', time() );
	update_post_meta( $post_id, '_mrzaib_nl_offset', 0 );
	update_post_meta( $post_id, '_mrzaib_nl_count', 0 );
	// Short delay so price, image and categories are saved before the email is built.
	wp_schedule_single_event( time() + 60, 'mrzaib_nl_send', array( (int) $post_id ) );
}

/** Email content for a product or post. */
function mrzaib_nl_content( $post_id ) {
	$post  = get_post( $post_id );
	$title = get_the_title( $post );
	$url   = get_permalink( $post );
	$img   = get_the_post_thumbnail_url( $post, 'large' );
	if ( 'product' === $post->post_type && function_exists( 'wc_get_product' ) ) {
		$p     = wc_get_product( $post_id );
		$price = '';
		if ( $p ) {
			$sale = $p->get_sale_price();
			$reg  = $p->get_regular_price();
			$fmt  = function ( $v ) {
				return html_entity_decode( wp_strip_all_tags( wc_price( $v ) ) );
			};
			if ( $sale && $reg && (float) $sale < (float) $reg ) {
				$price = esc_html( $fmt( $sale ) ) . ' <del style="color:#999;font-size:15px;font-weight:400">' . esc_html( $fmt( $reg ) ) . '</del>';
			} elseif ( $p->get_price() ) {
				$price = esc_html( $fmt( $p->get_price() ) );
			}
		}
		$sub  = get_post_meta( $post_id, '_mrzaib_subtitle', true );
		$desc = wp_trim_words( wp_strip_all_tags( $post->post_excerpt ? $post->post_excerpt : $post->post_content ), 40 );
		return array(
			'subject' => mrzaib_nl_subject( mrzaib_nl_opt( 'subj_product' ), $title ),
			'args'    => array(
				'eyebrow'   => __( 'New arrival', 'mrzaib' ),
				'title'     => $title,
				'preheader' => $sub ? $sub : $desc,
				'image'     => $img ? array( $img, $url ) : array(),
				'price'     => $price,
				'intro'     => ( $sub ? '<p style="margin:0 0 8px"><strong>' . esc_html( $sub ) . '</strong></p>' : '' ) . '<p style="margin:0">' . esc_html( $desc ) . '</p>',
				'button'    => array( __( 'Shop Now', 'mrzaib' ), $url ),
			),
		);
	}
	$desc = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 45 );
	return array(
		'subject' => mrzaib_nl_subject( mrzaib_nl_opt( 'subj_post' ), $title ),
		'args'    => array(
			'eyebrow'   => __( 'From our blog', 'mrzaib' ),
			'title'     => $title,
			'preheader' => wp_trim_words( $desc, 18 ),
			'image'     => $img ? array( $img, $url ) : array(),
			'intro'     => '<p style="margin:0">' . esc_html( $desc ) . '</p>',
			'button'    => array( __( 'Read More', 'mrzaib' ), $url ),
		),
	);
}

/** Send one batch; reschedule until every subscriber has the email. */
add_action( 'mrzaib_nl_send', function ( $post_id ) {
	$post_id = (int) $post_id;
	if ( 'publish' !== get_post_status( $post_id ) ) {
		return;
	}
	$offset = (int) get_post_meta( $post_id, '_mrzaib_nl_offset', true );
	$ids    = get_posts( array( 'post_type' => 'mz_subscriber', 'post_status' => 'publish', 'numberposts' => MRZAIB_NL_BATCH, 'offset' => $offset, 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ids' ) );
	if ( ! $ids ) {
		update_post_meta( $post_id, '_mrzaib_nl_done', time() );
		return;
	}
	$c    = mrzaib_nl_content( $post_id );
	$sent = (int) get_post_meta( $post_id, '_mrzaib_nl_count', true );
	foreach ( $ids as $sid ) {
		if ( mrzaib_nl_mail( $sid, get_the_title( $sid ), $c['subject'], $c['args'] ) ) {
			$sent++;
		}
	}
	update_post_meta( $post_id, '_mrzaib_nl_offset', $offset + count( $ids ) );
	update_post_meta( $post_id, '_mrzaib_nl_count', $sent );
	if ( count( $ids ) < MRZAIB_NL_BATCH ) {
		update_post_meta( $post_id, '_mrzaib_nl_done', time() );
	} else {
		wp_schedule_single_event( time() + 60, 'mrzaib_nl_send', array( $post_id ) );
	}
} );

/* ------------------------------------------------------------------
 * Admin: list columns, export, settings
 * ------------------------------------------------------------------ */
add_filter( 'manage_mz_subscriber_posts_columns', function ( $c ) {
	return array( 'cb' => $c['cb'], 'title' => __( 'Email', 'mrzaib' ), 'mz_source' => __( 'Source', 'mrzaib' ), 'date' => __( 'Subscribed', 'mrzaib' ) );
} );
add_action( 'manage_mz_subscriber_posts_custom_column', function ( $col, $id ) {
	if ( 'mz_source' === $col ) {
		echo esc_html( ucfirst( (string) get_post_meta( $id, '_mz_source', true ) ) );
	}
}, 10, 2 );
add_filter( 'post_row_actions', function ( $a, $post ) {
	if ( 'mz_subscriber' === $post->post_type ) {
		unset( $a['inline hide-if-no-js'], $a['view'] );
	}
	return $a;
}, 10, 2 );
add_filter( 'enter_title_here', function ( $t, $post ) {
	return 'mz_subscriber' === $post->post_type ? __( 'Subscriber email', 'mrzaib' ) : $t;
}, 10, 2 );
/* Subscribers added by hand in the dashboard get a token and proper email title. */
add_action( 'save_post_mz_subscriber', function ( $id, $post ) {
	if ( wp_is_post_revision( $id ) || 'publish' !== $post->post_status ) {
		return;
	}
	mrzaib_nl_token( $id );
	if ( ! get_post_meta( $id, '_mz_source', true ) ) {
		update_post_meta( $id, '_mz_source', 'admin' );
	}
}, 10, 2 );

add_action( 'admin_menu', function () {
	add_submenu_page( 'edit.php?post_type=mz_subscriber', __( 'Newsletter Settings', 'mrzaib' ), __( 'Newsletter Settings', 'mrzaib' ), 'manage_options', 'mrzaib-newsletter', 'mrzaib_nl_settings_page' );
} );

add_action( 'admin_post_mrzaib_nl_export', function () {
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'mrzaib_nl_export' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'mrzaib' ) );
	}
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'email', 'subscribed', 'source' ) );
	foreach ( get_posts( array( 'post_type' => 'mz_subscriber', 'post_status' => 'publish', 'numberposts' => -1 ) ) as $s ) {
		fputcsv( $out, array( $s->post_title, $s->post_date, get_post_meta( $s->ID, '_mz_source', true ) ) );
	}
	fclose( $out ); // phpcs:ignore
	exit;
} );

function mrzaib_nl_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$msg = '';
	if ( isset( $_POST['mrzaib_nl_save'] ) && check_admin_referer( 'mrzaib_nl_settings' ) ) {
		$o = array();
		foreach ( array( 'products', 'posts', 'welcome', 'admin_notify' ) as $k ) {
			$o[ $k ] = empty( $_POST[ $k ] ) ? 0 : 1;
		}
		foreach ( array( 'subj_product', 'subj_post', 'subj_welcome' ) as $k ) {
			$o[ $k ] = isset( $_POST[ $k ] ) ? sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) : '';
			if ( '' === $o[ $k ] ) {
				$d       = mrzaib_nl_defaults();
				$o[ $k ] = $d[ $k ];
			}
		}
		update_option( 'mrzaib_newsletter', $o );
		$msg = __( 'Settings saved.', 'mrzaib' );
	}
	if ( isset( $_POST['mrzaib_nl_test'] ) && check_admin_referer( 'mrzaib_nl_settings' ) ) {
		$to  = sanitize_email( wp_unslash( $_POST['test_to'] ?? '' ) );
		$pid = absint( $_POST['test_post'] ?? 0 );
		if ( is_email( $to ) && $pid ) {
			$c  = mrzaib_nl_content( $pid );
			$ok = mrzaib_send_html_mail( $to, '[Test] ' . $c['subject'], mrzaib_email_html( $c['args'] + array( 'unsub' => home_url( '/' ) ) ) );
			$msg = $ok ? sprintf( __( 'Test email sent to %s.', 'mrzaib' ), $to ) : __( 'Test email could not be sent. Check Email Settings (SMTP).', 'mrzaib' );
		} else {
			$msg = __( 'Enter a valid email and choose a product or post.', 'mrzaib' );
		}
	}
	if ( isset( $_POST['mrzaib_nl_resend'] ) && check_admin_referer( 'mrzaib_nl_settings' ) ) {
		$pid = absint( $_POST['test_post'] ?? 0 );
		if ( $pid && 'publish' === get_post_status( $pid ) ) {
			mrzaib_nl_queue( $pid );
			$msg = sprintf( __( '"%s" will be emailed to all subscribers in about a minute.', 'mrzaib' ), get_the_title( $pid ) );
		}
	}
	$count  = (int) wp_count_posts( 'mz_subscriber' )->publish;
	$recent = get_posts( array( 'post_type' => array( 'product', 'post' ), 'post_status' => 'publish', 'numberposts' => 30 ) );
	$sent   = get_posts( array( 'post_type' => array( 'product', 'post' ), 'post_status' => 'publish', 'numberposts' => 8, 'meta_key' => '_mrzaib_nl_sent', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Newsletter Settings', 'mrzaib' ); ?></h1>
		<?php if ( $msg ) : ?><div class="notice notice-success is-dismissible"><p><?php echo esc_html( $msg ); ?></p></div><?php endif; ?>
		<p style="font-size:14px"><?php echo esc_html( sprintf( _n( 'You have %d active subscriber.', 'You have %d active subscribers.', $count, 'mrzaib' ), $count ) ); ?>
			<a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=mz_subscriber' ) ); ?>"><?php esc_html_e( 'View subscribers', 'mrzaib' ); ?></a>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=mrzaib_nl_export' ), 'mrzaib_nl_export' ) ); ?>"><?php esc_html_e( 'Export CSV', 'mrzaib' ); ?></a></p>
		<p class="description"><?php echo wp_kses_post( sprintf( __( 'Emails are sent through your site email (SMTP) from <a href="%s">Form Submissions &gt; Email Settings</a>, in batches of %d per minute. Every email has an Unsubscribe link.', 'mrzaib' ), esc_url( admin_url( 'edit.php?post_type=mz_submission&page=mrzaib-email' ) ), MRZAIB_NL_BATCH ) ); ?></p>
		<form method="post">
			<?php wp_nonce_field( 'mrzaib_nl_settings' ); ?>
			<table class="form-table">
				<tr><th><?php esc_html_e( 'Automatic emails', 'mrzaib' ); ?></th><td>
					<label><input type="checkbox" name="products" value="1" <?php checked( mrzaib_nl_opt( 'products' ) ); ?>> <?php esc_html_e( 'Email all subscribers when a new product is published', 'mrzaib' ); ?></label><br>
					<label><input type="checkbox" name="posts" value="1" <?php checked( mrzaib_nl_opt( 'posts' ) ); ?>> <?php esc_html_e( 'Email all subscribers when a new blog post is published', 'mrzaib' ); ?></label><br>
					<label><input type="checkbox" name="welcome" value="1" <?php checked( mrzaib_nl_opt( 'welcome' ) ); ?>> <?php esc_html_e( 'Send a welcome email to new subscribers', 'mrzaib' ); ?></label><br>
					<label><input type="checkbox" name="admin_notify" value="1" <?php checked( mrzaib_nl_opt( 'admin_notify' ) ); ?>> <?php esc_html_e( 'Email me when someone subscribes', 'mrzaib' ); ?></label>
				</td></tr>
				<tr><th><label for="subj_product"><?php esc_html_e( 'New product subject', 'mrzaib' ); ?></label></th><td><input class="regular-text" id="subj_product" name="subj_product" value="<?php echo esc_attr( mrzaib_nl_opt( 'subj_product' ) ); ?>"></td></tr>
				<tr><th><label for="subj_post"><?php esc_html_e( 'New blog post subject', 'mrzaib' ); ?></label></th><td><input class="regular-text" id="subj_post" name="subj_post" value="<?php echo esc_attr( mrzaib_nl_opt( 'subj_post' ) ); ?>"></td></tr>
				<tr><th><label for="subj_welcome"><?php esc_html_e( 'Welcome subject', 'mrzaib' ); ?></label></th><td><input class="regular-text" id="subj_welcome" name="subj_welcome" value="<?php echo esc_attr( mrzaib_nl_opt( 'subj_welcome' ) ); ?>"><p class="description"><?php esc_html_e( 'Use {title} for the product/post name and {site} for the store name.', 'mrzaib' ); ?></p></td></tr>
			</table>
			<p><button class="button button-primary" name="mrzaib_nl_save" value="1"><?php esc_html_e( 'Save Settings', 'mrzaib' ); ?></button></p>

			<h2><?php esc_html_e( 'Test or send manually', 'mrzaib' ); ?></h2>
			<table class="form-table">
				<tr><th><?php esc_html_e( 'Product / post', 'mrzaib' ); ?></th><td><select name="test_post">
					<?php foreach ( $recent as $r ) : ?>
						<option value="<?php echo (int) $r->ID; ?>"><?php echo esc_html( ( 'product' === $r->post_type ? __( 'Product', 'mrzaib' ) : __( 'Post', 'mrzaib' ) ) . ': ' . get_the_title( $r ) ); ?></option>
					<?php endforeach; ?>
				</select></td></tr>
				<tr><th><?php esc_html_e( 'Send test to', 'mrzaib' ); ?></th><td><input type="email" class="regular-text" name="test_to" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>"></td></tr>
			</table>
			<p><button class="button" name="mrzaib_nl_test" value="1"><?php esc_html_e( 'Send Test Email', 'mrzaib' ); ?></button>
			<button class="button" name="mrzaib_nl_resend" value="1" onclick="return confirm('<?php echo esc_js( __( 'Email this product/post to ALL subscribers now?', 'mrzaib' ) ); ?>')"><?php esc_html_e( 'Send to All Subscribers', 'mrzaib' ); ?></button></p>
		</form>
		<?php if ( $sent ) : ?>
		<h2><?php esc_html_e( 'Recently sent', 'mrzaib' ); ?></h2>
		<table class="widefat striped" style="max-width:760px"><thead><tr><th><?php esc_html_e( 'Product / post', 'mrzaib' ); ?></th><th><?php esc_html_e( 'Queued', 'mrzaib' ); ?></th><th><?php esc_html_e( 'Emails sent', 'mrzaib' ); ?></th><th><?php esc_html_e( 'Status', 'mrzaib' ); ?></th></tr></thead><tbody>
			<?php foreach ( $sent as $s ) : ?>
			<tr><td><?php echo esc_html( get_the_title( $s ) ); ?></td><td><?php echo esc_html( wp_date( 'M j, Y g:i a', (int) get_post_meta( $s->ID, '_mrzaib_nl_sent', true ) ) ); ?></td><td><?php echo (int) get_post_meta( $s->ID, '_mrzaib_nl_count', true ); ?></td><td><?php echo get_post_meta( $s->ID, '_mrzaib_nl_done', true ) ? esc_html__( 'Done', 'mrzaib' ) : esc_html__( 'Sending…', 'mrzaib' ); ?></td></tr>
			<?php endforeach; ?>
		</tbody></table>
		<?php endif; ?>
	</div>
	<?php
}
