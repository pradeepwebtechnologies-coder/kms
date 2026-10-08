<?php
/**
 * Facts registry: the single source of truth for every business fact.
 *
 * Pages, structured data and llms.txt all read from here, so a corrected fact
 * (price, founding year, postcode…) changes everywhere at once. Text facts may
 * contain placeholders: {name} {founder} {founding_year} {years} {students}
 * {countries} {city}.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

const KMS_FACTS_OPTION = 'kms_facts';

/**
 * Field definitions grouped by section.
 *
 * Defaults are the values used most consistently on the live site (Oct 2026).
 * Values marked "confirm" in docs/02-FACTS-TO-CONFIRM.md must be checked by the school.
 *
 * @return array<string, array{title:string, fields:array<string, array>}>
 */
function kms_facts_schema() {
	$retreat_url = '/8-day-winter-music-retreat-in-pushkar-singing-mantra-chanting-kirtan-harmonium/';

	return array(
		'identity' => array(
			'title'  => __( 'Identity', 'kms-core' ),
			'fields' => array(
				'name'               => array( 'Business name', 'text', 'Krishna Music School' ),
				'alternate_name'     => array( 'Alternate name', 'text', 'Krishna Music School Pushkar' ),
				'tagline'            => array( 'Tagline', 'text', 'Live one-to-one Indian music classes, online and in Pushkar' ),
				'description'        => array( 'Short description (used in schema and llms.txt)', 'textarea', '{name} teaches harmonium, Indian singing, bhajan and kirtan, Hindustani classical vocal and tabla. Classes are live and one-to-one, online worldwide or in person at the historic Rangji Temple in Pushkar, Rajasthan, India. Founded in {founding_year} by musician and teacher {founder}.' ),
				'founding_year'      => array( 'Founding year', 'number', '2008', 'Years of teaching are calculated from this.' ),
				'founder_name'       => array( 'Founder name (exact spelling)', 'text', 'Vini Devda' ),
				'founder_title'      => array( 'Founder role', 'text', 'Founder and lead teacher' ),
				'founder_bio'        => array( 'Founder bio (2–4 sentences, no pronouns needed)', 'textarea', '{founder} is the third generation of a family of musicians and began learning Indian classical music at the age of five in the traditional guru–shishya way. {founder} holds a degree in Indian Classical Vocal Music from Government College, Ajmer, has taught students from {countries} countries since {founding_year}, and leads the Chokhi Vini Project, the school\'s Rajasthani folk band.' ),
				'founder_quote'      => array( 'Founder quote (shown next to the photo; empty = hidden)', 'textarea', "I believe everyone is musical — the question is not 'Can I learn?' but 'Am I ready to open my ears and heart?' I've taught complete beginners who thought they were tone-deaf, and watched them lead kirtans 10 weeks later. Music is patience, practice, and playfulness." ),
				'founder_education'  => array( 'Founder education (institution)', 'text', 'Government College, Ajmer', 'Shown as alumniOf in schema. Leave empty to omit.' ),
				'founder_credential' => array( 'Founder qualification', 'text', 'Degree in Indian Classical Vocal Music' ),
				'founder_knows'      => array( 'Founder expertise (comma separated)', 'text', 'Tabla, Hindustani classical vocal, Harmonium, Sitar, Dholak, Bhajan, Kirtan, Mantra chanting, Rajasthani folk music' ),
				'founder_image'      => array( 'Founder photo URL (Media Library)', 'url', 'https://krishnamusicschool.com/wp-content/uploads/2024/05/vinod-dewra.webp' ),
				'logo'               => array( 'Logo URL (for schema; the header uses Appearance → Customize → Site Identity)', 'url', 'https://krishnamusicschool.com/wp-content/uploads/2023/11/Black-Logo.webp' ),
				'default_image'      => array( 'Default share image and homepage photo URL (landscape, 1200 px or wider)', 'url', 'https://krishnamusicschool.com/wp-content/uploads/2025/10/IMG_20230826_14451146.jpg' ),
				'band_name'          => array( 'Performance wing name', 'text', 'Chokhi Vini Project' ),
			),
		),
		'location' => array(
			'title'  => __( 'Location (must match the Google Business Profile exactly)', 'kms-core' ),
			'fields' => array(
				'street'       => array( 'Street / landmark', 'text', 'Rangji Temple' ),
				'locality'     => array( 'Town', 'text', 'Pushkar' ),
				'district'     => array( 'District', 'text', 'Ajmer' ),
				'region'       => array( 'State', 'text', 'Rajasthan' ),
				'postal_code'  => array( 'PIN code', 'text', '305022', 'Pushkar is 305022. 305001 is Ajmer city.' ),
				'country'      => array( 'Country', 'text', 'India' ),
				'country_code' => array( 'Country code', 'text', 'IN' ),
				'lat'          => array( 'Latitude', 'text', '26.4897' ),
				'lng'          => array( 'Longitude', 'text', '74.5511' ),
				'map_url'      => array( 'Google Maps link', 'url', 'https://share.google/qgdy0KpwZBODGd44Y' ),
				'directions'   => array( 'How to find us', 'textarea', "10 minutes' walk from Pushkar bus stand and 15 minutes' walk from Pushkar Lake, next to Rangji Temple (ask any local). An auto-rickshaw from the bus stand costs about ₹50." ),
			),
		),
		'contact'  => array(
			'title'  => __( 'Contact', 'kms-core' ),
			'fields' => array(
				'phone_display' => array( 'Phone (display)', 'text', '+91 99286 58520' ),
				'phone_e164'    => array( 'Phone (international format)', 'text', '+919928658520' ),
				'whatsapp'      => array( 'WhatsApp number (digits only, with country code)', 'text', '919928658520' ),
				'email'         => array( 'Public email', 'email', 'krishnamusicschoolpushkar@gmail.com' ),
				'lead_email'    => array( 'Send enquiries to (comma separated)', 'text', '', 'Empty = the site admin email.' ),
				'hours_text'    => array( 'Opening hours (display)', 'text', 'Monday–Sunday, 9:00 AM – 9:00 PM IST' ),
				'opens'         => array( 'Opens (24h, IST)', 'text', '09:00' ),
				'closes'        => array( 'Closes (24h, IST)', 'text', '21:00' ),
				'response_time' => array( 'Reply time promise', 'text', 'We usually reply on WhatsApp within 2–4 hours (9 AM – 9 PM IST).' ),
				'autoreply'     => array( 'Send an automatic confirmation email to the student', 'checkbox', '0', 'Turn on only after an SMTP plugin is set up, or confirmations may land in spam.' ),
			),
		),
		'proof'    => array(
			'title'  => __( 'Proof (publish only numbers you can show)', 'kms-core' ),
			'fields' => array(
				'students'           => array( 'Students taught', 'text', '200+' ),
				'countries'          => array( 'Countries', 'text', '25+' ),
				'tripadvisor_rating' => array( 'TripAdvisor rating', 'text', '5.0' ),
				'tripadvisor_count'  => array( 'TripAdvisor review count', 'text', '55+' ),
				'tripadvisor_url'    => array( 'TripAdvisor listing URL', 'url', '' ),
				'google_rating'      => array( 'Google rating', 'text', '4.9' ),
				'google_count'       => array( 'Google review count', 'text', '61+' ),
				'google_url'         => array( 'Google reviews URL', 'url', 'https://www.google.com/search?kgmid=/g/11b6q347jc' ),
				'google_review_link' => array( 'Google "write a review" link', 'url', '', 'From Google Business Profile → Ask for reviews. Shown on the Reviews page.' ),
				'ratings_checked'    => array( 'Ratings last checked (YYYY-MM)', 'text', '2026-10' ),
			),
		),
		'profiles' => array(
			'title'  => __( 'Official profiles (schema sameAs)', 'kms-core' ),
			'fields' => array(
				'youtube'        => array( 'YouTube', 'url', 'https://www.youtube.com/@krishnamusicschoolpushkar' ),
				'facebook'       => array( 'Facebook', 'url', 'https://www.facebook.com/krishnamusicschoolpushkar' ),
				'instagram'      => array( 'Instagram', 'url', 'https://www.instagram.com/pushkarmusicretreat/' ),
				'google_kg'      => array( 'Google Knowledge Graph / Business Profile URL', 'url', 'https://www.google.com/search?kgmid=/g/11b6q347jc' ),
				'other_profiles' => array( 'Other profiles (one URL per line)', 'textarea', '' ),
			),
		),
		'classes'  => array(
			'title'  => __( 'Online classes (applies to every class)', 'kms-core' ),
			'fields' => array(
				'online_minutes'   => array( 'Online class length (minutes)', 'number', '40' ),
				'inperson_minutes' => array( 'In-person class length in Pushkar (minutes)', 'number', '60' ),
				'platforms'        => array( 'Video platforms', 'text', 'Zoom or Google Meet' ),
				'languages'        => array( 'Teaching languages', 'text', 'English, Hindi' ),
				'min_age'          => array( 'Minimum age', 'text', '8' ),
				'includes'         => array( 'Included with every class (one per line)', 'textarea', "Live one-to-one class on Zoom or Google Meet\nRecording of every class to practise with\nNotation and lyrics PDFs\nWhatsApp support between classes\nA practice routine made for your voice and schedule\nCertificate of completion after 10 classes" ),
				'guarantee'        => array( 'First-class guarantee', 'text', 'Not happy after your first class? We refund it in full.' ),
				'reschedule'       => array( 'Rescheduling rule', 'text', 'Reschedule free of charge up to 4 hours before your class.' ),
				'validity'         => array( 'Package validity', 'text', '5-class packages are valid for 3 months and 10-class packages for 6 months.' ),
				'payment_methods'  => array( 'Payment methods', 'text', 'UPI or bank transfer (India), PayPal or Wise (international)' ),
				'rate_usd'         => array( 'Rupees per 1 US dollar (for "≈ US$" display)', 'number', '88' ),
				'rate_eur'         => array( 'Rupees per 1 euro', 'number', '102' ),
				'rate_gbp'         => array( 'Rupees per 1 pound', 'number', '117' ),
			),
		),
		'retreats' => array(
			'title'  => __( 'Retreats in India', 'kms-core' ),
			'fields' => array(
				'retreat_batches' => array(
					'Retreat batches (one per line: Name | start YYYY-MM-DD | end YYYY-MM-DD | price INR | page URL | place | short description)',
					'textarea',
					"Pushkar Winter Music Retreat | 2026-11-11 | 2026-11-18 | 30000 | {$retreat_url} | Pushkar, Rajasthan | 8 days of singing, mantra chanting, kirtan, bhajan and harmonium. 16 hours of live teaching, maximum 10 students.\n" .
					"Pushkar Winter Music Retreat | 2026-12-11 | 2026-12-18 | 30000 | {$retreat_url} | Pushkar, Rajasthan | 8 days of singing, mantra chanting, kirtan, bhajan and harmonium. 16 hours of live teaching, maximum 10 students.\n" .
					"Pushkar Winter Music Retreat | 2027-01-11 | 2027-01-18 | 30000 | {$retreat_url} | Pushkar, Rajasthan | 8 days of singing, mantra chanting, kirtan, bhajan and harmonium. 16 hours of live teaching, maximum 10 students.\n" .
					"Pushkar Winter Music Retreat | 2027-02-11 | 2027-02-18 | 30000 | {$retreat_url} | Pushkar, Rajasthan | 8 days of singing, mantra chanting, kirtan, bhajan and harmonium. 16 hours of live teaching, maximum 10 students.",
					'Past batches hide automatically. Delete a batch as soon as it is full: every batch listed here is shown as open for booking, on the site and in schema (EducationEvent).',
				),
			),
		),
		'performances' => array(
			'title'  => __( 'Live performances', 'kms-core' ),
			'fields' => array(
				'performances' => array(
					'Performance types (one per line: Name | page URL | short description | starting price in INR, 0 = custom packages)',
					'textarea',
					"Folk Music Band | /folk-music-band-chokhi-vini-project-pushkar/ | Authentic Rajasthani folk with traditional instruments. 5–10 piece ensemble. | 80000\n" .
					"Cultural Concerts & Festivals | /rajasthani-cultural-concerts-festivals/ | Grand cultural events showcasing Rajasthani heritage. Multi-act productions. | 250000\n" .
					"Solo Artists | /solo-artists/ | Vocalist, sitar, bansuri, guitar, tabla. Intimate, elegant performances. | 15000\n" .
					"Bollywood Performances | /bollywood-dance-music-shows-for-events-weddings/ | High-energy crowd-pleasers for sangeet and parties. | 60000\n" .
					"Corporate Events | /corporate-musical-events-cultural-shows/ | Professional entertainment for corporate functions and cultural showcases. | 0\n" .
					"Sufi Musical Experience | /kabali-sufi-musical-experience/ | A mystical Sufi performance and spiritual journey. | 50000\n" .
					"Dinner Concerts | /dinner-concerts-private-music-evenings/ | Intimate musical evenings for private gatherings. | 40000\n" .
					"Fusion Instrumental | /fusion-instrumental-groups/ | Indian and Western instruments together. Contemporary and refined. | 70000\n" .
					"Devotional Music Nights | /bhajan-kirtan-devotional-music-nights-in-pushkar/ | Bhajan and kirtan sessions in a sacred atmosphere. | 35000",
					'Shown on the homepage and in llms.txt. Prices are the starting prices from the current site.',
				),
			),
		),
		'pages'    => array(
			'title'  => __( 'Key pages', 'kms-core' ),
			'fields' => array(
				'page_about'        => array( 'About page', 'page', '' ),
				'page_contact'      => array( 'Contact / enquiry page', 'page', '' ),
				'page_pricing'      => array( 'Pricing page', 'page', '' ),
				'page_faq'          => array( 'FAQ page', 'page', '' ),
				'page_reviews'      => array( 'Reviews page', 'page', '' ),
				'page_refund'       => array( 'Refund & cancellation policy', 'page', '' ),
				'page_privacy'      => array( 'Privacy policy', 'page', '' ),
				'page_terms'        => array( 'Terms and conditions', 'page', '' ),
				'page_performances' => array( 'Live performances page', 'page', '' ),
			),
		),
		'ai'       => array(
			'title'  => __( 'Search and AI engines', 'kms-core' ),
			'fields' => array(
				'schema_enabled'    => array( 'Output the KMS structured-data graph (replaces Rank Math / Yoast JSON-LD)', 'checkbox', '1' ),
				'strip_content_ld'  => array( 'Remove JSON-LD pasted inside page/post content (old fake ratings, broken blocks)', 'checkbox', '1' ),
				'allow_ai_crawlers' => array( 'Add explicit Allow rules for AI search and assistant crawlers to robots.txt', 'checkbox', '1' ),
				'llms_enabled'      => array( 'Serve /llms.txt', 'checkbox', '1' ),
				'llms_extra'        => array( 'Extra notes for AI assistants (appended to llms.txt)', 'textarea', '' ),
				'legacy_divi'       => array( 'Render old Divi pages without the Divi builder (strip [et_pb_*] shortcodes, keep the content)', 'checkbox', '1' ),
				'legacy_styles'     => array( 'Keep <style> blocks inside old page content', 'checkbox', '1', 'Turn off once a page is rebuilt, so the old styles stop overriding the theme.' ),
			),
		),
	);
}

