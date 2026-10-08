<?php
/**
 * Markup builders shared by shortcodes and theme templates.
 *
 * All output uses the km- class prefix (the companion theme styles it) and is
 * escaped at the point of output.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Collected FAQs and reviews actually rendered on this request (for schema).
 *
 * @param string     $type faq|course.
 * @param array|null $item Item to add; null to read.
 * @return array
 */
function kms_rendered( $type, $item = null ) {
	static $store = array(
		'faq' => array(),
	);
	if ( null === $item ) {
		return isset( $store[ $type ] ) ? array_values( $store[ $type ] ) : array();
	}
	$key                    = md5( wp_json_encode( $item ) );
	$store[ $type ][ $key ] = $item;
	return $store[ $type ];
}

/**
 * Enqueue the widget script once a widget is printed.
 */
function kms_need_script() {
	wp_enqueue_script( 'kms-core' );
}

/**
 * Button to the enquiry form.
 *
 * @param string $label  Button text.
 * @param string $course Optional class slug to preselect.
 * @param string $class  Extra classes.
 * @return string
 */
function kms_enquiry_button( $label = '', $course = '', $class = '' ) {
	$label = $label ? $label : __( 'Book a free 15-minute consultation', 'kms-core' );
	$url   = kms_enquiry_url();
	if ( $course ) {
		$url = add_query_arg( 'course', rawurlencode( $course ), strtok( $url, '#' ) ) . '#enquire';
	}
	return '<a class="' . esc_attr( trim( 'km-btn km-btn--primary ' . $class ) ) . '" href="' . esc_url( $url ) . '">' . esc_html( $label ) . kms_icon( 'arrow-right' ) . '</a>';
}

/**
 * WhatsApp button.
 *
 * @param string $message Prefilled message.
 * @param string $label   Button text.
 * @param string $class   Extra classes.
 * @return string
 */
function kms_whatsapp_button( $message = '', $label = '', $class = '' ) {
	$url = kms_whatsapp_url( $message ? $message : __( 'Hi! I would like to know more about your online music classes.', 'kms-core' ) );
	if ( ! $url ) {
		return '';
	}
	$label = $label ? $label : __( 'WhatsApp us', 'kms-core' );
	return '<a class="' . esc_attr( trim( 'km-btn km-btn--whatsapp ' . $class ) ) . '" href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . kms_icon( 'whatsapp' ) . esc_html( $label ) . '</a>';
}

/**
 * Star rating (visual + accessible text).
 *
 * @param float $rating Rating out of 5.
 * @return string
 */
