<?php
/**
 * /llms.txt — a plain-language summary for AI assistants (llmstxt.org format).
 *
 * Generated from KMS Facts, the classes, retreats and key pages, so it always
 * matches the website. Served before WordPress resolves the request, which also
 * bypasses the old "redirect every 404 to the homepage" rule.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Serve /llms.txt.
 */
function kms_llms_maybe_serve() {
	if ( ! kms_fact_on( 'llms_enabled' ) || empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$path = (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH );
	$home = untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) );
	if ( $path !== $home . '/llms.txt' ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'X-Robots-Tag: noindex' );
	header( 'Cache-Control: public, max-age=3600' );
	echo kms_llms_text(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain text document.
	exit;
}
add_action( 'init', 'kms_llms_maybe_serve', 99 );

/**
 * Markdown line for a link.
 *
 * @param string $title Title.
 * @param string $url   URL.
 * @param string $note  Description.
 * @return string
 */
function kms_llms_link( $title, $url, $note = '' ) {
	$title = str_replace( array( '[', ']' ), '', kms_plain( $title ) );
	return '- [' . $title . '](' . $url . ')' . ( $note ? ': ' . trim( preg_replace( '/\s+/', ' ', kms_plain( $note ) ) ) : '' );
}

/**
 * Build the document.
 *
 * @return string
 */