/**
 * Flattened defaults.
 *
 * @return array<string, string>
 */
function kms_facts_defaults() {
	$defaults = array();
	foreach ( kms_facts_schema() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$defaults[ $key ] = (string) $field[2];
		}
	}
	return $defaults;
}

/**
 * All facts (saved values merged over defaults).
 *
 * @return array<string, string>
 */
function kms_facts( $flush = false ) {
	static $cache = null;
	if ( null === $cache || $flush ) {
		$saved = get_option( KMS_FACTS_OPTION, array() );
		$cache = array_merge( kms_facts_defaults(), is_array( $saved ) ? $saved : array() );
	}
	return $cache;
}

/**
 * Refresh the in-request cache whenever the option changes.
 */
function kms_facts_flush() {
	kms_facts( true );
}
add_action( 'update_option_' . KMS_FACTS_OPTION, 'kms_facts_flush' );
add_action( 'add_option_' . KMS_FACTS_OPTION, 'kms_facts_flush' );

/**
 * Raw fact value.
 *
 * @param string $key     Fact key.
 * @param string $default Fallback when empty.
 * @return string
 */
function kms_fact( $key, $default = '' ) {
	$facts = kms_facts();
	$value = isset( $facts[ $key ] ) ? trim( (string) $facts[ $key ] ) : '';
	return '' === $value ? $default : $value;
}