function kms_stars( $rating ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	$full   = (int) round( $rating );
	$html   = '<span class="km-stars" role="img" aria-label="' . esc_attr( sprintf( /* translators: %s: rating */ __( '%s out of 5 stars', 'kms-core' ), $rating ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		$html .= kms_icon( 'star', '', $i <= $full ? 'is-on' : 'is-off' );
	}
	return $html . '</span>';
}

/**
 * Trust bar: ratings, years, students. Only facts that are filled in are shown.
 *
 * @return string
 */
function kms_render_trust_bar( $args = array() ) {
	$args  = wp_parse_args( is_array( $args ) ? $args : array(), array( 'students' => true ) );
	$items = array();
	if ( kms_fact( 'tripadvisor_rating' ) ) {
		$text    = sprintf( /* translators: 1: rating, 2: count */ __( '%1$s on TripAdvisor (%2$s reviews)', 'kms-core' ), kms_fact( 'tripadvisor_rating' ), kms_fact( 'tripadvisor_count' ) );
		$url     = kms_fact( 'tripadvisor_url' );
		$items[] = kms_icon( 'tripadvisor' ) . ( $url ? '<a href="' . esc_url( $url ) . '" rel="noopener" target="_blank">' . esc_html( $text ) . '</a>' : esc_html( $text ) );
	}
	if ( kms_fact( 'google_rating' ) ) {
		$text    = sprintf( /* translators: 1: rating, 2: count */ __( '%1$s on Google (%2$s reviews)', 'kms-core' ), kms_fact( 'google_rating' ), kms_fact( 'google_count' ) );
		$url     = kms_fact( 'google_url' );
		$items[] = kms_icon( 'google' ) . ( $url ? '<a href="' . esc_url( $url ) . '" rel="noopener" target="_blank">' . esc_html( $text ) . '</a>' : esc_html( $text ) );
	}
	$items[] = kms_icon( 'award' ) . esc_html( sprintf( /* translators: %s: years */ __( '%s years of teaching', 'kms-core' ), kms_years_teaching() . '+' ) );
	if ( $args['students'] && kms_fact( 'students' ) ) {
		$items[] = kms_icon( 'globe' ) . esc_html( sprintf( /* translators: 1: students, 2: countries */ __( '%1$s students from %2$s countries', 'kms-core' ), kms_fact( 'students' ), kms_fact( 'countries' ) ) );
	}
	return '<ul class="km-trust">' . implode( '', array_map( static function ( $i ) {
		return '<li>' . $i . '</li>';
	}, $items ) ) . '</ul>';
}

/**
 * Class cards.
 *
 * @param array $args { heading: h2|h3, mode: online|inperson|all, exclude: int }.
 * @return string
 */
function kms_render_course_cards( $args = array() ) {
	$args    = wp_parse_args(
		$args,
		array(
			'heading' => 'h3',
			'mode'    => 'all',
			'exclude' => 0,
		)
	);
	$heading = in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading'] : 'h3';
	$courses = kms_get_courses( $args['exclude'] ? array( 'post__not_in' => array( (int) $args['exclude'] ) ) : array() );
	if ( ! $courses ) {
		return '';
	}
	kms_need_script();
	$html = '<div class="km-cards km-cards--courses">';
	foreach ( $courses as $course ) {
		if ( 'all' !== $args['mode'] && ! kms_course_has_mode( $course->ID, $args['mode'] ) ) {
			continue;
		}
		$url     = get_permalink( $course );
		$tagline = kms_fill( (string) kms_meta( $course->ID, 'kms_tagline' ) );
		$from    = kms_course_from_price( $course->ID );
		$modes   = array();
		if ( kms_course_has_mode( $course->ID, 'online' ) ) {
			/* translators: %d: minutes */
			$modes[] = sprintf( __( 'Online · %d min', 'kms-core' ), kms_course_minutes( $course->ID ) );
		}
		if ( kms_course_has_mode( $course->ID, 'inperson' ) ) {
			$modes[] = __( 'In Pushkar', 'kms-core' );
		}

		$badge  = (string) kms_meta( $course->ID, 'kms_badge' );
		$level  = (string) kms_meta( $course->ID, 'kms_level' );
		$html  .= '<article class="km-card km-course-card' . ( $badge ? ' has-badge' : '' ) . '">';
		if ( $badge ) {
			$html .= '<p class="km-course-card__badge">' . kms_icon( 'star' ) . esc_html( $badge ) . '</p>';
		}
		$html .= '<div class="km-course-card__head"><span class="km-course-card__icon">' . kms_icon( (string) kms_meta( $course->ID, 'kms_icon' ) ) . '</span>';
		$html .= '<' . $heading . ' class="km-course-card__title"><a class="km-stretched" href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $course ) ) . '</a></' . $heading . '></div>';
		$html .= '<ul class="km-pills">';
		foreach ( $modes as $mode ) {
			$html .= '<li class="km-pill">' . esc_html( $mode ) . '</li>';
		}
		if ( $level ) {
			$html .= '<li class="km-pill km-pill--soft">' . esc_html( $level ) . '</li>';
		}
		$html .= '</ul>';
		if ( $tagline ) {
			$html .= '<p class="km-course-card__tagline">' . esc_html( $tagline ) . '</p>';
		}
		if ( $from ) {
			$html .= '<p class="km-course-card__price"><span class="km-course-card__from">' . esc_html__( 'From', 'kms-core' ) . '</span> ' . kms_price_html( $from ) . ' <span class="km-course-card__per">' . esc_html__( 'per class', 'kms-core' ) . '</span></p>';
		}
		// The title link is stretched over the whole card; this is its visual cue.
		$html .= '<span class="km-course-card__more" aria-hidden="true">' . esc_html__( 'Class details and prices', 'kms-core' ) . kms_icon( 'arrow-right' ) . '</span>';
		$html .= '</article>';
	}
	return $html . '</div>';
}

/**
 * Currency switcher buttons.
 *
 * @return string
 */
function kms_render_currency_switcher() {
	kms_need_script();
	$html = '<div class="km-currency" role="group" aria-label="' . esc_attr__( 'Show approximate prices in', 'kms-core' ) . '"><span class="km-currency__label">' . esc_html__( 'Show prices in', 'kms-core' ) . '</span>';
	foreach ( array(
		'USD' => 'US$',
		'EUR' => '€',
		'GBP' => '£',
		'INR' => '₹',
	) as $code => $symbol ) {
		$html .= '<button type="button" class="km-currency__btn" data-cur="' . esc_attr( $code ) . '" aria-pressed="' . ( 'USD' === $code ? 'true' : 'false' ) . '">' . esc_html( $symbol . ' ' . $code ) . '</button>';
	}
	return $html . '</div>';
}

/**
 * Price plans for one class.
 *
 * @param int    $post_id Course ID.
 * @param string $heading Heading tag for plan names.
 * @return string
 */
function kms_render_course_pricing( $post_id, $heading = 'h3' ) {
	$tiers = kms_course_tiers( $post_id );
	if ( ! $tiers ) {
		return '';
	}
	$slug    = get_post_field( 'post_name', $post_id );
	$heading = in_array( $heading, array( 'h2', 'h3', 'h4' ), true ) ? $heading : 'h3';

	// "Best value" only where it is true: the package with the lowest price per class,
	// and only if that is cheaper than a single class.
	$best_key = '';
	$lowest   = PHP_INT_MAX;
	foreach ( $tiers as $tier ) {
		if ( $tier['classes'] > 1 && $tier['per_class'] < $lowest ) {
			$lowest   = $tier['per_class'];
			$best_key = $tier['key'];
		}
	}
	if ( 'single' === $tiers[0]['key'] && $lowest >= $tiers[0]['per_class'] ) {
		$best_key = '';
	}

	$html = '<div class="km-pricing">' . kms_render_currency_switcher() . '<div class="km-plans">';
	foreach ( $tiers as $tier ) {
		$featured = ( $tier['key'] === $best_key );
		$html    .= '<div class="km-plan' . ( $featured ? ' is-featured' : '' ) . '">';
		if ( $featured ) {
			$html .= '<p class="km-plan__badge">' . esc_html__( 'Best value', 'kms-core' ) . '</p>';
		}
		$html .= '<' . $heading . ' class="km-plan__name">' . esc_html( $tier['label'] ) . '</' . $heading . '>';
		$html .= '<p class="km-plan__price">' . kms_price_html( $tier['inr'], $tier['usd'] ) . '</p>';
		if ( $tier['classes'] > 1 ) {
			/* translators: %s: price per class */
			$html .= '<p class="km-plan__per">' . sprintf( esc_html__( '%s per class', 'kms-core' ), kms_price_html( $tier['per_class'], $tier['usd'] > 0 ? round( $tier['usd'] / $tier['classes'], 2 ) : 0 ) ) . '</p>';
		} else {
			$html .= '<p class="km-plan__per">' . esc_html__( 'Try one class first', 'kms-core' ) . '</p>';
		}
		$html .= kms_enquiry_button( __( 'Free consultation', 'kms-core' ), $slug, $featured ? '' : 'km-btn--outline' );
		$html .= '</div>';
	}
	$html .= '</div>';
	$notes = array_filter(
		array(
			kms_fill( (string) kms_meta( $post_id, 'kms_price_note' ) ),
			kms_fact_text( 'guarantee' ),
			kms_fact_text( 'validity' ),
			__( 'Prices are in Indian rupees. Other currencies are approximate.', 'kms-core' ) . ' ' . sprintf( /* translators: %s: payment methods */ __( 'Pay by %s.', 'kms-core' ), kms_fact( 'payment_methods' ) ),
		)
	);
	$html .= '<ul class="km-pricing__notes">';
	foreach ( $notes as $note ) {
		$html .= '<li>' . kms_icon( 'check' ) . esc_html( $note ) . '</li>';
	}
	return $html . '</ul></div>';
}

/**
 * Price comparison table across all classes.
 *
 * @return string
 */
function kms_render_price_table() {
	$courses = kms_get_courses();
	if ( ! $courses ) {
		return '';
	}
	$html  = '<div class="km-pricing km-pricing--table">' . kms_render_currency_switcher();
	$html .= '<div class="km-table-wrap"><table class="km-table"><caption class="screen-reader-text">' . esc_html__( 'Prices for online classes', 'kms-core' ) . '</caption><thead><tr>';
	$html .= '<th scope="col">' . esc_html__( 'Class', 'kms-core' ) . '</th><th scope="col">' . esc_html__( 'Single class', 'kms-core' ) . '</th><th scope="col">' . esc_html__( '5 classes', 'kms-core' ) . '</th><th scope="col">' . esc_html__( '10 classes', 'kms-core' ) . '</th></tr></thead><tbody>';
	foreach ( $courses as $course ) {
		$by_key = array();
		foreach ( kms_course_tiers( $course->ID ) as $tier ) {
			$by_key[ $tier['key'] ] = $tier;
		}
		$html .= '<tr><th scope="row"><a href="' . esc_url( get_permalink( $course ) ) . '">' . esc_html( get_the_title( $course ) ) . '</a>';
		$note  = kms_fill( (string) kms_meta( $course->ID, 'kms_price_note' ) );
		if ( $note ) {
			$html .= '<span class="km-table__note">' . esc_html( $note ) . '</span>';
		}
		$html .= '</th>';
		foreach ( array(
			'single' => __( 'Single class', 'kms-core' ),
			'pack5'  => __( '5 classes', 'kms-core' ),
			'pack10' => __( '10 classes', 'kms-core' ),
		) as $key => $label ) {
			// data-label names the column when the table becomes a stack of cards on phones.
			$html .= '<td data-label="' . esc_attr( $label ) . '"' . ( isset( $by_key[ $key ] ) ? '>' . kms_price_html( $by_key[ $key ]['inr'], $by_key[ $key ]['usd'] ) : ' class="is-empty"><span class="km-muted">—</span>' ) . '</td>';
		}
		$html .= '</tr>';
	}
	$html .= '</tbody></table></div>';
	$html .= '<p class="km-pricing__fine">' . esc_html( __( 'Prices are in Indian rupees; other currencies are approximate and depend on the exchange rate on the day you pay.', 'kms-core' ) . ' ' . sprintf( /* translators: %s: payment methods */ __( 'Pay by %s.', 'kms-core' ), kms_fact( 'payment_methods' ) ) ) . '</p>';
	return $html . '</div>';
}

/**
 * Reviews.
 *
 * @param array $args { limit:int, course:string, heading:string }.
 * @return string
 */
function kms_render_reviews( $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'limit'  => 6,
			'course' => '',
		)
	);
	$query = array( 'posts_per_page' => (int) $args['limit'] > 0 ? (int) $args['limit'] : 100 );
	if ( $args['course'] ) {
		$query['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => 'kms_course',
				'value' => sanitize_title( $args['course'] ),
			),
		);
	}
	$reviews = kms_get_reviews( $query );
	if ( ! $reviews && $args['course'] ) {
		$reviews = kms_get_reviews( array( 'posts_per_page' => $query['posts_per_page'] ) );
	}
	if ( ! $reviews ) {
		return '';
	}

	$html = '<div class="km-reviews">';
	foreach ( $reviews as $review ) {
		$source     = (string) kms_meta( $review->ID, 'kms_source' );
		$source_url = (string) kms_meta( $review->ID, 'kms_source_url' );
		if ( '' === $source_url ) {
			// Fall back to the listing so readers can still check the original.
			$source_url = 'TripAdvisor' === $source ? kms_fact( 'tripadvisor_url' ) : ( 'Google' === $source ? kms_fact( 'google_url' ) : '' );
		}
		$date       = (string) kms_meta( $review->ID, 'kms_date' );
		$verified   = (bool) kms_meta( $review->ID, 'kms_verified' );
		$details    = array_filter(
			array(
				(string) kms_meta( $review->ID, 'kms_subject' ),
				preg_match( '/^\d{4}-\d{2}$/', $date ) ? date_i18n( 'M Y', strtotime( $date . '-01' ) ) : $date,
			)
		);

		$source_html = '';
		if ( $source ) {
			/* translators: %s: platform name */
			$label       = sprintf( __( 'Review on %s', 'kms-core' ), $source );
			$badge       = $verified ? kms_icon( 'shield-check' ) : '';
			$source_html = '<span class="km-review__source">' . $badge . ( $source_url ? '<a href="' . esc_url( $source_url ) . '" target="_blank" rel="noopener nofollow">' . esc_html( $label ) . '</a>' : esc_html( $label ) ) . '</span>';
		}

		$html .= '<figure class="km-review">';
		$html .= '<div class="km-review__top">' . kms_stars( (int) kms_meta( $review->ID, 'kms_rating' ) ) . $source_html . '</div>';
		$html .= '<blockquote class="km-review__quote">' . wpautop( esc_html( wp_strip_all_tags( $review->post_content ) ) ) . '</blockquote>';
		$html .= '<figcaption class="km-review__by"><strong>' . esc_html( get_the_title( $review ) ) . '</strong>';
		$loc   = (string) kms_meta( $review->ID, 'kms_location' );
		if ( $loc ) {
			$html .= '<span class="km-review__loc">' . esc_html( $loc ) . '</span>';
		}
		if ( $details ) {
			$html .= '<span class="km-review__detail">' . esc_html( implode( ' · ', $details ) ) . '</span>';
		}
		$html .= '</figcaption></figure>';
	}
	return $html . '</div>';
}

