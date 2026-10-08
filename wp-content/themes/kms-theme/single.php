<?php
/**
 * Blog posts: named author, published and updated dates, a class recommendation.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'km-post' ); ?>>
		<header class="km-page-head km-page-head--post">
			<div class="km-wrap km-wrap--content">
				<?php km_breadcrumbs(); ?>
				<h1 class="km-page-head__title"><?php the_title(); ?></h1>
				<?php km_post_meta(); ?>
				<p class="km-post-meta km-post-meta--sub"><?php echo esc_html( km_reading_time() ); ?></p>
			</div>
		</header>

		<?php // Posts keep a light reading area: the old posts' own styles were written for it. ?>
		<div class="km-light km-post__body">
		<div class="km-wrap km-wrap--content">
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="km-featured"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?></figure>
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

			<?php get_template_part( 'template-parts/post-cta' ); ?>

			<?php
			$km_tags = get_the_tag_list( '<ul class="km-tags"><li>', '</li><li>', '</li></ul>' );
			if ( $km_tags && ! is_wp_error( $km_tags ) ) {
				echo $km_tags; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>

			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="km-muted">' . esc_html__( 'Previous', 'kms-theme' ) . '</span> %title',
					'next_text' => '<span class="km-muted">' . esc_html__( 'Next', 'kms-theme' ) . '</span> %title',
				)
			);
			?>
		</div>
		</div>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		echo '<div class="km-light"><div class="km-wrap km-wrap--content">';
		comments_template();
		echo '</div></div>';
	}
endwhile;

get_footer();