/**
 * Whether a checkbox fact is on.
 *
 * @param string $key Fact key.
 * @return bool
 */
function kms_fact_on( $key ) {
	return '1' === kms_fact( $key, '0' );
}

/**
 * Years of teaching, calculated so the number never goes stale.
 *
 * @return int
 */
function kms_years_teaching() {
	$year = (int) kms_fact( 'founding_year', '2008' );
	return max( 1, (int) wp_date( 'Y' ) - $year );
}

/**
 * Fact with placeholders replaced.
 *
 * @param string $key Fact key.
 * @return string
 */
function kms_fact_text( $key ) {
	return kms_fill( kms_fact( $key ) );
}

/**
 * Replace {placeholders} in any string with facts.
 *
 * @param string $text Text containing placeholders.
 * @return string
 */
function kms_fill( $text ) {
	$text = (string) $text;
	if ( false === strpos( $text, '{' ) ) {
		return $text;
	}
	static $map = null;
	if ( null === $map || doing_action( 'update_option_' . KMS_FACTS_OPTION ) ) {
		$map = array(
			'{name}'             => kms_fact( 'name' ),
			'{founder}'          => kms_fact( 'founder_name' ),
			'{founding_year}'    => kms_fact( 'founding_year' ),
			'{years}'            => kms_years_teaching() . '+',
			'{students}'         => kms_fact( 'students' ),
			'{countries}'        => kms_fact( 'countries' ),
			'{city}'             => kms_fact( 'locality' ),
			'{band}'             => kms_fact( 'band_name' ),
			'{minutes}'          => kms_fact( 'online_minutes' ),
			'{inperson_minutes}' => kms_fact( 'inperson_minutes' ),
			'{platforms}'        => kms_fact( 'platforms' ),
			'{languages}'        => kms_fact( 'languages' ),
			'{min_age}'          => kms_fact( 'min_age' ),
			'{phone}'            => kms_fact( 'phone_display' ),
			'{email}'            => kms_fact( 'email' ),
			'{hours}'            => kms_fact( 'hours_text' ),
			'{payment_methods}'  => kms_fact( 'payment_methods' ),
			'{guarantee}'        => kms_fact( 'guarantee' ),
			'{reschedule}'       => kms_fact( 'reschedule' ),
			'{validity}'         => kms_fact( 'validity' ),
			'{response_time}'    => kms_fact( 'response_time' ),
			'{directions}'       => kms_fact( 'directions' ),
			'{address}'          => kms_address_line(),
		);
		if ( function_exists( 'kms_price_summary' ) ) {
			$summary                = kms_price_summary();
			$map['{single_from}']   = $summary['single'] ? kms_format_inr( $summary['single'] ) : '';
			$map['{price_from}']    = $summary['per_class'] ? kms_format_inr( $summary['per_class'] ) : '';
		}
	}
	return strtr( $text, $map );
}

