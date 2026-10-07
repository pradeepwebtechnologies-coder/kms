<?php
/**
 * Real 404 page. The old site redirected every missing URL to the homepage,
 * which Google treats as a soft 404 and which hid broken links.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
km_page_header( __( 'Page not found', 'kms-theme' ), __( 'The page you are looking for has moved or no longer exists. These links will get you back on track.', 'kms-theme' ) );
?>
<div class="km-section km-section--tight">
	<div class="km-wrap km-wrap--content">
		<?php get_search_form(); ?>
		<?php if ( function_exists( 'kms_render_course_cards' ) ) : ?>
			<h2><?php esc_html_e( 'Online classes', 'kms-theme' ); ?></h2>
			<?php echo kms_render_course_cards( array( 'heading' => 'h3' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>
		<p class="km-actions">
			<a class="km-btn km-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to the homepage', 'kms-theme' ); ?></a>
			<?php km_whatsapp_button( __( 'Hi! I could not find a page on your website.', 'kms-theme' ), __( 'Ask us on WhatsApp', 'kms-theme' ), 'km-btn--outline' ); ?>
		</p>
	</div>
</div>
<?php
get_footer();
