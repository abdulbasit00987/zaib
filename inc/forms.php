<?php
/**
 * Contact + Return forms: submission storage (Dashboard > Form Submissions),
 * branded HTML emails (customer thank-you + admin notification),
 * Email Settings page with WP Mail SMTP setup.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ------------------------------------------------------------------
 * Settings
 * ------------------------------------------------------------------ */
function mrzaib_forms_defaults() {
	return array(
		'notify_to'      => '',
		'send_thanks'    => 1,
		'contact_subj'   => 'Thank you for contacting {site}',
		'contact_msg'    => 'We have received your message and our team will get back to you within 24 hours.',
		'return_subj'    => 'Your return request has been received – {site}',
		'return_msg'     => 'We have received your return request. Our team will review it and contact you within 24 hours.',
	);
}

function mrzaib_forms_opt( $key ) {
	$o = wp_parse_args( (array) get_option( 'mrzaib_forms', array() ), mrzaib_forms_defaults() );
	return isset( $o[ $key ] ) ? $o[ $key ] : '';
}

/** Where admin notifications go: Email Settings > Notification email, else Customizer email, else admin email. */
function mrzaib_forms_notify_to() {
	$to = trim( (string) mrzaib_forms_opt( 'notify_to' ) );
	if ( '' === $to ) {
		$to = mrzaib_opt( 'email', get_option( 'admin_email' ) );
	}
	$list = array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', $to ) ) ), 'is_email' );
	return $list ? $list : array( get_option( 'admin_email' ) );
}

/* ------------------------------------------------------------------
 * Submissions post type
 * ------------------------------------------------------------------ */