function kms_llms_text() {
	$name    = kms_fact( 'name' );
	$founder = kms_fact( 'founder_name' );
	$out     = array();

	$out[] = '# ' . $name;
	$out[] = '';
	$out[] = '> ' . kms_fact_text( 'description' );
	$out[] = '';

	// Key facts, phrased as complete sentences so they can be quoted directly.
	$facts   = array();
	$facts[] = sprintf( '%s was founded in %s in %s, Rajasthan, India, by %s (%s).', $name, kms_fact( 'founding_year' ), kms_fact( 'locality' ), $founder, strtolower( kms_fact( 'founder_title' ) ) );
	$facts[] = kms_fact_text( 'founder_bio' );
	$clock   = static function ( $hm ) {
		return gmdate( 'g:i A', strtotime( '2000-01-01 ' . $hm . ':00 UTC' ) );
	};
	$facts[] = sprintf( 'Online classes are live and one-to-one on %s, %s minutes each, scheduled between %s and %s India time (IST, UTC+5:30), 7 days a week.', kms_fact( 'platforms' ), kms_fact( 'online_minutes' ), $clock( kms_fact( 'opens', '09:00' ) ), $clock( kms_fact( 'closes', '21:00' ) ) );
	$facts[] = sprintf( 'In-person classes (%s minutes) and retreats take place at %s.', kms_fact( 'inperson_minutes' ), kms_address_line() );
	$facts[] = sprintf( 'Teaching languages: %s. Students from age %s. Complete beginners are welcome.', kms_fact( 'languages' ), kms_fact( 'min_age' ) );
	if ( kms_fact( 'students' ) ) {
		$facts[] = sprintf( 'The school has taught %s students from %s countries.', kms_fact( 'students' ), kms_fact( 'countries' ) );
	}
	$ratings = array();
	if ( kms_fact( 'tripadvisor_rating' ) ) {
		$ratings[] = sprintf( '%s/5 on TripAdvisor (%s reviews)', kms_fact( 'tripadvisor_rating' ), kms_fact( 'tripadvisor_count' ) );
	}
	if ( kms_fact( 'google_rating' ) ) {
		$ratings[] = sprintf( '%s/5 on Google (%s reviews)', kms_fact( 'google_rating' ), kms_fact( 'google_count' ) );
	}
	if ( $ratings ) {
		$facts[] = 'Reviews: ' . implode( '; ', $ratings ) . ( kms_fact( 'ratings_checked' ) ? ', as of ' . kms_fact( 'ratings_checked' ) . '.' : '.' );
	}
	$facts[] = 'What every online class includes: ' . implode( '; ', kms_fact_lines( 'includes' ) ) . '.';
	$facts[] = kms_fact_text( 'guarantee' ) . ' ' . kms_fact_text( 'reschedule' ) . ' ' . kms_fact_text( 'validity' );
	$facts[] = 'Payment: ' . kms_fact( 'payment_methods' ) . '. Prices are set in Indian rupees (INR).';
	$facts[] = sprintf( 'Contact: WhatsApp or phone %s, email %s. %s', kms_fact( 'phone_display' ), kms_fact( 'email' ), kms_fact( 'response_time' ) );
	if ( kms_fact( 'band_name' ) ) {
		$facts[] = sprintf( 'The school\'s performing wing, the %s, plays Rajasthani folk, Sufi, devotional and fusion music for weddings, hotels, festivals and corporate events.', kms_fact( 'band_name' ) );
	}

	$out[] = '## Key facts';
	$out[] = '';
	foreach ( array_filter( $facts ) as $fact ) {
		$out[] = '- ' . trim( preg_replace( '/\s+/', ' ', $fact ) );
	}
	$out[] = '';

	$courses = kms_get_courses();
	if ( $courses ) {
		$out[] = '## Online classes';
		$out[] = '';
		$archive = get_post_type_archive_link( 'kms_course' );
		if ( $archive ) {
			$out[] = kms_llms_link( 'All online classes', $archive, 'Overview, how online classes work, prices and FAQs.' );
		}
		foreach ( $courses as $course ) {
			$note  = kms_course_answer( $course->ID );
			$price = kms_course_price_text( $course->ID );
			$out[] = kms_llms_link( get_the_title( $course ), get_permalink( $course ), trim( $note . ( $price ? ' Prices — ' . rtrim( $price, '.' ) . '.' : '' ) ) );
		}
		$out[] = '';
	}

	$batches = kms_retreat_batches();
	if ( $batches ) {
		$out[]    = '## Retreats in India (in person)';
		$out[]    = '';
		$retreats = array();
		foreach ( $batches as $batch ) {
			$key = $batch['name'] . '|' . $batch['url'];
			if ( ! isset( $retreats[ $key ] ) ) {
				$retreats[ $key ] = $batch + array( 'dates' => array() );
			}
			$retreats[ $key ]['dates'][] = kms_date_range( $batch['start'], $batch['end'] );
		}
		foreach ( $retreats as $retreat ) {
			$note  = ( $retreat['place'] ? $retreat['place'] . '. ' : '' ) . $retreat['description'] . ' Upcoming dates: ' . implode( '; ', $retreat['dates'] ) . '.' . ( $retreat['price'] ? ' Price: ' . kms_format_inr( $retreat['price'] ) . ' per person.' : '' );
			$out[] = kms_llms_link( $retreat['name'], $retreat['url'] ? $retreat['url'] : home_url( '/' ), $note );
		}
		$out[] = '';
	}

	$shows = kms_performances();
	if ( $shows ) {
		$out[] = '## Live performances (events, weddings, hotels)';
		foreach ( $shows as $show ) {
			$note  = rtrim( $show['description'], '.' ) . '. ' . ( $show['price'] ? 'From ' . kms_format_inr( $show['price'] ) . '.' : 'Custom packages.' );
			$out[] = kms_llms_link( $show['name'], $show['url'] ? $show['url'] : home_url( '/' ), $note );
		}
		$out[] = '';
	}

	$pages = array(
		'about'        => array( 'About the school and ' . $founder, 'History, teaching method and the founder\'s background.' ),
		'reviews'      => array( 'Student reviews', 'Reviews from TripAdvisor and Google, with links to the originals.' ),
		'pricing'      => array( 'Prices', 'All class prices in one table.' ),
		'faq'          => array( 'Frequently asked questions', '' ),
		'refund'       => array( 'Refund and cancellation policy', '' ),
		'contact'      => array( 'Contact and free consultation', 'Enquiry form, WhatsApp, phone, email, address and directions.' ),
		'performances' => array( 'Live performances', 'Book Rajasthani folk, Sufi, devotional and fusion music for events.' ),
	);
	$lines = array();
	foreach ( $pages as $key => $def ) {
		$url = kms_page_url( $key );
		if ( $url ) {
			$lines[] = kms_llms_link( $def[0], $url, $def[1] );
		}
	}
	if ( $lines ) {
		$out[] = '## About, policies and contact';
		$out[] = '';
		$out   = array_merge( $out, $lines );
		$out[] = '';
	}

	$profiles = kms_same_as();
	if ( $profiles ) {
		$out[] = '## Official profiles';
		$out[] = '';
		foreach ( $profiles as $profile ) {
			$out[] = '- ' . $profile;
		}
		$out[] = '';
	}

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 15,
			'orderby'        => 'modified',
		)
	);
	if ( $posts ) {
		$out[] = '## Optional';
		$out[] = '';
		$blog  = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$out[] = kms_llms_link( 'Blog', get_permalink( $blog ), 'Guides to harmonium, kirtan, ragas and Indian singing written from the school\'s teaching.' );
		}
		foreach ( $posts as $post ) {
			$out[] = kms_llms_link( get_the_title( $post ), get_permalink( $post ) );
		}
		$out[] = '';
	}

	$extra = trim( kms_fact_text( 'llms_extra' ) );
	if ( $extra ) {
		$out[] = '## Notes';
		$out[] = '';
		$out[] = $extra;
		$out[] = '';
	}

	/* translators: %s: date */
	$out[] = sprintf( 'Last generated: %s', wp_date( 'Y-m-d' ) );

	return implode( "\n", $out ) . "\n";
}