/**
 * FAQ list (native details/summary; no JavaScript) and registration for FAQPage schema.
 *
 * @param array $args { group:string, limit:int, heading:string, open_first:bool }.
 * @return string
 */
function kms_render_faqs( $args = array() ) {
	$args    = wp_parse_args(
		$args,
		array(
			'group'      => 'general',
			'limit'      => 0,
			'heading'    => 'h3',
			'open_first' => false,
		)
	);
	$heading = in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading'] : 'h3';
	$faqs    = kms_get_faqs( $args['group'], (int) $args['limit'] );
	if ( ! $faqs ) {
		return '';
	}
	$html = '<div class="km-faqs">';
	foreach ( $faqs as $i => $faq ) {
		$question = kms_fill( get_the_title( $faq ) );
		$answer   = kms_fill( wpautop( wptexturize( do_shortcode( $faq->post_content ) ) ) );
		kms_rendered(
			'faq',
			array(
				'q' => $question,
				'a' => trim( wp_strip_all_tags( $answer ) ),
			)
		);
		$open  = ( $args['open_first'] && 0 === $i ) ? ' open' : '';
		$html .= '<details class="km-faq"' . $open . '><summary><' . $heading . ' class="km-faq__q">' . esc_html( $question ) . '</' . $heading . '>' . kms_icon( 'chevron-down', '', 'km-faq__icon' ) . '</summary>';
		$html .= '<div class="km-faq__a">' . wp_kses_post( $answer ) . '</div></details>';
	}
	return $html . '</div>';
}

