<?php
/**
 * Site header.
 *
 * @package KMS_Theme
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="km-skip" href="#main"><?php esc_html_e( 'Skip to content', 'kms-theme' ); ?></a>

<?php
$km_notice = km_announcement();
if ( $km_notice ) :
	?>
	<div class="km-announce" role="region" aria-label="<?php esc_attr_e( 'Announcement', 'kms-theme' ); ?>">
		<div class="km-wrap">
			<?php if ( $km_notice[1] ) : ?>
				<a href="<?php echo esc_url( $km_notice[1] ); ?>"><?php echo km_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $km_notice[0] ); ?><?php echo km_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php else : ?>
				<span><?php echo esc_html( $km_notice[0] ); ?></span>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<header class="km-header" data-km-header>
	<div class="km-wrap km-header__inner">
		<?php km_logo(); ?>

		<?php if ( is_page_template( 'page-templates/landing.php' ) ) : ?>
			<a class="km-btn km-btn--primary km-header__cta" href="<?php echo esc_url( km_cta_url() ); ?>"><?php esc_html_e( 'Free consultation', 'kms-theme' ); ?></a>
		<?php else : ?>
		<button class="km-nav-toggle" type="button" aria-expanded="false" aria-controls="km-nav" data-km-nav-toggle>
			<?php echo km_icon( 'menu', '', 'km-nav-toggle__open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo km_icon( 'x', '', 'km-nav-toggle__close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'kms-theme' ); ?></span>
		</button>

		<nav class="km-nav" id="km-nav" aria-label="<?php esc_attr_e( 'Main', 'kms-theme' ); ?>" data-km-nav>
			<?php km_primary_nav(); ?>
			<a class="km-btn km-btn--primary km-header__cta" href="<?php echo esc_url( km_cta_url() ); ?>"><?php esc_html_e( 'Free consultation', 'kms-theme' ); ?></a>
		</nav>
		<?php endif; ?>
	</div>
</header>

<main id="main" class="km-main" tabindex="-1">
