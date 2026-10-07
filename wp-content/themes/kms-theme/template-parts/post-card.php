<?php
/**
 * Blog post card.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
<article <?php post_class( 'km-card km-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="km-post-card__media"><?php the_post_thumbnail( 'km-card', array( 'loading' => 'lazy' ) ); ?></div>
	<?php endif; ?>
	<div class="km-post-card__body">
		<?php
		$km_cats = get_the_category();
		if ( $km_cats ) :
			?>
			<p class="km-eyebrow"><?php echo esc_html( $km_cats[0]->name ); ?></p>
		<?php endif; ?>
		<h2 class="km-post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<p class="km-post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26, '…' ) ); ?></p>
		<p class="km-post-meta"><time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'j M Y' ) ); ?></time> · <?php echo esc_html( km_reading_time() ); ?></p>
	</div>
</article>
