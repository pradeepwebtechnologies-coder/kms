<?php
/**
 * WP-CLI commands.
 *
 *     wp kms import [--publish] [--replace-pages]
 *     wp kms llms
 *     wp kms health
 *
 * @package KMS_Core
 */

defined( 'ABSPATH' ) || exit;

/**
 * Krishna Music School tools.
 */
class KMS_CLI {

	/**
	 * Import the starter classes, FAQs, reviews and pages.
	 *
	 * ## OPTIONS
	 *
	 * [--publish]
	 * : Publish classes, FAQs and reviews instead of saving drafts.
	 *
	 * [--replace-pages]
	 * : Replace the content of existing About, Reviews and Contact pages.
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Flags.
	 */
	public function import( $args, $assoc_args ) {
		$log = kms_import_starter(
			array(
				'publish'       => ! empty( $assoc_args['publish'] ),
				'replace_pages' => ! empty( $assoc_args['replace-pages'] ),
			)
		);
		foreach ( $log as $line ) {
			WP_CLI::log( $line );
		}
		WP_CLI::success( 'Starter content imported.' );
	}

	/**
	 * Print the generated llms.txt.
	 */
	public function llms() {
		WP_CLI::line( kms_llms_text() );
	}

	/**
	 * Run the site checks.
	 */
	public function health() {
		foreach ( kms_health_checks() as $check ) {
			WP_CLI::log( strtoupper( $check['status'] ) . ': ' . $check['label'] . ( $check['detail'] ? ' — ' . $check['detail'] : '' ) );
		}
	}
}

WP_CLI::add_command( 'kms', 'KMS_CLI' );
