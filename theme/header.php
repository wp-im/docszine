<?php
/**
 * Site header.
 *
 * @package Docszine
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'docszine' ); ?></a>
<?php $docszine_has_primary_menu = has_nav_menu( 'primary' ); ?>
<header class="site-header" id="masthead">
	<div class="header-inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><span class="brand-mark" aria-hidden="true"></span><span class="brand-name"><?php bloginfo( 'name' ); ?></span></a>
			<?php endif; ?>
		</div>
		<?php if ( $docszine_has_primary_menu ) : ?>
		<nav class="global-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'docszine' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-menu',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>
		<?php endif; ?>
		<div class="header-search"><?php get_search_form(); ?></div>
		<button class="header-menu" type="button" data-global-menu aria-expanded="false" aria-controls="global-menu-panel" aria-label="<?php echo esc_attr( $docszine_has_primary_menu ? __( 'Open menu', 'docszine' ) : __( 'Open search', 'docszine' ) ); ?>"><span></span></button>
	</div>
</header>
<div class="global-menu" id="global-menu-panel" data-global-menu-panel aria-hidden="true" hidden>
	<?php if ( $docszine_has_primary_menu ) : ?>
	<nav aria-label="<?php esc_attr_e( 'Mobile navigation', 'docszine' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-primary-menu',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
	</nav>
	<?php endif; ?>
	<div class="mobile-search"><?php get_search_form(); ?></div>
</div>