/**
 * "At a glance" facts about the school (quotable by AI assistants).
 *
 * @return string
 */
function kms_render_facts() {
	$subjects = wp_list_pluck( kms_get_courses(), 'post_title' );
	$from     = 0;
	foreach ( kms_get_courses() as $course ) {
		$price = kms_course_from_price( $course->ID );
		if ( $price && ( ! $from || $price < $from ) ) {
			$from = $price;
		}
	}
	$founder_role = kms_fact( 'founder_title' ) ? kms_fact( 'founder_title' ) : __( 'Founder', 'kms-core' );
	$rows         = array(
		__( 'Founded', 'kms-core' )                    => sprintf( /* translators: 1: year, 2: town */ __( '%1$s in %2$s, Rajasthan, India', 'kms-core' ), kms_fact( 'founding_year' ), kms_fact( 'locality' ) ),
		$founder_role                                  => kms_fact( 'founder_name' ),
		__( 'Teaching experience', 'kms-core' )        => sprintf( /* translators: %s: years */ __( '%s years', 'kms-core' ), kms_years_teaching() . '+' ),
		__( 'Subjects', 'kms-core' )                   => implode( ', ', $subjects ),
		__( 'Online classes', 'kms-core' )             => sprintf( /* translators: 1: platforms, 2: minutes */ __( 'Live and one-to-one on %1$s, %2$s minutes each', 'kms-core' ), kms_fact( 'platforms' ), kms_fact( 'online_minutes' ) ),
		__( 'In person', 'kms-core' )                  => sprintf( /* translators: 1: address, 2: minutes */ __( '%1$s (%2$s-minute classes and retreats)', 'kms-core' ), kms_address_line(), kms_fact( 'inperson_minutes' ) ),
		__( 'Students', 'kms-core' )                   => trim( kms_fact( 'students' ) . ( kms_fact( 'countries' ) ? ' ' . sprintf( /* translators: %s: countries */ __( 'from %s countries', 'kms-core' ), kms_fact( 'countries' ) ) : '' ) ),
		__( 'Languages', 'kms-core' )                  => kms_fact( 'languages' ),
		__( 'Ages', 'kms-core' )                       => kms_fact( 'min_age' ) ? kms_fact( 'min_age' ) . '+' : '',
		__( 'Prices', 'kms-core' )                     => $from ? sprintf( /* translators: %s: price */ __( 'From %s per class', 'kms-core' ), kms_format_inr( $from ) ) : '',
		__( 'Hours', 'kms-core' )                      => kms_fact( 'hours_text' ),
	);
	$reviews  = array();
	if ( kms_fact( 'tripadvisor_rating' ) ) {
		$reviews[] = sprintf( /* translators: 1: rating, 2: count */ __( '%1$s/5 on TripAdvisor (%2$s reviews)', 'kms-core' ), kms_fact( 'tripadvisor_rating' ), kms_fact( 'tripadvisor_count' ) );
	}
	if ( kms_fact( 'google_rating' ) ) {
		$reviews[] = sprintf( /* translators: 1: rating, 2: count */ __( '%1$s/5 on Google (%2$s reviews)', 'kms-core' ), kms_fact( 'google_rating' ), kms_fact( 'google_count' ) );
	}
	if ( $reviews ) {
		$checked                              = kms_fact( 'ratings_checked' );
		$rows[ __( 'Reviews', 'kms-core' ) ] = implode( '; ', $reviews ) . ( $checked ? ' — ' . sprintf( /* translators: %s: month */ __( 'checked %s', 'kms-core' ), date_i18n( 'M Y', strtotime( $checked . '-01' ) ) ) : '' );
	}

	$html = '<dl class="km-facts">';
	foreach ( array_filter( $rows ) as $label => $value ) {
		$html .= '<div class="km-facts__row"><dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $value ) . '</dd></div>';
	}
	return $html . '</dl>';
}

