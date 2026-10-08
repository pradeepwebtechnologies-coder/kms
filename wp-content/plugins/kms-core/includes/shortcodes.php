<?php
/**
 * Shortcodes so editors can place any building block on any page.
 *
 * [kms_courses mode="online|inperson|all" heading="h3"]
 * [kms_pricing course="harmonium"]      (omit course for the comparison table)
 * [kms_reviews limit="6" course=""]
 * [kms_faqs group="general" limit="0" heading="h3"]
 * [kms_facts]                           "At a glance" table
 * [kms_steps]  [kms_includes]  [kms_founder]  [kms_trust]
 * [kms_retreats limit="4"]
 * [kms_timezone]
 * [kms_contact]  [kms_review_links]
 * [kms_whatsapp text="Hi!" label="WhatsApp us"]
 * [kms_button label="Book a free consultation" course=""]
 * [kms_enquiry_form course="" title=""] (see leads.php)
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register shortcodes.
 */
function kms_register_shortcodes() {
	add_shortcode(
		'kms_courses',
		static function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'mode'    => 'all',
					'heading' => 'h3',
				),
				$atts,
				'kms_courses'
			);
			return kms_render_course_cards( $atts );
		}
	);

	add_shortcode(
		'kms_pricing',
		static function ( $atts ) {
			$atts = shortcode_atts( array( 'course' => '' ), $atts, 'kms_pricing' );
			if ( $atts['course'] ) {
				$course = get_page_by_path( sanitize_title( $atts['course'] ), OBJECT, 'kms_course' );
				return $course ? kms_render_course_pricing( $course->ID ) : '';
			}
			return kms_render_price_table();
		}
	);

	add_shortcode(
		'kms_reviews',
		static function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'limit'  => 6,
					'course' => '',
				),
				$atts,
				'kms_reviews'
			);
			return kms_render_reviews( $atts );
		}
	);

	add_shortcode(
		'kms_faqs',
		static function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'group'   => 'general',
					'limit'   => 0,
					'heading' => 'h3',
				),
				$atts,
				'kms_faqs'
			);
			return kms_render_faqs( $atts );
		}
	);

	add_shortcode( 'kms_facts', 'kms_render_facts' );
	add_shortcode( 'kms_steps', 'kms_render_steps' );
	add_shortcode( 'kms_includes', 'kms_render_includes' );
	add_shortcode( 'kms_trust', 'kms_render_trust_bar' );
	add_shortcode( 'kms_contact', 'kms_render_contact_list' );
	add_shortcode( 'kms_review_links', 'kms_render_review_links' );
	add_shortcode( 'kms_stats', 'kms_render_stats' );
	add_shortcode(
		'kms_performances',
		static function ( $atts ) {
			return kms_render_performances( shortcode_atts( array( 'limit' => 0, 'heading' => 'h3' ), $atts, 'kms_performances' ) );
		}
	);

	add_shortcode(
		'kms_founder',
		static function ( $atts ) {
			$atts = shortcode_atts( array( 'heading' => 'h2' ), $atts, 'kms_founder' );
			return kms_render_founder( $atts['heading'] );
		}
	);

	add_shortcode(
		'kms_retreats',
		static function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'limit'   => 4,
					'heading' => 'h3',
				),
				$atts,
				'kms_retreats'
			);
			$html = kms_render_retreats( $atts );
			return $html ? $html : '<p>' . esc_html__( 'New retreat dates will be announced soon. Message us to join the waiting list.', 'kms-core' ) . '</p>';
		}
	);

	add_shortcode(
		'kms_timezone',
		static function () {
			return '<p class="km-tz-line">' . kms_render_timezone() . '</p>';
		}
	);

	add_shortcode(
		'kms_whatsapp',
		static function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'text'  => '',
					'label' => '',
				),
				$atts,
				'kms_whatsapp'
			);
			return kms_whatsapp_button( $atts['text'], $atts['label'] );
		}
	);

	add_shortcode(
		'kms_button',
		static function ( $atts ) {
			$atts = shortcode_atts(
				array(
					'label'  => '',
					'course' => '',
				),
				$atts,
				'kms_button'
			);
			return kms_enquiry_button( $atts['label'], $atts['course'] );
		}
	);
}
add_action( 'init', 'kms_register_shortcodes' );
