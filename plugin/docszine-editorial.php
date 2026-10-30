<?php
/**
 * Plugin Name: Docszine Editorial
 * Description: Issue metadata, editorial order, settings, and reading shortcodes for the Docszine Classic Theme.
 * Version: 0.1.1
 * Requires at least: 6.6
 * Requires PHP: 8.0
 * Author: Docszine
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: docszine-editorial
 *
 * @package Docszine_Editorial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DOCSZINE_EDITORIAL_VERSION', '0.1.1' );
define( 'DOCSZINE_EDITORIAL_PATH', plugin_dir_path( __FILE__ ) );
define( 'DOCSZINE_EDITORIAL_URL', plugin_dir_url( __FILE__ ) );

$docszine_editorial_includes = array(
	'includes/taxonomy.php',
	'includes/meta.php',
	'includes/settings.php',
	'includes/shortcodes.php',
);

foreach ( $docszine_editorial_includes as $docszine_editorial_include ) {
	$docszine_editorial_file = DOCSZINE_EDITORIAL_PATH . $docszine_editorial_include;
	if ( file_exists( $docszine_editorial_file ) ) {
		require_once $docszine_editorial_file;
	}
}