/**
 * "At a glance" table for one class.
 *
 * @param int $post_id Course ID.
 * @return string
 */
function kms_render_course_facts( $post_id ) {
	$formats = array();
	if ( kms_course_has_mode( $post_id, 'online' ) ) {
		/* translators: %s: platforms */
		$formats[] = sprintf( __( 'Live one-to-one online (%s)', 'kms-core' ), kms_fact( 'platforms' ) );
	}
	if ( kms_course_has_mode( $post_id, 'inperson' ) ) {
		$formats[] = __( 'in person in Pushkar', 'kms-core' );
	}
	$length = array();
	if ( kms_course_has_mode( $post_id, 'online' ) ) {
		/* translators: %d: minutes */
		$length[] = sprintf( __( '%d minutes online', 'kms-core' ), kms_course_minutes( $post_id ) );
	}
	if ( kms_course_has_mode( $post_id, 'inperson' ) ) {
		/* translators: %s: minutes */
		$length[] = sprintf( __( '%s minutes in Pushkar', 'kms-core' ), kms_fact( 'inperson_minutes' ) );
	}
	$from = kms_course_from_price( $post_id );

	$rows = array(
		__( 'Format', 'kms-core' )       => ucfirst( implode( ' · ', $formats ) ),
		__( 'Class length', 'kms-core' ) => implode( ' · ', $length ),
		__( 'Level', 'kms-core' )        => (string) kms_meta( $post_id, 'kms_level' ),
		__( 'Teacher', 'kms-core' )      => kms_fact( 'founder_name' ),
		__( 'Language', 'kms-core' )     => kms_fact( 'languages' ),
		__( 'Ages', 'kms-core' )         => kms_fact( 'min_age' ) ? kms_fact( 'min_age' ) . '+' : '',
	);

	$html = '<dl class="km-facts km-facts--course">';
	foreach ( array_filter( $rows ) as $label => $value ) {
		$html .= '<div class="km-facts__row"><dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $value ) . '</dd></div>';
	}
	if ( $from ) {
		$html .= '<div class="km-facts__row"><dt>' . esc_html__( 'Price', 'kms-core' ) . '</dt><dd>' . esc_html__( 'From', 'kms-core' ) . ' ' . kms_price_html( $from ) . ' ' . esc_html__( 'per class', 'kms-core' ) . '</dd></div>';
	}
	$html .= '<div class="km-facts__row"><dt>' . esc_html__( 'When', 'kms-core' ) . '</dt><dd>' . kms_render_timezone( true ) . '</dd></div>';
	return $html . '</dl>';
}

/**
 * Teaching hours in IST and in the visitor's own time zone (converted in the browser).
 *
 * @param bool $compact Short single-line version.
 * @return string
 */
