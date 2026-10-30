<?php
/**
 * Docszine functions and definitions.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$docszine_includes = array(
	'setup',
	'enqueue',
	'customizer',
	'queries',
	'docs-tree',
	'template-tags',
	'template-functions',
);

foreach ( $docszine_includes as $docszine_include ) {
	$docszine_file = get_template_directory() . '/inc/' . $docszine_include . '.php';
	if ( file_exists( $docszine_file ) ) {
		require_once $docszine_file;
	}
}
