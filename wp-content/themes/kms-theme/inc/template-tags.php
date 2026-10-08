<?php
/**
 * Template helpers. Every KMS Core call is guarded so the theme still renders
 * (without classes, prices or forms) if the plugin is inactive.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon from the KMS Core library.
 *
 * @param string $name  Icon.
 * @param string $label Accessible label.
 * @param string $class Extra class.
 * @return string
 */
function km_icon( $name, $label = '', $class = '' ) {
	return function_exists( 'kms_icon' ) ? kms_icon( $name, $label, $class ) : '';
}

/**
 * Fact from KMS Core.
 *
 * @param string $key     Key.
 * @param string $default Default.
 * @return string
 */
function km_fact( $key, $default = '' ) {
	return function_exists( 'kms_fact' ) ? kms_fact( $key, $default ) : $default;
}

/**
 * Where "free consultation" buttons go.
 *
 * @return string
 */
function km_cta_url() {
	if ( function_exists( 'kms_enquiry_url' ) ) {
		return kms_enquiry_url();
	}
	$contact = get_page_by_path( 'contact-us' );
	return $contact ? get_permalink( $contact ) : home_url( '/' );
}

/**
 * WhatsApp link.
 *
 * @param string $message Prefilled text.
 * @return string
 */
function km_whatsapp_url( $message = '' ) {
	return function_exists( 'kms_whatsapp_url' ) ? kms_whatsapp_url( $message ) : '';
}

/**
 * Print a WhatsApp button (if a number is set).
 *
 * @param string $message Prefilled text.
 * @param string $label   Label.
 * @param string $class   Extra classes.
 */