function kms_render_timezone( $compact = false ) {
	kms_need_script();
	$start = kms_fact( 'opens', '09:00' );
	$end   = kms_fact( 'closes', '21:00' );
	$fmt   = static function ( $hm ) {
		return gmdate( 'g:i A', strtotime( '2000-01-01 ' . $hm . ':00 UTC' ) );
	};
	/* translators: 1: start time, 2: end time */
	$ist  = sprintf( __( '%1$s – %2$s India time (IST), 7 days a week', 'kms-core' ), $fmt( $start ), $fmt( $end ) );
	$html = '<span class="km-tz' . ( $compact ? ' km-tz--compact' : '' ) . '" data-km-tz data-start="' . esc_attr( $start ) . '" data-end="' . esc_attr( $end ) . '">';
	$html .= '<span class="km-tz__ist">' . esc_html( $ist ) . '</span>';
	$html .= '<span class="km-tz__local" hidden>' . kms_icon( 'clock' ) . '<span data-km-tz-local></span></span>';
	return $html . '</span>';
}

/**
 * Three steps to start (from the school's own process).
 *
 * @return string
 */
function kms_render_steps() {
	$steps = array(
		array(
			'icon'  => 'message-circle',
			'title' => __( 'Tell us what you want to learn', 'kms-core' ),
			'text'  => __( 'Send a WhatsApp message or the short form: the instrument or style, your level and the times that suit you.', 'kms-core' ),
		),
		array(
			'icon'  => 'phone',
			'title' => __( 'Free 15-minute consultation', 'kms-core' ),
			'text'  => __( 'A call to talk about your goals, choose the right format and answer your questions. No obligation.', 'kms-core' ),
		),
		array(
			'icon'  => 'video',
			'title' => __( 'Your first class', 'kms-core' ),
			/* translators: %s: guarantee */
			'text'  => sprintf( __( 'You get a Zoom or Google Meet link and start playing or singing from the first minute. %s', 'kms-core' ), kms_fact_text( 'guarantee' ) ),
		),
	);
	$html  = '<ol class="km-steps">';
	foreach ( $steps as $i => $step ) {
		$html .= '<li class="km-step"><span class="km-step__num" aria-hidden="true">' . ( $i + 1 ) . '</span>' . kms_icon( $step['icon'], '', 'km-step__icon' ) . '<h3 class="km-step__title">' . esc_html( $step['title'] ) . '</h3><p>' . esc_html( $step['text'] ) . '</p></li>';
	}
	return $html . '</ol>';
}

/**
 * What every class includes.
 *
 * @return string
 */
function kms_render_includes() {
	$lines = kms_fact_lines( 'includes' );
	if ( ! $lines ) {
		return '';
	}
	$html = '<ul class="km-checklist">';
	foreach ( $lines as $line ) {
		$html .= '<li>' . kms_icon( 'check' ) . esc_html( $line ) . '</li>';
	}
	return $html . '</ul>';
}

/**
 * List from a multi-line class field.
 *
 * @param int    $post_id Course ID.
 * @param string $key     Meta key.
 * @param string $icon    Icon name.
 * @return string
 */
function kms_render_meta_list( $post_id, $key, $icon = 'check' ) {
	$lines = kms_meta_lines( $post_id, $key );
	if ( ! $lines ) {
		return '';
	}
	$html = '<ul class="km-checklist">';
	foreach ( $lines as $line ) {
		$html .= '<li>' . kms_icon( $icon ) . esc_html( kms_fill( $line ) ) . '</li>';
	}
	return $html . '</ul>';
}

/**
 * Founder card: photo, credentials, bio.
 *
 * @param string $heading Heading tag for the name.
 * @return string
 */
function kms_render_founder( $heading = 'h3' ) {
	$heading = in_array( $heading, array( 'h2', 'h3', 'h4' ), true ) ? $heading : 'h3';
	$name    = kms_fact( 'founder_name' );
	$creds   = array_filter(
		array(
			/* translators: %s: years */
			sprintf( __( 'Teaching since %1$s (%2$s years)', 'kms-core' ), kms_fact( 'founding_year' ), kms_years_teaching() . '+' ),
			trim( kms_fact( 'founder_credential' ) . ( kms_fact( 'founder_education' ) ? ', ' . kms_fact( 'founder_education' ) : '' ), ', ' ),
			kms_fact( 'founder_knows' ) ? __( 'Teaches', 'kms-core' ) . ': ' . implode( ', ', array_slice( array_map( 'trim', explode( ',', kms_fact( 'founder_knows' ) ) ), 0, 5 ) ) : '',
			kms_fact( 'band_name' ) ? sprintf( /* translators: %s: band name */ __( 'Leads the %s folk band', 'kms-core' ), kms_fact( 'band_name' ) ) : '',
		)
	);
	$about   = kms_page_url( 'about' );

	$html  = '<div class="km-founder">';
	// Used only when the photo has no alt text in the Media Library (which describes the actual photo).
	$alt   = implode( ', ', array_filter( array( $name, kms_fact( 'founder_title' ), kms_fact( 'name' ) ) ) );
	$badge = '';
	if ( kms_fact( 'tripadvisor_rating' ) ) {
		/* translators: 1: rating, 2: number of reviews */
		$badge = '<p class="km-founder__badge">' . kms_icon( 'star' ) . '<span><strong>' . esc_html( sprintf( __( '%s/5 on TripAdvisor', 'kms-core' ), kms_fact( 'tripadvisor_rating' ) ) ) . '</strong>' . esc_html( sprintf( __( '%s reviews of the school', 'kms-core' ), kms_fact( 'tripadvisor_count' ) ) ) . '</span></p>';
	}
	$html .= '<div class="km-founder__photo">' . kms_fact_image( 'founder_image', 'large', array( 'alt' => $alt, 'loading' => 'lazy' ) ) . $badge . '</div>';
	$html .= '<div class="km-founder__body">';
	$html .= '<p class="km-eyebrow">' . esc_html( kms_fact( 'founder_title' ) ) . '</p>';
	$html .= '<' . $heading . ' class="km-founder__name">' . esc_html( $name ) . '</' . $heading . '>';
	$html .= '<p class="km-founder__bio">' . esc_html( kms_fact_text( 'founder_bio' ) ) . '</p>';
	if ( kms_fact( 'founder_quote' ) ) {
		$html .= '<blockquote class="km-founder__quote"><p>' . esc_html( kms_fact_text( 'founder_quote' ) ) . '</p></blockquote>';
	}
	$html .= '<ul class="km-checklist km-checklist--compact">';
	foreach ( $creds as $cred ) {
		$html .= '<li>' . kms_icon( 'award' ) . esc_html( $cred ) . '</li>';
	}
	$html .= '</ul>';
	if ( $about && ! is_page( url_to_postid( $about ) ) ) {
		/* translators: %s: name */
		$html .= '<a class="km-link-arrow" href="' . esc_url( $about ) . '">' . esc_html( sprintf( __( 'More about %s and the school', 'kms-core' ), $name ) ) . kms_icon( 'arrow-right' ) . '</a>';
	}
	return $html . '</div></div>';
}

