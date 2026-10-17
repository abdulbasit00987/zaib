<?php
/**
 * Customer Reviews + Customer Questions widget (reference-style, theme native).
 *
 * Reviews  = WooCommerce product reviews (comment_type "review") + meta:
 *            rating, mz_title, mz_recommend (yes|no), mz_images (attachment ids), mz_useful, verified.
 * Questions = comments of type "mz_question" on the product; the store answer is any approved
 *            child comment written by a shop manager / admin (reply from Comments screen).
 *
 * @package mrzaib
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

/* ---------- helpers ---------- */
function mrzaib_initials( $name ) {
	$name  = trim( preg_replace( '/[^\p{L}\p{N}\s]/u', ' ', (string) $name ) );
	$parts = preg_split( '/\s+/', $name );
	$i     = '';
	foreach ( array_slice( $parts, 0, 2 ) as $p ) {
		$i .= mb_strtoupper( mb_substr( $p, 0, 1 ) );
	}
	return $i ? $i : 'A';
}
function mrzaib_short_name( $name ) {
	$parts = preg_split( '/\s+/', trim( (string) $name ) );
	if ( count( $parts ) > 1 ) {
		return $parts[0] . ' ' . mb_strtoupper( mb_substr( end( $parts ), 0, 1 ) ) . '.';
	}
	return $parts[0];
}
function mrzaib_rel_date( $gmt ) {
	$ts   = strtotime( $gmt . ' UTC' );
	$diff = time() - $ts;
	if ( $diff < DAY_IN_SECONDS ) {
		return __( 'Today', 'mrzaib' );
	}
	if ( $diff < 2 * DAY_IN_SECONDS ) {
		return __( 'Yesterday', 'mrzaib' );
	}
	if ( $diff < 7 * DAY_IN_SECONDS ) {
		/* translators: %d days */
		return sprintf( __( '%d days ago', 'mrzaib' ), floor( $diff / DAY_IN_SECONDS ) );
	}
	if ( $diff < 14 * DAY_IN_SECONDS ) {
		return __( 'Last week', 'mrzaib' );
	}
	if ( $diff < 35 * DAY_IN_SECONDS ) {
		/* translators: %d weeks */
		return sprintf( __( '%d weeks ago', 'mrzaib' ), floor( $diff / WEEK_IN_SECONDS ) );
	}
	return date_i18n( 'M j, Y', $ts );
}
function mrzaib_stars( $rating, $size = 16 ) {
	$out = '<span class="mzr-stars" style="--s:' . (int) $size . 'px" aria-label="' . esc_attr( sprintf( __( '%s out of 5 stars', 'mrzaib' ), $rating ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$fill = max( 0, min( 1, $rating - ( $i - 1 ) ) ) * 100;
		$out .= '<span class="mzr-star"><span style="width:' . esc_attr( $fill ) . '%"></span></span>';
	}
	return $out . '</span>';
}
function mrzaib_store_name() {
	return get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'MR ZAIB';
}

/* ---------- data ---------- */
function mrzaib_get_reviews( $pid ) {
	$comments = get_comments( array( 'post_id' => $pid, 'status' => 'approve', 'type' => 'review', 'parent' => 0, 'orderby' => 'comment_date_gmt', 'order' => 'DESC' ) );
	$out      = array();
	foreach ( $comments as $c ) {
		$imgs = array();
		foreach ( (array) get_comment_meta( $c->comment_ID, 'mz_images', true ) as $aid ) {
			if ( $aid && wp_attachment_is_image( $aid ) ) {
				$imgs[] = array( 't' => wp_get_attachment_image_url( $aid, 'thumbnail' ), 'f' => wp_get_attachment_image_url( $aid, 'large' ) );
			}
		}
		$replies = get_comments( array( 'parent' => $c->comment_ID, 'status' => 'approve' ) );
		$reply   = $replies ? array( 'body' => $replies[0]->comment_content, 'date' => mrzaib_rel_date( $replies[0]->comment_date_gmt ) ) : null;
		$out[]   = array(
			'id'        => (int) $c->comment_ID,
			'name'      => mrzaib_short_name( $c->comment_author ),
			'initials'  => mrzaib_initials( $c->comment_author ),
			'rating'    => (int) get_comment_meta( $c->comment_ID, 'rating', true ),
			'title'     => (string) get_comment_meta( $c->comment_ID, 'mz_title', true ),
			'body'      => $c->comment_content,
			'recommend' => (string) get_comment_meta( $c->comment_ID, 'mz_recommend', true ),
			'verified'  => (bool) get_comment_meta( $c->comment_ID, 'verified', true ),
			'useful'    => (int) get_comment_meta( $c->comment_ID, 'mz_useful', true ),
			'date'      => mrzaib_rel_date( $c->comment_date_gmt ),
			'ts'        => strtotime( $c->comment_date_gmt ),
			'images'    => $imgs,
			'reply'     => $reply,
		);
	}
	return $out;
}
function mrzaib_get_questions( $pid ) {
	$qs  = get_comments( array( 'post_id' => $pid, 'status' => 'approve', 'type' => 'mz_question', 'parent' => 0, 'orderby' => 'comment_date_gmt', 'order' => 'DESC' ) );
	$out = array();
	foreach ( $qs as $q ) {
		$ans = null;
		foreach ( get_comments( array( 'parent' => $q->comment_ID, 'status' => 'approve', 'orderby' => 'comment_date_gmt', 'order' => 'ASC' ) ) as $a ) {
			$ans = array( 'body' => $a->comment_content, 'date' => mrzaib_rel_date( $a->comment_date_gmt ) );
			break;
		}
		$out[] = array(
			'id'     => (int) $q->comment_ID,
			'q'      => $q->comment_content,
			'name'   => mrzaib_short_name( $q->comment_author ),
			'date'   => mrzaib_rel_date( $q->comment_date_gmt ),
			'ts'     => strtotime( $q->comment_date_gmt ),
			'answer' => $ans,
		);
	}
	return $out;
}

/* Questions must not count as reviews / show in WC review lists. */
add_action( 'pre_get_comments', function ( $q ) {
	if ( is_admin() ) {
		return;
	}
	$type = $q->query_vars['type'] ?? '';
	if ( '' === $type || 'all' === $type ) {
		$not                         = (array) ( $q->query_vars['type__not_in'] ?? array() );
		$not[]                       = 'mz_question';
		$q->query_vars['type__not_in'] = $not;
	}
} );
/* Show "Questions" type in the admin comments type filter. */
add_filter( 'admin_comment_types_dropdown', function ( $t ) {
	$t['mz_question'] = __( 'Product questions', 'mrzaib' );
	return $t;
} );

/* ---------- render ---------- */
function mrzaib_reviews_widget() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$pid       = $product->get_id();
	$reviews   = mrzaib_get_reviews( $pid );
	$questions = mrzaib_get_questions( $pid );
	$count     = count( $reviews );
	$sum       = 0;
	$dist      = array( 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 );
	$rec_yes   = 0;
	$rec_all   = 0;
	$media     = array();
	foreach ( $reviews as $r ) {
		$sum += $r['rating'];
		if ( isset( $dist[ $r['rating'] ] ) ) {
			$dist[ $r['rating'] ]++;
		}
		if ( $r['recommend'] ) {
			$rec_all++;
			$rec_yes += ( 'yes' === $r['recommend'] ) ? 1 : 0;
		}
		foreach ( $r['images'] as $im ) {
			$media[] = $im + array( 'rid' => $r['id'] );
		}
	}
	$avg   = $count ? round( $sum / $count, 1 ) : 0;
	$max   = max( 1, max( $dist ) );
	$rec   = $rec_all ? round( $rec_yes / $rec_all * 100 ) : 0;
	$thumb = get_the_post_thumbnail_url( $pid, 'thumbnail' );
	$store = mrzaib_store_name();
	$data  = array(
		'pid'       => $pid,
		'nonce'     => wp_create_nonce( 'mzr_' . $pid ),
		'ajax'      => admin_url( 'admin-ajax.php' ),
		'title'     => $product->get_name(),
		'store'     => $store,
		'storeIni'  => mrzaib_initials( $store ),
		'reviews'   => $reviews,
		'questions' => $questions,
	);
	?>
	<section class="mzr" id="reviews" data-mzr="<?php echo esc_attr( wp_json_encode( $data ) ); ?>">
		<div class="mzr-tabs" role="tablist">
			<button type="button" class="mzr-tab is-active" role="tab" aria-selected="true" data-tab="reviews"><?php esc_html_e( 'Reviews', 'mrzaib' ); ?> <span class="mzr-badge"><?php echo (int) $count; ?></span></button>
			<button type="button" class="mzr-tab" role="tab" aria-selected="false" data-tab="questions"><?php esc_html_e( 'Questions', 'mrzaib' ); ?> <span class="mzr-badge"><?php echo count( $questions ); ?></span></button>
		</div>

		<div class="mzr-panel is-active" data-panel="reviews">
			<div class="mzr-summary">
				<div class="mzr-score">
					<div class="mzr-avg"><strong><?php echo esc_html( number_format( $avg, 1 ) ); ?></strong><?php echo mrzaib_stars( $avg, 22 ); // phpcs:ignore ?></div>
					<p class="mzr-based"><?php printf( esc_html( _n( 'Based on %s review', 'Based on %s reviews', $count, 'mrzaib' ) ), esc_html( number_format_i18n( $count ) ) ); ?>
						<span class="mzr-info" tabindex="0" aria-label="<?php esc_attr_e( 'How reviews work', 'mrzaib' ); ?>">i<span class="mzr-tip"><?php esc_html_e( 'Reviews are written by customers. Verified buyer means the reviewer bought this product from our store.', 'mrzaib' ); ?></span></span></p>
					<button type="button" class="mzr-btn mzr-btn--primary" data-open="review"><?php esc_html_e( 'Write a review', 'mrzaib' ); ?></button>
				</div>
				<div class="mzr-bars">
					<?php foreach ( $dist as $star => $n ) : ?>
						<button type="button" class="mzr-bar" data-star="<?php echo (int) $star; ?>" <?php echo $n ? '' : 'aria-disabled="true"'; ?> aria-label="<?php echo esc_attr( sprintf( __( '%1$d %2$d-star reviews. Show only these.', 'mrzaib' ), $n, $star ) ); ?>">
							<span class="mzr-bar-l"><?php echo (int) $star; ?> <span class="mzr-mini-star">★</span></span>
							<span class="mzr-bar-t"><span style="width:<?php echo esc_attr( round( $n / $max * 100 ) ); ?>%"></span></span>
							<span class="mzr-bar-n"><?php echo (int) $n; ?></span>
						</button>
					<?php endforeach; ?>
				</div>
				<div class="mzr-rec">
					<?php if ( $rec_all ) : ?>
						<span class="mzr-rec-ico"><?php echo mrzaib_icon( 'check' ); // phpcs:ignore ?></span>
						<p><strong><?php echo (int) $rec; ?>%</strong> <?php esc_html_e( 'would recommend this product', 'mrzaib' ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( $media ) : ?>
				<div class="mzr-media">
					<p class="mzr-media-h"><?php esc_html_e( 'Customer photos and videos', 'mrzaib' ); ?></p>
					<div class="mzr-strip">
						<button type="button" class="mzr-strip-arrow is-prev" aria-label="<?php esc_attr_e( 'Previous', 'mrzaib' ); ?>"></button>
						<div class="mzr-strip-track">
							<?php foreach ( $media as $i => $m ) : ?>
								<button type="button" class="mzr-tile" data-full="<?php echo esc_url( $m['f'] ); ?>" data-rid="<?php echo (int) $m['rid']; ?>"><img src="<?php echo esc_url( $m['t'] ); ?>" alt="" loading="lazy"></button>
							<?php endforeach; ?>
						</div>
						<button type="button" class="mzr-strip-arrow is-next" aria-label="<?php esc_attr_e( 'Next', 'mrzaib' ); ?>"></button>
					</div>
				</div>
			<?php endif; ?>

			<div class="mzr-toolbar">
				<label class="mzr-search"><?php echo mrzaib_icon( 'search' ); // phpcs:ignore ?><input type="search" placeholder="<?php esc_attr_e( 'Search reviews', 'mrzaib' ); ?>" data-search="reviews"></label>
				<div class="mzr-tools">
					<select class="mzr-pill" data-filter="reviews" aria-label="<?php esc_attr_e( 'Filters', 'mrzaib' ); ?>">
						<option value=""><?php esc_html_e( 'Filters: All ratings', 'mrzaib' ); ?></option>
						<?php for ( $s = 5; $s >= 1; $s-- ) : ?><option value="<?php echo (int) $s; ?>"><?php echo esc_html( sprintf( __( '%d stars', 'mrzaib' ), $s ) ); ?></option><?php endfor; ?>
						<option value="photos"><?php esc_html_e( 'With photos', 'mrzaib' ); ?></option>
						<option value="verified"><?php esc_html_e( 'Verified buyers', 'mrzaib' ); ?></option>
					</select>
					<select class="mzr-pill" data-sort="reviews" aria-label="<?php esc_attr_e( 'Sort reviews', 'mrzaib' ); ?>">
						<option value="recent"><?php esc_html_e( 'Sort: Most recent', 'mrzaib' ); ?></option>
						<option value="high"><?php esc_html_e( 'Sort: Highest rating', 'mrzaib' ); ?></option>
						<option value="low"><?php esc_html_e( 'Sort: Lowest rating', 'mrzaib' ); ?></option>
						<option value="useful"><?php esc_html_e( 'Sort: Most useful', 'mrzaib' ); ?></option>
					</select>
				</div>
			</div>
			<ul class="mzr-list" data-list="reviews"></ul>
			<div class="mzr-more"><p class="mzr-showing" data-showing="reviews"></p><button type="button" class="mzr-btn" data-more="reviews"><?php esc_html_e( 'Show more reviews', 'mrzaib' ); ?></button></div>
		</div>

		<div class="mzr-panel" data-panel="questions" hidden>
			<div class="mzr-toolbar">
				<label class="mzr-search"><?php echo mrzaib_icon( 'search' ); // phpcs:ignore ?><input type="search" placeholder="<?php esc_attr_e( 'Search questions', 'mrzaib' ); ?>" data-search="questions"></label>
				<div class="mzr-tools">
					<select class="mzr-pill" data-sort="questions" aria-label="<?php esc_attr_e( 'Sort questions', 'mrzaib' ); ?>">
						<option value="recent"><?php esc_html_e( 'Sort: Most recent', 'mrzaib' ); ?></option>
						<option value="oldest"><?php esc_html_e( 'Sort: Oldest', 'mrzaib' ); ?></option>
						<option value="answered"><?php esc_html_e( 'Sort: Answered first', 'mrzaib' ); ?></option>
					</select>
					<button type="button" class="mzr-btn mzr-btn--primary" data-open="question"><?php esc_html_e( 'Ask a question', 'mrzaib' ); ?></button>
				</div>
			</div>
			<ul class="mzr-list" data-list="questions"></ul>
			<div class="mzr-more"><p class="mzr-showing" data-showing="questions"></p><button type="button" class="mzr-btn" data-more="questions"><?php esc_html_e( 'Show more questions', 'mrzaib' ); ?></button></div>
		</div>

		<!-- Write a review modal (5 steps) -->
		<div class="mzr-modal" data-modal="review" hidden>
			<div class="mzr-dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Write a review', 'mrzaib' ); ?>">
				<div class="mzr-progress"><span></span><span></span><span></span><span></span><span></span></div>
				<div class="mzr-dtop"><button type="button" class="mzr-back" aria-label="<?php esc_attr_e( 'Back', 'mrzaib' ); ?>">&#8592;</button><button type="button" class="mzr-x" data-close aria-label="<?php esc_attr_e( 'Close', 'mrzaib' ); ?>">&times;</button></div>
				<form class="mzr-form" data-form="review" novalidate>
					<div class="mzr-step is-active" data-step="1">
						<?php if ( $thumb ) : ?><img class="mzr-pimg" src="<?php echo esc_url( $thumb ); ?>" alt=""><?php endif; ?>
						<h3><?php printf( esc_html__( 'How would you rate %s?', 'mrzaib' ), esc_html( $product->get_name() ) ); ?></h3>
						<p class="mzr-sub"><?php esc_html_e( 'Tap a star to rate', 'mrzaib' ); ?></p>
						<div class="mzr-rate" role="radiogroup">
							<?php for ( $s = 1; $s <= 5; $s++ ) : ?><button type="button" class="mzr-rate-star" data-v="<?php echo (int) $s; ?>" role="radio" aria-label="<?php echo esc_attr( sprintf( _n( '%d star', '%d stars', $s, 'mrzaib' ), $s ) ); ?>">★</button><?php endfor; ?>
						</div>
						<input type="hidden" name="rating" value="">
						<button type="button" class="mzr-btn mzr-btn--primary mzr-next" disabled><?php esc_html_e( 'Continue', 'mrzaib' ); ?></button>
					</div>
					<div class="mzr-step" data-step="2">
						<h3><?php esc_html_e( 'Add photos', 'mrzaib' ); ?></h3>
						<p class="mzr-sub"><?php esc_html_e( 'Shoppers trust reviews with photos the most.', 'mrzaib' ); ?></p>
						<label class="mzr-upload"><input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple>
							<span class="mzr-upload-ico">+</span><strong><?php esc_html_e( 'Add photos', 'mrzaib' ); ?></strong><span><?php esc_html_e( 'Choose files or drag them here', 'mrzaib' ); ?></span><small><?php esc_html_e( 'Up to 5 photos, 5 MB each', 'mrzaib' ); ?></small></label>
						<div class="mzr-previews"></div>
						<button type="button" class="mzr-btn mzr-btn--secondary mzr-next" data-skip><?php esc_html_e( 'Skip', 'mrzaib' ); ?></button>
					</div>
					<div class="mzr-step" data-step="3">
						<h3><?php esc_html_e( 'What stood out, good or bad?', 'mrzaib' ); ?></h3>
						<p class="mzr-sub"><?php esc_html_e( 'Helpful reviews mention scent, lasting, projection and value.', 'mrzaib' ); ?></p>
						<label class="mzr-field"><span><?php esc_html_e( 'Review title', 'mrzaib' ); ?></span><input type="text" name="title" maxlength="120" placeholder="<?php esc_attr_e( 'Sum it up in a sentence', 'mrzaib' ); ?>"></label>
						<label class="mzr-field"><span><?php esc_html_e( 'Your review', 'mrzaib' ); ?> <em>*</em></span><textarea name="body" rows="5" placeholder="<?php esc_attr_e( 'What did you like? What could be better?', 'mrzaib' ); ?>"></textarea><small class="mzr-count"></small></label>
						<button type="button" class="mzr-btn mzr-btn--primary mzr-next" disabled data-min="10"><?php esc_html_e( 'Continue', 'mrzaib' ); ?></button>
					</div>
					<div class="mzr-step" data-step="4">
						<h3><?php esc_html_e( 'Would you recommend this product?', 'mrzaib' ); ?></h3>
						<div class="mzr-yn">
							<button type="button" data-rec="yes"><?php esc_html_e( 'Yes', 'mrzaib' ); ?></button>
							<button type="button" data-rec="no"><?php esc_html_e( 'No', 'mrzaib' ); ?></button>
						</div>
						<input type="hidden" name="recommend" value="">
						<button type="button" class="mzr-btn mzr-btn--primary mzr-next" disabled><?php esc_html_e( 'Continue', 'mrzaib' ); ?></button>
					</div>
					<div class="mzr-step" data-step="5">
						<h3><?php esc_html_e( 'About you', 'mrzaib' ); ?></h3>
						<label class="mzr-field"><span><?php esc_html_e( 'Your name', 'mrzaib' ); ?> <em>*</em></span><input type="text" name="name" required placeholder="<?php esc_attr_e( 'Ali', 'mrzaib' ); ?>"><small><?php esc_html_e( 'Shown with your review.', 'mrzaib' ); ?></small></label>
						<label class="mzr-field"><span><?php esc_html_e( 'Email', 'mrzaib' ); ?> <em>*</em></span><input type="email" name="email" required placeholder="ali@example.com"><small><?php esc_html_e( 'Private. Used to verify your purchase.', 'mrzaib' ); ?></small></label>
						<button type="submit" class="mzr-btn mzr-btn--primary" disabled><?php esc_html_e( 'Submit review', 'mrzaib' ); ?></button>
						<p class="mzr-err" hidden></p>
					</div>
					<div class="mzr-step mzr-done" data-step="6">
						<div class="mzr-done-ico"><?php echo mrzaib_icon( 'check' ); // phpcs:ignore ?></div>
						<h3><?php esc_html_e( 'Thank you for your review!', 'mrzaib' ); ?></h3>
						<p class="mzr-sub" data-msg></p>
						<button type="button" class="mzr-btn mzr-btn--primary" data-close><?php esc_html_e( 'Done', 'mrzaib' ); ?></button>
					</div>
				</form>
			</div>
		</div>

		<!-- Ask a question modal -->
		<div class="mzr-modal" data-modal="question" hidden>
			<div class="mzr-dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Ask a question', 'mrzaib' ); ?>">
				<div class="mzr-dtop"><span></span><button type="button" class="mzr-x" data-close aria-label="<?php esc_attr_e( 'Close', 'mrzaib' ); ?>">&times;</button></div>
				<form class="mzr-form" data-form="question" novalidate>
					<div class="mzr-step is-active">
						<h3 class="mzr-left"><?php esc_html_e( 'Ask a question', 'mrzaib' ); ?></h3>
						<div class="mzr-prod"><?php if ( $thumb ) : ?><img src="<?php echo esc_url( $thumb ); ?>" alt=""><?php endif; ?><span><?php echo esc_html( $product->get_name() ); ?></span></div>
						<label class="mzr-field"><span><?php esc_html_e( 'Your question', 'mrzaib' ); ?> <em>*</em></span><textarea name="question" rows="3" required placeholder="<?php esc_attr_e( 'What would you like to know before you buy?', 'mrzaib' ); ?>"></textarea></label>
						<label class="mzr-field"><span><?php esc_html_e( 'Your name', 'mrzaib' ); ?> <em>*</em></span><input type="text" name="name" required placeholder="<?php esc_attr_e( 'Ali', 'mrzaib' ); ?>"><small><?php esc_html_e( 'Shown with your question.', 'mrzaib' ); ?></small></label>
						<label class="mzr-field"><span><?php esc_html_e( 'Email', 'mrzaib' ); ?> <em>*</em></span><input type="email" name="email" required placeholder="ali@example.com"><small><?php esc_html_e( "Private. We'll email you when the store answers.", 'mrzaib' ); ?></small></label>
						<button type="submit" class="mzr-btn mzr-btn--primary mzr-full" disabled><?php esc_html_e( 'Send question', 'mrzaib' ); ?></button>
						<p class="mzr-err" hidden></p>
						<p class="mzr-legal"><?php printf( wp_kses_post( __( 'By submitting, you agree to our <a href="%1$s">Terms</a> and <a href="%2$s">Privacy Policy</a>.', 'mrzaib' ) ), esc_url( home_url( '/terms-of-service/' ) ), esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy-2/' ) ) ); ?></p>
					</div>
					<div class="mzr-step mzr-done">
						<div class="mzr-done-ico"><?php echo mrzaib_icon( 'check' ); // phpcs:ignore ?></div>
						<h3><?php esc_html_e( 'Question sent!', 'mrzaib' ); ?></h3>
						<p class="mzr-sub"><?php esc_html_e( "We'll email you as soon as the store answers.", 'mrzaib' ); ?></p>
						<button type="button" class="mzr-btn mzr-btn--primary" data-close><?php esc_html_e( 'Done', 'mrzaib' ); ?></button>
					</div>
				</form>
			</div>
		</div>

		<div class="mzr-lightbox" hidden><button type="button" class="mzr-x" data-close-lb aria-label="<?php esc_attr_e( 'Close', 'mrzaib' ); ?>">&times;</button><img src="" alt=""></div>
	</section>
	<?php
}

/* Place the widget after the accordions, before related products; drop WC's reviews accordion. */
add_action( 'woocommerce_after_single_product_summary', 'mrzaib_reviews_widget', 14 );
add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	unset( $tabs['reviews'] );
	return $tabs;
}, 99 );

