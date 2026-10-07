<?php
/**
 * A single online class.
 *
 * Answer-first: the opening paragraph and the "at a glance" table answer
 * who/what/how/how much before anything else — the format AI answers quote.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$km_id      = get_the_ID();
	$km_slug    = get_post_field( 'post_name', $km_id );
	$km_tagline = function_exists( 'kms_meta' ) ? kms_fill( (string) kms_meta( $km_id, 'kms_tagline' ) ) : '';
	$km_answer  = function_exists( 'kms_course_answer' ) ? kms_course_answer( $km_id ) : get_the_excerpt();
	$km_wa      = function_exists( 'kms_meta' ) ? (string) kms_meta( $km_id, 'kms_wa_text' ) : '';
	?>

	<article <?php post_class( 'km-course' ); ?>>
		<header class="km-page-head km-page-head--course">
			<div class="km-wrap km-course__head<?php echo has_post_thumbnail() ? ' has-media' : ''; ?>">
				<div class="km-course__intro">
					<?php km_breadcrumbs(); ?>
					<h1 class="km-page-head__title"><?php the_title(); ?></h1>
					<?php if ( $km_tagline ) : ?>
						<p class="km-page-head__lead"><?php echo esc_html( $km_tagline ); ?></p>
					<?php endif; ?>
					<?php if ( $km_answer ) : ?>
						<p class="km-answer"><?php echo esc_html( $km_answer ); ?></p>
					<?php endif; ?>
					<div class="km-actions">
						<?php
						if ( function_exists( 'kms_enquiry_button' ) ) {
							echo kms_enquiry_button( __( 'Book a free 15-minute consultation', 'kms-theme' ), $km_slug, 'km-btn--lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						km_whatsapp_button( $km_wa, __( 'Ask on WhatsApp', 'kms-theme' ), 'km-btn--outline km-btn--lg' );
						?>
					</div>
				</div>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="km-course__media"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?></figure>
				<?php endif; ?>
			</div>
		</header>

		<div class="km-wrap km-course__layout">
			<div class="km-course__main">
				<?php if ( function_exists( 'kms_render_course_facts' ) ) : ?>
					<section class="km-block" aria-labelledby="km-glance">
						<h2 id="km-glance"><?php esc_html_e( 'At a glance', 'kms-theme' ); ?></h2>
						<?php echo kms_render_course_facts( $km_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</section>
				<?php endif; ?>

				<?php if ( function_exists( 'kms_render_meta_list' ) ) : ?>
					<?php $km_who = kms_render_meta_list( $km_id, 'kms_audience', 'user' ); ?>
					<?php if ( $km_who ) : ?>
						<section class="km-block" aria-labelledby="km-who">
							<h2 id="km-who"><?php esc_html_e( 'Who this class is for', 'kms-theme' ); ?></h2>
							<?php echo $km_who; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</section>
					<?php endif; ?>

					<?php $km_out = kms_render_meta_list( $km_id, 'kms_outcomes', 'check' ); ?>
					<?php if ( $km_out ) : ?>
						<section class="km-block" aria-labelledby="km-outcomes">
							<h2 id="km-outcomes"><?php esc_html_e( 'What you will be able to do', 'kms-theme' ); ?></h2>
							<?php echo $km_out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</section>
					<?php endif; ?>
				<?php endif; ?>

				<div class="km-entry">
					<?php the_content(); ?>
				</div>

				<?php if ( function_exists( 'kms_render_meta_list' ) ) : ?>
					<?php $km_need = kms_render_meta_list( $km_id, 'kms_requirements', 'laptop' ); ?>
					<?php if ( $km_need ) : ?>
						<section class="km-block" aria-labelledby="km-need">
							<h2 id="km-need"><?php esc_html_e( 'What you need', 'kms-theme' ); ?></h2>
							<?php echo $km_need; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</section>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( function_exists( 'kms_render_course_pricing' ) ) : ?>
					<?php $km_pricing = kms_render_course_pricing( $km_id, 'h3' ); ?>
					<?php if ( $km_pricing ) : ?>
						<section class="km-block" id="prices" aria-labelledby="km-prices">
							<h2 id="km-prices"><?php esc_html_e( 'Prices', 'kms-theme' ); ?></h2>
							<?php echo $km_pricing; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</section>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( function_exists( 'kms_render_founder' ) ) : ?>
					<section class="km-block" aria-labelledby="km-teacher">
						<h2 id="km-teacher"><?php esc_html_e( 'Your teacher', 'kms-theme' ); ?></h2>
						<?php echo kms_render_founder( 'h3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</section>
				<?php endif; ?>

				<?php if ( function_exists( 'kms_render_reviews' ) ) : ?>
					<?php $km_reviews = kms_render_reviews( array( 'limit' => 4, 'course' => $km_slug ) ); ?>
					<?php if ( $km_reviews ) : ?>
						<section class="km-block" aria-labelledby="km-reviews">
							<h2 id="km-reviews"><?php esc_html_e( 'What students say', 'kms-theme' ); ?></h2>
							<?php echo $km_reviews; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</section>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( function_exists( 'kms_render_faqs' ) ) : ?>
					<?php $km_faqs = kms_render_faqs( array( 'group' => $km_slug . ',pricing' ) ); ?>
					<?php if ( $km_faqs ) : ?>
						<section class="km-block" aria-labelledby="km-faq">
							<h2 id="km-faq"><?php esc_html_e( 'Frequently asked questions', 'kms-theme' ); ?></h2>
							<?php echo $km_faqs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</section>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( function_exists( 'kms_render_enquiry_form' ) ) : ?>
					<section class="km-block km-block--form" aria-labelledby="km-start">
						<h2 id="km-start"><?php esc_html_e( 'Book your free consultation', 'kms-theme' ); ?></h2>
						<p><?php echo esc_html( km_fact( 'response_time' ) ); ?></p>
						<?php echo kms_render_enquiry_form( array( 'course' => $km_slug ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</section>
				<?php endif; ?>
			</div>

			<?php if ( km_has_kms() ) : ?>
			<aside class="km-course__aside" aria-label="<?php esc_attr_e( 'Enrol', 'kms-theme' ); ?>">
				<div class="km-enrol-card">
					<?php
					$km_from = function_exists( 'kms_course_from_price' ) ? kms_course_from_price( $km_id ) : 0;
					if ( $km_from ) :
						?>
						<p class="km-enrol-card__price"><span><?php esc_html_e( 'From', 'kms-theme' ); ?></span> <?php echo kms_price_html( $km_from ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <span><?php esc_html_e( 'per class', 'kms-theme' ); ?></span></p>
					<?php endif; ?>
					<ul class="km-checklist km-checklist--compact">
						<li><?php echo km_icon( 'video' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( kms_fill( __( 'Live one-to-one on {platforms}', 'kms-theme' ) ) ); ?></li>
						<li><?php echo km_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( /* translators: %d: minutes */ __( '%d-minute classes', 'kms-theme' ), function_exists( 'kms_course_minutes' ) ? kms_course_minutes( $km_id ) : 40 ) ); ?></li>
						<li><?php echo km_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( km_fact( 'guarantee' ) ); ?></li>
					</ul>
					<?php
					if ( function_exists( 'kms_enquiry_button' ) ) {
						echo kms_enquiry_button( __( 'Free consultation', 'kms-theme' ), $km_slug, 'km-btn--block' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					km_whatsapp_button( $km_wa, __( 'WhatsApp', 'kms-theme' ), 'km-btn--outline km-btn--block' );
					?>
				</div>
			</aside>
			<?php endif; ?>
		</div>
	</article>

	<?php if ( function_exists( 'kms_render_course_cards' ) ) : ?>
		<?php $km_more = kms_render_course_cards( array( 'heading' => 'h3', 'exclude' => $km_id ) ); ?>
		<?php if ( $km_more ) : ?>
			<section class="km-section km-section--cream" aria-labelledby="km-more">
				<div class="km-wrap">
					<?php km_section_header( '', __( 'Other online classes', 'kms-theme' ), '', 'km-more' ); ?>
					<?php echo $km_more; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</section>
		<?php endif; ?>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
