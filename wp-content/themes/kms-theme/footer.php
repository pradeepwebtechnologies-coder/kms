<?php
/**
 * Site footer.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

$km_courses = function_exists( 'kms_get_courses' ) ? kms_get_courses() : array();
?>
</main>

<footer class="km-footer">
	<?php if ( ! is_page_template( 'page-templates/landing.php' ) ) : ?>
	<div class="km-wrap km-footer__grid">
		<div class="km-footer__brand">
			<?php km_logo( true ); ?>
			<?php if ( km_fact( 'description' ) && function_exists( 'kms_fact_text' ) ) : ?>
				<p><?php echo esc_html( kms_fact_text( 'description' ) ); ?></p>
			<?php endif; ?>
			<?php km_social_links(); ?>
		</div>

		<?php if ( $km_courses ) : ?>
			<nav class="km-footer__col" aria-labelledby="km-foot-classes">
				<h2 class="km-footer__title" id="km-foot-classes"><?php esc_html_e( 'Online classes', 'kms-theme' ); ?></h2>
				<ul>
					<?php foreach ( $km_courses as $km_course ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $km_course ) ); ?>"><?php echo esc_html( kms_course_short_title( $km_course ) ); ?></a></li>
					<?php endforeach; ?>
					<?php if ( function_exists( 'kms_page_url' ) && kms_page_url( 'pricing' ) ) : ?>
						<li><a href="<?php echo esc_url( kms_page_url( 'pricing' ) ); ?>"><?php esc_html_e( 'Prices', 'kms-theme' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</nav>
		<?php endif; ?>

		<nav class="km-footer__col" aria-labelledby="km-foot-school">
			<h2 class="km-footer__title" id="km-foot-school"><?php esc_html_e( 'The school', 'kms-theme' ); ?></h2>
			<ul>
				<?php foreach ( km_footer_school_links() as $km_link ) : ?>
					<li><a href="<?php echo esc_url( $km_link[1] ); ?>"><?php echo esc_html( $km_link[0] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<div class="km-footer__col">
			<h2 class="km-footer__title"><?php esc_html_e( 'Contact', 'kms-theme' ); ?></h2>
			<?php
			if ( function_exists( 'kms_render_contact_list' ) ) {
				echo kms_render_contact_list(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in KMS Core.
			}
			?>
			<a class="km-btn km-btn--primary" href="<?php echo esc_url( km_cta_url() ); ?>"><?php esc_html_e( 'Book a free consultation', 'kms-theme' ); ?></a>
		</div>
	</div>
	<?php endif; ?>

	<div class="km-wrap km-footer__bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( km_fact( 'name', get_bloginfo( 'name' ) ) ); ?> · <?php echo esc_html( trim( km_fact( 'locality', '' ) . ', ' . km_fact( 'region', '' ) . ', ' . km_fact( 'country', '' ), ', ' ) ); ?></p>
		<?php km_footer_links(); ?>
	</div>
</footer>

<?php if ( km_whatsapp_url() ) : ?>
	<div class="km-sticky-cta" data-km-sticky>
		<a class="km-btn km-btn--whatsapp" href="<?php echo esc_url( km_whatsapp_url( __( 'Hi! I would like to know more about your online music classes.', 'kms-theme' ) ) ); ?>" target="_blank" rel="noopener"><?php echo km_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'WhatsApp', 'kms-theme' ); ?></a>
		<a class="km-btn km-btn--primary" href="<?php echo esc_url( km_cta_url() ); ?>"><?php esc_html_e( 'Free consultation', 'kms-theme' ); ?></a>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
