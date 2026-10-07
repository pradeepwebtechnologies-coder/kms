<?php
/**
 * Structured data: one connected JSON-LD graph per page.
 *
 * Organization ↔ founder (Person) ↔ Course ↔ Offer ↔ EducationEvent, linked by @id,
 * built only from KMS Facts and class fields so it can never disagree with the page.
 * No AggregateRating: Google does not allow self-serving review markup for a
 * business's own reviews, so ratings are shown as text with links instead.
 *
 * Printed in the footer so FAQs rendered anywhere on the page are included.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether KMS outputs the graph (and silences other JSON-LD).
 *
 * @return bool
 */
function kms_schema_enabled() {
	return kms_fact_on( 'schema_enabled' );
}

/**
 * Silence SEO plugins' JSON-LD so there is exactly one graph.
 */
function kms_schema_silence_others() {
	if ( ! kms_schema_enabled() ) {
		return;
	}
	// Rank Math: returning an empty array prints nothing.
	add_filter(
		'rank_math/json_ld',
		static function () {
			return array();
		},
		PHP_INT_MAX
	);
	// Yoast SEO.
	add_filter( 'wpseo_json_ld_output', '__return_false', PHP_INT_MAX );
	// All in One SEO.
	add_filter( 'aioseo_schema_disable', '__return_true', PHP_INT_MAX );
}
add_action( 'init', 'kms_schema_silence_others', 1 );

/**
 * Remove JSON-LD blocks pasted into post content (old AggregateRating, broken blocks).
 *
 * The non-greedy match runs to the first closing tag, which also removes the
 * malformed nested block on the old homepage.
 *
 * @param string $content Post content.
 * @return string
 */
function kms_strip_content_jsonld( $content ) {
	if ( ! kms_fact_on( 'strip_content_ld' ) || false === stripos( $content, 'ld+json' ) ) {
		return $content;
	}
	return (string) preg_replace( '#<script\b[^>]*application/ld\+json[^>]*>.*?</script>#is', '', $content );
}
add_filter( 'the_content', 'kms_strip_content_jsonld', 999 );

/**
 * Stable node IDs.
 *
 * @param string $node organization|website|founder|logo.
 * @return string
 */
function kms_schema_id( $node ) {
	return home_url( '/#' . $node );
}

/**
 * ImageObject for a URL, with real dimensions when it is a Media Library file.
 *
 * @param string $url Image URL.
 * @param string $id  Optional @id.
 * @return array|string
 */
function kms_schema_image( $url, $id = '' ) {
	if ( ! $url ) {
		return '';
	}
	$image = array(
		'@type' => 'ImageObject',
		'url'   => $url,
	);
	if ( $id ) {
		$image['@id'] = $id;
	}
	$attachment = attachment_url_to_postid( $url );
	if ( $attachment ) {
		$meta = wp_get_attachment_metadata( $attachment );
		if ( ! empty( $meta['width'] ) ) {
			$image['width']  = (int) $meta['width'];
			$image['height'] = (int) $meta['height'];
		}
	}
	return $image;
}

/**
 * Featured image URL of a post or the default share image.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function kms_post_image_url( $post_id = 0 ) {
	if ( $post_id && has_post_thumbnail( $post_id ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
		if ( $src ) {
			return $src[0];
		}
	}
	return kms_fact( 'default_image' );
}

/**
 * Comma-separated fact → array.
 *
 * @param string $value Comma-separated text.
 * @return string[]
 */
function kms_csv( $value ) {
	return array_values( array_filter( array_map( 'trim', explode( ',', (string) $value ) ) ) );
}

/**
 * Language names → BCP 47 codes where known.
 *
 * @return string[]
 */
function kms_language_codes() {
	$map = array(
		'english' => 'en',
		'hindi'   => 'hi',
	);
	$out = array();
	foreach ( kms_csv( kms_fact( 'languages' ) ) as $lang ) {
		$key   = strtolower( $lang );
		$out[] = isset( $map[ $key ] ) ? $map[ $key ] : $lang;
	}
	return $out;
}

