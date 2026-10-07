<?php
/**
 * Template Name: Landing page (no menu)
 * Template Post Type: page
 *
 * For ad campaigns and newsletters: logo, content and the enquiry call to action,
 * without the main menu or footer columns that pull visitors away.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	km_page_header( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<article <?php post_class( 'km-page km-page--landing' ); ?>>
		<div class="km-wrap km-wrap--content">
			<div class="km-entry">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
