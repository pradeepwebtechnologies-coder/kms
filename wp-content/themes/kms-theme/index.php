<?php
/**
 * Blog index and fallback template.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

$km_blog_id = (int) get_option( 'page_for_posts' );
if ( is_home() && $km_blog_id ) {
	km_page_header( get_the_title( $km_blog_id ), __( 'Guides to harmonium, kirtan, ragas and Indian singing, written from our teaching in Pushkar.', 'kms-theme' ) );
} elseif ( is_archive() ) {
	km_page_header( wp_strip_all_tags( get_the_archive_title() ), wp_strip_all_tags( get_the_archive_description() ) );
} elseif ( is_search() ) {
	/* translators: %s: search query */
	km_page_header( sprintf( __( 'Search results for “%s”', 'kms-theme' ), get_search_query() ) );
} else {
	km_page_header( get_bloginfo( 'name' ) );
}
?>

<div class="km-section km-section--tight">
	<div class="km-wrap">
		<?php if ( is_search() ) : ?>
			<div class="km-search-bar"><?php get_search_form(); ?></div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="km-cards km-cards--posts">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 1,
					'prev_text' => __( 'Previous', 'kms-theme' ),
					'next_text' => __( 'Next', 'kms-theme' ),
				)
			);
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found. Try another search, or browse the online classes.', 'kms-theme' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</div>

<?php
get_footer();
