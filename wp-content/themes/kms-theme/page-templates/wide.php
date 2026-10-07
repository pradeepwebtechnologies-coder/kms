<?php
/**
 * Template Name: Wide content
 * Template Post Type: page, post
 *
 * Same as the default template with a wider content area, for pages built
 * from full-width blocks (galleries, columns, tables).
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	km_page_header( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<article <?php post_class( 'km-page km-page--wide' ); ?>>
		<div class="km-wrap km-wrap--wide">
			<div class="km-entry">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
