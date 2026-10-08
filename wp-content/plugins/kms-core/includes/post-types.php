<?php
/**
 * Content types: classes, reviews, FAQs and enquiries.
 *
 * Classes live at /online-classes/{slug}/ with the hub at /online-classes/.
 * Reviews and FAQs have no public URLs; they are displayed by shortcodes and templates.
 * Enquiries are private and only visible to editors and administrators.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Meta fields per content type: key => [ type, default ].
 *
 * @return array<string, array<string, array{0:string,1:mixed}>>
 */
function kms_meta_fields() {
	return array(
		'kms_course' => array(
			'kms_tagline'      => array( 'string', '' ),
			'kms_answer'       => array( 'string', '' ),
			'kms_audience'     => array( 'string', '' ),
			'kms_level'        => array( 'string', 'Complete beginner to intermediate' ),
			'kms_outcomes'     => array( 'string', '' ),
			'kms_requirements' => array( 'string', '' ),
			'kms_modes'        => array( 'string', 'online,inperson' ),
			'kms_minutes'      => array( 'integer', 0 ),
			'kms_price_single' => array( 'integer', 0 ),
			'kms_price_pack5'  => array( 'integer', 0 ),
			'kms_price_pack10' => array( 'integer', 0 ),
			'kms_usd_single'   => array( 'number', 0 ),
			'kms_usd_pack5'    => array( 'number', 0 ),
			'kms_usd_pack10'   => array( 'number', 0 ),
			'kms_price_note'   => array( 'string', '' ),
			'kms_teaches'      => array( 'string', '' ),
			'kms_icon'         => array( 'string', 'music' ),
			'kms_badge'        => array( 'string', '' ),
			'kms_wa_text'      => array( 'string', '' ),
		),
		'kms_review' => array(
			'kms_location'   => array( 'string', '' ),
			'kms_source'     => array( 'string', 'TripAdvisor' ),
			'kms_source_url' => array( 'string', '' ),
			'kms_date'       => array( 'string', '' ),
			'kms_subject'    => array( 'string', '' ),
			'kms_mode'       => array( 'string', 'inperson' ),
			'kms_rating'     => array( 'integer', 5 ),
			'kms_verified'   => array( 'boolean', false ),
			'kms_course'     => array( 'string', '' ),
		),
	);
}

/**
 * Register post types, taxonomy and meta.
 */