/**
 * Postal address node.
 *
 * @return array
 */
function kms_schema_address() {
	return array_filter(
		array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => kms_fact( 'street' ),
			'addressLocality' => kms_fact( 'locality' ),
			'addressRegion'   => kms_fact( 'region' ),
			'postalCode'      => kms_fact( 'postal_code' ),
			'addressCountry'  => kms_fact( 'country_code' ),
		)
	);
}

/**
 * Price range across classes and retreats, e.g. "₹1,350–₹30,000".
 *
 * @return string
 */
function kms_price_range() {
	$prices = array();
	foreach ( kms_get_courses() as $course ) {
		foreach ( kms_course_tiers( $course->ID ) as $tier ) {
			$prices[] = $tier['per_class'];
			$prices[] = $tier['inr'];
		}
	}
	foreach ( kms_retreat_batches() as $batch ) {
		if ( $batch['price'] ) {
			$prices[] = $batch['price'];
		}
	}
	$prices = array_filter( $prices );
	if ( ! $prices ) {
		return '';
	}
	return kms_format_inr( min( $prices ) ) . '–' . kms_format_inr( max( $prices ) );
}

/**
 * The school.
 *
 * @return array
 */
function kms_schema_organization() {
	$days    = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );
	$catalog = array();
	foreach ( kms_get_courses() as $course ) {
		$catalog[] = array(
			'@type'       => 'Offer',
			'itemOffered' => array( '@id' => get_permalink( $course ) . '#course' ),
		);
	}

	$org = array(
		'@type'                     => array( 'EducationalOrganization', 'LocalBusiness' ),
		'@id'                       => kms_schema_id( 'organization' ),
		'name'                      => kms_fact( 'name' ),
		'alternateName'             => kms_fact( 'alternate_name' ),
		'url'                       => home_url( '/' ),
		'description'               => kms_fact_text( 'description' ),
		'slogan'                    => kms_fact_text( 'tagline' ),
		'foundingDate'              => kms_fact( 'founding_year' ),
		'founder'                   => array( '@id' => kms_schema_id( 'founder' ) ),
		'logo'                      => kms_schema_image( kms_fact( 'logo' ), kms_schema_id( 'logo' ) ),
		'image'                     => kms_fact( 'default_image' ),
		'telephone'                 => kms_fact( 'phone_e164' ),
		'email'                     => kms_fact( 'email' ),
		'address'                   => kms_schema_address(),
		'hasMap'                    => kms_fact( 'map_url' ),
		'areaServed'                => 'Worldwide',
		'knowsAbout'                => kms_csv( kms_fact( 'founder_knows' ) ),
		'paymentAccepted'           => kms_fact( 'payment_methods' ),
		'priceRange'                => kms_price_range(),
		'sameAs'                    => kms_same_as(),
		'contactPoint'              => array(
			array_filter(
				array(
					'@type'             => 'ContactPoint',
					'contactType'       => 'admissions',
					'telephone'         => kms_fact( 'phone_e164' ),
					'email'             => kms_fact( 'email' ),
					'availableLanguage' => kms_csv( kms_fact( 'languages' ) ),
					'areaServed'        => 'Worldwide',
				)
			),
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => $days,
				'opens'     => kms_fact( 'opens', '09:00' ),
				'closes'    => kms_fact( 'closes', '21:00' ),
			),
		),
	);

	if ( kms_fact( 'lat' ) && kms_fact( 'lng' ) ) {
		$org['geo'] = array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => (float) kms_fact( 'lat' ),
			'longitude' => (float) kms_fact( 'lng' ),
		);
	}
	if ( $catalog ) {
		$org['hasOfferCatalog'] = array(
			'@type'           => 'OfferCatalog',
			'name'            => __( 'Online Indian music classes', 'kms-core' ),
			'itemListElement' => $catalog,
		);
	}
	return array_filter( $org );
}

