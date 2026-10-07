<?php
/**
 * Search form.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;

$km_id = wp_unique_id( 'km-search-' );
?>
<form role="search" method="get" class="km-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $km_id ); ?>"><?php esc_html_e( 'Search the site', 'kms-theme' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $km_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search articles and classes…', 'kms-theme' ); ?>">
	<button type="submit" class="km-btn km-btn--primary"><?php esc_html_e( 'Search', 'kms-theme' ); ?></button>
</form>