/**
 * Upcoming retreats: one card per retreat, with every upcoming batch as a date chip.
 *
 * @param array $args { limit:int (batches), heading:string }.
 * @return string
 */
function kms_render_retreats( $args = array() ) {
	$args    = wp_parse_args(
		$args,
		array(
			'limit'   => 8,
			'heading' => 'h3',
		)
	);
	$heading = in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading'] : 'h3';
	$batches = array_slice( kms_retreat_batches(), 0, max( 1, (int) $args['limit'] ) );
	if ( ! $batches ) {
		return '';
	}

	$retreats = array();
	foreach ( $batches as $batch ) {
		$key = $batch['name'] . '|' . $batch['url'];
		if ( ! isset( $retreats[ $key ] ) ) {
			$retreats[ $key ] = $batch + array( 'dates' => array() );
		}
		$retreats[ $key ]['dates'][] = $batch;
	}

	$html = '<div class="km-cards km-cards--retreats">';
	foreach ( $retreats as $retreat ) {
		$html .= '<article class="km-card km-retreat">';
		$html .= '<' . $heading . ' class="km-retreat__name">' . ( $retreat['url'] ? '<a href="' . esc_url( $retreat['url'] ) . '">' . esc_html( $retreat['name'] ) . '</a>' : esc_html( $retreat['name'] ) ) . '</' . $heading . '>';
		if ( $retreat['place'] ) {
			$html .= '<p class="km-retreat__place">' . kms_icon( 'map-pin' ) . esc_html( $retreat['place'] ) . '</p>';
		}
		if ( $retreat['description'] ) {
			$html .= '<p>' . esc_html( $retreat['description'] ) . '</p>';
		}
		$html .= '<ul class="km-retreat__dates" aria-label="' . esc_attr__( 'Upcoming dates', 'kms-core' ) . '">';
		foreach ( $retreat['dates'] as $date ) {
			$html .= '<li>' . kms_icon( 'calendar' ) . '<time datetime="' . esc_attr( $date['start'] ) . '">' . esc_html( kms_date_range( $date['start'], $date['end'] ) ) . '</time></li>';
		}
		$html .= '</ul>';
		if ( $retreat['price'] ) {
			$html .= '<p class="km-retreat__price">' . kms_price_html( $retreat['price'] ) . ' <span class="km-muted">' . esc_html__( 'per person', 'kms-core' ) . '</span></p>';
		}
		$html .= '<div class="km-actions">';
		if ( $retreat['url'] ) {
			$html .= '<a class="km-btn km-btn--primary" href="' . esc_url( $retreat['url'] ) . '">' . esc_html__( 'Programme and booking', 'kms-core' ) . kms_icon( 'arrow-right' ) . '</a>';
		}
		/* translators: %s: retreat name */
		$html .= kms_whatsapp_button( sprintf( __( 'Hi! I am interested in the %s.', 'kms-core' ), $retreat['name'] ), __( 'Ask on WhatsApp', 'kms-core' ), 'km-btn--outline' );
		$html .= '</div></article>';
	}
	return $html . '</div>';
}

/**
 * Key numbers: years of teaching, students, countries and the TripAdvisor rating.
 *
 * @return string
 */
function kms_render_stats() {
	$stats = array( array( kms_years_teaching() . '+', __( 'Years of teaching', 'kms-core' ) ) );
	if ( kms_fact( 'students' ) ) {
		$stats[] = array( kms_fact( 'students' ), __( 'Students taught', 'kms-core' ) );
	}
	if ( kms_fact( 'countries' ) ) {
		$stats[] = array( kms_fact( 'countries' ), __( 'Countries', 'kms-core' ) );
	}
	if ( kms_fact( 'tripadvisor_rating' ) ) {
		/* translators: %s: number of reviews */
		$stats[] = array( kms_fact( 'tripadvisor_rating' ) . '/5', sprintf( __( 'TripAdvisor, %s reviews', 'kms-core' ), kms_fact( 'tripadvisor_count' ) ) );
	}
	$html = '<ul class="km-stats">';
	foreach ( $stats as $stat ) {
		$html .= '<li><strong class="km-stats__num">' . esc_html( $stat[0] ) . '</strong><span class="km-stats__label">' . esc_html( $stat[1] ) . '</span></li>';
	}
	return $html . '</ul>';
}