/**
 * The website.
 *
 * @return array
 */
function kms_schema_website() {
	return array_filter(
		array(
			'@type'         => 'WebSite',
			'@id'           => kms_schema_id( 'website' ),
			'url'           => home_url( '/' ),
			'name'          => kms_fact( 'name' ),
			'alternateName' => kms_fact( 'alternate_name' ),
			'publisher'     => array( '@id' => kms_schema_id( 'organization' ) ),
			'inLanguage'    => get_bloginfo( 'language' ),
		)
	);
}

/**
 * The founder and lead teacher.
 *
 * @return array
 */
function kms_schema_founder() {
	$person = array(
		'@type'       => 'Person',
		'@id'         => kms_schema_id( 'founder' ),
		'name'        => kms_fact( 'founder_name' ),
		'jobTitle'    => kms_fact( 'founder_title' ),
		'description' => kms_fact_text( 'founder_bio' ),
		'image'       => kms_fact( 'founder_image' ),
		'url'         => kms_page_url( 'about' ) ? kms_page_url( 'about' ) : home_url( '/' ),
		'worksFor'    => array( '@id' => kms_schema_id( 'organization' ) ),
		'knowsAbout'  => kms_csv( kms_fact( 'founder_knows' ) ),
	);
	$school = kms_fact( 'founder_education' );
	if ( $school ) {
		$person['alumniOf'] = array(
			'@type' => 'CollegeOrUniversity',
			'name'  => $school,
		);
		if ( kms_fact( 'founder_credential' ) ) {
			$person['hasCredential'] = array(
				'@type'              => 'EducationalOccupationalCredential',
				'credentialCategory' => 'degree',
				'name'               => kms_fact( 'founder_credential' ),
				'recognizedBy'       => array(
					'@type' => 'CollegeOrUniversity',
					'name'  => $school,
				),
			);
		}
	}
	return array_filter( $person );
}

/**
 * Breadcrumb trail for the current request: [ [ name, url ], … ].
 *
 * Shared by the visible breadcrumbs (theme) and the BreadcrumbList node.
 *
 * @return array<int, array{0:string,1:string}>
 */
function kms_breadcrumb_trail() {
	if ( is_front_page() ) {
		return array();
	}
	$trail = array( array( __( 'Home', 'kms-core' ), home_url( '/' ) ) );

	if ( is_singular( 'kms_course' ) || is_post_type_archive( 'kms_course' ) ) {
		$trail[] = array( __( 'Online classes', 'kms-core' ), (string) get_post_type_archive_link( 'kms_course' ) );
		if ( is_singular() ) {
			$trail[] = array( get_the_title(), get_permalink() );
		}
		return $trail;
	}

	$blog_id = (int) get_option( 'page_for_posts' );
	if ( is_singular( 'post' ) || is_home() || is_category() || is_tag() ) {
		if ( $blog_id ) {
			$trail[] = array( get_the_title( $blog_id ), get_permalink( $blog_id ) );
		}
		if ( is_singular( 'post' ) ) {
			$cats = get_the_category();
			if ( $cats ) {
				$trail[] = array( $cats[0]->name, get_category_link( $cats[0] ) );
			}
			$trail[] = array( get_the_title(), get_permalink() );
		} elseif ( is_category() || is_tag() ) {
			$term    = get_queried_object();
			$trail[] = array( $term->name, get_term_link( $term ) );
		}
		return $trail;
	}

	if ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$trail[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$trail[] = array( get_the_title(), get_permalink() );
		return $trail;
	}

	if ( is_search() ) {
		$trail[] = array( __( 'Search results', 'kms-core' ), '' );
	} elseif ( is_404() ) {
		$trail[] = array( __( 'Page not found', 'kms-core' ), '' );
	} elseif ( is_singular() ) {
		$trail[] = array( get_the_title(), get_permalink() );
	}
	return $trail;
}

/**
 * Canonical URL of the current request (for @id values).
 *
 * @return string
 */
