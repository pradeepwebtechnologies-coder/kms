<?php
/**
 * Comments. The heading is an H2 — on the old site the comment count was an H1.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="km-comments" aria-labelledby="km-comments-title">
	<?php if ( have_comments() ) : ?>
		<h2 id="km-comments-title" class="km-comments__title">
			<?php
			$km_count = (int) get_comments_number();
			/* translators: %d: number of comments */
			echo esc_html( sprintf( _n( '%d comment', '%d comments', $km_count, 'kms-theme' ), $km_count ) );
			?>
		</h2>
		<ol class="km-comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 40,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php else : ?>
		<h2 id="km-comments-title" class="screen-reader-text"><?php esc_html_e( 'Comments', 'kms-theme' ); ?></h2>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		)
	);
	?>
</section>