/* ---------- AJAX ---------- */
function mrzaib_mzr_check() {
	$pid = isset( $_POST['pid'] ) ? absint( $_POST['pid'] ) : 0;
	if ( ! $pid || 'product' !== get_post_type( $pid ) || ! check_ajax_referer( 'mzr_' . $pid, 'nonce', false ) ) {
		wp_send_json_error( array( 'msg' => __( 'Session expired. Please reload the page.', 'mrzaib' ) ) );
	}
	if ( ! empty( $_POST['website'] ) ) { // honeypot
		wp_send_json_error( array( 'msg' => 'spam' ) );
	}
	return $pid;
}

add_action( 'wp_ajax_mzr_review', 'mrzaib_ajax_review' );
add_action( 'wp_ajax_nopriv_mzr_review', 'mrzaib_ajax_review' );
function mrzaib_ajax_review() {
	$pid    = mrzaib_mzr_check();
	$rating = isset( $_POST['rating'] ) ? absint( $_POST['rating'] ) : 0;
	$body   = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';
	$title  = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$name   = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$rec    = ( isset( $_POST['recommend'] ) && 'no' === $_POST['recommend'] ) ? 'no' : 'yes';
	if ( $rating < 1 || $rating > 5 || mb_strlen( $body ) < 10 || ! $name || ! is_email( $email ) ) {
		wp_send_json_error( array( 'msg' => __( 'Please fill all required fields.', 'mrzaib' ) ) );
	}
	$verified = function_exists( 'wc_customer_bought_product' ) && wc_customer_bought_product( $email, get_current_user_id(), $pid );
	$cid      = wp_new_comment( array(
		'comment_post_ID'      => $pid,
		'comment_author'       => $name,
		'comment_author_email' => $email,
		'comment_content'      => $body,
		'comment_type'         => 'review',
		'user_id'              => get_current_user_id(),
	), true );
	if ( is_wp_error( $cid ) ) {
		wp_send_json_error( array( 'msg' => $cid->get_error_message() ) );
	}
	update_comment_meta( $cid, 'rating', $rating );
	update_comment_meta( $cid, 'mz_title', $title );
	update_comment_meta( $cid, 'mz_recommend', $rec );
	update_comment_meta( $cid, 'verified', $verified ? 1 : 0 );

	/* photos */
	$ids = array();
	if ( ! empty( $_FILES['images'] ) && is_array( $_FILES['images']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$files = $_FILES['images']; // phpcs:ignore
		$n     = min( 5, count( $files['name'] ) );
		for ( $i = 0; $i < $n; $i++ ) {
			if ( UPLOAD_ERR_OK !== $files['error'][ $i ] || $files['size'][ $i ] > 5 * MB_IN_BYTES ) {
				continue;
			}
			$check = wp_check_filetype_and_ext( $files['tmp_name'][ $i ], $files['name'][ $i ] );
			if ( ! in_array( $check['type'], array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
				continue;
			}
			$_FILES['mzr_one'] = array(
				'name'     => $files['name'][ $i ],
				'type'     => $files['type'][ $i ],
				'tmp_name' => $files['tmp_name'][ $i ],
				'error'    => $files['error'][ $i ],
				'size'     => $files['size'][ $i ],
			);
			$aid = media_handle_upload( 'mzr_one', $pid, array(), array( 'test_form' => false ) );
			if ( ! is_wp_error( $aid ) ) {
				$ids[] = $aid;
			}
		}
	}
	if ( $ids ) {
		update_comment_meta( $cid, 'mz_images', $ids );
	}
	if ( class_exists( 'WC_Comments' ) ) {
		WC_Comments::clear_transients( $pid );
	}
	$approved = '1' === (string) wp_get_comment_status( $cid ) || 'approved' === wp_get_comment_status( $cid );
	wp_send_json_success( array(
		'approved' => $approved,
		'msg'      => $approved ? __( 'Your review is now live.', 'mrzaib' ) : __( 'Your review will appear after a quick check by our team.', 'mrzaib' ),
	) );
}

add_action( 'wp_ajax_mzr_question', 'mrzaib_ajax_question' );
add_action( 'wp_ajax_nopriv_mzr_question', 'mrzaib_ajax_question' );
function mrzaib_ajax_question() {
	$pid   = mrzaib_mzr_check();
	$q     = isset( $_POST['question'] ) ? sanitize_textarea_field( wp_unslash( $_POST['question'] ) ) : '';
	$name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( mb_strlen( $q ) < 3 || ! $name || ! is_email( $email ) ) {
		wp_send_json_error( array( 'msg' => __( 'Please fill all required fields.', 'mrzaib' ) ) );
	}
	$cid = wp_insert_comment( array(
		'comment_post_ID'      => $pid,
		'comment_author'       => $name,
		'comment_author_email' => $email,
		'comment_content'      => $q,
		'comment_type'         => 'mz_question',
		'comment_approved'     => 0,
		'comment_author_IP'    => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
	) );
	if ( ! $cid ) {
		wp_send_json_error( array( 'msg' => __( 'Could not send. Please try again.', 'mrzaib' ) ) );
	}
	/* Email the store in the background so the customer never waits on the mail server. */
	wp_schedule_single_event( time(), 'mrzaib_notify_question', array( (int) $cid ) );
	wp_send_json_success();
}

add_action( 'wp_ajax_mzr_useful', 'mrzaib_ajax_useful' );
add_action( 'wp_ajax_nopriv_mzr_useful', 'mrzaib_ajax_useful' );
function mrzaib_ajax_useful() {
	$pid = mrzaib_mzr_check();
	$cid = isset( $_POST['cid'] ) ? absint( $_POST['cid'] ) : 0;
	$c   = $cid ? get_comment( $cid ) : null;
	if ( ! $c || (int) $c->comment_post_ID !== $pid ) {
		wp_send_json_error();
	}
	$n = (int) get_comment_meta( $cid, 'mz_useful', true ) + 1;
	update_comment_meta( $cid, 'mz_useful', $n );
	wp_send_json_success( array( 'n' => $n ) );
}

add_action( 'mrzaib_notify_question', 'wp_notify_moderator' );

/* When the store answers a question (admin reply), approve it and email the asker. */
add_action( 'comment_post', function ( $cid, $approved ) {
	$c = get_comment( $cid );
	if ( ! $c || ! $c->comment_parent ) {
		return;
	}
	$parent = get_comment( $c->comment_parent );
	if ( ! $parent || 'mz_question' !== $parent->comment_type ) {
		return;
	}
	if ( user_can( (int) $c->user_id, 'moderate_comments' ) ) {
		wp_set_comment_status( $parent->comment_ID, 'approve' );
		wp_mail(
			$parent->comment_author_email,
			sprintf( __( 'Your question about %s was answered', 'mrzaib' ), get_the_title( $parent->comment_post_ID ) ),
			sprintf( "%s\n\n%s\n\n%s", $parent->comment_content, $c->comment_content, get_permalink( $parent->comment_post_ID ) . '#reviews' )
		);
	}
}, 10, 2 );

/* ---------- Demo reviews & questions ---------- */
function mrzaib_seed_reviews() {
	$names   = array( 'Ahmed Raza', 'Fatima Noor', 'Hamza Ali', 'Ayesha Khan', 'Bilal Ahmed', 'Usman Tariq', 'Sana Malik', 'Zain Abbas', 'Hira Shah', 'Omar Farooq', 'Mariam Iqbal', 'Daniyal' );
	$samples = array(
		array( 5, 'Long lasting and classy', 'Lasts the whole day on my skin and I get compliments at the office. Packaging was neat and delivery was quick. (Dummy review)', 'yes' ),
		array( 5, 'Best blind buy', 'Ordered without testing and it turned out amazing. Smells premium, projection is strong for the first few hours. (Dummy review)', 'yes' ),
		array( 4, 'Great for evenings', 'Very nice warm scent, perfect for evening wear. Wish the 50ml bottle was a bit bigger. (Dummy review)', 'yes' ),
		array( 5, '', 'Bohat zabardast khushbu hai, poora din chalti hai. Cash on delivery bhi smooth thi. (Dummy review)', 'yes' ),
		array( 5, 'Value for money', 'Quality is like international brands at a fraction of the price. Will order again. (Dummy review)', 'yes' ),
		array( 3, 'Good but soft', 'Nice smell but projection is softer than I expected. Lasting is around 5 to 6 hours. (Dummy review)', 'no' ),
		array( 5, 'Perfect gift', 'Bought it as a gift for my brother and he loved it. The box looks premium. (Dummy review)', 'yes' ),
		array( 4, '', 'Fresh opening and a smooth dry down. Good for daily use in summer. (Dummy review)', 'yes' ),
	);
	$qs = array(
		array( 'How long does it last?', 'It lasts 8 to 12 hours on skin depending on weather and skin type. (Dummy answer)' ),
		array( 'Is it good for summer?', 'Yes, it works well in summer, just apply 2 to 3 sprays on pulse points. (Dummy answer)' ),
		array( 'Kya ye original hai? Cash on delivery available hai?', 'Ji haan, sab products original hain aur poore Pakistan mein Cash on Delivery available hai. (Dummy answer)' ),
	);
	$admin = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
	$admin = $admin ? $admin[0] : null;
	$imgs  = array();
	foreach ( array( 1, 2, 3, 4 ) as $n ) {
		$id = mrzaib_import_image( sprintf( 'product-%02d-2.jpg', $n ) );
		if ( $id ) {
			$imgs[] = $id;
		}
	}
	$done = 0;
	$pids = get_posts( array( 'post_type' => 'product', 'numberposts' => -1, 'fields' => 'ids', 'orderby' => 'ID', 'order' => 'ASC' ) );
	foreach ( $pids as $k => $pid ) {
		if ( get_post_meta( $pid, '_mrzaib_reviews_seeded', true ) ) {
			continue;
		}
		$count = 4 + ( $k % 5 );
		for ( $i = 0; $i < $count; $i++ ) {
			$s    = $samples[ ( $i + $k ) % count( $samples ) ];
			$name = $names[ ( $i * 3 + $k ) % count( $names ) ];
			$date = gmdate( 'Y-m-d H:i:s', time() - ( ( $i * 9 + $k + 2 ) * DAY_IN_SECONDS ) );
			$cid  = wp_insert_comment( array(
				'comment_post_ID'      => $pid,
				'comment_author'       => $name,
				'comment_author_email' => sanitize_title( $name ) . '@example.com',
				'comment_content'      => $s[2],
				'comment_type'         => 'review',
				'comment_approved'     => 1,
				'comment_date'         => get_date_from_gmt( $date ),
				'comment_date_gmt'     => $date,
			) );
			update_comment_meta( $cid, 'rating', $s[0] );
			update_comment_meta( $cid, 'mz_title', $s[1] );
			update_comment_meta( $cid, 'mz_recommend', $s[3] );
			update_comment_meta( $cid, 'verified', ( $i % 3 ) ? 1 : 0 );
			update_comment_meta( $cid, 'mz_useful', ( $i * 2 + $k ) % 6 );
			if ( $imgs && 0 === $i % 3 ) {
				update_comment_meta( $cid, 'mz_images', array( $imgs[ ( $i + $k ) % count( $imgs ) ] ) );
			}
		}
		foreach ( $qs as $j => $q ) {
			if ( $j > ( $k % 3 ) ) {
				break;
			}
			$date = gmdate( 'Y-m-d H:i:s', time() - ( ( $j * 20 + $k + 10 ) * DAY_IN_SECONDS ) );
			$qid  = wp_insert_comment( array(
				'comment_post_ID'      => $pid,
				'comment_author'       => $names[ ( $j + $k + 5 ) % count( $names ) ],
				'comment_author_email' => 'customer' . $j . '@example.com',
				'comment_content'      => $q[0],
				'comment_type'         => 'mz_question',
				'comment_approved'     => 1,
				'comment_date'         => get_date_from_gmt( $date ),
				'comment_date_gmt'     => $date,
			) );
			$adate = gmdate( 'Y-m-d H:i:s', strtotime( $date . ' UTC' ) + 2 * DAY_IN_SECONDS );
			wp_insert_comment( array(
				'comment_post_ID'      => $pid,
				'comment_parent'       => $qid,
				'comment_author'       => mrzaib_store_name(),
				'comment_author_email' => get_option( 'admin_email' ),
				'comment_content'      => $q[1],
				'comment_type'         => 'comment',
				'comment_approved'     => 1,
				'user_id'              => $admin ? $admin->ID : 0,
				'comment_date'         => get_date_from_gmt( $adate ),
				'comment_date_gmt'     => $adate,
			) );
		}
		if ( class_exists( 'WC_Comments' ) ) {
			WC_Comments::clear_transients( $pid );
		}
		update_post_meta( $pid, '_mrzaib_reviews_seeded', 1 );
		$done++;
	}
	update_option( 'woocommerce_enable_reviews', 'yes' );
	update_option( 'woocommerce_review_rating_verification_required', 'no' );
	return sprintf( 'Demo reviews & questions added to %d products.', $done );
}
