<?php
/**
 * Admin meta boxes for classes and reviews.
 *
 * Plain fields on purpose: they work in both the block editor and the classic editor,
 * and every value has one obvious home.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field labels and help for the class editor.
 *
 * @return array<string, array{0:string,1:string,2?:string}> key => [ label, input type, help ].
 */
function kms_course_field_ui() {
	return array(
		'kms_tagline'      => array( __( 'One-line promise (shown under the title and on cards)', 'kms-core' ), 'text' ),
		'kms_answer'       => array( __( 'Answer-first summary: 2–3 sentences saying who it is for, how it works and what it costs. AI assistants quote this.', 'kms-core' ), 'textarea' ),
		'kms_audience'     => array( __( 'Who it is for (one per line)', 'kms-core' ), 'textarea' ),
		'kms_level'        => array( __( 'Level', 'kms-core' ), 'text' ),
		'kms_outcomes'     => array( __( 'What you will be able to do (one per line)', 'kms-core' ), 'textarea' ),
		'kms_requirements' => array( __( 'What you need (one per line)', 'kms-core' ), 'textarea' ),
		'kms_modes'        => array( __( 'Formats', 'kms-core' ), 'modes' ),
		'kms_minutes'      => array( __( 'Online class length in minutes (0 = use KMS Facts)', 'kms-core' ), 'number' ),
		'kms_price_single' => array( __( 'Price: single class (₹)', 'kms-core' ), 'number' ),
		'kms_price_pack5'  => array( __( 'Price: 5 classes (₹, 0 = not offered)', 'kms-core' ), 'number' ),
		'kms_price_pack10' => array( __( 'Price: 10 classes (₹, 0 = not offered)', 'kms-core' ), 'number' ),
		'kms_usd_single'   => array( __( 'Optional exact US$ price: single class (0 = show ≈ conversion)', 'kms-core' ), 'number' ),
		'kms_usd_pack5'    => array( __( 'Optional exact US$ price: 5 classes', 'kms-core' ), 'number' ),
		'kms_usd_pack10'   => array( __( 'Optional exact US$ price: 10 classes', 'kms-core' ), 'number' ),
		'kms_price_note'   => array( __( 'Price note (e.g. "Long-term programmes priced on request")', 'kms-core' ), 'text' ),
		'kms_teaches'      => array( __( 'Skills taught, comma separated (schema "teaches")', 'kms-core' ), 'text' ),
		'kms_icon'         => array( __( 'Icon', 'kms-core' ), 'icon' ),
		'kms_badge'        => array( __( 'Card badge (optional, e.g. "Most popular")', 'kms-core' ), 'text' ),
		'kms_wa_text'      => array( __( 'WhatsApp message prefilled by the button', 'kms-core' ), 'text' ),
	);
}

/**
 * Field labels for reviews.
 *
 * @return array<string, array{0:string,1:string}>
 */
function kms_review_field_ui() {
	return array(
		'kms_verified'   => array( __( 'Verified: I have matched this review to the public listing below', 'kms-core' ), 'checkbox' ),
		'kms_source'     => array( __( 'Source', 'kms-core' ), 'source' ),
		'kms_source_url' => array( __( 'Link to the review or listing', 'kms-core' ), 'url' ),
		'kms_location'   => array( __( 'Reviewer location (e.g. "London, UK")', 'kms-core' ), 'text' ),
		'kms_date'       => array( __( 'Review month (YYYY-MM)', 'kms-core' ), 'text' ),
		'kms_subject'    => array( __( 'What they studied (e.g. "Tabla lessons, 1 week")', 'kms-core' ), 'text' ),
		'kms_mode'       => array( __( 'Format', 'kms-core' ), 'mode' ),
		'kms_rating'     => array( __( 'Star rating (1–5)', 'kms-core' ), 'number' ),
		'kms_course'     => array( __( 'Show on class page (class slug, optional)', 'kms-core' ), 'text' ),
	);
}

/**
 * Register meta boxes.
 */