/**
 * Replace placeholders in page/post content, so editors can write {founder} or {years}
 * and the text follows KMS Facts. Only the known tokens above are touched.
 *
 * @param string $content Content.
 * @return string
 */
function kms_fill_content( $content ) {
	return kms_fill( $content );
}
add_filter( 'the_content', 'kms_fill_content', 12 );
add_filter( 'the_title', 'kms_fill_content', 12 );
add_filter( 'get_the_excerpt', 'kms_fill_content', 12 );

/**
 * Lines of a textarea fact as an array (empty lines removed).
 *
 * @param string $key Fact key.
 * @return string[]
 */
function kms_fact_lines( $key ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', kms_fact_text( $key ) ) ) ) );
}

/**
 * WhatsApp click-to-chat URL.
 *
 * @param string $message Prefilled message.
 * @return string
 */
function kms_whatsapp_url( $message = '' ) {
	$number = preg_replace( '/\D+/', '', kms_fact( 'whatsapp' ) );
	if ( '' === $number ) {
		return '';
	}
	$url = 'https://wa.me/' . $number;
	if ( '' !== $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}
	return $url;
}

/**
 * Official profile URLs for schema sameAs.
 *
 * @return string[]
 */
function kms_same_as() {
	$urls = array();
	foreach ( array( 'google_kg', 'youtube', 'facebook', 'instagram', 'tripadvisor_url' ) as $key ) {
		$urls[] = kms_fact( $key );
	}
	$urls = array_merge( $urls, kms_fact_lines( 'other_profiles' ) );
	$urls = array_filter( $urls, 'wp_http_validate_url' );
	return array_values( array_unique( $urls ) );
}

