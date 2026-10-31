<?php
/**
 * Front-end assets.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a cache-busting version for a theme asset.
 *
 * @param string $relative_path Theme-relative asset path.
 * @return string
 */
function docszine_asset_version( $relative_path ) {
	$path = get_theme_file_path( '/' . ltrim( $relative_path, '/' ) );
	$hash = is_readable( $path ) ? hash_file( 'sha256', $path ) : false;
	return $hash ? substr( $hash, 0, 12 ) : wp_get_theme()->get( 'Version' );
}

/** Enqueue theme styles and scripts. */
function docszine_enqueue_assets() {
	wp_enqueue_style(
		'docszine-style',
		get_stylesheet_uri(),
		array(),
		docszine_asset_version( 'style.css' )
	);
	wp_enqueue_style(
		'docszine',
		get_theme_file_uri( '/assets/css/magazine.css' ),
		array( 'docszine-style' ),
		docszine_asset_version( 'assets/css/magazine.css' )
	);

	wp_enqueue_script(
		'docszine-navigation',
		get_theme_file_uri( '/assets/js/navigation.js' ),
		array(),
		docszine_asset_version( 'assets/js/navigation.js' ),
		true
	);

	wp_localize_script( 'docszine-navigation', 'docszineNavigation', array( 'scrollRegion' => __( 'Scrollable content. Use the arrow keys to read more.', 'docszine' ) ) );

	if ( is_singular( array( 'post', 'page' ) ) ) {
		wp_enqueue_script(
			'docszine-article',
			get_theme_file_uri( '/assets/js/article.js' ),
			array(),
			docszine_asset_version( 'assets/js/article.js' ),
			true
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'docszine_enqueue_assets' );
