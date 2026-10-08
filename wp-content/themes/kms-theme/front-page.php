<?php
/**
 * Homepage: built to turn a first visit into a free consultation for online classes.
 *
 * The look follows the school's current site (dark, saffron, Merriweather) and the order
 * follows the questions a new student asks: what can I learn → is it any good → how does
 * it work → who teaches → what does it cost → retreats and performances → how do I start.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

get_header();
$km_core = km_has_kms();
?>

<section class="km-hero" aria-labelledby="km-hero-title">
	<div class="km-wrap km-hero__grid">
		<div class="km-hero__text">
			<p class="km-eyebrow km-eyebrow--light"><?php echo esc_html( km_text( 'km_hero_eyebrow', 'Live one-to-one classes from Pushkar, India' ) ); ?></p>
			<h1 class="km-hero__title" id="km-hero-title"><?php echo km_highlight( km_text( 'km_hero_title', 'Online Indian Music Classes, *Taught Live from Pushkar*' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in km_highlight(). ?></h1>
			<p class="km-hero__lead"><?php echo esc_html( km_text( 'km_hero_text', 'Learn harmonium, singing, bhajan and kirtan, Hindustani classical vocal or tabla one-to-one with {founder}. {years} years of teaching, complete beginners welcome, and classes at times that work in your time zone.' ) ); ?></p>
			<div class="km-actions">
				<a class="km-btn km-btn--primary km-btn--lg" href="<?php echo esc_url( km_cta_url() ); ?>"><?php esc_html_e( 'Book a free 15-minute consultation', 'kms-theme' ); ?><?php echo km_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php km_whatsapp_button( '', __( 'WhatsApp us', 'kms-theme' ), 'km-btn--ghost km-btn--lg' ); ?>
			</div>
			<?php
			if ( $km_core ) {
				echo kms_render_trust_bar( array( 'students' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in KMS Core.
				$km_sum = kms_price_summary();
				if ( $km_sum['single'] ) {
					echo '<p class="km-hero__pricing"><strong>' . esc_html__( 'Prices:', 'kms-theme' ) . '</strong> ';
					/* translators: %s: price of one class */
					echo sprintf( esc_html__( 'single class %s', 'kms-theme' ), kms_price_html( $km_sum['single'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in KMS Core.
					if ( $km_sum['per_class'] && $km_sum['per_class'] < $km_sum['single'] ) {
						/* translators: %s: lowest price per class in a package */
						echo ' · ' . sprintf( esc_html__( 'from %s per class in a package', 'kms-theme' ), kms_price_html( $km_sum['per_class'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					echo '</p>';
				}
			}
			?>
		</div>
		<figure class="km-hero__media">
			<?php km_hero_image(); ?>
			<?php km_hero_badge(); ?>
		</figure>
	</div>
</section>

<?php if ( $km_core && kms_get_courses() ) : ?>
	<section class="km-section km-light" id="classes" aria-labelledby="km-classes-title">
		<div class="km-wrap">
			<?php km_section_header( __( 'Online classes', 'kms-theme' ), __( 'Choose what you want to learn', 'kms-theme' ), __( 'Live and one-to-one, online or in Pushkar. Complete beginners, yoga teachers and advanced students are all welcome.', 'kms-theme' ), 'km-classes-title' ); ?>
			<?php echo kms_render_course_cards( array( 'heading' => 'h3' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="km-cta-box km-light">
				<h3 class="km-cta-box__title"><?php esc_html_e( 'Ready to start learning?', 'kms-theme' ); ?></h3>
				<p><?php esc_html_e( 'No experience needed: most students start as complete beginners. Tell us what you would like to learn and we will suggest the right class and times.', 'kms-theme' ); ?></p>
				<div class="km-actions">
					<a class="km-btn km-btn--primary" href="<?php echo esc_url( km_cta_url() ); ?>"><?php esc_html_e( 'Book a free consultation', 'kms-theme' ); ?><?php echo km_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<a class="km-btn km-btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'kms_course' ) ); ?>"><?php esc_html_e( 'Compare all classes', 'kms-theme' ); ?></a>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $km_core ) : ?>
	<?php $km_reviews = kms_render_reviews( array( 'limit' => 3 ) ); ?>
	<section class="km-section" aria-labelledby="km-reviews-title">
		<div class="km-wrap">
			<?php km_section_header( __( 'Reviews', 'kms-theme' ), __( 'What our students *say*', 'kms-theme' ), $km_reviews ? __( 'Written by students on TripAdvisor and Google — most of them after lessons in Pushkar.', 'kms-theme' ) : '', 'km-reviews-title' ); ?>
			<?php echo $km_reviews; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo kms_render_stats(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $km_reviews && kms_page_url( 'reviews' ) ) : ?>
				<p class="km-center"><a class="km-link-arrow" href="<?php echo esc_url( kms_page_url( 'reviews' ) ); ?>"><?php esc_html_e( 'Read all reviews', 'kms-theme' ); ?><?php echo km_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="km-section km-section--alt" aria-labelledby="km-how-title">
		<div class="km-wrap">
			<?php km_section_header( __( 'How it works', 'kms-theme' ), __( 'Start in three simple steps', 'kms-theme' ), '', 'km-how-title' ); ?>
			<?php echo kms_render_steps(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="km-split">
				<div class="km-panel">
					<h3 class="km-h3"><?php esc_html_e( 'Included with every class', 'kms-theme' ); ?></h3>
					<?php echo kms_render_includes(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div class="km-panel">
					<h3 class="km-h3"><?php esc_html_e( 'Class times in your time zone', 'kms-theme' ); ?></h3>
					<p class="km-tz-line"><?php echo kms_render_timezone(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
					<p class="km-muted"><?php esc_html_e( 'Students in Europe usually take morning or afternoon classes; in the Americas, early-morning or late-evening classes; in Australia, afternoon or evening classes.', 'kms-theme' ); ?></p>
				</div>
			</div>
		</div>
	</section>

	<section class="km-section km-section--brown" aria-labelledby="km-teacher-title">
		<div class="km-wrap">
			<?php km_section_header( __( 'Your teacher', 'kms-theme' ), sprintf( /* translators: %s: founder */ __( 'Learn with *%s*', 'kms-theme' ), km_fact( 'founder_name' ) ), '', 'km-teacher-title' ); ?>
			<?php echo kms_render_founder( 'h3' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>

	<?php $km_prices = kms_render_price_table(); ?>
	<?php if ( $km_prices ) : ?>
		<section class="km-section" id="prices" aria-labelledby="km-prices-title">
			<div class="km-wrap">
				<?php km_section_header( __( 'Prices', 'kms-theme' ), __( 'Clear prices, no surprises', 'kms-theme' ), kms_fact_text( 'guarantee' ), 'km-prices-title' ); ?>
				<?php echo $km_prices; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $km_retreats = kms_render_retreats( array( 'limit' => 4 ) ); ?>
	<?php if ( $km_retreats ) : ?>
		<section class="km-section km-section--brown" aria-labelledby="km-retreats-title">
			<div class="km-wrap">
				<?php // Length, group size and programme come from each retreat's own description in KMS Facts. ?>
				<?php km_section_header( __( 'Come to India', 'kms-theme' ), km_retreats_title( 4 ), '', 'km-retreats-title' ); ?>
				<?php echo $km_retreats; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</section>
	<?php endif; ?>

	<?php $km_shows = kms_render_performances(); ?>
	<?php if ( $km_shows ) : ?>
		<section class="km-section km-section--alt" aria-labelledby="km-shows-title">
			<div class="km-wrap">
				<?php km_section_header( __( 'Live performances', 'kms-theme' ), __( 'Book Rajasthani music for your *event*', 'kms-theme' ), __( 'Folk bands, Sufi and devotional music, fusion and Bollywood shows for weddings, hotels, festivals and corporate events.', 'kms-theme' ), 'km-shows-title' ); ?>
				<?php echo $km_shows; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="km-actions km-actions--center">
					<?php if ( kms_page_url( 'performances' ) ) : ?>
						<a class="km-btn km-btn--outline" href="<?php echo esc_url( kms_page_url( 'performances' ) ); ?>"><?php esc_html_e( 'All performance options', 'kms-theme' ); ?><?php echo km_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<?php endif; ?>
					<?php km_whatsapp_button( __( 'Hi! I would like to book a performance.', 'kms-theme' ), __( 'Ask about a performance', 'kms-theme' ), 'km-btn--outline' ); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php $km_faqs = kms_render_faqs( array( 'group' => 'general', 'limit' => 8 ) ); ?>
	<?php if ( $km_faqs ) : ?>
		<section class="km-section" aria-labelledby="km-faq-title">
			<div class="km-wrap km-faq-columns">
				<?php km_section_header( __( 'FAQ', 'kms-theme' ), __( 'Questions students ask', 'kms-theme' ), '', 'km-faq-title' ); ?>
				<?php echo $km_faqs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( kms_page_url( 'faq' ) ) : ?>
					<p class="km-center"><a class="km-link-arrow" href="<?php echo esc_url( kms_page_url( 'faq' ) ); ?>"><?php esc_html_e( 'All questions and answers', 'kms-theme' ); ?><?php echo km_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></p>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="km-section km-section--brown km-start" aria-labelledby="km-start-title">
		<div class="km-wrap km-wrap--narrow">
			<?php km_section_header( __( 'Start here', 'kms-theme' ), __( 'Start your *musical journey* today', 'kms-theme' ), kms_fact( 'response_time' ), 'km-start-title' ); ?>
			<ul class="km-inline-checks">
				<li><?php echo km_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Online worldwide', 'kms-theme' ); ?></li>
				<li><?php echo km_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Times that suit your time zone', 'kms-theme' ); ?></li>
				<li><?php echo km_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Complete beginners welcome', 'kms-theme' ); ?></li>
				<li><?php echo km_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( /* translators: %s: years */ __( '%s years of teaching', 'kms-theme' ), kms_years_teaching() . '+' ) ); ?></li>
			</ul>
			<?php echo kms_render_enquiry_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