/**
 * Live performance types from KMS Facts.
 *
 * @param array $args { limit:int, heading:string }.
 * @return string
 */
function kms_render_performances( $args = array() ) {
	$args    = wp_parse_args(
		$args,
		array(
			'limit'   => 0,
			'heading' => 'h3',
		)
	);
	$heading = in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading'] : 'h3';
	$items   = kms_performances();
	if ( (int) $args['limit'] > 0 ) {
		$items = array_slice( $items, 0, (int) $args['limit'] );
	}
	if ( ! $items ) {
		return '';
	}
	$icons = array(
		'folk'       => 'drum',
		'band'       => 'drum',
		'solo'       => 'mic-vocal',
		'bollywood'  => 'music',
		'corporate'  => 'award',
		'sufi'       => 'sparkles',
		'dinner'     => 'star',
		'fusion'     => 'headphones',
		'devotional' => 'heart-handshake',
		'concert'    => 'sparkles',
	);
	$html = '<div class="km-cards km-cards--shows">';
	foreach ( $items as $item ) {
		$icon = 'music';
		foreach ( $icons as $word => $name ) {
			if ( false !== stripos( $item['name'], $word ) ) {
				$icon = $name;
				break;
			}
		}
		$name  = $item['url'] ? '<a class="km-stretched" href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['name'] ) . '</a>' : esc_html( $item['name'] );
		$html .= '<article class="km-card km-show"><span class="km-show__icon">' . kms_icon( $icon ) . '</span><div class="km-show__body">';
		$html .= '<' . $heading . ' class="km-show__name">' . $name . '</' . $heading . '>';
		if ( $item['description'] ) {
			$html .= '<p class="km-show__desc">' . esc_html( $item['description'] ) . '</p>';
		}
		/* translators: %s: starting price */
		$html .= '<p class="km-show__price">' . ( $item['price'] ? sprintf( esc_html__( 'From %s', 'kms-core' ), '<strong>' . esc_html( kms_format_inr( $item['price'] ) ) . '</strong>' ) : esc_html__( 'Custom packages', 'kms-core' ) ) . '</p>';
		$html .= '</div></article>';
	}
	return $html . '</div>';
}

/**
 * Contact block: WhatsApp, phone, email, address, hours.
 *
 * @return string
 */
function kms_render_contact_list() {
	$items = array();
	if ( kms_fact( 'whatsapp' ) ) {
		$items[] = kms_icon( 'whatsapp' ) . '<a href="' . esc_url( kms_whatsapp_url() ) . '" target="_blank" rel="noopener">' . esc_html( sprintf( /* translators: %s: phone */ __( 'WhatsApp %s', 'kms-core' ), kms_fact( 'phone_display' ) ) ) . '</a>';
	}
	if ( kms_fact( 'phone_e164' ) ) {
		$items[] = kms_icon( 'phone' ) . '<a href="tel:' . esc_attr( kms_fact( 'phone_e164' ) ) . '">' . esc_html( kms_fact( 'phone_display' ) ) . '</a>';
	}
	if ( kms_fact( 'email' ) ) {
		$items[] = kms_icon( 'mail' ) . '<a href="mailto:' . esc_attr( antispambot( kms_fact( 'email' ) ) ) . '">' . esc_html( antispambot( kms_fact( 'email' ) ) ) . '</a>';
	}
	$items[] = kms_icon( 'map-pin' ) . ( kms_fact( 'map_url' ) ? '<a href="' . esc_url( kms_fact( 'map_url' ) ) . '" target="_blank" rel="noopener">' . esc_html( kms_address_line() ) . '</a>' : esc_html( kms_address_line() ) );
	if ( kms_fact( 'hours_text' ) ) {
		$items[] = kms_icon( 'clock' ) . esc_html( kms_fact( 'hours_text' ) );
	}
	return '<ul class="km-contact-list"><li>' . implode( '</li><li>', $items ) . '</li></ul>';
}

/**
 * "Leave a review" links for past students.
 *
 * @return string
 */
function kms_render_review_links() {
	$links = array();
	if ( kms_fact( 'google_review_link' ) ) {
		$links[] = '<a class="km-btn km-btn--outline" href="' . esc_url( kms_fact( 'google_review_link' ) ) . '" target="_blank" rel="noopener">' . kms_icon( 'google' ) . esc_html__( 'Review us on Google', 'kms-core' ) . '</a>';
	}
	if ( kms_fact( 'tripadvisor_url' ) ) {
		$links[] = '<a class="km-btn km-btn--outline" href="' . esc_url( kms_fact( 'tripadvisor_url' ) ) . '" target="_blank" rel="noopener">' . kms_icon( 'tripadvisor' ) . esc_html__( 'Review us on TripAdvisor', 'kms-core' ) . '</a>';
	}
	return $links ? '<p class="km-actions">' . implode( ' ', $links ) . '</p>' : '';
}