/**
 * Formatted postal address on one line.
 *
 * @return string
 */
function kms_address_line() {
	$parts = array(
		kms_fact( 'street' ),
		kms_fact( 'locality' ),
		trim( kms_fact( 'region' ) . ' ' . kms_fact( 'postal_code' ) ),
		kms_fact( 'country' ),
	);
	return implode( ', ', array_filter( $parts ) );
}

/**
 * URL of a key page (by setting, then by common slug).
 *
 * @param string $key      One of about, contact, pricing, faq, reviews, refund, privacy, terms, performances.
 * @param string $fragment Optional #fragment.
 * @return string Empty string when the page does not exist.
 */
function kms_page_url( $key, $fragment = '' ) {
	$slugs = array(
		'about'        => array( 'about-us', 'about' ),
		'contact'      => array( 'contact-us', 'contact' ),
		'pricing'      => array( 'pricing' ),
		'faq'          => array( 'faq' ),
		'reviews'      => array( 'hear-from-our-attendees', 'reviews', 'testimonials' ),
		'refund'       => array( 'refund-policy', 'refund-and-cancellation-policy' ),
		'privacy'      => array( 'privacy-policy' ),
		'terms'        => array( 'terms-and-conditions', 'terms' ),
		'performances' => array( 'performances', 'live-performances', 'grand-music-concerts-stage-programs' ),
	);

	$url = '';
	$id  = (int) kms_fact( 'page_' . $key );
	if ( $id && 'publish' === get_post_status( $id ) ) {
		$url = get_permalink( $id );
	} elseif ( 'privacy' === $key && get_privacy_policy_url() ) {
		$url = get_privacy_policy_url();
	} elseif ( isset( $slugs[ $key ] ) ) {
		foreach ( $slugs[ $key ] as $slug ) {
			$page = get_page_by_path( $slug );
			if ( $page && 'publish' === $page->post_status ) {
				$url = get_permalink( $page );
				break;
			}
		}
	}

	if ( $url && $fragment ) {
		$url .= '#' . ltrim( $fragment, '#' );
	}
	return (string) $url;
}