function kms_current_url() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		return (string) get_permalink();
	}
	if ( is_post_type_archive( 'kms_course' ) ) {
		return (string) get_post_type_archive_link( 'kms_course' );
	}
	if ( is_home() && get_option( 'page_for_posts' ) ) {
		return (string) get_permalink( (int) get_option( 'page_for_posts' ) );
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? '' : $link;
	}
	return '';
}

/**
 * Build the full graph for the current request.
 *
 * @return array
 */
function kms_schema_graph() {
	$graph = array( kms_schema_organization(), kms_schema_website(), kms_schema_founder() );
	$url   = kms_current_url();
	if ( ! $url || is_404() || is_search() ) {
		return $graph;
	}

	$page_id    = $url . '#webpage';
	$about_id   = (int) url_to_postid( kms_page_url( 'about' ) );
	$contact_id = (int) url_to_postid( kms_page_url( 'contact' ) );
	$type       = 'WebPage';
	if ( is_post_type_archive( 'kms_course' ) || is_home() || is_category() || is_tag() ) {
		$type = 'CollectionPage';
	} elseif ( $about_id && is_page( $about_id ) ) {
		$type = 'AboutPage';
	} elseif ( $contact_id && is_page( $contact_id ) ) {
		$type = 'ContactPage';
	}

	$webpage = array(
		'@type'      => $type,
		'@id'        => $page_id,
		'url'        => $url,
		'name'       => wp_get_document_title(),
		'isPartOf'   => array( '@id' => kms_schema_id( 'website' ) ),
		'inLanguage' => get_bloginfo( 'language' ),
	);
	if ( is_front_page() || 'AboutPage' === $type ) {
		$webpage['about'] = array( '@id' => kms_schema_id( 'organization' ) );
	}
	if ( is_singular() ) {
		$post_id                         = get_queried_object_id();
		$webpage['datePublished']        = get_the_date( 'c', $post_id );
		$webpage['dateModified']         = get_the_modified_date( 'c', $post_id );
		$webpage['primaryImageOfPage']   = kms_schema_image( kms_post_image_url( $post_id ) );
	} else {
		$webpage['primaryImageOfPage'] = kms_schema_image( kms_fact( 'default_image' ) );
	}

	$trail = kms_breadcrumb_trail();
	if ( count( $trail ) > 1 ) {
		$items = array();
		foreach ( $trail as $i => $crumb ) {
			$item = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $crumb[0] ),
			);
			if ( $crumb[1] ) {
				$item['item'] = $crumb[1];
			}
			$items[] = $item;
		}
		$graph[]               = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $url . '#breadcrumb',
			'itemListElement' => $items,
		);
		$webpage['breadcrumb'] = array( '@id' => $url . '#breadcrumb' );
	}

	// Class hub: list of classes (eligible for Google's course list).
	if ( is_post_type_archive( 'kms_course' ) ) {
		$items = array();
		foreach ( kms_get_courses() as $i => $course ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'url'      => get_permalink( $course ),
				'name'     => get_the_title( $course ),
			);
		}
		if ( $items ) {
			$webpage['mainEntity'] = array(
				'@type'           => 'ItemList',
				'itemListElement' => $items,
			);
		}
	}

	if ( is_singular( 'kms_course' ) ) {
		$graph[]                = kms_schema_course( get_queried_object_id() );
		$webpage['mainEntity'] = array( '@id' => $url . '#course' );
	}

	if ( is_singular( 'post' ) ) {
		$graph[] = kms_schema_article( get_queried_object_id(), $page_id );
	}

	foreach ( kms_retreat_batches() as $batch ) {
		if ( $batch['url'] && untrailingslashit( $batch['url'] ) === untrailingslashit( $url ) ) {
			$graph[] = kms_schema_event( $batch );
		}
	}

	$faqs = kms_rendered( 'faq' );
	if ( $faqs ) {
		$questions = array();
		foreach ( $faqs as $faq ) {
			$questions[] = array(
				'@type'          => 'Question',
				'name'           => $faq['q'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $faq['a'],
				),
			);
		}
		$graph[] = array(
			'@type'      => 'FAQPage',
			'@id'        => $url . '#faq',
			'isPartOf'   => array( '@id' => $page_id ),
			'mainEntity' => $questions,
		);
	}

	$graph[] = array_filter( $webpage );
	return $graph;
}

