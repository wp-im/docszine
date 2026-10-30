<?php
/**
 * Theme setup.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Configure theme features and menu locations. */
function docszine_setup() {
	load_theme_textdomain( 'docszine', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_editor_style( 'assets/css/editor-style.css' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'docszine' ),
			'footer'  => __( 'Footer Menu', 'docszine' ),
		)
	);
}
add_action( 'after_setup_theme', 'docszine_setup' );

/** Register the two shared Classic Theme widget areas. */
function docszine_register_sidebars() {
	register_sidebar(
		array(
			'name'          => __( 'Leftbar', 'docszine' ),
			'id'            => 'docszine-leftbar',
			'description'   => __( 'Optional content below the Docs Tree and Issue navigation.', 'docszine' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
	register_sidebar(
		array(
			'name'          => __( 'Rightbar', 'docszine' ),
			'id'            => 'docszine-rightbar',
			'description'   => __( 'Optional contextual content beside the reading column.', 'docszine' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'docszine_register_sidebars' );

/** Set the standard article reading width used by classic editor media defaults. */
function docszine_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'docszine_content_width', 620 );
}
add_action( 'after_setup_theme', 'docszine_content_width', 0 );
