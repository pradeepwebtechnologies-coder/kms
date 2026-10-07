<?php
/**
 * /online-classes/ — the hub for every online class.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

km_page_header(
	km_text( 'km_hub_title', 'Online Indian Music Classes' ),
	km_text( 'km_hub_intro', 'Live one-to-one classes with {founder} on Zoom or Google Meet, for complete beginners to advanced students anywhere in the world. Choose an instrument or style below; every student starts with a free 15-minute consultation.' )
);
?>

<section class="km-section km-section--tight" aria-labelledby="km-hub-classes">
	<div class="km-wrap">
		<h2 class="screen-reader-text" id="km-hub-classes"><?php esc_html_e( 'Classes', 'kms-theme' ); ?></h2>
		<?php
		if ( function_exists( 'kms_render_course_cards' ) ) {
			echo kms_render_course_cards( array( 'heading' => 'h3' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</div>
</section>

<?php if ( km_has_kms() ) : ?>
	<section class="km-section km-section--cream" aria-labelledby="km-hub-how">
		<div class="km-wrap">
			<?php km_section_header( __( 'How it works', 'kms-theme' ), __( 'How online classes work', 'kms-theme' ), '', 'km-hub-how' ); ?>
			<?php echo kms_render_steps(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="km-split">
				<div>
					<h3 class="km-h3"><?php esc_html_e( 'Included with every class', 'kms-theme' ); ?></h3>
					<?php echo kms_render_includes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="km-panel">
					<h3 class="km-h3"><?php esc_html_e( 'Class times in your time zone', 'kms-theme' ); ?></h3>
					<p class="km-tz-line"><?php echo kms_render_timezone(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<h3 class="km-h3"><?php esc_html_e( 'What you need', 'kms-theme' ); ?></h3>
					<p><?php echo esc_html( kms_fill( __( 'A laptop or tablet with {platforms}, a stable internet connection, a quiet space and headphones — plus your instrument for harmonium or tabla.', 'kms-theme' ) ) ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="km-section" id="prices" aria-labelledby="km-hub-prices">
		<div class="km-wrap">
			<?php km_section_header( __( 'Prices', 'kms-theme' ), __( 'Prices for every class', 'kms-theme' ), kms_fact_text( 'guarantee' ), 'km-hub-prices' ); ?>
			<?php echo kms_render_price_table(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>

	<section class="km-section km-section--cream" aria-labelledby="km-hub-teacher">
		<div class="km-wrap">
			<?php km_section_header( __( 'Your teacher', 'kms-theme' ), sprintf( /* translators: %s: founder */ __( 'Learn with %s', 'kms-theme' ), km_fact( 'founder_name' ) ), '', 'km-hub-teacher' ); ?>
			<?php echo kms_render_founder( 'h3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>

	<?php $km_faqs = kms_render_faqs( array( 'group' => 'general' ) ); ?>
	<?php if ( $km_faqs ) : ?>
		<section class="km-section" aria-labelledby="km-hub-faq">
			<div class="km-wrap km-wrap--narrow">
				<?php km_section_header( __( 'FAQ', 'kms-theme' ), __( 'Questions about online classes', 'kms-theme' ), '', 'km-hub-faq' ); ?>
				<?php echo $km_faqs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="km-section km-section--cream" aria-labelledby="km-hub-start">
		<div class="km-wrap km-wrap--narrow">
			<?php km_section_header( __( 'Start here', 'kms-theme' ), __( 'Book your free 15-minute consultation', 'kms-theme' ), kms_fact( 'response_time' ), 'km-hub-start' ); ?>
			<?php echo kms_render_enquiry_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
