<?php
/**
 * Small curated Customizer surface.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme presentation settings.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function docszine_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'docszine_navigation',
		array(
			'title'       => __( 'Documentation Navigation', 'docszine' ),
			'description' => __( 'Choose the optional Page that anchors the site documentation tree. The theme uses native Page hierarchy and menu order below it.', 'docszine' ),
			'priority'    => 150,
		)
	);

	$wp_customize->add_setting(
		'docszine_docs_root_page',
		array(
			'default'           => 0,
			'type'              => 'theme_mod',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'docszine_docs_root_page',
		array(
			'section'     => 'docszine_navigation',
			'label'       => __( 'Docs Root page', 'docszine' ),
			'description' => __( 'Optional. Its child Pages become the first level of the Docs Tree.', 'docszine' ),
			'type'        => 'dropdown-pages',
		)
	);

	$wp_customize->add_section(
		'docszine_footer',
		array(
			'title'    => __( 'Footer', 'docszine' ),
			'priority' => 160,
		)
	);

	$wp_customize->add_setting(
		'docszine_footer_text',
		array(
			'default'           => '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'docszine_footer_text',
		array(
			'section' => 'docszine_footer',
			'label'   => __( 'Footer text', 'docszine' ),
			'type'    => 'text',
		)
	);
}
add_action( 'customize_register', 'docszine_customize_register' );