function kms_add_meta_boxes() {
	// Prices are edited most often, so they sit in the editor sidebar where they are always visible.
	add_meta_box( 'kms_course_prices', __( 'Prices', 'kms-core' ), 'kms_render_course_price_box', 'kms_course', 'side', 'high' );
	add_meta_box( 'kms_course_details', __( 'Class details and answer-first summary', 'kms-core' ), 'kms_render_course_box', 'kms_course', 'normal', 'high' );
	add_meta_box( 'kms_review_details', __( 'Review details', 'kms-core' ), 'kms_render_review_box', 'kms_review', 'normal', 'high' );
	add_meta_box( 'kms_lead_details', __( 'Enquiry', 'kms-core' ), 'kms_render_lead_box', 'kms_lead', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'kms_add_meta_boxes' );

/**
 * Generic field renderer.
 *
 * @param string $key   Meta key.
 * @param array  $ui    UI definition.
 * @param mixed  $value Current value.
 */
function kms_render_meta_field( $key, $ui, $value ) {
	$id = 'kms-field-' . $key;
	echo '<p class="kms-field"><label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $ui[0] ) . '</strong></label><br>';
	switch ( $ui[1] ) {
		case 'textarea':
			echo '<textarea class="widefat" rows="4" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( (string) $value ) . '</textarea>';
			break;
		case 'number':
			echo '<input class="widefat" type="number" step="any" min="0" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $value ) . '">';
			break;
		case 'url':
			echo '<input class="widefat" type="url" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $value ) . '">';
			break;
		case 'checkbox':
			echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="1" ' . checked( (bool) $value, true, false ) . '>';
			break;
		case 'modes':
			$modes = array_map( 'trim', explode( ',', (string) $value ) );
			foreach ( array(
				'online'   => __( 'Online', 'kms-core' ),
				'inperson' => __( 'In person in Pushkar', 'kms-core' ),
			) as $mode => $label ) {
				echo '<label style="margin-right:1em"><input type="checkbox" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $mode ) . '" ' . checked( in_array( $mode, $modes, true ), true, false ) . '> ' . esc_html( $label ) . '</label>';
			}
			break;
		case 'mode':
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '">';
			foreach ( array(
				'online'   => __( 'Online class', 'kms-core' ),
				'inperson' => __( 'In person in Pushkar', 'kms-core' ),
				'retreat'  => __( 'Retreat', 'kms-core' ),
			) as $mode => $label ) {
				echo '<option value="' . esc_attr( $mode ) . '" ' . selected( $value, $mode, false ) . '>' . esc_html( $label ) . '</option>';
			}
			echo '</select>';
			break;
		case 'source':
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '">';
			foreach ( array( 'TripAdvisor', 'Google', 'Facebook', 'YouTube', 'Email (with permission)' ) as $source ) {
				echo '<option ' . selected( $value, $source, false ) . '>' . esc_html( $source ) . '</option>';
			}
			echo '</select>';
			break;
		case 'icon':
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '">';
			foreach ( array( 'piano', 'mic-vocal', 'heart-handshake', 'list-music', 'drum', 'music', 'headphones', 'sparkles' ) as $icon ) {
				echo '<option value="' . esc_attr( $icon ) . '" ' . selected( $value, $icon, false ) . '>' . esc_html( $icon ) . '</option>';
			}
			echo '</select>';
			break;
		default:
			echo '<input class="widefat" type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $value ) . '">';
	}
	echo '</p>';
}

/**
 * Whether a class field belongs in the Prices box.
 *
 * @param string $key Meta key.
 * @return bool
 */
function kms_is_price_field( $key ) {
	return 0 === strpos( $key, 'kms_price_' ) || 0 === strpos( $key, 'kms_usd_' );
}

/**
 * Prices box (sidebar).
 *
 * @param WP_Post $post Current post.
 */
function kms_render_course_price_box( $post ) {
	echo '<p class="description">' . esc_html__( 'Entered once, reused on the class page, the pricing page, Google structured data and llms.txt. Visitors abroad see an approximate conversion unless you set an exact US$ price.', 'kms-core' ) . '</p>';
	foreach ( kms_course_field_ui() as $key => $ui ) {
		if ( kms_is_price_field( $key ) ) {
			kms_render_meta_field( $key, $ui, get_post_meta( $post->ID, $key, true ) );
		}
	}
}

/**
 * Class details box (below the content).
 *
 * @param WP_Post $post Current post.
 */
function kms_render_course_box( $post ) {
	wp_nonce_field( 'kms_save_meta', 'kms_meta_nonce' );
	foreach ( kms_course_field_ui() as $key => $ui ) {
		if ( ! kms_is_price_field( $key ) ) {
			kms_render_meta_field( $key, $ui, get_post_meta( $post->ID, $key, true ) );
		}
	}
	echo '<p class="description">' . esc_html__( 'Class FAQs: create FAQs under FAQs and put them in the FAQ group whose slug matches this class slug.', 'kms-core' ) . '</p>';
}

/**
 * Review details box.
 *
 * @param WP_Post $post Current post.
 */
function kms_render_review_box( $post ) {
	wp_nonce_field( 'kms_save_meta', 'kms_meta_nonce' );
	echo '<p class="description">' . esc_html__( 'Use the reviewer\'s name as the title and paste their words, unedited, into the main text box. Only publish reviews that exist on a public platform or that you have written permission to use.', 'kms-core' ) . '</p>';
	foreach ( kms_review_field_ui() as $key => $ui ) {
		kms_render_meta_field( $key, $ui, get_post_meta( $post->ID, $key, true ) );
	}
}

/**
 * Save class and review meta.
 *
 * @param int $post_id Post ID.
 */
function kms_save_meta_boxes( $post_id ) {
	if ( ! isset( $_POST['kms_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['kms_meta_nonce'] ) ), 'kms_save_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$type = get_post_type( $post_id );
	if ( 'kms_course' === $type ) {
		$fields = kms_course_field_ui();
	} elseif ( 'kms_review' === $type ) {
		$fields = kms_review_field_ui();
	} else {
		return;
	}

	foreach ( $fields as $key => $ui ) {
		if ( 'checkbox' === $ui[1] ) {
			update_post_meta( $post_id, $key, ! empty( $_POST[ $key ] ) );
			continue;
		}
		if ( 'modes' === $ui[1] ) {
			$modes = isset( $_POST[ $key ] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST[ $key ] ) ) : array();
			update_post_meta( $post_id, $key, implode( ',', array_intersect( $modes, array( 'online', 'inperson' ) ) ) );
			continue;
		}
		if ( isset( $_POST[ $key ] ) ) {
			// Sanitised by the registered meta sanitize callback.
			update_post_meta( $post_id, $key, wp_unslash( $_POST[ $key ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}
	}
}
add_action( 'save_post', 'kms_save_meta_boxes' );
