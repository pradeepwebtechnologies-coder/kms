<?php
/**
 * Navigation: the main menu, plus a sensible default when no menu is assigned.
 *
 * The default puts Online Classes first (the business goal) and uses real URLs
 * for parent items — the old menu used "#" placeholders 358 times.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print the primary navigation.
 */
function km_primary_nav() {
	wp_nav_menu(
		array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'km-menu',
			'menu_id'        => 'km-menu',
			'depth'          => 2,
			'fallback_cb'    => 'km_fallback_menu',
		)
	);
}

/**
 * Published page URL by slug.
 *
 * @param string $slug Page path.
 * @return string
 */
function km_page_link( $slug ) {
	$page = get_page_by_path( $slug );
	return ( $page && 'publish' === $page->post_status ) ? get_permalink( $page ) : '';
}

/**
 * Default menu structure: [ label, url, children[] ].
 *
 * @return array<int, array{0:string,1:string,2:array}>
 */
function km_default_menu_items() {
	$items = array();

	// Online classes.
	$classes = array();
	if ( function_exists( 'kms_get_courses' ) ) {
		foreach ( kms_get_courses() as $course ) {
			$classes[] = array( kms_course_short_title( $course ), get_permalink( $course ), array() );
		}
	}
	$pricing = function_exists( 'kms_page_url' ) ? kms_page_url( 'pricing' ) : km_page_link( 'pricing' );
	if ( $pricing ) {
		$classes[] = array( __( 'Prices', 'kms-theme' ), $pricing, array() );
	}
	$archive = get_post_type_archive_link( 'kms_course' );
	if ( $archive ) {
		$items[] = array( __( 'Online classes', 'kms-theme' ), $archive, $classes );
	}

	// Retreats.
	$retreats = array();
	$winter   = km_page_link( '8-day-winter-music-retreat-in-pushkar-singing-mantra-chanting-kirtan-harmonium' );
	$summer   = km_page_link( 'summer-music-retreat-upper-bhagsu' );
	if ( $winter ) {
		$retreats[] = array( __( 'Pushkar winter retreat', 'kms-theme' ), $winter, array() );
	}
	if ( $summer ) {
		$retreats[] = array( __( 'Himalaya summer retreat', 'kms-theme' ), $summer, array() );
	}
	$multi = km_page_link( 'multi-instrument-training' );
	if ( $multi ) {
		$retreats[] = array( __( 'Classes in Pushkar', 'kms-theme' ), $multi, array() );
	}
	if ( $retreats ) {
		$items[] = array( __( 'Retreats', 'kms-theme' ), $retreats[0][1], $retreats );
	}

	// Live performances.
	$shows = array();
	foreach ( array(
		'folk-music-band-chokhi-vini-project-pushkar' => __( 'Folk band (Chokhi Vini Project)', 'kms-theme' ),
		'solo-artists'                                => __( 'Solo artists', 'kms-theme' ),
		'kabali-sufi-musical-experience'              => __( 'Sufi music', 'kms-theme' ),
		'bhajan-kirtan-devotional-music-nights-in-pushkar' => __( 'Devotional music nights', 'kms-theme' ),
		'dinner-concerts-private-music-evenings'      => __( 'Dinner concerts', 'kms-theme' ),
		'fusion-instrumental-groups'                  => __( 'Fusion instrumental', 'kms-theme' ),
		'bollywood-dance-music-shows-for-events-weddings' => __( 'Bollywood shows', 'kms-theme' ),
		'corporate-musical-events-cultural-shows'     => __( 'Corporate events', 'kms-theme' ),
		'rajasthani-cultural-concerts-festivals'      => __( 'Cultural concerts and festivals', 'kms-theme' ),
	) as $slug => $label ) {
		$url = km_page_link( $slug );
		if ( $url ) {
			$shows[] = array( $label, $url, array() );
		}
	}
	$performances = function_exists( 'kms_page_url' ) ? kms_page_url( 'performances' ) : '';
	if ( $shows || $performances ) {
		$items[] = array( __( 'Performances', 'kms-theme' ), $performances ? $performances : $shows[0][1], $shows );
	}

	// About.
	$about = array();
	foreach ( array(
		'about'   => __( 'About the school', 'kms-theme' ),
		'reviews' => __( 'Student reviews', 'kms-theme' ),
		'faq'     => __( 'FAQ', 'kms-theme' ),
	) as $key => $label ) {
		$url = function_exists( 'kms_page_url' ) ? kms_page_url( $key ) : '';
		if ( $url ) {
			$about[] = array( $label, $url, array() );
		}
	}
	$blog = (int) get_option( 'page_for_posts' );
	if ( $blog ) {
		$about[] = array( __( 'Blog', 'kms-theme' ), get_permalink( $blog ), array() );
	}
	if ( $about ) {
		$items[] = array( __( 'About', 'kms-theme' ), $about[0][1], $about );
	}

	return $items;
}

