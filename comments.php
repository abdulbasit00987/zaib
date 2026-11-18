<?php
/**
 * Comments.
 *
 * @package mrzaib
 */
if ( post_password_required() ) {
	return;
}
?>
<div class="comments-area">
	<?php if ( have_comments() ) : ?>
		<ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) ); ?></ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>
	<?php comment_form(); ?>
</div>
