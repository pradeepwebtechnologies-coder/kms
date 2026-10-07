<?php
/**
 * Prices: one table per class, shown in INR with approximate USD/EUR/GBP.
 *
 * INR is always shown because it is what students actually pay (UPI/bank) and
 * what PayPal/Wise convert from. The visitor's currency is shown as "≈" unless
 * the school sets an exact US$ price on the class.
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rupees per unit of each foreign currency.
 *
 * @return array<string, float>
 */
function kms_currency_rates() {
	return array(
		'USD' => max( 1, (float) kms_fact( 'rate_usd', '88' ) ),
		'EUR' => max( 1, (float) kms_fact( 'rate_eur', '102' ) ),
		'GBP' => max( 1, (float) kms_fact( 'rate_gbp', '117' ) ),
	);
}

/**
 * Indian-style grouping: 250000 → ₹2,50,000.
 *
 * @param int|float $amount Amount in rupees.
 * @return string
 */
function kms_format_inr( $amount ) {
	$amount = (string) (int) round( (float) $amount );
	$last3  = substr( $amount, -3 );
	$rest   = substr( $amount, 0, -3 );
	if ( '' !== $rest ) {
		$rest  = preg_replace( '/\B(?=(\d{2})+(?!\d))/', ',', $rest );
		$last3 = $rest . ',' . $last3;
	}
	return '₹' . $last3;
}

/**
 * Format a foreign amount.
 *
 * @param float  $amount   Amount.
 * @param string $currency USD|EUR|GBP.
 * @return string
 */
function kms_format_foreign( $amount, $currency ) {
	$symbols = array(
		'USD' => 'US$',
		'EUR' => '€',
		'GBP' => '£',
	);
	$symbol  = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : $currency . ' ';
	return $symbol . number_format( (float) $amount, 0 );
}

/**
 * Convert rupees to a foreign currency, rounded to a whole unit.
 *
 * @param int    $inr      Rupees.
 * @param string $currency USD|EUR|GBP.
 * @return int
 */
function kms_convert( $inr, $currency ) {
	$rates = kms_currency_rates();
	return isset( $rates[ $currency ] ) ? (int) round( $inr / $rates[ $currency ] ) : 0;
}

/**
 * Price tiers for a class.
 *
 * @param int $post_id Course ID.
 * @return array<int, array{key:string,label:string,classes:int,inr:int,usd:float,per_class:int}>
 */
function kms_course_tiers( $post_id ) {
	$tiers = array();
	$defs  = array(
		'single' => array( __( 'Single class', 'kms-core' ), 1 ),
		'pack5'  => array( __( '5 classes', 'kms-core' ), 5 ),
		'pack10' => array( __( '10 classes', 'kms-core' ), 10 ),
	);
	foreach ( $defs as $key => $def ) {
		$inr = (int) kms_meta( $post_id, 'kms_price_' . $key );
		if ( $inr <= 0 ) {
			continue;
		}
		$tiers[] = array(
			'key'       => $key,
			'label'     => $def[0],
			'classes'   => $def[1],
			'inr'       => $inr,
			'usd'       => (float) kms_meta( $post_id, 'kms_usd_' . $key ),
			'per_class' => (int) round( $inr / $def[1] ),
		);
	}
	return $tiers;
}

/**
 * Lowest single-class price of a class (for "from" labels).
 *
 * @param int $post_id Course ID.
 * @return int 0 when no price is set.
 */
function kms_course_from_price( $post_id ) {
	$tiers = kms_course_tiers( $post_id );
	if ( ! $tiers ) {
		return 0;
	}
	return (int) min( wp_list_pluck( $tiers, 'per_class' ) );
}

/**
 * Price markup with data attributes for the client-side currency switcher.
 *
 * Output: "₹13,500 ≈ US$153". With a different currency chosen, the script
 * swaps the "≈" part; the rupee amount always stays.
 *
 * @param int   $inr Rupees.
 * @param float $usd Optional exact USD price.
 * @return string
 */
function kms_price_html( $inr, $usd = 0.0 ) {
	$inr     = (int) $inr;
	$approx  = $usd > 0 ? (float) $usd : kms_convert( $inr, 'USD' );
	$is_exact = $usd > 0;
	$data    = array(
		'data-inr'   => $inr,
		'data-usd'   => $approx,
		'data-eur'   => kms_convert( $inr, 'EUR' ),
		'data-gbp'   => kms_convert( $inr, 'GBP' ),
		'data-exact' => $is_exact ? 'usd' : '',
	);
	$attrs   = '';
	foreach ( $data as $name => $value ) {
		$attrs .= ' ' . $name . '="' . esc_attr( (string) $value ) . '"';
	}
	$alt = ( $is_exact ? '' : '≈ ' ) . kms_format_foreign( $approx, 'USD' );

	return '<span class="km-price"' . $attrs . '><span class="km-price__inr">' . esc_html( kms_format_inr( $inr ) ) . '</span> <span class="km-price__alt">' . esc_html( $alt ) . '</span></span>';
}

/**
 * Plain-text price line for llms.txt and schema descriptions.
 *
 * @param int $post_id Course ID.
 * @return string
 */
function kms_course_price_text( $post_id ) {
	$parts = array();
	foreach ( kms_course_tiers( $post_id ) as $tier ) {
		$usd     = $tier['usd'] > 0 ? 'US$' . number_format( $tier['usd'], 0 ) : '≈ US$' . kms_convert( $tier['inr'], 'USD' );
		$parts[] = $tier['label'] . ': ' . kms_format_inr( $tier['inr'] ) . ' (' . $usd . ')';
	}
	$note = trim( (string) kms_meta( $post_id, 'kms_price_note' ) );
	if ( $note ) {
		$parts[] = $note;
	}
	return implode( '; ', $parts );
}

/**
 * Lowest single-class price and lowest per-class price across all classes.
 *
 * @return array{single:int, per_class:int}
 */
function kms_price_summary() {
	static $summary = null;
	if ( null !== $summary ) {
		return $summary;
	}
	$summary = array(
		'single'    => 0,
		'per_class' => 0,
	);
	foreach ( kms_get_courses() as $course ) {
		foreach ( kms_course_tiers( $course->ID ) as $tier ) {
			if ( 1 === $tier['classes'] && ( ! $summary['single'] || $tier['inr'] < $summary['single'] ) ) {
				$summary['single'] = $tier['inr'];
			}
			if ( ! $summary['per_class'] || $tier['per_class'] < $summary['per_class'] ) {
				$summary['per_class'] = $tier['per_class'];
			}
		}
	}
	return $summary;
}

/**
 * Replace class-level placeholders ({price_single}, {price_pack5}, {price_pack10}, {minutes})
 * so summaries always match the price fields.
 *
 * @param int    $post_id Course ID.
 * @param string $text    Text.
 * @return string
 */
function kms_course_fill( $post_id, $text ) {
	$map = array( '{minutes}' => (string) kms_course_minutes( $post_id ) );
	foreach ( kms_course_tiers( $post_id ) as $tier ) {
		$usd                                  = $tier['usd'] > 0 ? 'US$' . number_format( $tier['usd'], 0 ) : '≈ US$' . kms_convert( $tier['inr'], 'USD' );
		$map[ '{price_' . $tier['key'] . '}' ] = kms_format_inr( $tier['inr'] ) . ' (' . $usd . ')';
	}
	return kms_fill( strtr( (string) $text, $map ) );
}