/**
 * Render the default menu with the same markup as wp_nav_menu().
 */
function km_fallback_menu() {
	$current = is_singular() ? (string) get_permalink() : '';
	echo '<ul id="km-menu" class="km-menu">';
	foreach ( km_default_menu_items() as $item ) {
		list( $label, $url, $children ) = $item;
		$classes                        = 'menu-item' . ( $children ? ' menu-item-has-children' : '' );
		echo '<li class="' . esc_attr( $classes ) . '"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
		if ( $children ) {
			echo '<ul class="sub-menu">';
			foreach ( $children as $child ) {
				$aria = untrailingslashit( $child[1] ) === untrailingslashit( $current ) ? ' aria-current="page"' : '';
				echo '<li class="menu-item"><a href="' . esc_url( $child[1] ) . '"' . $aria . '>' . esc_html( $child[0] ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</ul>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Footer legal links (menu, else defaults).
 */
function km_footer_links() {
	if ( has_nav_menu( 'footer' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => 'km-legal',
				'depth'          => 1,
			)
		);
		return;
	}
	$links = array();
	if ( function_exists( 'kms_page_url' ) ) {
		foreach ( array(
			'privacy' => __( 'Privacy policy', 'kms-theme' ),
			'terms'   => __( 'Terms and conditions', 'kms-theme' ),
			'refund'  => __( 'Refund and cancellation', 'kms-theme' ),
		) as $key => $label ) {
			$url = kms_page_url( $key );
			if ( $url ) {
				$links[] = '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
			}
		}
	}
	if ( $links ) {
		echo '<ul class="km-legal">' . implode( '', $links ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

/**
 * Footer "The school" links: [ label, url ].
 *
 * @return array<int, array{0:string,1:string}>
 */
function km_footer_school_links() {
	$links = array();
	$add   = static function ( $label, $url ) use ( &$links ) {
		if ( $url ) {
			$links[] = array( $label, $url );
		}
	};
	$page  = static function ( $key ) {
		return function_exists( 'kms_page_url' ) ? kms_page_url( $key ) : '';
	};
	$add( __( 'About the school', 'kms-theme' ), $page( 'about' ) );
	$add( __( 'Student reviews', 'kms-theme' ), $page( 'reviews' ) );
	$add( __( 'Pushkar winter retreat', 'kms-theme' ), km_page_link( '8-day-winter-music-retreat-in-pushkar-singing-mantra-chanting-kirtan-harmonium' ) );
	$add( __( 'Himalaya summer retreat', 'kms-theme' ), km_page_link( 'summer-music-retreat-upper-bhagsu' ) );
	if ( ! function_exists( 'kms_performances' ) || ! kms_performances() ) {
		$performances = $page( 'performances' );
		$add( __( 'Book a live performance', 'kms-theme' ), $performances ? $performances : km_page_link( 'folk-music-band-chokhi-vini-project-pushkar' ) );
	}
	$add( __( 'FAQ', 'kms-theme' ), $page( 'faq' ) );
	$blog = (int) get_option( 'page_for_posts' );
	$add( __( 'Blog', 'kms-theme' ), $blog ? get_permalink( $blog ) : '' );
	return $links;
}