/**
 * Where "Book a free consultation" buttons point: the enquiry form.
 *
 * @return string
 */
function kms_enquiry_url() {
	$url = kms_page_url( 'contact', 'enquire' );
	return $url ? $url : home_url( '/#enquire' );
}

/**
 * Retreat batches parsed from the settings textarea.
 *
 * @param bool $upcoming_only Hide batches whose end date has passed.
 * @return array<int, array<string, mixed>>
 */
function kms_retreat_batches( $upcoming_only = true ) {
	$batches = array();
	$today   = wp_date( 'Y-m-d' );
	foreach ( kms_fact_lines( 'retreat_batches' ) as $line ) {
		$cols = array_map( 'trim', explode( '|', $line ) );
		if ( count( $cols ) < 3 || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $cols[1] ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $cols[2] ) ) {
			continue;
		}
		$batch = array(
			'name'        => $cols[0],
			'start'       => $cols[1],
			'end'         => $cols[2],
			'price'       => isset( $cols[3] ) ? (int) preg_replace( '/\D+/', '', $cols[3] ) : 0,
			'url'         => isset( $cols[4] ) && '' !== $cols[4] ? kms_absolute_url( $cols[4] ) : '',
			'place'       => isset( $cols[5] ) ? $cols[5] : '',
			'description' => isset( $cols[6] ) ? $cols[6] : '',
		);
		if ( $upcoming_only && $batch['end'] < $today ) {
			continue;
		}
		$batches[] = $batch;
	}
	usort(
		$batches,
		static function ( $a, $b ) {
			return strcmp( $a['start'], $b['start'] );
		}
	);
	return $batches;
}

