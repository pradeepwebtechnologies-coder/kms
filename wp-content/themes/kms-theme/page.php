<?php
/**
 * Pages. The title is always the single H1 (14 pages on the old site had none or two).
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$km_legacy = false !== strpos( (string) get_post_field( 'post_content', get_the_ID() ), '[et_pb_' );
	km_page_header( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<article <?php post_class( 'km-page' ); ?>>
		<div class="km-wrap <?php echo $km_legacy ? 'km-wrap--legacy' : 'km-wrap--content'; ?>">
			<?php if ( has_post_thumbnail() && ! $km_legacy ) : ?>
				<figure class="km-featured"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager' ) ); ?></figure>
			<?php endif; ?>
			<div class="km-entry">
				<?php the_content(); ?>
			</div>
			<?php
			wp_link_pages(
				array(
					'before' => '<nav class="km-page-links">',
					'after'  => '</nav>',
				)
			);
			?>
		</div>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;

get_footer();