/**
 * Course node with offers and course instances.
 *
 * @param int $post_id Course ID.
 * @return array
 */
function kms_schema_course( $post_id ) {
	$url    = get_permalink( $post_id );
	$offers = array();
	foreach ( kms_course_tiers( $post_id ) as $tier ) {
		$offers[] = array(
			'@type'         => 'Offer',
			'name'          => $tier['label'],
			'category'      => 'Paid',
			'price'         => $tier['inr'],
			'priceCurrency' => 'INR',
			'availability'  => 'https://schema.org/InStock',
			'url'           => $url,
		);
	}

	$instances = array();
	$level     = (string) kms_meta( $post_id, 'kms_level' );
	if ( kms_course_has_mode( $post_id, 'online' ) ) {
		$schedule = array(
			'@type'           => 'Schedule',
			'duration'        => 'PT' . kms_course_minutes( $post_id ) . 'M',
			'repeatFrequency' => 'Weekly',
		);
		if ( (int) kms_meta( $post_id, 'kms_price_pack10' ) ) {
			$schedule['repeatCount'] = 10;
		}
		$instances[] = array(
			'@type'          => 'CourseInstance',
			'courseMode'     => 'Online',
			'courseSchedule' => $schedule,
			'instructor'     => array( '@id' => kms_schema_id( 'founder' ) ),
			'inLanguage'     => kms_language_codes(),
		);
	}
	if ( kms_course_has_mode( $post_id, 'inperson' ) ) {
		$instances[] = array(
			'@type'          => 'CourseInstance',
			'courseMode'     => 'Onsite',
			'courseSchedule' => array(
				'@type'           => 'Schedule',
				'duration'        => 'PT' . (int) kms_fact( 'inperson_minutes', '60' ) . 'M',
				'repeatFrequency' => 'Daily',
			),
			'location'       => array(
				'@type'   => 'Place',
				'name'    => kms_fact( 'name' ),
				'address' => kms_schema_address(),
			),
			'instructor'     => array( '@id' => kms_schema_id( 'founder' ) ),
		);
	}

	return array_filter(
		array(
			'@type'               => 'Course',
			'@id'                 => $url . '#course',
			'name'                => get_the_title( $post_id ),
			'description'         => kms_course_answer( $post_id ) ? kms_course_answer( $post_id ) : wp_strip_all_tags( (string) kms_meta( $post_id, 'kms_tagline' ) ),
			'url'                 => $url,
			'image'               => kms_post_image_url( $post_id ),
			'provider'            => array( '@id' => kms_schema_id( 'organization' ) ),
			'inLanguage'          => kms_language_codes(),
			'educationalLevel'    => $level,
			'coursePrerequisites' => false !== stripos( $level, 'beginner' ) ? __( 'None — complete beginners are welcome.', 'kms-core' ) : '',
			'teaches'             => kms_csv( kms_meta( $post_id, 'kms_teaches' ) ),
			'isAccessibleForFree' => false,
			'offers'              => $offers,
			'hasCourseInstance'   => $instances,
		)
	);
}

/**
 * Blog post node.
 *
 * @param int    $post_id Post ID.
 * @param string $page_id WebPage @id.
 * @return array
 */