function kms_register_post_types() {
	register_post_type(
		'kms_course',
		array(
			'labels'        => array(
				'name'               => __( 'Classes', 'kms-core' ),
				'singular_name'      => __( 'Class', 'kms-core' ),
				'add_new_item'       => __( 'Add new class', 'kms-core' ),
				'edit_item'          => __( 'Edit class', 'kms-core' ),
				'view_item'          => __( 'View class', 'kms-core' ),
				'all_items'          => __( 'All classes', 'kms-core' ),
				'search_items'       => __( 'Search classes', 'kms-core' ),
				'not_found'          => __( 'No classes found.', 'kms-core' ),
				'archives'           => __( 'Online classes', 'kms-core' ),
				'menu_name'          => __( 'Classes', 'kms-core' ),
				'featured_image'     => __( 'Class image (1200×800 or larger)', 'kms-core' ),
				'set_featured_image' => __( 'Set class image', 'kms-core' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'has_archive'   => 'online-classes',
			'rewrite'       => array(
				'slug'       => 'online-classes',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-format-audio',
			'menu_position' => 21,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
		)
	);

	register_post_type(
		'kms_review',
		array(
			'labels'              => array(
				'name'          => __( 'Reviews', 'kms-core' ),
				'singular_name' => __( 'Review', 'kms-core' ),
				'add_new_item'  => __( 'Add review (reviewer name as title)', 'kms-core' ),
				'edit_item'     => __( 'Edit review', 'kms-core' ),
				'all_items'     => __( 'All reviews', 'kms-core' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-star-filled',
			'menu_position'       => 22,
			'supports'            => array( 'title', 'editor', 'page-attributes' ),
		)
	);

	register_post_type(
		'kms_faq',
		array(
			'labels'              => array(
				'name'          => __( 'FAQs', 'kms-core' ),
				'singular_name' => __( 'FAQ', 'kms-core' ),
				'add_new_item'  => __( 'Add question (question as title, answer below)', 'kms-core' ),
				'edit_item'     => __( 'Edit question', 'kms-core' ),
				'all_items'     => __( 'All FAQs', 'kms-core' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-editor-help',
			'menu_position'       => 23,
			'supports'            => array( 'title', 'editor', 'page-attributes' ),
		)
	);

	register_taxonomy(
		'kms_faq_group',
		'kms_faq',
		array(
			'labels'            => array(
				'name'          => __( 'FAQ groups', 'kms-core' ),
				'singular_name' => __( 'FAQ group', 'kms-core' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
		)
	);

	register_post_type(
		'kms_lead',
		array(
			'labels'          => array(
				'name'          => __( 'Enquiries', 'kms-core' ),
				'singular_name' => __( 'Enquiry', 'kms-core' ),
				'edit_item'     => __( 'Enquiry', 'kms-core' ),
				'all_items'     => __( 'All enquiries', 'kms-core' ),
				'not_found'     => __( 'No enquiries yet.', 'kms-core' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 20,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'capabilities'    => array(
				'create_posts' => 'do_not_allow',
				'edit_posts'   => 'edit_others_posts',
			),
			'map_meta_cap'    => true,
		)
	);

	foreach ( kms_meta_fields() as $post_type => $fields ) {
		foreach ( $fields as $key => $def ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => $def[0],
					'single'            => true,
					'default'           => $def[1],
					'show_in_rest'      => 'kms_course' === $post_type,
					'sanitize_callback' => 'kms_sanitize_meta_value',
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'kms_register_post_types' );

/**
 * Sanitise a meta value according to its registered type.
 *
 * @param mixed  $value    Raw value.
 * @param string $meta_key Meta key.
 * @return mixed
 */
function kms_sanitize_meta_value( $value, $meta_key = '' ) {
	foreach ( kms_meta_fields() as $fields ) {
		if ( ! isset( $fields[ $meta_key ] ) ) {
			continue;
		}
		switch ( $fields[ $meta_key ][0] ) {
			case 'integer':
				return absint( $value );
			case 'number':
				return is_numeric( $value ) ? (float) $value : 0;
			case 'boolean':
				return (bool) $value;
		}
		if ( 'kms_source_url' === $meta_key ) {
			return esc_url_raw( $value );
		}
		return sanitize_textarea_field( $value );
	}
	return sanitize_text_field( $value );
}

/**
 * Course meta with default fallback.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @return mixed
 */
function kms_meta( $post_id, $key ) {
	$value = get_post_meta( $post_id, $key, true );
	if ( '' === $value || null === $value ) {
		$type = get_post_type( $post_id );
		$defs = kms_meta_fields();
		return isset( $defs[ $type ][ $key ] ) ? $defs[ $type ][ $key ][1] : '';
	}
	return $value;
}

/**
 * Multi-line meta as an array of non-empty lines.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @return string[]
 */
function kms_meta_lines( $post_id, $key ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) kms_meta( $post_id, $key ) ) ) ) );
}

/**
 * Published classes in menu order.
 *
 * @param array $args Extra WP_Query args.
 * @return WP_Post[]
 */
function kms_get_courses( $args = array() ) {
	return get_posts(
		wp_parse_args(
			$args,
			array(
				'post_type'        => 'kms_course',
				'post_status'      => 'publish',
				'posts_per_page'   => 50,
				'orderby'          => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'suppress_filters' => false,
			)
		)
	);
}

/**
 * Whether a class is offered in a mode.
 *
 * @param int    $post_id Course ID.
 * @param string $mode    online|inperson.
 * @return bool
 */
function kms_course_has_mode( $post_id, $mode ) {
	$modes = array_map( 'trim', explode( ',', (string) kms_meta( $post_id, 'kms_modes' ) ) );
	return in_array( $mode, $modes, true );
}

/**
 * Class length in minutes (per-class override, else the global fact).
 *
 * @param int $post_id Course ID.
 * @return int
 */
function kms_course_minutes( $post_id ) {
	$minutes = (int) kms_meta( $post_id, 'kms_minutes' );
	return $minutes > 0 ? $minutes : (int) kms_fact( 'online_minutes', '40' );
}

/**
 * Answer-first summary of a class (meta, then excerpt).
 *
 * @param int $post_id Course ID.
 * @return string
 */
function kms_course_answer( $post_id ) {
	$answer = trim( (string) kms_meta( $post_id, 'kms_answer' ) );
	if ( '' === $answer ) {
		$answer = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : '';
	}
	return kms_course_fill( $post_id, $answer );
}

/**
 * Publish only reviews that are verified when the school chooses to (filterable).
 *
 * @param array $args Extra query args.
 * @return WP_Post[]
 */
function kms_get_reviews( $args = array() ) {
	return get_posts(
		wp_parse_args(
			$args,
			array(
				'post_type'      => 'kms_review',
				'post_status'    => 'publish',
				'posts_per_page' => 6,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
			)
		)
	);
}

/**
 * FAQs in a group (or all).
 *
 * @param string $group Term slug, comma separated slugs, or "all".
 * @param int    $limit Maximum number (0 = no limit).
 * @return WP_Post[]
 */
function kms_get_faqs( $group = 'general', $limit = 0 ) {
	$args = array(
		'post_type'      => 'kms_faq',
		'post_status'    => 'publish',
		'posts_per_page' => $limit > 0 ? $limit : 100,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'ASC',
		),
	);
	if ( 'all' !== $group && '' !== $group ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'kms_faq_group',
				'field'    => 'slug',
				'terms'    => array_map( 'trim', explode( ',', $group ) ),
			),
		);
	}
	return get_posts( $args );
}

/**
 * Short class name for menus and footers: "Online Tabla Classes" → "Tabla".
 *
 * @param WP_Post|int $course Course.
 * @return string
 */
function kms_course_short_title( $course ) {
	$title = get_the_title( $course );
	$short = preg_replace( array( '/^Online\s+/i', '/\s+Classes(?=\s*\(|$)/i' ), '', $title );
	return '' !== trim( (string) $short ) ? trim( (string) $short ) : $title;
}