add_action( 'init', function () {
	register_post_type( 'mz_submission', array(
		'labels'          => array(
			'name'          => __( 'Form Submissions', 'mrzaib' ),
			'singular_name' => __( 'Submission', 'mrzaib' ),
			'menu_name'     => __( 'Form Submissions', 'mrzaib' ),
			'all_items'     => __( 'All Submissions', 'mrzaib' ),
			'edit_item'     => __( 'Submission', 'mrzaib' ),
			'search_items'  => __( 'Search Submissions', 'mrzaib' ),
			'not_found'     => __( 'No submissions yet.', 'mrzaib' ),
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_position'   => 26,
		'menu_icon'       => 'dashicons-email-alt',
		'supports'        => array( 'title' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
} );

function mrzaib_submission_types() {
	return array(
		'contact' => __( 'Contact', 'mrzaib' ),
		'return'  => __( 'Return', 'mrzaib' ),
	);
}

/* Unread bubble on the menu. */
add_action( 'admin_menu', function () {
	global $menu;
	$q = new WP_Query( array( 'post_type' => 'mz_submission', 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_mz_status', 'meta_value' => 'new' ) ); // phpcs:ignore
	$n = (int) $q->found_posts;
	if ( ! $n || empty( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=mz_submission' === $item[2] ) {
			$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $n . '</span></span>'; // phpcs:ignore
		}
	}
}, 99 );

/* List columns. */
add_filter( 'manage_mz_submission_posts_columns', function () {
	return array(
		'cb'        => '<input type="checkbox">',
		'mz_type'   => __( 'Form', 'mrzaib' ),
		'title'     => __( 'Name', 'mrzaib' ),
		'mz_email'  => __( 'Email', 'mrzaib' ),
		'mz_phone'  => __( 'Phone', 'mrzaib' ),
		'mz_ref'    => __( 'Order / Message', 'mrzaib' ),
		'mz_status' => __( 'Status', 'mrzaib' ),
		'date'      => __( 'Date', 'mrzaib' ),
	);
} );
add_action( 'manage_mz_submission_posts_custom_column', function ( $col, $id ) {
	$type = get_post_meta( $id, '_mz_type', true );
	switch ( $col ) {
		case 'mz_type':
			$types = mrzaib_submission_types();
			echo '<span class="mzsub-type mzsub-' . esc_attr( $type ) . '">' . esc_html( isset( $types[ $type ] ) ? $types[ $type ] : $type ) . '</span>';
			break;
		case 'mz_email':
			$e = get_post_meta( $id, '_mz_email', true );
			echo $e ? '<a href="mailto:' . esc_attr( $e ) . '">' . esc_html( $e ) . '</a>' : '—';
			break;
		case 'mz_phone':
			$p = get_post_meta( $id, '_mz_phone', true );
			echo $p ? esc_html( $p ) : '—';
			break;
		case 'mz_ref':
			$f = (array) get_post_meta( $id, '_mz_fields', true );
			if ( 'return' === $type ) {
				echo '<strong>#' . esc_html( isset( $f['Order number'] ) ? $f['Order number'] : '' ) . '</strong> · ' . esc_html( isset( $f['Product'] ) ? $f['Product'] : '' ) . ' · ' . esc_html( isset( $f['Request type'] ) ? $f['Request type'] : '' );
			} else {
				echo esc_html( wp_trim_words( isset( $f['Message'] ) ? $f['Message'] : '', 12 ) );
			}
			break;
		case 'mz_status':
			$s = get_post_meta( $id, '_mz_status', true );
			$s = $s ? $s : 'new';
			$l = array( 'new' => __( 'New', 'mrzaib' ), 'read' => __( 'Read', 'mrzaib' ), 'done' => __( 'Resolved', 'mrzaib' ) );
			echo '<span class="mzsub-st mzsub-st-' . esc_attr( $s ) . '">' . esc_html( isset( $l[ $s ] ) ? $l[ $s ] : $s ) . '</span>';
			break;
	}
}, 10, 2 );

/* Filter views: All | Contact | Return */
add_filter( 'views_edit-mz_submission', function ( $views ) {
	$base = admin_url( 'edit.php?post_type=mz_submission' );
	$cur  = isset( $_GET['mz_type'] ) ? sanitize_key( $_GET['mz_type'] ) : ''; // phpcs:ignore
	$out  = array();
	$out['all'] = '<a href="' . esc_url( $base ) . '"' . ( '' === $cur ? ' class="current"' : '' ) . '>' . esc_html__( 'All', 'mrzaib' ) . '</a>';
	foreach ( mrzaib_submission_types() as $k => $label ) {
		$q         = new WP_Query( array( 'post_type' => 'mz_submission', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_mz_type', 'meta_value' => $k ) ); // phpcs:ignore
		$out[ $k ] = '<a href="' . esc_url( add_query_arg( 'mz_type', $k, $base ) ) . '"' . ( $cur === $k ? ' class="current"' : '' ) . '>' . esc_html( $label ) . ' <span class="count">(' . (int) $q->found_posts . ')</span></a>';
	}
	if ( isset( $views['trash'] ) ) {
		$out['trash'] = $views['trash'];
	}
	return $out;
} );
add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() || 'mz_submission' !== $q->get( 'post_type' ) || empty( $_GET['mz_type'] ) ) { // phpcs:ignore
		return;
	}
	$q->set( 'meta_key', '_mz_type' );
	$q->set( 'meta_value', sanitize_key( $_GET['mz_type'] ) ); // phpcs:ignore
} );
add_filter( 'post_row_actions', function ( $a, $post ) {
	if ( 'mz_submission' === $post->post_type ) {
		unset( $a['inline hide-if-no-js'] );
		if ( isset( $a['edit'] ) ) {
			$a['edit'] = '<a href="' . esc_url( get_edit_post_link( $post->ID ) ) . '">' . esc_html__( 'View', 'mrzaib' ) . '</a>';
		}
	}
	return $a;
}, 10, 2 );

/* Detail screen. */
add_action( 'add_meta_boxes_mz_submission', function () {
	remove_meta_box( 'submitdiv', 'mz_submission', 'side' );
	add_meta_box( 'mzsub_details', __( 'Submission details', 'mrzaib' ), 'mrzaib_submission_box', 'mz_submission', 'normal', 'high' );
	add_meta_box( 'mzsub_status', __( 'Status', 'mrzaib' ), 'mrzaib_submission_status_box', 'mz_submission', 'side', 'high' );
} );

function mrzaib_submission_box( $post ) {
	$id     = $post->ID;
	$fields = (array) get_post_meta( $id, '_mz_fields', true );
	$type   = get_post_meta( $id, '_mz_type', true );
	$types  = mrzaib_submission_types();
	$email  = get_post_meta( $id, '_mz_email', true );
	$phone  = get_post_meta( $id, '_mz_phone', true );
	if ( 'new' === get_post_meta( $id, '_mz_status', true ) ) {
		update_post_meta( $id, '_mz_status', 'read' );
	}
	echo '<p><span class="mzsub-type mzsub-' . esc_attr( $type ) . '">' . esc_html( isset( $types[ $type ] ) ? $types[ $type ] : $type ) . '</span> &nbsp; ' . esc_html( get_the_date( 'j M Y, g:i a', $post ) ) . '</p>';
	echo '<table class="widefat striped mzsub-table"><tbody>';
	foreach ( $fields as $label => $val ) {
		echo '<tr><th>' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $val ) ) . '</td></tr>';
	}
	echo '</tbody></table><p class="mzsub-actions">';
	if ( $email ) {
		echo '<a class="button button-primary" href="mailto:' . esc_attr( $email ) . '?subject=' . rawurlencode( 'Re: ' . get_bloginfo( 'name' ) ) . '">' . esc_html__( 'Reply by email', 'mrzaib' ) . '</a> ';
	}
	if ( $phone ) {
		$wa = preg_replace( '/\D+/', '', $phone );
		if ( 0 === strpos( $wa, '0' ) ) {
			$wa = '92' . substr( $wa, 1 );
		}
		echo '<a class="button" href="tel:' . esc_attr( $phone ) . '">' . esc_html__( 'Call', 'mrzaib' ) . '</a> ';
		echo '<a class="button" target="_blank" rel="noopener" href="https://wa.me/' . esc_attr( $wa ) . '">' . esc_html__( 'WhatsApp', 'mrzaib' ) . '</a>';
	}
	echo '</p>';
}

function mrzaib_submission_status_box( $post ) {
	$s = get_post_meta( $post->ID, '_mz_status', true );
	wp_nonce_field( 'mzsub_status', 'mzsub_status_nonce' );
	echo '<select name="mzsub_status" style="width:100%">';
	foreach ( array( 'new' => __( 'New', 'mrzaib' ), 'read' => __( 'Read', 'mrzaib' ), 'done' => __( 'Resolved', 'mrzaib' ) ) as $k => $l ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $s, $k, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select><p><button class="button button-primary" type="submit">' . esc_html__( 'Save status', 'mrzaib' ) . '</button> ';
	echo '<a class="submitdelete" style="color:#b32d2e;margin-left:8px" href="' . esc_url( get_delete_post_link( $post->ID ) ) . '">' . esc_html__( 'Move to Trash', 'mrzaib' ) . '</a></p>';
	echo '<input type="hidden" name="post_status" value="publish">';
}
add_action( 'save_post_mz_submission', function ( $id ) {
	if ( isset( $_POST['mzsub_status_nonce'], $_POST['mzsub_status'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mzsub_status_nonce'] ) ), 'mzsub_status' ) ) {
		update_post_meta( $id, '_mz_status', sanitize_key( $_POST['mzsub_status'] ) );
	}
} );

add_action( 'admin_head', function () {
	$s = get_current_screen();
	if ( ! $s || ( 'mz_submission' !== $s->post_type && false === strpos( (string) $s->id, 'mrzaib-email' ) ) ) {
		return;
	}
	echo '<style>
	.mzsub-type{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px}
	.mzsub-contact{background:#111;color:#deba68}.mzsub-return{background:#deba68;color:#111}
	.mzsub-st{display:inline-block;padding:2px 9px;border-radius:20px;font-size:12px}
	.mzsub-st-new{background:#fff4d6;color:#8a6a1f;font-weight:600}.mzsub-st-read{background:#eef0f3;color:#555}.mzsub-st-done{background:#e6f4ea;color:#1e7b34}
	.mzsub-table th{width:180px;font-weight:600}.mzsub-actions .button{margin-right:6px}
	#post-body-content #titlediv #title{pointer-events:none;background:#f6f7f7}
	.mzes-wrap .card{max-width:820px;padding:8px 24px 18px}.mzes-wrap h2{margin-top:18px}
	.mzes-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600}
	.mzes-ok{background:#e6f4ea;color:#1e7b34}.mzes-no{background:#fdecea;color:#b32d2e}
	</style>';
} );

/* ------------------------------------------------------------------
 * Front-end handling (POST → save → emails → redirect)
 * ------------------------------------------------------------------ */
add_action( 'template_redirect', function () {
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) { // phpcs:ignore
		return;
	}
	$type = isset( $_POST['mrzaib_contact'] ) ? 'contact' : ( isset( $_POST['mrzaib_return'] ) ? 'return' : '' ); // phpcs:ignore
	if ( ! $type ) {
		return;
	}
	$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : '';
	$page  = is_singular() ? get_permalink( get_queried_object_id() ) : '';
	$back  = remove_query_arg( array( 'mz_sent', 'mz_err' ), $page ? $page : ( wp_get_raw_referer() ? wp_get_raw_referer() : home_url( '/' ) ) );
	if ( ! wp_verify_nonce( $nonce, 'mrzaib_' . $type ) ) {
		wp_safe_redirect( add_query_arg( 'mz_err', 'expired', $back ) . '#mz-form' );
		exit;
	}
	// Honeypot: bots fill the hidden field.
	if ( ! empty( $_POST['mz_website'] ) ) { // phpcs:ignore
		wp_safe_redirect( add_query_arg( 'mz_sent', $type, $back ) . '#mz-form' );
		exit;
	}
	$g = function ( $k, $textarea = false ) {
		$v = isset( $_POST[ $k ] ) ? wp_unslash( $_POST[ $k ] ) : ''; // phpcs:ignore
		return $textarea ? sanitize_textarea_field( $v ) : sanitize_text_field( $v );
	};
	if ( 'contact' === $type ) {
		$name   = $g( 'c_name' );
		$email  = sanitize_email( $g( 'c_email' ) );
		$phone  = $g( 'c_phone' );
		$fields = array(
			'Name'    => $name,
			'Email'   => $email,
			'Phone'   => $phone,
			'Message' => $g( 'c_msg', true ),
		);
		$ok = $name && is_email( $email ) && $fields['Message'];
	} else {
		$name   = $g( 'r_name' );
		$email  = sanitize_email( $g( 'r_email' ) );
		$phone  = $g( 'r_phone' );
		$fields = array(
			'Name'         => $name,
			'Phone'        => $phone,
			'Email'        => $email,
			'Order number' => ltrim( $g( 'r_order' ), '#' ),
			'Product'      => $g( 'r_product' ),
			'Request type' => $g( 'r_type' ),
			'Reason'       => $g( 'r_reason', true ),
		);
		$ok = $name && $phone && $fields['Order number'] && $fields['Product'] && $fields['Reason'] && is_email( $email );
	}
	if ( ! $ok ) {
		wp_safe_redirect( add_query_arg( 'mz_err', 'fields', $back ) . '#mz-form' );
		exit;
	}

	$id = wp_insert_post( array(
		'post_type'   => 'mz_submission',
		'post_status' => 'publish',
		'post_title'  => $name . ( 'return' === $type ? ' — #' . $fields['Order number'] : '' ),
	) );
	if ( $id && ! is_wp_error( $id ) ) {
		update_post_meta( $id, '_mz_type', $type );
		update_post_meta( $id, '_mz_fields', $fields );
		update_post_meta( $id, '_mz_email', $email );
		update_post_meta( $id, '_mz_phone', $phone );
		update_post_meta( $id, '_mz_status', 'new' );
	}
	mrzaib_forms_send_emails( $type, $fields, $email, $id );
	wp_safe_redirect( add_query_arg( 'mz_sent', $type, $back ) . '#mz-form' );
	exit;
} );

/** Front-end notice for templates. */
function mrzaib_form_notice( $type ) {
	if ( isset( $_GET['mz_sent'] ) && $type === $_GET['mz_sent'] ) { // phpcs:ignore
		$msg = 'return' === $type
			? __( 'Return request received. Our team will contact you within 24 hours. A confirmation has been sent to your email.', 'mrzaib' )
			: __( 'Thank you! Your message has been sent. We will get back to you soon.', 'mrzaib' );
		echo '<div class="form-success" role="status">' . esc_html( $msg ) . '</div>';
	} elseif ( isset( $_GET['mz_err'] ) ) { // phpcs:ignore
		$msg = 'expired' === $_GET['mz_err'] ? __( 'The form expired. Please try again.', 'mrzaib' ) : __( 'Please fill all required fields correctly.', 'mrzaib' ); // phpcs:ignore
		echo '<div class="form-error" role="alert">' . esc_html( $msg ) . '</div>';
	}
	echo '<div class="mz-hp" aria-hidden="true"><input type="text" name="mz_website" tabindex="-1" autocomplete="off"></div>';
}

/* ------------------------------------------------------------------
 * Emails
 * ------------------------------------------------------------------ */
function mrzaib_email_logo_url() {
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$u = wp_get_attachment_image_url( $logo_id, 'medium' );
		if ( $u ) {
			return $u;
		}
	}
	return MRZAIB_URI . '/assets/images/logo-gold.png';
}

/**
 * Branded HTML email (black header with logo, gold accents, white card).
 *
 * @param array $a title, intro (html allowed: p/strong/br), rows (label=>value), list (steps), button (text,url), button2 (text,url), preheader.
 */
function mrzaib_email_html( $a ) {
	$a     = wp_parse_args( $a, array( 'title' => '', 'eyebrow' => '', 'intro' => '', 'rows' => array(), 'list' => array(), 'button' => array(), 'button2' => array(), 'preheader' => '', 'note' => '' ) );
	$gold  = '#deba68';
	$site  = get_bloginfo( 'name' );
	$font  = "'Jost','Helvetica Neue',Helvetica,Arial,sans-serif";
	$serif = "'Playfair Display',Georgia,'Times New Roman',serif";
	ob_start();
	?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?php echo esc_html( $a['title'] ); ?></title></head>
<body style="margin:0;padding:0;background:#f4f1ea;-webkit-text-size-adjust:100%">
<div style="display:none;max-height:0;overflow:hidden;opacity:0"><?php echo esc_html( $a['preheader'] ); ?></div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f4f1ea;padding:28px 12px">
<tr><td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 6px 24px rgba(0,0,0,.06)">
	<tr><td align="center" style="background:#000000;padding:26px 20px 22px">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="text-decoration:none"><img src="<?php echo esc_url( mrzaib_email_logo_url() ); ?>" alt="<?php echo esc_attr( $site ); ?>" width="150" style="display:block;width:150px;max-width:60%;height:auto;border:0"></a>
	</td></tr>
	<tr><td style="height:4px;line-height:4px;font-size:0;background:linear-gradient(90deg,#a8843c,<?php echo esc_attr( $gold ); ?>,#a8843c);background-color:<?php echo esc_attr( $gold ); ?>">&nbsp;</td></tr>
	<tr><td style="padding:38px 40px 10px;font-family:<?php echo $font; // phpcs:ignore ?>;color:#333;font-size:15px;line-height:1.7">
		<?php if ( $a['eyebrow'] ) : ?><p style="margin:0 0 8px;font-size:12px;letter-spacing:3px;text-transform:uppercase;color:#a8843c;font-weight:600"><?php echo esc_html( $a['eyebrow'] ); ?></p><?php endif; ?>
		<h1 style="margin:0 0 16px;font-family:<?php echo $serif; // phpcs:ignore ?>;font-size:28px;line-height:1.25;color:#111;font-weight:600"><?php echo esc_html( $a['title'] ); ?></h1>
		<?php echo wp_kses( $a['intro'], array( 'p' => array( 'style' => array() ), 'strong' => array(), 'br' => array(), 'a' => array( 'href' => array(), 'style' => array() ) ) ); ?>
	</td></tr>
	<?php if ( $a['rows'] ) : ?>
	<tr><td style="padding:10px 40px 6px">
		<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #efe6d2;border-radius:10px;overflow:hidden;font-family:<?php echo $font; // phpcs:ignore ?>">
		<?php $i = 0; foreach ( $a['rows'] as $label => $val ) : if ( '' === (string) $val ) { continue; } ?>
			<tr style="background:<?php echo ( $i++ % 2 ) ? '#ffffff' : '#fbf8f1'; ?>">
				<td valign="top" style="padding:12px 16px;width:34%;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#a8843c;font-weight:600;border-bottom:1px solid #f3ecdc"><?php echo esc_html( $label ); ?></td>
				<td valign="top" style="padding:12px 16px;font-size:14.5px;color:#111;border-bottom:1px solid #f3ecdc;word-break:break-word"><?php echo nl2br( esc_html( $val ) ); ?></td>
			</tr>
		<?php endforeach; ?>
		</table>
	</td></tr>
	<?php endif; ?>
	<?php if ( $a['list'] ) : ?>
	<tr><td style="padding:18px 40px 0;font-family:<?php echo $font; // phpcs:ignore ?>">
		<p style="margin:0 0 10px;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#111;font-weight:700"><?php esc_html_e( 'What happens next', 'mrzaib' ); ?></p>
		<?php foreach ( $a['list'] as $n => $step ) : ?>
		<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 10px"><tr>
			<td valign="top" style="width:28px"><div style="width:24px;height:24px;line-height:24px;border-radius:50%;background:<?php echo esc_attr( $gold ); ?>;color:#111;text-align:center;font-size:12px;font-weight:700"><?php echo (int) $n + 1; ?></div></td>
			<td style="padding-left:10px;font-size:14.5px;color:#444;line-height:1.6"><?php echo esc_html( $step ); ?></td>
		</tr></table>
		<?php endforeach; ?>
	</td></tr>
	<?php endif; ?>
	<?php if ( $a['button'] ) : ?>
	<tr><td align="center" style="padding:26px 40px 8px">
		<a href="<?php echo esc_url( $a['button'][1] ); ?>" style="display:inline-block;background:<?php echo esc_attr( $gold ); ?>;color:#111111;text-decoration:none;font-family:<?php echo $font; // phpcs:ignore ?>;font-size:14px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;padding:15px 34px;border-radius:4px"><?php echo esc_html( $a['button'][0] ); ?></a>
		<?php if ( $a['button2'] ) : ?>
		&nbsp;<a href="<?php echo esc_url( $a['button2'][1] ); ?>" style="display:inline-block;margin-top:8px;background:#111;color:<?php echo esc_attr( $gold ); ?>;text-decoration:none;font-family:<?php echo $font; // phpcs:ignore ?>;font-size:14px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;padding:15px 30px;border-radius:4px"><?php echo esc_html( $a['button2'][0] ); ?></a>
		<?php endif; ?>
	</td></tr>
	<?php endif; ?>
	<tr><td style="padding:22px 40px 34px;font-family:<?php echo $font; // phpcs:ignore ?>;font-size:14px;color:#666;line-height:1.7">
		<?php if ( $a['note'] ) : ?><p style="margin:0 0 14px"><?php echo esc_html( $a['note'] ); ?></p><?php endif; ?>
		<p style="margin:0"><?php esc_html_e( 'Warm regards,', 'mrzaib' ); ?><br><strong style="color:#111"><?php echo esc_html( sprintf( __( 'Team %s', 'mrzaib' ), $site ) ); ?></strong></p>
	</td></tr>
	<tr><td align="center" style="background:#000;padding:24px 20px;font-family:<?php echo $font; // phpcs:ignore ?>;font-size:12.5px;color:#bbb;line-height:1.8">
		<?php
		$bits = array();
		if ( mrzaib_opt( 'phone' ) ) {
			$bits[] = '<a href="tel:' . esc_attr( mrzaib_opt( 'phone' ) ) . '" style="color:' . $gold . ';text-decoration:none">' . esc_html( mrzaib_opt( 'phone' ) ) . '</a>';
		}
		if ( mrzaib_opt( 'whatsapp' ) ) {
			$bits[] = '<a href="https://wa.me/' . esc_attr( mrzaib_opt( 'whatsapp' ) ) . '" style="color:' . $gold . ';text-decoration:none">WhatsApp</a>';
		}
		if ( is_email( mrzaib_opt( 'email' ) ) ) {
			$bits[] = '<a href="mailto:' . esc_attr( mrzaib_opt( 'email' ) ) . '" style="color:' . $gold . ';text-decoration:none">' . esc_html( mrzaib_opt( 'email' ) ) . '</a>';
		}
		$bits[] = '<a href="' . esc_url( home_url( '/' ) ) . '" style="color:' . $gold . ';text-decoration:none">' . esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ) . '</a>';
		echo implode( ' &nbsp;·&nbsp; ', $bits ); // phpcs:ignore
		$soc = array();
		foreach ( array( 'facebook', 'instagram', 'tiktok', 'youtube', 'x', 'snapchat', 'pinterest' ) as $s ) {
			$u = get_theme_mod( 'mrzaib_social_' . $s );
			if ( $u ) {
				$soc[] = '<a href="' . esc_url( $u ) . '" style="color:#ddd;text-decoration:none">' . esc_html( ucfirst( $s ) ) . '</a>';
			}
		}
		if ( $soc ) {
			echo '<br>' . implode( ' &nbsp;|&nbsp; ', $soc ); // phpcs:ignore
		}
		?>
		<br><span style="color:#777">&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . $site ); ?>. <?php esc_html_e( 'All rights reserved.', 'mrzaib' ); ?></span>
	</td></tr>
</table>
</td></tr></table>
</body></html>
	<?php
	return ob_get_clean();
}

/** Send with HTML content type and the site name as sender name. */
function mrzaib_send_html_mail( $to, $subject, $html, $headers = array() ) {
	$headers[] = 'Content-Type: text/html; charset=UTF-8';
	$name_cb   = function ( $n ) {
		return ( 'WordPress' === $n ) ? get_bloginfo( 'name' ) : $n;
	};
	add_filter( 'wp_mail_from_name', $name_cb );
	$ok = wp_mail( $to, $subject, $html, $headers );
	remove_filter( 'wp_mail_from_name', $name_cb );
	return $ok;
}

function mrzaib_forms_send_emails( $type, $fields, $email, $id ) {
	$site    = get_bloginfo( 'name' );
	$is_ret  = 'return' === $type;
	$name    = $fields['Name'];
	$first   = strtok( $name, ' ' );
	$edit    = $id ? admin_url( 'post.php?post=' . (int) $id . '&action=edit' ) : admin_url( 'edit.php?post_type=mz_submission' );

	// Admin notification.
	$admin_html = mrzaib_email_html( array(
		'eyebrow'   => $is_ret ? __( 'Return form', 'mrzaib' ) : __( 'Contact form', 'mrzaib' ),
		'title'     => $is_ret ? sprintf( __( 'New return request #%s', 'mrzaib' ), $fields['Order number'] ) : sprintf( __( 'New message from %s', 'mrzaib' ), $name ),
		'preheader' => $is_ret ? $fields['Product'] . ' · ' . $fields['Request type'] : wp_trim_words( $fields['Message'], 15 ),
		'intro'     => '<p style="margin:0 0 6px">' . esc_html( sprintf( __( 'A customer just submitted the %s form on your website. Details:', 'mrzaib' ), $is_ret ? __( 'return', 'mrzaib' ) : __( 'contact', 'mrzaib' ) ) ) . '</p>',
		'rows'      => $fields + array( 'Submitted' => wp_date( 'j M Y, g:i a' ) ),
		'button'    => array( __( 'View in dashboard', 'mrzaib' ), $edit ),
		'button2'   => $email ? array( __( 'Reply to customer', 'mrzaib' ), 'mailto:' . $email ) : array(),
	) );
	$headers = array();
	if ( $email ) {
		$headers[] = 'Reply-To: ' . str_replace( array( "\r", "\n", '<', '>' ), '', $name ) . ' <' . $email . '>';
	}
	$subject = $is_ret ? sprintf( '[%s] Return request #%s – %s', $site, $fields['Order number'], $name ) : sprintf( '[%s] New contact message – %s', $site, $name );
	mrzaib_send_html_mail( mrzaib_forms_notify_to(), $subject, $admin_html, $headers );

	// Customer thank-you.
	if ( ! $email || ! mrzaib_forms_opt( 'send_thanks' ) ) {
		return;
	}
	$subj = str_replace( '{site}', $site, $is_ret ? mrzaib_forms_opt( 'return_subj' ) : mrzaib_forms_opt( 'contact_subj' ) );
	$msg  = $is_ret ? mrzaib_forms_opt( 'return_msg' ) : mrzaib_forms_opt( 'contact_msg' );
	$rows = $is_ret
		? array( 'Order number' => '#' . $fields['Order number'], 'Product' => $fields['Product'], 'Request type' => $fields['Request type'], 'Reason' => $fields['Reason'] )
		: array( 'Your message' => $fields['Message'] );
	$list = $is_ret ? array(
		__( 'Our team reviews your request within 24 hours.', 'mrzaib' ),
		sprintf( __( 'We will call or WhatsApp you on %s to arrange pickup or exchange.', 'mrzaib' ), $fields['Phone'] ),
		__( 'Please keep the perfume and box in their original condition until then.', 'mrzaib' ),
	) : array();
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$html = mrzaib_email_html( array(
		'eyebrow'   => $is_ret ? __( 'Return request received', 'mrzaib' ) : __( 'Message received', 'mrzaib' ),
		'title'     => sprintf( __( 'Thank you, %s!', 'mrzaib' ), $first ),
		'preheader' => $msg,
		'intro'     => '<p style="margin:0">' . esc_html( $msg ) . '</p>',
		'rows'      => $rows,
		'list'      => $list,
		'button'    => array( __( 'Continue shopping', 'mrzaib' ), $shop ),
		'note'      => __( 'If you have anything to add, simply reply to this email.', 'mrzaib' ),
	) );
	$to_admin = mrzaib_forms_notify_to();
	mrzaib_send_html_mail( $email, $subj, $html, array( 'Reply-To: ' . $site . ' <' . $to_admin[0] . '>' ) );
}

/* ------------------------------------------------------------------
 * Email Settings page (Form Submissions > Email Settings)
 * ------------------------------------------------------------------ */
add_action( 'admin_menu', function () {
	add_submenu_page( 'edit.php?post_type=mz_submission', __( 'Email Settings', 'mrzaib' ), __( 'Email Settings', 'mrzaib' ), 'manage_options', 'mrzaib-email', 'mrzaib_email_settings_page' );
} );

function mrzaib_smtp_presets() {
	return array(
		'hosting'   => array( 'label' => 'Hosting / cPanel email (info@yourdomain.com)', 'host' => '', 'port' => 465, 'enc' => 'ssl' ),
		'hostinger' => array( 'label' => 'Hostinger email', 'host' => 'smtp.hostinger.com', 'port' => 465, 'enc' => 'ssl' ),
		'gmail'     => array( 'label' => 'Gmail (App Password)', 'host' => 'smtp.gmail.com', 'port' => 465, 'enc' => 'ssl' ),
		'outlook'   => array( 'label' => 'Outlook / Microsoft 365', 'host' => 'smtp.office365.com', 'port' => 587, 'enc' => 'tls' ),
		'zoho'      => array( 'label' => 'Zoho Mail', 'host' => 'smtp.zoho.com', 'port' => 465, 'enc' => 'ssl' ),
		'brevo'     => array( 'label' => 'Brevo (Sendinblue) SMTP', 'host' => 'smtp-relay.brevo.com', 'port' => 587, 'enc' => 'tls' ),
		'custom'    => array( 'label' => 'Custom', 'host' => '', 'port' => 587, 'enc' => 'tls' ),
	);
}

function mrzaib_wpms_active() {
	return class_exists( '\WPMailSMTP\Options' );
}

function mrzaib_email_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$notices = array();
	if ( isset( $_POST['mzes_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mzes_nonce'] ) ), 'mzes' ) ) {
		$act = isset( $_POST['mzes_action'] ) ? sanitize_key( $_POST['mzes_action'] ) : '';
		if ( 'install' === $act && function_exists( 'mrzaib_install_plugin' ) ) {
			$notices[] = array( 'info', mrzaib_install_plugin( 'wp-mail-smtp', 'wp-mail-smtp/wp_mail_smtp.php', 'WP Mail SMTP' ) );
			update_option( 'wp_mail_smtp_activation_prevent_redirect', true );
			delete_transient( 'wp_mail_smtp_activation_redirect' );
			echo '<script>location.href=' . wp_json_encode( admin_url( 'edit.php?post_type=mz_submission&page=mrzaib-email&mz_installed=1' ) ) . ';</script>';
		}
		if ( 'save' === $act ) {
			$o = array(
				'notify_to'    => implode( ', ', array_filter( array_map( 'sanitize_email', array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $_POST['notify_to'] ?? '' ) ) ) ) ), 'is_email' ) ),
				'send_thanks'  => empty( $_POST['send_thanks'] ) ? 0 : 1,
				'contact_subj' => sanitize_text_field( wp_unslash( $_POST['contact_subj'] ?? '' ) ),
				'contact_msg'  => sanitize_textarea_field( wp_unslash( $_POST['contact_msg'] ?? '' ) ),
				'return_subj'  => sanitize_text_field( wp_unslash( $_POST['return_subj'] ?? '' ) ),
				'return_msg'   => sanitize_textarea_field( wp_unslash( $_POST['return_msg'] ?? '' ) ),
			);
			update_option( 'mrzaib_forms', $o );
			$notices[] = array( 'success', __( 'Form email settings saved.', 'mrzaib' ) );

			if ( mrzaib_wpms_active() && ! empty( $_POST['smtp_host'] ) ) {
				$from = sanitize_email( wp_unslash( $_POST['from_email'] ?? '' ) );
				$user = sanitize_text_field( wp_unslash( $_POST['smtp_user'] ?? '' ) );
				$enc  = sanitize_key( $_POST['smtp_enc'] ?? 'ssl' );
				$smtp = array(
					'host'       => sanitize_text_field( wp_unslash( $_POST['smtp_host'] ) ),
					'port'       => (int) ( $_POST['smtp_port'] ?? 465 ),
					'encryption' => in_array( $enc, array( 'ssl', 'tls', 'none' ), true ) ? $enc : 'ssl',
					'autotls'    => true,
					'auth'       => true,
					'user'       => $user,
				);
				$pass = isset( $_POST['smtp_pass'] ) ? trim( (string) wp_unslash( $_POST['smtp_pass'] ) ) : ''; // phpcs:ignore -- password kept as typed.
				if ( '' !== $pass ) {
					$smtp['pass'] = $pass;
				}
				\WPMailSMTP\Options::init()->set( array(
					'mail' => array(
						'mailer'           => 'smtp',
						'from_email'       => $from ? $from : ( is_email( $user ) ? $user : get_option( 'admin_email' ) ),
						'from_name'        => sanitize_text_field( wp_unslash( $_POST['from_name'] ?? get_bloginfo( 'name' ) ) ),
						'from_email_force' => true,
						'from_name_force'  => true,
						'return_path'      => true,
					),
					'smtp' => $smtp,
				), false, false );
				update_option( 'mrzaib_smtp_preset', sanitize_key( $_POST['smtp_preset'] ?? 'hosting' ) );
				update_option( 'wp_mail_smtp_activation_prevent_redirect', true );
				$notices[] = array( 'success', __( 'WP Mail SMTP settings saved. Now send a test email below.', 'mrzaib' ) );
			}
		}
		if ( 'test' === $act ) {
			$err = '';
			$cb  = function ( $e ) use ( &$err ) {
				$err = $e->get_error_message();
			};
			add_action( 'wp_mail_failed', $cb );
			$to   = sanitize_email( wp_unslash( $_POST['test_to'] ?? '' ) );
			$to   = $to ? $to : mrzaib_forms_notify_to()[0];
			$html = mrzaib_email_html( array(
				'eyebrow' => __( 'Test email', 'mrzaib' ),
				'title'   => __( 'Your emails are working!', 'mrzaib' ),
				'intro'   => '<p style="margin:0">' . esc_html__( 'This test was sent from Form Submissions > Email Settings. Contact and return form emails will look like this.', 'mrzaib' ) . '</p>',
				'button'  => array( __( 'Visit website', 'mrzaib' ), home_url( '/' ) ),
			) );
			$ok = mrzaib_send_html_mail( $to, sprintf( '[%s] Test email', get_bloginfo( 'name' ) ), $html );
			remove_action( 'wp_mail_failed', $cb );
			$notices[] = $ok ? array( 'success', sprintf( __( 'Test email sent to %s. Check the inbox (and spam folder).', 'mrzaib' ), $to ) ) : array( 'error', __( 'Test email failed: ', 'mrzaib' ) . ( $err ? $err : __( 'unknown error', 'mrzaib' ) ) );
		}
	}
	if ( isset( $_GET['mz_installed'] ) ) { // phpcs:ignore
		$notices[] = array( 'success', mrzaib_wpms_active() ? __( 'WP Mail SMTP is installed and active. Fill the SMTP fields below.', 'mrzaib' ) : __( 'WP Mail SMTP could not be activated. Install it from Plugins > Add New.', 'mrzaib' ) );
	}

	$wpms    = mrzaib_wpms_active();
	$wo      = $wpms ? \WPMailSMTP\Options::init() : null;
	$presets = mrzaib_smtp_presets();
	$preset  = get_option( 'mrzaib_smtp_preset', 'hosting' );
	$host    = $wo ? $wo->get( 'smtp', 'host' ) : '';
	$mailer  = $wo ? $wo->get( 'mail', 'mailer' ) : '';
	$domain  = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	?>
	<div class="wrap mzes-wrap">
		<h1><?php esc_html_e( 'Email Settings', 'mrzaib' ); ?></h1>
		<?php foreach ( $notices as $n ) : ?><div class="notice notice-<?php echo esc_attr( $n[0] ); ?> is-dismissible"><p><?php echo esc_html( $n[1] ); ?></p></div><?php endforeach; ?>
		<form method="post">
			<?php wp_nonce_field( 'mzes', 'mzes_nonce' ); ?>
			<div class="card">
				<h2>1. <?php esc_html_e( 'Where should form submissions arrive?', 'mrzaib' ); ?></h2>
				<table class="form-table"><tr>
					<th><label for="notify_to"><?php esc_html_e( 'Notification email', 'mrzaib' ); ?></label></th>
					<td><input type="text" class="regular-text" id="notify_to" name="notify_to" value="<?php echo esc_attr( mrzaib_forms_opt( 'notify_to' ) ); ?>" placeholder="<?php echo esc_attr( implode( ', ', mrzaib_forms_notify_to() ) ); ?>">
					<p class="description"><?php esc_html_e( 'Every contact and return request is emailed here. Separate several emails with commas. Empty = the email in Customizer > MR ZAIB Options > Contact, else the site admin email.', 'mrzaib' ); ?></p></td>
				</tr><tr>
					<th><?php esc_html_e( 'Thank-you email to customer', 'mrzaib' ); ?></th>
					<td><label><input type="checkbox" name="send_thanks" value="1" <?php checked( mrzaib_forms_opt( 'send_thanks' ) ); ?>> <?php esc_html_e( 'Send a branded thank-you email to the customer after they submit', 'mrzaib' ); ?></label></td>
				</tr><tr>
					<th><?php esc_html_e( 'Contact thank-you', 'mrzaib' ); ?></th>
					<td><input type="text" class="large-text" name="contact_subj" value="<?php echo esc_attr( mrzaib_forms_opt( 'contact_subj' ) ); ?>"><textarea class="large-text" rows="2" name="contact_msg"><?php echo esc_textarea( mrzaib_forms_opt( 'contact_msg' ) ); ?></textarea><p class="description"><?php esc_html_e( 'Subject, then message. {site} = your store name.', 'mrzaib' ); ?></p></td>
				</tr><tr>
					<th><?php esc_html_e( 'Return thank-you', 'mrzaib' ); ?></th>
					<td><input type="text" class="large-text" name="return_subj" value="<?php echo esc_attr( mrzaib_forms_opt( 'return_subj' ) ); ?>"><textarea class="large-text" rows="2" name="return_msg"><?php echo esc_textarea( mrzaib_forms_opt( 'return_msg' ) ); ?></textarea></td>
				</tr></table>
			</div>

			<div class="card">
				<h2>2. <?php esc_html_e( 'Sending (WP Mail SMTP)', 'mrzaib' ); ?>
					<?php if ( $wpms && 'smtp' === $mailer && $host ) : ?><span class="mzes-pill mzes-ok"><?php esc_html_e( 'Configured', 'mrzaib' ); ?></span>
					<?php else : ?><span class="mzes-pill mzes-no"><?php esc_html_e( 'Not configured', 'mrzaib' ); ?></span><?php endif; ?></h2>
				<p><?php esc_html_e( 'Without SMTP many hosts (and XAMPP) cannot deliver email, or it lands in spam. Create an email on your hosting (for example info@', 'mrzaib' ); ?><?php echo esc_html( $domain ); ?>)<?php esc_html_e( ' and enter its login here.', 'mrzaib' ); ?></p>
				<?php if ( ! $wpms ) : ?>
					<p><button class="button button-primary" name="mzes_action" value="install"><?php esc_html_e( 'Install & Activate WP Mail SMTP', 'mrzaib' ); ?></button></p>
				<?php else : ?>
				<table class="form-table"><tr>
					<th><?php esc_html_e( 'Email provider', 'mrzaib' ); ?></th>
					<td><select name="smtp_preset" id="mz_preset">
						<?php foreach ( $presets as $k => $p ) : ?><option value="<?php echo esc_attr( $k ); ?>" data-host="<?php echo esc_attr( $p['host'] ); ?>" data-port="<?php echo (int) $p['port']; ?>" data-enc="<?php echo esc_attr( $p['enc'] ); ?>" <?php selected( $preset, $k ); ?>><?php echo esc_html( $p['label'] ); ?></option><?php endforeach; ?>
					</select><p class="description"><?php esc_html_e( 'cPanel hosting: SMTP host is usually mail.yourdomain.com, port 465, SSL. Gmail needs an App Password (Google Account > Security > App passwords), not your normal password.', 'mrzaib' ); ?></p></td>
				</tr><tr>
					<th><label><?php esc_html_e( 'SMTP host', 'mrzaib' ); ?></label></th>
					<td><input type="text" class="regular-text" name="smtp_host" id="mz_host" value="<?php echo esc_attr( $host ); ?>" placeholder="mail.<?php echo esc_attr( $domain ); ?>"></td>
				</tr><tr>
					<th><?php esc_html_e( 'Port / Encryption', 'mrzaib' ); ?></th>
					<td><input type="number" name="smtp_port" id="mz_port" value="<?php echo esc_attr( $wo->get( 'smtp', 'port' ) ? $wo->get( 'smtp', 'port' ) : 465 ); ?>" style="width:90px">
					<select name="smtp_enc" id="mz_enc"><?php foreach ( array( 'ssl' => 'SSL', 'tls' => 'TLS', 'none' => 'None' ) as $k => $l ) : ?><option value="<?php echo esc_attr( $k ); ?>" <?php selected( $wo->get( 'smtp', 'encryption' ) ? $wo->get( 'smtp', 'encryption' ) : 'ssl', $k ); ?>><?php echo esc_html( $l ); ?></option><?php endforeach; ?></select></td>
				</tr><tr>
					<th><?php esc_html_e( 'SMTP username', 'mrzaib' ); ?></th>
					<td><input type="text" class="regular-text" name="smtp_user" value="<?php echo esc_attr( $wo->get( 'smtp', 'user' ) ); ?>" placeholder="info@<?php echo esc_attr( $domain ); ?>" autocomplete="off"><p class="description"><?php esc_html_e( 'Usually the full email address.', 'mrzaib' ); ?></p></td>
				</tr><tr>
					<th><?php esc_html_e( 'SMTP password', 'mrzaib' ); ?></th>
					<td><input type="password" class="regular-text" name="smtp_pass" value="" placeholder="<?php echo $wo->get( 'smtp', 'pass' ) ? esc_attr__( 'Saved (leave empty to keep)', 'mrzaib' ) : ''; ?>" autocomplete="new-password"><p class="description"><?php esc_html_e( 'Stored encrypted by WP Mail SMTP.', 'mrzaib' ); ?></p></td>
				</tr><tr>
					<th><?php esc_html_e( 'From email / name', 'mrzaib' ); ?></th>
					<td><input type="email" class="regular-text" name="from_email" value="<?php echo esc_attr( 'smtp' === $mailer ? $wo->get( 'mail', 'from_email' ) : '' ); ?>" placeholder="info@<?php echo esc_attr( $domain ); ?>">
					<input type="text" name="from_name" value="<?php echo esc_attr( $wo->get( 'mail', 'from_name' ) ? $wo->get( 'mail', 'from_name' ) : get_bloginfo( 'name' ) ); ?>">
					<p class="description"><?php esc_html_e( 'Use the same email as the SMTP username, otherwise many servers reject or spam the message.', 'mrzaib' ); ?></p></td>
				</tr></table>
				<p class="description"><a href="<?php echo esc_url( admin_url( 'admin.php?page=wp-mail-smtp' ) ); ?>"><?php esc_html_e( 'Advanced options in WP Mail SMTP', 'mrzaib' ); ?></a></p>
				<?php endif; ?>
			</div>
			<p><button class="button button-primary button-hero" name="mzes_action" value="save"><?php esc_html_e( 'Save settings', 'mrzaib' ); ?></button></p>

			<div class="card">
				<h2>3. <?php esc_html_e( 'Send a test email', 'mrzaib' ); ?></h2>
				<p><input type="email" class="regular-text" name="test_to" placeholder="<?php echo esc_attr( mrzaib_forms_notify_to()[0] ); ?>"> <button class="button" name="mzes_action" value="test" formnovalidate><?php esc_html_e( 'Send test', 'mrzaib' ); ?></button></p>
			</div>
		</form>
		<script>
		(function(){var s=document.getElementById('mz_preset');if(!s)return;s.addEventListener('change',function(){var o=s.options[s.selectedIndex];if(o.dataset.host){document.getElementById('mz_host').value=o.dataset.host;}else if(s.value==='hosting'){document.getElementById('mz_host').value='mail.<?php echo esc_js( $domain ); ?>';}document.getElementById('mz_port').value=o.dataset.port;document.getElementById('mz_enc').value=o.dataset.enc;});})();
		</script>
	</div>
	<?php
}

/* Admin notice until SMTP is set up (only on dashboard + submissions screens). */
add_action( 'admin_notices', function () {
	$s = get_current_screen();
	if ( ! current_user_can( 'manage_options' ) || ! $s || ! in_array( $s->id, array( 'dashboard', 'edit-mz_submission' ), true ) ) {
		return;
	}
	if ( mrzaib_wpms_active() && 'smtp' === \WPMailSMTP\Options::init()->get( 'mail', 'mailer' ) && \WPMailSMTP\Options::init()->get( 'smtp', 'host' ) ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>MR ZAIB:</strong> ' . esc_html__( 'Set up email sending so contact and return form emails are delivered.', 'mrzaib' ) . ' <a class="button button-primary" style="margin-left:8px" href="' . esc_url( admin_url( 'edit.php?post_type=mz_submission&page=mrzaib-email' ) ) . '">' . esc_html__( 'Email Settings', 'mrzaib' ) . '</a></p></div>';
} );