/**
 * Performance types from KMS Facts.
 *
 * @return array<int, array{name:string, url:string, description:string, price:int}>
 */
function kms_performances() {
	$items = array();
	foreach ( kms_fact_lines( 'performances' ) as $line ) {
		$cols = array_map( 'trim', explode( '|', $line ) );
		if ( '' === $cols[0] ) {
			continue;
		}
		$items[] = array(
			'name'        => $cols[0],
			'url'         => isset( $cols[1] ) && '' !== $cols[1] ? kms_absolute_url( $cols[1] ) : '',
			'description' => isset( $cols[2] ) ? $cols[2] : '',
			'price'       => isset( $cols[3] ) ? (int) preg_replace( '/\D+/', '', $cols[3] ) : 0,
		);
	}
	return $items;
}

/**
 * Turn a site-relative path into an absolute URL.
 *
 * @param string $url Absolute URL or /path/.
 * @return string
 */
function kms_absolute_url( $url ) {
	return 0 === strpos( $url, '/' ) ? home_url( $url ) : esc_url_raw( $url );
}

/**
 * Human date range, e.g. "11–18 Nov 2026" or "28 Dec 2026 – 4 Jan 2027".
 *
 * @param string $start Y-m-d.
 * @param string $end   Y-m-d.
 * @return string
 */
function kms_date_range( $start, $end ) {
	$s = strtotime( $start . ' 12:00:00' );
	$e = strtotime( $end . ' 12:00:00' );
	if ( gmdate( 'Y-m', $s ) === gmdate( 'Y-m', $e ) ) {
		return gmdate( 'j', $s ) . '–' . gmdate( 'j M Y', $e );
	}
	if ( gmdate( 'Y', $s ) === gmdate( 'Y', $e ) ) {
		return gmdate( 'j M', $s ) . ' – ' . gmdate( 'j M Y', $e );
	}
	return gmdate( 'j M Y', $s ) . ' – ' . gmdate( 'j M Y', $e );
}

/**
 * Image markup for a fact holding a Media Library URL (uses srcset when the URL is an attachment).
 *
 * @param string $key   Fact key.
 * @param string $size  Image size.
 * @param array  $attrs Extra attributes.
 * @return string
 */
function kms_fact_image( $key, $size = 'large', $attrs = array() ) {
	$url = kms_fact( $key );
	if ( '' === $url ) {
		return '';
	}
	$id = attachment_url_to_postid( $url );
	if ( $id ) {
		// Alt text set in the Media Library describes the actual photo, so it wins over a generated one.
		if ( '' !== trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) ) {
			unset( $attrs['alt'] );
		}
		return wp_get_attachment_image( $id, $size, false, $attrs );
	}
	$attr_html = '';
	foreach ( wp_parse_args( $attrs, array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ) as $name => $value ) {
		$attr_html .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
	}
	return '<img src="' . esc_url( $url ) . '"' . $attr_html . '>';
}

/* -------------------------------------------------------------------------
 * Settings screen: Settings → KMS Facts
 * ---------------------------------------------------------------------- */

/**
 * Register the settings page.
 */
function kms_facts_admin_menu() {
	add_options_page(
		__( 'KMS Facts', 'kms-core' ),
		__( 'KMS Facts', 'kms-core' ),
		'manage_options',
		'kms-facts',
		'kms_facts_render_page'
	);
}
add_action( 'admin_menu', 'kms_facts_admin_menu' );

/**
 * Register the option with sanitisation.
 */
function kms_facts_register_setting() {
	register_setting(
		'kms_facts_group',
		KMS_FACTS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kms_facts_sanitize',
			'default'           => array(),
		)
	);
}
add_action( 'init', 'kms_facts_register_setting' );

/**
 * Sanitise submitted facts by field type.
 *
 * @param mixed $input Raw input.
 * @return array<string, string>
 */
