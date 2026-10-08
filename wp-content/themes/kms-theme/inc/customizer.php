<?php
/**
 * Customizer: homepage hero, announcement bar and class-hub texts.
 *
 * Facts (prices, years, phone…) are not set here — they come from
 * Settings → KMS Facts so they can never disagree between pages.
 * Texts accept the placeholders {founder}, {years}, {name}, {countries}.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Text settings: key => [ label, default, type ].
 *
 * @return array<string, array{0:string,1:string,2:string}>
 */
function km_customizer_texts() {
	return array(
		'km_announcement_text' => array( __( 'Announcement bar text (empty = next retreat date automatically)', 'kms-theme' ), '', 'text' ),
		'km_announcement_url'  => array( __( 'Announcement bar link', 'kms-theme' ), '', 'url' ),
		'km_hero_eyebrow'      => array( __( 'Hero: small line above the title', 'kms-theme' ), 'Live one-to-one classes from Pushkar, India', 'text' ),
		'km_hero_title'        => array( __( 'Hero: main title (the page H1). Wrap words in *asterisks* to colour them.', 'kms-theme' ), 'Online Indian Music Classes, *Taught Live from Pushkar*', 'text' ),
		'km_hero_text'         => array( __( 'Hero: paragraph', 'kms-theme' ), 'Learn harmonium, singing, bhajan and kirtan, Hindustani classical vocal or tabla one-to-one with {founder}. {years} years of teaching, complete beginners welcome, and classes at times that work in your time zone.', 'textarea' ),
		'km_hero_image_alt'    => array( __( 'Hero: image description (alt text; empty = the Media Library alt text)', 'kms-theme' ), '', 'text' ),
		'km_hub_title'         => array( __( 'Online classes page: title', 'kms-theme' ), 'Online Indian Music Classes', 'text' ),
		'km_hub_intro'         => array( __( 'Online classes page: introduction', 'kms-theme' ), 'Live one-to-one classes with {founder} on Zoom or Google Meet, for complete beginners to advanced students anywhere in the world. Choose an instrument or style below; every student starts with a free 15-minute consultation.', 'textarea' ),
	);
}

/**
 * Register settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer.
 */
function km_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'km_home',
		array(
			'title'       => __( 'Krishna Music School', 'kms-theme' ),
			'description' => __( 'Homepage and class-hub texts. Prices, phone numbers and other facts are edited in Settings → KMS Facts.', 'kms-theme' ),
			'priority'    => 30,
		)
	);

	foreach ( km_customizer_texts() as $key => $def ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $def[1],
				'sanitize_callback' => 'url' === $def[2] ? 'esc_url_raw' : ( 'textarea' === $def[2] ? 'sanitize_textarea_field' : 'sanitize_text_field' ),
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $def[0],
				'section' => 'km_home',
				'type'    => $def[2],
			)
		);
	}

	$wp_customize->add_setting(
		'km_hero_image',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'km_hero_image',
			array(
				'label'       => __( 'Hero image (landscape, at least 1200 px wide)', 'kms-theme' ),
				'description' => __( 'Empty = the founder photo from KMS Facts.', 'kms-theme' ),
				'section'     => 'km_home',
				'mime_type'   => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'km_customize_register' );

/**
 * Announcement bar data: [ text, url ] or null.
 *
 * @return array{0:string,1:string}|null
 */
function km_announcement() {
	$text = trim( (string) get_theme_mod( 'km_announcement_text', '' ) );
	$url  = (string) get_theme_mod( 'km_announcement_url', '' );
	if ( '' === $text && function_exists( 'kms_retreat_batches' ) ) {
		$batches = kms_retreat_batches();
		if ( $batches ) {
			$next = $batches[0];
			/* translators: 1: retreat name, 2: dates */
			$text = sprintf( __( '%1$s · next batch %2$s', 'kms-theme' ), $next['name'], kms_date_range( $next['start'], $next['end'] ) );
			$url  = $url ? $url : $next['url'];
		}
	}
	return '' === $text ? null : array( $text, $url );
}