function kms_schema_article( $post_id, $page_id ) {
	$post  = get_post( $post_id );
	$cats  = wp_list_pluck( get_the_category( $post_id ), 'name' );
	$words = str_word_count( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );

	/**
	 * Author of blog posts. Defaults to the founder: the school's articles are written
	 * from the founder's teaching. Filter to return a different Person node.
	 */
	$author = apply_filters( 'kms_schema_article_author', array( '@id' => kms_schema_id( 'founder' ) ), $post );

	return array_filter(
		array(
			'@type'            => 'BlogPosting',
			'@id'              => get_permalink( $post_id ) . '#article',
			'headline'         => mb_substr( wp_strip_all_tags( get_the_title( $post_id ) ), 0, 110 ),
			'description'      => has_excerpt( $post_id ) ? wp_strip_all_tags( get_the_excerpt( $post_id ) ) : '',
			'datePublished'    => get_the_date( 'c', $post_id ),
			'dateModified'     => get_the_modified_date( 'c', $post_id ),
			'author'           => $author,
			'publisher'        => array( '@id' => kms_schema_id( 'organization' ) ),
			'mainEntityOfPage' => array( '@id' => $page_id ),
			'image'            => kms_schema_image( kms_post_image_url( $post_id ) ),
			'articleSection'   => $cats,
			'wordCount'        => $words,
			'inLanguage'       => get_bloginfo( 'language' ),
		)
	);
}

/**
 * Retreat batch as an EducationEvent.
 *
 * @param array $batch Batch from kms_retreat_batches().
 * @return array
 */
function kms_schema_event( $batch ) {
	$event = array(
		'@type'               => 'EducationEvent',
		'@id'                 => $batch['url'] . '#batch-' . $batch['start'],
		'name'                => $batch['name'] . ' (' . kms_date_range( $batch['start'], $batch['end'] ) . ')',
		'description'         => $batch['description'],
		'startDate'           => $batch['start'],
		'endDate'             => $batch['end'],
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'url'                 => $batch['url'],
		'image'               => array( kms_fact( 'default_image' ) ),
		'location'            => array(
			'@type'   => 'Place',
			'name'    => $batch['place'] ? kms_fact( 'name' ) . ', ' . $batch['place'] : kms_fact( 'name' ),
			'address' => kms_schema_address(),
		),
		'organizer'           => array( '@id' => kms_schema_id( 'organization' ) ),
		'performer'           => array( '@id' => kms_schema_id( 'founder' ) ),
	);
	if ( false === stripos( $batch['place'], kms_fact( 'locality' ) ) && $batch['place'] ) {
		// A retreat held elsewhere (e.g. the Himalayas): use the place name, not the Pushkar address.
		$event['location'] = array(
			'@type'   => 'Place',
			'name'    => $batch['place'],
			'address' => array(
				'@type'           => 'PostalAddress',
				'addressLocality' => $batch['place'],
				'addressCountry'  => kms_fact( 'country_code' ),
			),
		);
	}
	if ( $batch['price'] ) {
		$event['offers'] = array(
			'@type'         => 'Offer',
			'price'         => $batch['price'],
			'priceCurrency' => 'INR',
			'availability'  => 'https://schema.org/InStock',
			'url'           => $batch['url'],
		);
	}
	return array_filter( $event );
}

/**
 * Decode HTML entities in every string of the graph (titles arrive as "Bhajan &#038; Kirtan").
 *
 * @param mixed $value Graph or value.
 * @return mixed
 */
function kms_schema_plain( $value ) {
	if ( is_array( $value ) ) {
		return array_map( 'kms_schema_plain', $value );
	}
	return is_string( $value ) ? kms_plain( $value ) : $value;
}

/**
 * Print the graph.
 */
function kms_schema_print() {
	if ( ! kms_schema_enabled() || is_admin() || is_feed() || is_robots() ) {
		return;
	}
	$graph = kms_schema_plain( array_values( array_filter( kms_schema_graph() ) ) );
	$json  = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | ( defined( 'WP_DEBUG' ) && WP_DEBUG ? JSON_PRETTY_PRINT : 0 )
	);
	if ( ! $json ) {
		return;
	}
	echo "\n<script type=\"application/ld+json\" class=\"kms-schema\">" . str_replace( '</', '<\/', $json ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded, closing tags neutralised.
}
add_action( 'wp_footer', 'kms_schema_print', 20 );