function kms_facts_sanitize( $input ) {
	$input = is_array( $input ) ? $input : array();
	// The settings screen submits every field (unticked checkboxes are absent, so they mean "off").
	// Programmatic updates (importer, WP-CLI) send only some keys and must not wipe the others.
	$from_form = ! empty( $input['_form'] );
	$saved     = get_option( KMS_FACTS_OPTION, array() );
	$clean     = ( $from_form || ! is_array( $saved ) ) ? array() : $saved;
	foreach ( kms_facts_schema() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$type = $field[1];
			if ( ! array_key_exists( $key, $input ) && ! ( $from_form && 'checkbox' === $type ) ) {
				continue;
			}
			$value = isset( $input[ $key ] ) ? $input[ $key ] : ''; // options.php has already unslashed.
			switch ( $type ) {
				case 'checkbox':
					$clean[ $key ] = empty( $value ) ? '0' : '1';
					break;
				case 'textarea':
					$clean[ $key ] = sanitize_textarea_field( $value );
					break;
				case 'url':
					$clean[ $key ] = esc_url_raw( trim( $value ) );
					break;
				case 'email':
					$clean[ $key ] = sanitize_email( $value );
					break;
				case 'number':
					$clean[ $key ] = is_numeric( $value ) ? (string) ( 0 + $value ) : '';
					break;
				case 'page':
					$clean[ $key ] = (string) absint( $value );
					break;
				default:
					$clean[ $key ] = sanitize_text_field( $value );
			}
		}
	}
	return $clean;
}

/**
 * Render the settings page.
 */
function kms_facts_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$facts = kms_facts();
	?>
	<div class="wrap kms-facts">
		<h1><?php esc_html_e( 'KMS Facts — one place for every business fact', 'kms-core' ); ?></h1>
		<p class="description" style="max-width:780px">
			<?php esc_html_e( 'Everything here appears on the website, in Google structured data and in /llms.txt for AI assistants. Change a value once and it updates everywhere. Text fields may use the placeholders {name}, {founder}, {founding_year}, {years}, {students}, {countries} and {city}.', 'kms-core' ); ?>
		</p>
		<?php kms_health_render_panel(); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'kms_facts_group' ); ?>
			<input type="hidden" name="<?php echo esc_attr( KMS_FACTS_OPTION ); ?>[_form]" value="1">
			<?php foreach ( kms_facts_schema() as $section_key => $section ) : ?>
				<h2 class="title" id="kms-<?php echo esc_attr( $section_key ); ?>"><?php echo esc_html( $section['title'] ); ?></h2>
				<table class="form-table" role="presentation">
					<?php
					foreach ( $section['fields'] as $key => $field ) :
						list( $label, $type ) = $field;
						$help                 = isset( $field[3] ) ? $field[3] : '';
						$name                 = KMS_FACTS_OPTION . '[' . $key . ']';
						$id                   = 'kms-fact-' . $key;
						$value                = isset( $facts[ $key ] ) ? $facts[ $key ] : '';
						?>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
							<td>
								<?php if ( 'textarea' === $type ) : ?>
									<textarea class="large-text code" rows="<?php echo 'retreat_batches' === $key ? 6 : 4; ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
								<?php elseif ( 'checkbox' === $type ) : ?>
									<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( '1', $value ); ?>>
								<?php elseif ( 'page' === $type ) : ?>
									<?php
									wp_dropdown_pages(
										array(
											'name'              => esc_attr( $name ),
											'id'                => esc_attr( $id ),
											'selected'          => (int) $value,
											'show_option_none'  => esc_html__( '— Detect automatically —', 'kms-core' ),
											'option_none_value' => '0',
										)
									);
									?>
								<?php else : ?>
									<?php $input_type = in_array( $type, array( 'number', 'url', 'email' ), true ) ? $type : 'text'; ?>
									<input class="regular-text" type="<?php echo esc_attr( $input_type ); ?>" <?php echo 'number' === $input_type ? 'step="any"' : ''; ?> id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
								<?php endif; ?>
								<?php if ( $help ) : ?>
									<p class="description"><?php echo esc_html( $help ); ?></p>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endforeach; ?>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Plain text: no tags, entities decoded (for JSON-LD, llms.txt, emails).
 *
 * @param string $text Text.
 * @return string
 */
function kms_plain( $text ) {
	return html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}