function km_whatsapp_button( $message = '', $label = '', $class = '' ) {
	if ( function_exists( 'kms_whatsapp_button' ) ) {
		echo kms_whatsapp_button( $message, $label, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in KMS Core.
	}
}

/**
 * Site logo: Customizer logo, else the logo URL from KMS Facts, else the site name.
 *
 * @param bool $in_footer Footer copy: lazy-loaded, not a priority download.
 */
function km_logo( $in_footer = false ) {
	$name     = km_fact( 'name', get_bloginfo( 'name' ) );
	$priority = $in_footer
		? array(
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
		: array(
			'loading'       => 'eager',
			'fetchpriority' => 'high',
		);
	echo '<a class="km-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home">';
	if ( has_custom_logo() ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		echo wp_get_attachment_image(
			$logo_id,
			'medium',
			false,
			array_merge(
				array(
					'alt'   => $name,
					'class' => 'km-logo__img',
				),
				$priority
			)
		); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} elseif ( km_fact( 'logo' ) ) {
		$url = km_fact( 'logo' );
		$id  = attachment_url_to_postid( $url );
		if ( $id ) {
			echo wp_get_attachment_image(
				$id,
				'medium',
				false,
				array_merge(
					array(
						'alt'   => $name,
						'class' => 'km-logo__img',
					),
					$priority
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo '<img class="km-logo__img" src="' . esc_url( $url ) . '" alt="' . esc_attr( $name ) . '" width="500" height="141"' . ( $in_footer ? ' loading="lazy"' : ' fetchpriority="high"' ) . '>';
		}
	} else {
		echo '<span class="km-logo__text">' . esc_html( $name ) . '</span>';
	}
	echo '</a>';
}

/**
 * Hero image attachment ID (Customizer choice, else the default share image from KMS Facts).
 *
 * @return int
 */
function km_hero_image_id() {
	$id = (int) get_theme_mod( 'km_hero_image' );
	if ( $id ) {
		return $id;
	}
	$url = km_fact( 'default_image', km_fact( 'founder_image' ) );
	return $url ? (int) attachment_url_to_postid( $url ) : 0;
}

/**
 * Hero image markup.
 */
function km_hero_image() {
	$id  = km_hero_image_id();
	$url = $id ? (string) wp_get_attachment_url( $id ) : km_fact( 'default_image', km_fact( 'founder_image' ) );
	// Alt text: the Customizer, else the Media Library. The built-in description fits the school's rooftop photo only.
	$alt = (string) get_theme_mod( 'km_hero_image_alt', '' );
	if ( '' === $alt && $id ) {
		$alt = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
	}
	if ( '' === $alt ) {
		$alt = false !== strpos( $url, 'IMG_20230826_14451146' )
			/* translators: %s: founder */
			? sprintf( __( '%s on a rooftop in Pushkar, with temple spires behind', 'kms-theme' ), km_fact( 'founder_name', '' ) )
			: km_fact( 'name', get_bloginfo( 'name' ) );
	}
	$args = array(
		'class'         => 'km-hero__img',
		'alt'           => $alt,
		'loading'       => 'eager',
		'fetchpriority' => 'high',
		'sizes'         => '(min-width: 960px) 520px, 100vw',
	);
	if ( $id ) {
		echo wp_get_attachment_image( $id, 'large', false, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}
	if ( $url ) {
		echo '<img class="km-hero__img" src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" width="1600" height="900" fetchpriority="high">';
	}
}

/**
 * Breadcrumbs (shared trail with the BreadcrumbList schema).
 */
function km_breadcrumbs() {
	if ( ! function_exists( 'kms_breadcrumb_trail' ) ) {
		return;
	}
	$trail = kms_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return;
	}
	echo '<nav class="km-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'kms-theme' ) . '"><ol>';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		echo '<li>';
		if ( $i === $last || ! $crumb[1] ) {
			echo '<span aria-current="page">' . esc_html( wp_strip_all_tags( $crumb[0] ) ) . '</span>';
		} else {
			echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( wp_strip_all_tags( $crumb[0] ) ) . '</a>';
		}
		echo '</li>';
	}
	echo '</ol></nav>';
}

/**
 * Section heading block.
 *
 * @param string $eyebrow Small label above.
 * @param string $title   H2 text.
 * @param string $intro   Optional intro.
 * @param string $id      Heading ID for aria-labelledby.
 */
function km_section_header( $eyebrow, $title, $intro = '', $id = '' ) {
	echo '<header class="km-section__head">';
	if ( $eyebrow ) {
		echo '<p class="km-eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	echo '<h2 class="km-section__title"' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . '>' . km_highlight( $title ) . '</h2>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in km_highlight().
	if ( $intro ) {
		echo '<p class="km-section__intro">' . esc_html( $intro ) . '</p>';
	}
	echo '</header>';
}

/**
 * Page header band: breadcrumbs, H1, optional lead.
 *
 * @param string $title Title (defaults to the current title).
 * @param string $lead  Optional lead paragraph.
 * @param string $extra Optional extra HTML (already escaped).
 */
function km_page_header( $title = '', $lead = '', $extra = '' ) {
	$title = $title ? $title : get_the_title();
	echo '<header class="km-page-head"><div class="km-wrap">';
	km_breadcrumbs();
	echo '<h1 class="km-page-head__title">' . esc_html( wp_strip_all_tags( $title ) ) . '</h1>';
	if ( $lead ) {
		echo '<p class="km-page-head__lead">' . esc_html( $lead ) . '</p>';
	}
	echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by caller.
	echo '</div></header>';
}

/**
 * Post meta: published and updated dates, author.
 */
function km_post_meta() {
	$published = get_the_date( 'j M Y' );
	$modified  = get_the_modified_date( 'j M Y' );
	$author    = km_fact( 'founder_name', get_the_author() );
	echo '<p class="km-post-meta">';
	echo esc_html__( 'By', 'kms-theme' ) . ' <span class="km-post-meta__author">' . esc_html( $author ) . '</span>';
	echo ' · <time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( $published ) . '</time>';
	if ( $modified !== $published ) {
		echo ' · ' . esc_html__( 'Updated', 'kms-theme' ) . ' <time datetime="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . esc_html( $modified ) . '</time>';
	}
	echo '</p>';
}

/**
 * Social profile links.
 */
function km_social_links() {
	$profiles = array(
		'youtube'   => array( km_fact( 'youtube' ), 'YouTube' ),
		'instagram' => array( km_fact( 'instagram' ), 'Instagram' ),
		'facebook'  => array( km_fact( 'facebook' ), 'Facebook' ),
	);
	if ( km_fact( 'tripadvisor_url' ) ) {
		$profiles['tripadvisor'] = array( km_fact( 'tripadvisor_url' ), 'TripAdvisor' );
	}
	$items = '';
	foreach ( $profiles as $icon => $profile ) {
		if ( $profile[0] ) {
			$items .= '<li><a href="' . esc_url( $profile[0] ) . '" target="_blank" rel="noopener">' . km_icon( $icon, sprintf( /* translators: %s: network */ __( '%s (opens in a new tab)', 'kms-theme' ), $profile[1] ) ) . '</a></li>';
		}
	}
	if ( $items ) {
		echo '<ul class="km-social">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Homepage retreat heading. Names the town only when every listed batch takes place there.
 *
 * @param int $limit Number of batches shown.
 * @return string
 */
function km_retreats_title( $limit ) {
	$towns = array();
	foreach ( array_slice( kms_retreat_batches(), 0, $limit ) as $batch ) {
		$parts   = explode( ',', $batch['place'] );
		$towns[] = trim( $parts[0] );
	}
	$towns = array_unique( $towns );
	if ( 1 === count( $towns ) && '' !== $towns[0] ) {
		/* translators: %s: town, e.g. Pushkar */
		return sprintf( __( 'Music retreats in %s', 'kms-theme' ), $towns[0] );
	}
	return __( 'Music retreats in India', 'kms-theme' );
}

/**
 * Escape text and colour the parts wrapped in *asterisks* with the saffron gradient.
 *
 * @param string $text Plain text.
 * @return string HTML.
 */
function km_highlight( $text ) {
	return (string) preg_replace( '/\*([^*]+)\*/', '<span class="km-hl">$1</span>', esc_html( $text ) );
}

/**
 * Badge on the hero photo: students and countries from KMS Facts.
 */
function km_hero_badge() {
	$students  = km_fact( 'students', '' );
	$countries = km_fact( 'countries', '' );
	if ( '' === $students ) {
		return;
	}
	echo '<p class="km-hero__badge"><span class="km-hero__badge-icon">' . km_icon( 'globe' ) . '</span><span><strong>' . esc_html( sprintf( /* translators: %s: number of students */ __( '%s students', 'kms-theme' ), $students ) ) . '</strong>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ( '' !== $countries ) {
		echo esc_html( sprintf( /* translators: %s: number of countries */ __( 'from %s countries', 'kms-theme' ), $countries ) );
	}
	echo '</span></p>';
}

/**
 * Text from the Customizer with KMS placeholders filled.
 *
 * @param string $key     Theme mod.
 * @param string $default Default text.
 * @return string
 */
function km_text( $key, $default ) {
	$text = (string) get_theme_mod( $key, $default );
	return function_exists( 'kms_fill' ) ? kms_fill( $text ) : str_replace( array( '{founder}', '{years}', '{name}' ), '', $text );
}

/**
 * Estimated reading time.
 *
 * @return string
 */
function km_reading_time() {
	$words = str_word_count( wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', get_the_ID() ) ) ) );
	$mins  = max( 1, (int) round( $words / 220 ) );
	/* translators: %d: minutes */
	return sprintf( _n( '%d min read', '%d min read', $mins, 'kms-theme' ), $mins );
}
