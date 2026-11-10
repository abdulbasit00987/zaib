<?php
/**
 * Policy pages content, order confirmation note and editable FAQs.
 *
 * - Policy pages (Privacy, Shipping, Terms, Return & Refund) get full content once,
 *   only when a page is still empty or still holds the old placeholder text.
 *   After that the pages are normal WordPress pages: edit them in Pages.
 * - Order confirmation note (Customizer > MR ZAIB Options > Order Confirmation Note)
 *   shows on the product page, cart, checkout, thank-you page and order emails.
 * - FAQs are edited in Pages > FAQ Questions.
 *
 * @package mrzaib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MRZAIB_POLICY_V', 3 );

/* ---------- order confirmation note ---------- */
function mrzaib_confirm_default_short() {
	return 'After you order, our team will call or WhatsApp you on your number to confirm it and share our account details. Send the delivery charges with the payment slip, and your order is dispatched. Pay the rest in cash to the rider on delivery.';
}
function mrzaib_confirm_default_full() {
	return 'How your order works: after you place your order, our team will call or WhatsApp you on the phone number you entered to confirm it and share our account details. Please send the delivery charges shown at checkout to that account and share the payment slip (screenshot) with our team. Your order is then dispatched to your address. On delivery, pay the rider the order amount in cash, without the delivery charges, because you have already paid them.';
}
function mrzaib_confirm_text( $full = true ) {
	return $full ? mrzaib_opt( 'confirm_full', mrzaib_confirm_default_full() ) : mrzaib_opt( 'confirm_short', mrzaib_confirm_default_short() );
}
function mrzaib_confirm_box( $full = true, $extra_class = '' ) {
	if ( ! mrzaib_opt( 'confirm_on', '1' ) ) {
		return '';
	}
	$t = trim( mrzaib_confirm_text( $full ) );
	if ( '' === $t ) {
		return '';
	}
	$ico = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg>';
	return '<div class="mz-confirm ' . esc_attr( $extra_class ) . '"><span class="mz-confirm-ico">' . $ico . '</span><p>' . esc_html( $t ) . '</p></div>';
}

