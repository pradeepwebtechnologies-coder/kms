<?php
/**
 * End-of-article call to action: recommends the class that matches the post.
 *
 * Matching is by keywords in the post title/categories, falling back to the
 * online class hub. Every one of the 68 posts on the old site ended without
 * a contextual next step.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'kms_get_courses' ) ) {
	return;
}

$km_haystack = strtolower( get_the_title() . ' ' . implode( ' ', wp_list_pluck( get_the_category(), 'name' ) ) );
$km_map      = array(
	'tabla'                  => array( 'tabla', 'tala', 'rhythm', 'taal' ),
	'harmonium'              => array( 'harmonium' ),
	'bhajan-kirtan'          => array( 'kirtan', 'bhajan', 'mantra', 'chant', 'devotional' ),
	'indian-classical-vocal' => array( 'raga', 'raag', 'khayal', 'thaat', 'classical' ),
	'singing'                => array( 'sing', 'vocal', 'voice', 'alankar', 'palta', 'swara', 'sa re ga ma' ),
);
$km_match    = null;
foreach ( $km_map as $km_slug => $km_words ) {
	foreach ( $km_words as $km_word ) {
		if ( false !== strpos( $km_haystack, $km_word ) ) {
			$km_match = get_page_by_path( $km_slug, OBJECT, 'kms_course' );
			break 2;
		}
	}
}
if ( $km_match && 'publish' !== $km_match->post_status ) {
	$km_match = null;
}
$km_url   = $km_match ? get_permalink( $km_match ) : get_post_type_archive_link( 'kms_course' );
$km_title = $km_match ? get_the_title( $km_match ) : __( 'Online Indian music classes', 'kms-theme' );
$km_text  = $km_match ? kms_course_answer( $km_match->ID ) : kms_fill( __( 'Live one-to-one classes with {founder} in harmonium, singing, bhajan and kirtan, Hindustani classical vocal and tabla. Complete beginners welcome.', 'kms-theme' ) );
?>
<aside class="km-post-cta km-light" aria-labelledby="km-post-cta-title">
	<p class="km-eyebrow"><?php esc_html_e( 'Learn this with a teacher', 'kms-theme' ); ?></p>
	<h2 class="km-post-cta__title" id="km-post-cta-title"><a href="<?php echo esc_url( $km_url ); ?>"><?php echo esc_html( $km_title ); ?></a></h2>
	<p><?php echo esc_html( $km_text ); ?></p>
	<div class="km-actions">
		<?php echo kms_enquiry_button( __( 'Book a free 15-minute consultation', 'kms-theme' ), $km_match ? $km_match->post_name : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<a class="km-btn km-btn--outline" href="<?php echo esc_url( $km_url ); ?>"><?php esc_html_e( 'See the class', 'kms-theme' ); ?></a>
	</div>
</aside>