add_action( 'customize_register', function ( $c ) {
	$c->add_section( 'mrzaib_confirm', array(
		'title'       => __( 'Order Confirmation Note', 'mrzaib' ),
		'panel'       => 'mrzaib_panel',
		'description' => __( 'This note tells customers that your team will call or WhatsApp them to confirm the order, that they send the delivery charges in advance with a payment slip, and pay the rest in cash to the rider. The short text shows on product pages and the cart. The full text shows on checkout, the thank-you page and order emails.', 'mrzaib' ),
		'priority'    => 160,
	) );
	$c->add_setting( 'mrzaib_confirm_on', array( 'default' => '1', 'sanitize_callback' => function ( $v ) { return $v ? '1' : ''; } ) );
	$c->add_control( 'mrzaib_confirm_on', array( 'label' => __( 'Show the order confirmation note', 'mrzaib' ), 'section' => 'mrzaib_confirm', 'type' => 'checkbox' ) );
	$c->add_setting( 'mrzaib_confirm_short', array( 'default' => mrzaib_confirm_default_short(), 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$c->add_control( 'mrzaib_confirm_short', array( 'label' => __( 'Short text (product page, cart)', 'mrzaib' ), 'section' => 'mrzaib_confirm', 'type' => 'textarea' ) );
	$c->add_setting( 'mrzaib_confirm_full', array( 'default' => mrzaib_confirm_default_full(), 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$c->add_control( 'mrzaib_confirm_full', array( 'label' => __( 'Full text (checkout, thank-you page, emails)', 'mrzaib' ), 'section' => 'mrzaib_confirm', 'type' => 'textarea' ) );
}, 20 );

/* Product page: under the add-to-cart area. */
add_action( 'woocommerce_single_product_summary', function () {
	echo mrzaib_confirm_box( false, 'mz-confirm--pdp' ); // phpcs:ignore
}, 39 );
/* Cart: above the Proceed to checkout button. */
add_action( 'woocommerce_proceed_to_checkout', function () {
	echo mrzaib_confirm_box( false, 'mz-confirm--cart' ); // phpcs:ignore
}, 5 );
/* Checkout: right above Place order. */
add_action( 'woocommerce_review_order_before_submit', function () {
	echo mrzaib_confirm_box( true, 'mz-confirm--checkout' ); // phpcs:ignore
} );
/* Thank-you page. */
add_action( 'woocommerce_before_thankyou', function () {
	echo mrzaib_confirm_box( true, 'mz-confirm--thanks' ); // phpcs:ignore
}, 5 );
/* Customer order emails. */
add_action( 'woocommerce_email_before_order_table', function ( $order, $sent_to_admin, $plain_text ) {
	if ( $sent_to_admin || ! mrzaib_opt( 'confirm_on', '1' ) ) {
		return;
	}
	$t = mrzaib_confirm_text( true );
	if ( $plain_text ) {
		echo "\n" . esc_html( $t ) . "\n\n";
	} else {
		echo '<p style="margin:0 0 18px;padding:12px 14px;background:#fbf6ea;border:1px solid #deba68;border-radius:6px;color:#111">' . esc_html( $t ) . '</p>';
	}
}, 5, 3 );

/* Cash on Delivery description at checkout. */
add_filter( 'woocommerce_gateway_description', function ( $desc, $id ) {
	if ( 'cod' === $id && mrzaib_opt( 'confirm_on', '1' ) ) {
		return esc_html__( 'Pay the order amount in cash to the rider on delivery. Delivery charges are paid in advance after our team confirms your order.', 'mrzaib' );
	}
	return $desc;
}, 10, 2 );

/* ---------- shortcodes used in the policy pages ---------- */
add_shortcode( 'mrzaib_contact_line', function () {
	$parts = array();
	if ( mrzaib_opt( 'phone' ) ) {
		$parts[] = 'Phone / WhatsApp: <a href="tel:' . esc_attr( preg_replace( '/[^\d+]/', '', mrzaib_opt( 'phone' ) ) ) . '">' . esc_html( mrzaib_opt( 'phone' ) ) . '</a>';
	}
	if ( mrzaib_opt( 'email' ) && is_email( mrzaib_opt( 'email' ) ) ) {
		$parts[] = 'Email: <a href="mailto:' . esc_attr( mrzaib_opt( 'email' ) ) . '">' . esc_html( mrzaib_opt( 'email' ) ) . '</a>';
	}
	$parts[] = 'or our <a href="' . esc_url( home_url( '/contact-us/' ) ) . '">Contact Us</a> page';
	return implode( ' &middot; ', $parts );
} );
add_shortcode( 'mrzaib_confirm_note', function () {
	return esc_html( mrzaib_confirm_text( true ) );
} );

/* ---------- policy page content ---------- */
function mrzaib_pb_h( $t ) {
	return '<!-- wp:heading --><h2>' . $t . '</h2><!-- /wp:heading -->';
}
function mrzaib_pb_p( $t ) {
	return '<!-- wp:paragraph --><p>' . $t . '</p><!-- /wp:paragraph -->';
}
function mrzaib_pb_ul( $items ) {
	return '<!-- wp:list --><ul class="wp-block-list"><li>' . implode( '</li><li>', $items ) . '</li></ul><!-- /wp:list -->';
}
function mrzaib_policy_content( $slug ) {
	$u = function ( $p ) {
		return esc_url( home_url( $p ) );
	};
	switch ( $slug ) {
		case 'shipping-policy':
			return mrzaib_pb_p( 'At <strong>MR ZAIB</strong> we deliver our perfumes to every city in Pakistan. This page explains how your order is confirmed, how delivery charges are paid and how long delivery takes.' )
				. mrzaib_pb_h( 'How your order works' )
				. mrzaib_pb_ul( array(
					'<strong>1. Order confirmation:</strong> after you place your order, our team will call or WhatsApp you on the phone number you entered in the order form to confirm your order.',
					'<strong>2. Pay delivery charges in advance:</strong> on that call or WhatsApp, our team shares our account details. Send the delivery charges shown on the website at checkout, then send us the payment slip (screenshot).',
					'<strong>3. Dispatch:</strong> once we receive the delivery charges, your order is dispatched to your address.',
					'<strong>4. Pay the rest on delivery:</strong> when your order arrives, pay the rider the order amount in cash, without the delivery charges, because you have already paid them.',
				) )
				. mrzaib_pb_p( 'Please enter a correct, active mobile number and keep your phone and WhatsApp on, so we can confirm and dispatch your order quickly.' )
				. mrzaib_pb_p( 'If we cannot reach you after a few attempts by call or WhatsApp, the order may be put on hold or cancelled.' )
				. mrzaib_pb_h( 'Delivery charges' )
				. mrzaib_pb_p( '[mrzaib_shipping] The delivery charges for your order are shown at checkout.' )
				. mrzaib_pb_p( '<strong>Delivery charges are paid in advance</strong> to the account our team shares with you on the confirmation call or WhatsApp.' )
				. mrzaib_pb_h( 'Payment: Cash on Delivery' )
				. mrzaib_pb_p( 'The order amount is <strong>Cash on Delivery (COD)</strong>. You pay the rider in cash when your parcel arrives, without the delivery charges you already paid in advance.' )
				. mrzaib_pb_h( 'Delivery time' )
				. mrzaib_pb_ul( array(
					'Orders are dispatched within 1 to 2 working days after confirmation and receipt of the delivery charges.',
					'[mrzaib_delivery_time]',
					'Orders placed on Sundays or public holidays are processed on the next working day.',
					'During sales and festive seasons, delivery can take 1 to 3 extra days.',
				) )
				. mrzaib_pb_h( 'Tracking your order' )
				. mrzaib_pb_p( 'Once your order is dispatched, we share the courier tracking number with you. You can also check your order status on our <a href="' . $u( '/track-order/' ) . '">Track Your Order</a> page.' )
				. mrzaib_pb_h( 'Receiving your parcel' )
				. mrzaib_pb_ul( array(
					'Please check that the parcel is sealed and not damaged before you pay the courier.',
					'If anything is wrong with your order (wrong, damaged or leaking bottle), contact us within 48 hours of delivery with photos and a short video of the parcel.',
					'Wrong or incomplete addresses can delay delivery. Please add a nearby landmark if possible.',
				) )
				. mrzaib_pb_h( 'Questions?' )
				. mrzaib_pb_p( 'Contact our team: [mrzaib_contact_line].' );

		case 'refund-policy':
			return mrzaib_pb_p( 'We want you to love your <strong>MR ZAIB</strong> fragrance. If something is not right, you can return or exchange it under the terms below.' )
				. mrzaib_pb_h( '15-day returns and exchanges' )
				. mrzaib_pb_ul( array(
					'You can request a return or exchange within <strong>15 days of delivery</strong>.',
					'The bottle must be at least <strong>80% full</strong> and come back with its original box and packaging.',
					'Gift items, testers and products bought on clearance may not be returnable unless they arrive damaged or wrong.',
				) )
				. mrzaib_pb_h( 'Damaged, leaking or wrong product' )
				. mrzaib_pb_p( 'If your parcel arrives damaged, leaking or with the wrong product, contact us within <strong>48 hours of delivery</strong> with photos and a short video of the parcel. We will replace it or refund you, and we cover the delivery charges in these cases.' )
				. mrzaib_pb_h( 'How to start a return' )
				. mrzaib_pb_ul( array(
					'Fill the <a href="' . $u( '/returns/' ) . '">Return Form</a> with your order number, phone number and reason.',
					'Our team will contact you on your phone number within 24 hours to arrange the pickup or tell you where to send the parcel.',
					'Once we receive and check the product, we process your exchange or refund.',
				) )
				. mrzaib_pb_h( 'Refunds' )
				. mrzaib_pb_ul( array(
					'Refunds are sent by bank transfer, JazzCash or Easypaisa to the account you share with our team.',
					'Refunds are processed within 5 to 7 working days after we receive and approve the returned product.',
					'Delivery charges paid in advance are not refundable once the order is dispatched, unless the product was damaged, leaking or wrong.',
					'For change-of-mind returns, the return delivery charges are paid by the customer.',
				) )
				. mrzaib_pb_h( 'Order cancellation' )
				. mrzaib_pb_p( 'You can cancel your order before it is dispatched, for example when our team calls you to confirm it. If you already paid the delivery charges and cancel before dispatch, we refund them. Once an order is dispatched, it can only be returned under this policy.' )
				. mrzaib_pb_h( 'Questions?' )
				. mrzaib_pb_p( 'Contact our team: [mrzaib_contact_line].' );

		case 'terms-of-service':
			return mrzaib_pb_p( 'Welcome to <strong>MR ZAIB</strong>. By using this website and placing an order, you agree to the terms below. Please read them carefully.' )
				. mrzaib_pb_h( 'About us' )
				. mrzaib_pb_p( 'MR ZAIB is a Pakistani perfume brand selling long lasting perfumes online across Pakistan. In these terms, "we", "us" and "our" mean MR ZAIB, and "you" means the customer using this website.' )
				. mrzaib_pb_h( 'Products' )
				. mrzaib_pb_ul( array(
					'We do our best to show our products, notes and colours accurately. Bottle and box designs can change slightly from the photos.',
					'How long a fragrance lasts and how far it projects depends on skin type, weather and how it is applied.',
					'Some of our fragrances are inspired by well known scents. They are original MR ZAIB perfumes and are not made by, linked to or endorsed by the brands that inspired them. Brand names are mentioned only to describe the scent style.',
					'Perfume is for external use only. If you have sensitive skin, do a small patch test first. Keep away from eyes, flames and children.',
				) )
				. mrzaib_pb_h( 'Prices' )
				. mrzaib_pb_p( 'All prices are in Pakistani Rupees (PKR). Prices and offers can change at any time without notice, but the price confirmed for your order will not change after confirmation.' )
				. mrzaib_pb_h( 'Orders and confirmation' )
				. mrzaib_pb_ul( array(
					'After you place an order, <strong>our team will call or WhatsApp you on the phone number you entered in the order form</strong> to confirm it and share our account details.',
					'<strong>Delivery charges (as shown on the website at checkout) are paid in advance</strong> to that account, with the payment slip sent to our team. Orders are dispatched only after confirmation and receipt of the delivery charges.',
					'Please enter your correct name, address and an active phone number. If we cannot reach you, we may hold or cancel the order.',
					'We may refuse or cancel an order in case of stock problems, pricing errors, incomplete details or suspected fake orders.',
				) )
				. mrzaib_pb_h( 'Payment' )
				. mrzaib_pb_p( 'Delivery charges are paid in advance as explained above. The order amount is <strong>Cash on Delivery</strong>: you pay it to the rider in cash when the parcel is delivered, without the delivery charges you already paid. Refusing to receive confirmed COD orders again and again may stop us from accepting your future orders.' )
				. mrzaib_pb_h( 'Delivery, returns and refunds' )
				. mrzaib_pb_p( 'Delivery is covered in our <a href="' . $u( '/shipping-policy/' ) . '">Shipping Policy</a>, and returns and refunds in our <a href="' . $u( '/refund-policy/' ) . '">Return &amp; Refund Policy</a>.' )
				. mrzaib_pb_h( 'Reviews and questions' )
				. mrzaib_pb_p( 'Reviews, photos and questions you post must be honest and respectful. We may remove content that is false, offensive or spam.' )
				. mrzaib_pb_h( 'Website content' )
				. mrzaib_pb_p( 'The MR ZAIB name, logo, photos, designs and text on this website belong to MR ZAIB. Please do not copy or use them without our written permission.' )
				. mrzaib_pb_h( 'Limitation of liability' )
				. mrzaib_pb_p( 'We are not responsible for any reaction or loss caused by misuse of our products or by delays outside our control, such as courier delays, weather or strikes. Our liability for any order is limited to the amount you paid for it.' )
				. mrzaib_pb_h( 'Governing law' )
				. mrzaib_pb_p( 'These terms are governed by the laws of Pakistan.' )
				. mrzaib_pb_h( 'Changes to these terms' )
				. mrzaib_pb_p( 'We may update these terms from time to time. The latest version will always be on this page.' )
				. mrzaib_pb_h( 'Contact us' )
				. mrzaib_pb_p( '[mrzaib_contact_line].' );

		case 'privacy-policy-2':
			return mrzaib_pb_p( 'Your privacy matters to <strong>MR ZAIB</strong>. This policy explains what information we collect, why we collect it and how we keep it safe.' )
				. mrzaib_pb_h( 'Information we collect' )
				. mrzaib_pb_ul( array(
					'<strong>Order details:</strong> your name, phone number, delivery address, city and email when you place an order.',
					'<strong>Messages:</strong> what you send us through the contact form, return form, reviews or product questions.',
					'<strong>Newsletter:</strong> your email address if you subscribe to our emails.',
					'<strong>Website data:</strong> basic technical data such as your browser, device and pages visited, collected through cookies.',
				) )
				. mrzaib_pb_h( 'How we use your information' )
				. mrzaib_pb_ul( array(
					'To process and deliver your order.',
					'<strong>To call or WhatsApp you on the phone number you entered to confirm your order and share our account details for the delivery charges.</strong>',
					'To share courier tracking details and answer your questions, returns and complaints.',
					'To send offers and new product emails, only if you subscribed. Every email has an unsubscribe link.',
					'To improve our website, products and service.',
				) )
				. mrzaib_pb_h( 'Sharing your information' )
				. mrzaib_pb_p( 'We never sell or rent your personal information. We only share the details needed to deliver your order (name, phone number and address) with our courier partners, and we use trusted services to run this website and send emails.' )
				. mrzaib_pb_h( 'Payments' )
				. mrzaib_pb_p( 'We do not collect or store any card or bank details on this website. Delivery charges are sent by you to our account, and the order amount is paid in cash on delivery. Payment slips you send us are used only to verify your payment.' )
				. mrzaib_pb_h( 'Cookies' )
				. mrzaib_pb_p( 'We use cookies to keep your cart, remember your preferences and understand how our website is used. You can turn cookies off in your browser, but some parts of the website, such as the cart, may not work properly.' )
				. mrzaib_pb_h( 'Keeping your data safe' )
				. mrzaib_pb_p( 'We take reasonable steps to protect your information and only keep it as long as we need it for orders, returns, legal and accounting reasons.' )
				. mrzaib_pb_h( 'Your choices' )
				. mrzaib_pb_p( 'You can ask us to see, correct or delete your personal information, or unsubscribe from our emails at any time.' )
				. mrzaib_pb_h( 'Changes to this policy' )
				. mrzaib_pb_p( 'We may update this policy from time to time. The latest version will always be on this page.' )
				. mrzaib_pb_h( 'Contact us' )
				. mrzaib_pb_p( '[mrzaib_contact_line].' );
	}
	return '';
}

/* Fill the policy pages once. A page the admin already wrote is never touched. */
function mrzaib_fill_policy_pages( $force = false ) {
	$done = 0;
	foreach ( array( 'privacy-policy-2', 'shipping-policy', 'terms-of-service', 'refund-policy' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			continue;
		}
		$c   = trim( wp_strip_all_tags( $page->post_content ) );
		$v1  = false !== strpos( $page->post_content, 'will be confirmed with you when our team calls' )
			|| false !== strpos( $page->post_content, 'Delivery charges are confirmed with you on this confirmation call' )
			|| false !== strpos( $page->post_content, 'to confirm your order and its delivery charges' )
			|| false !== strpos( $page->post_content, 'Because all orders are Cash on Delivery, refunds are sent' );
		$v1  = $v1 || false !== strpos( $page->post_content, 'If your order has free delivery, no advance payment is needed' );
		$old = $v1 || '' === $c
			|| false !== strpos( $c, '(Replace with' )
			|| ( false !== strpos( $page->post_content, '[mrzaib_shipping]' ) && false === strpos( $page->post_content, 'How your order works' ) )
			|| 0 === strpos( $c, 'Not happy with a fragrance? Return it within 15 days' )
			|| 0 === strpos( $c, 'Return & Refund PolicyNot happy with a fragrance?' )
			|| 0 === strpos( $c, 'Return &amp; Refund PolicyNot happy with a fragrance?' );
		if ( $force || $old ) {
			wp_update_post( array( 'ID' => $page->ID, 'post_content' => mrzaib_policy_content( $slug ) ) );
			$done++;
		}
	}
	return $done;
}
add_action( 'admin_init', function () {
	if ( (int) get_option( 'mrzaib_policy_v' ) >= MRZAIB_POLICY_V || ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	update_option( 'mrzaib_policy_v', MRZAIB_POLICY_V );
	mrzaib_fill_policy_pages();
} );

/* ---------- FAQs (Pages > FAQ Questions) ---------- */
function mrzaib_default_faqs() {
	return array(
		array( 'Orders & Delivery', 'How will my order be confirmed?', 'After you place your order, our team will call or WhatsApp you on the phone number you entered in the order form to confirm your order. Please keep your phone on.' ),
		array( 'Orders & Delivery', 'How do I pay the delivery charges?', 'Delivery charges are paid in advance. On the confirmation call or WhatsApp, our team shares our account details. Send the delivery charges shown at checkout and share the payment slip, then your order is dispatched.' ),
		array( 'Orders & Delivery', 'How much are the delivery charges?', 'The delivery charges for your city are shown on the website at checkout.' ),
		array( 'Orders & Delivery', 'Do you offer Cash on Delivery?', 'Yes. The order amount is Cash on Delivery all over Pakistan. You pay the rider in cash when your parcel arrives, without the delivery charges, because they are paid in advance.' ),
		array( 'Orders & Delivery', 'How long does delivery take?', 'Orders are dispatched within 1 to 2 working days after confirmation and payment of the delivery charges, and usually arrive in 2 to 5 working days, depending on your city.' ),
		array( 'Orders & Delivery', 'How can I track my order?', 'We share the courier tracking number after dispatch. You can also use the Track Your Order page with your order number and email.' ),
		array( 'Orders & Delivery', 'Can I cancel my order?', 'Yes, you can cancel before your order is dispatched, for example on the confirmation call.' ),
		array( 'Products', 'How long do your perfumes last?', 'Most MR ZAIB perfumes last 8 to 12 hours on skin, depending on skin type and weather.' ),
		array( 'Products', 'Are MR ZAIB perfumes original?', 'Yes. All our fragrances are original MR ZAIB perfumes made with high-concentration oils. Some are inspired by famous scents, but they are our own perfumes.' ),
		array( 'Products', 'Can I try before buying?', 'Yes. Our Tester range lets you try scents before choosing a full bottle.' ),
		array( 'Returns', 'What is your return policy?', 'You can return or exchange within 15 days of delivery. The bottle must be at least 80% full with its original packaging.' ),
		array( 'Returns', 'How do I start a return?', 'Fill the Return Form and our team will contact you on your phone number within 24 hours.' ),
	);
}
function mrzaib_get_faqs() {
	$f = get_option( 'mrzaib_faqs', null );
	return is_array( $f ) ? $f : mrzaib_default_faqs();
}
function mrzaib_faq_groups() {
	$out = array();
	foreach ( mrzaib_get_faqs() as $r ) {
		if ( empty( $r[1] ) ) {
			continue;
		}
		$g           = '' !== trim( (string) $r[0] ) ? $r[0] : __( 'General', 'mrzaib' );
		$out[ $g ][] = array( $r[1], $r[2] );
	}
	return $out;
}

add_action( 'admin_menu', function () {
	add_pages_page( __( 'FAQ Questions', 'mrzaib' ), __( 'FAQ Questions', 'mrzaib' ), 'edit_pages', 'mrzaib-faqs', 'mrzaib_faqs_page' );
} );
function mrzaib_faqs_page() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}
	$saved = false;
	if ( isset( $_POST['mrzaib_faqs_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['mrzaib_faqs_nonce'] ), 'mrzaib_faqs' ) ) {
		if ( isset( $_POST['mz_reset'] ) ) {
			delete_option( 'mrzaib_faqs' );
		} else {
			$g    = isset( $_POST['faq_g'] ) ? (array) wp_unslash( $_POST['faq_g'] ) : array(); // phpcs:ignore
			$q    = isset( $_POST['faq_q'] ) ? (array) wp_unslash( $_POST['faq_q'] ) : array(); // phpcs:ignore
			$a    = isset( $_POST['faq_a'] ) ? (array) wp_unslash( $_POST['faq_a'] ) : array(); // phpcs:ignore
			$rows = array();
			foreach ( $q as $i => $qq ) {
				$qq = sanitize_text_field( $qq );
				if ( '' === $qq ) {
					continue;
				}
				$rows[] = array( sanitize_text_field( $g[ $i ] ?? '' ), $qq, sanitize_textarea_field( $a[ $i ] ?? '' ) );
			}
			update_option( 'mrzaib_faqs', $rows );
		}
		$saved = true;
	}
	$faqs = mrzaib_get_faqs();
	$page = get_page_by_path( 'faqs' );
	?>
	<div class="wrap mzfaq">
		<h1><?php esc_html_e( 'FAQ Questions', 'mrzaib' ); ?></h1>
		<p><?php esc_html_e( 'Edit the questions and answers shown on your FAQs page. Questions with the same Section name are grouped together. Leave a question empty to remove it.', 'mrzaib' ); ?>
		<?php if ( $page ) : ?> <a href="<?php echo esc_url( get_permalink( $page ) ); ?>" target="_blank"><?php esc_html_e( 'View FAQs page', 'mrzaib' ); ?> &rarr;</a><?php endif; ?></p>
		<?php if ( $saved ) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'FAQs saved.', 'mrzaib' ); ?></p></div><?php endif; ?>
		<form method="post">
			<?php wp_nonce_field( 'mrzaib_faqs', 'mrzaib_faqs_nonce' ); ?>
			<div class="mzfaq-rows">
				<?php foreach ( $faqs as $r ) : ?>
					<?php mrzaib_faq_row( $r ); ?>
				<?php endforeach; ?>
			</div>
			<template id="mzfaq-tpl"><?php mrzaib_faq_row( array( '', '', '' ) ); ?></template>
			<p><button type="button" class="button" id="mzfaq-add">+ <?php esc_html_e( 'Add question', 'mrzaib' ); ?></button></p>
			<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Save FAQs', 'mrzaib' ); ?></button>
			<button type="submit" name="mz_reset" value="1" class="button-link" style="margin-left:14px;color:#b32d2e" onclick="return confirm('Reset all FAQs to the theme defaults?')"><?php esc_html_e( 'Reset to defaults', 'mrzaib' ); ?></button></p>
		</form>
	</div>
	<style>
	.mzfaq-row{display:grid;grid-template-columns:180px 1fr auto;gap:10px;align-items:start;background:#fff;border:1px solid #dcdcde;border-left:4px solid #deba68;border-radius:6px;padding:12px;margin:10px 0;max-width:1100px}
	.mzfaq-row label{display:block;font-weight:600;font-size:12px;margin-bottom:3px}
	.mzfaq-row input,.mzfaq-row textarea{width:100%}
	.mzfaq-row .mzfaq-tools{display:flex;flex-direction:column;gap:4px;padding-top:18px}
	.mzfaq-row .mzfaq-del{color:#b32d2e}
	@media(max-width:782px){.mzfaq-row{grid-template-columns:1fr}.mzfaq-row .mzfaq-tools{flex-direction:row;padding:0}}
	</style>
	<script>
	(function(){var w=document.querySelector('.mzfaq-rows');
	document.getElementById('mzfaq-add').addEventListener('click',function(){var t=document.getElementById('mzfaq-tpl').content.cloneNode(true);var last=w.querySelector('.mzfaq-row:last-child [name="faq_g[]"]');if(last)t.querySelector('[name="faq_g[]"]').value=last.value;w.appendChild(t);w.lastElementChild.querySelector('[name="faq_q[]"]').focus();});
	w.addEventListener('click',function(e){var r=e.target.closest('.mzfaq-row');if(!r)return;
	if(e.target.closest('.mzfaq-del')){e.preventDefault();r.remove();}
	if(e.target.closest('.mzfaq-up')&&r.previousElementSibling){e.preventDefault();w.insertBefore(r,r.previousElementSibling);}
	if(e.target.closest('.mzfaq-down')&&r.nextElementSibling){e.preventDefault();w.insertBefore(r.nextElementSibling,r);}});})();
	</script>
	<?php
}
function mrzaib_faq_row( $r ) {
	?>
	<div class="mzfaq-row">
		<div><label><?php esc_html_e( 'Section', 'mrzaib' ); ?></label><input type="text" name="faq_g[]" value="<?php echo esc_attr( $r[0] ); ?>" placeholder="Orders &amp; Delivery"></div>
		<div><label><?php esc_html_e( 'Question', 'mrzaib' ); ?></label><input type="text" name="faq_q[]" value="<?php echo esc_attr( $r[1] ); ?>">
			<label style="margin-top:8px"><?php esc_html_e( 'Answer', 'mrzaib' ); ?></label><textarea name="faq_a[]" rows="3"><?php echo esc_textarea( $r[2] ); ?></textarea></div>
		<div class="mzfaq-tools"><button type="button" class="button mzfaq-up" title="Move up">&uarr;</button><button type="button" class="button mzfaq-down" title="Move down">&darr;</button><button type="button" class="button-link mzfaq-del"><?php esc_html_e( 'Remove', 'mrzaib' ); ?></button></div>
	</div>
	<?php
}

/* Link to the FAQ editor from the FAQs page edit screen. */
add_action( 'edit_form_after_title', function ( $post ) {
	if ( 'page' === $post->post_type && 'page-templates/faqs.php' === get_page_template_slug( $post ) ) {
		echo '<div class="notice notice-info inline" style="margin:12px 0"><p>' . esc_html__( 'The questions and answers on this page are edited in', 'mrzaib' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=page&page=mrzaib-faqs' ) ) . '"><strong>' . esc_html__( 'Pages > FAQ Questions', 'mrzaib' ) . '</strong></a>.</p></div>';
	}
} );

/* FAQPage structured data on the FAQs page. */
add_action( 'wp_head', function () {
	if ( ! is_page_template( 'page-templates/faqs.php' ) ) {
		return;
	}
	$items = array();
	foreach ( mrzaib_faq_groups() as $list ) {
		foreach ( $list as $f ) {
			$items[] = array( '@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ) );
		}
	}
	if ( $items ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items ) ) . "</script>\n";
	}
} );
