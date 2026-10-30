<?php
/**
 * Dependency-free contract test for WordPress.org theme metadata.
 *
 * Run with: php tests/test-readme-contract.php
 *
 * @package Docszine
 */

$theme_dir = dirname( __DIR__ ) . '/theme';

function docszine_readme_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

$style  = file_get_contents( $theme_dir . '/style.css' );
$readme = file_get_contents( $theme_dir . '/readme.txt' );

docszine_readme_assert( false === strpos( $style, "\r" ), 'style.css uses one line-ending convention' );
docszine_readme_assert( false === strpos( $readme, "\r" ), 'readme.txt uses one line-ending convention' );

$style_headers = array();
foreach ( array( 'Theme Name', 'Author', 'Description', 'Version', 'Requires at least', 'Tested up to', 'Requires PHP', 'License', 'License URI', 'Text Domain' ) as $header ) {
	docszine_readme_assert( preg_match( '/^' . preg_quote( $header, '/' ) . ':\s*(.+)$/m', $style, $match ), "style.css header exists: {$header}" );
	$style_headers[ $header ] = trim( $match[1] );
}

foreach ( array( '=== Docszine ===', '== Description ==', '== Frequently Asked Questions ==', '== Changelog ==', '== Upgrade Notice ==', '== Resources ==', '== License ==' ) as $section ) {
	docszine_readme_assert( false !== strpos( $readme, $section ), "readme section exists: {$section}" );
}

$readme_headers = array();
foreach ( array( 'Contributors', 'Requires at least', 'Tested up to', 'Requires PHP', 'Stable tag', 'License', 'License URI' ) as $header ) {
	docszine_readme_assert( preg_match( '/^' . preg_quote( $header, '/' ) . ':\s*(.+)$/m', $readme, $match ), "readme header exists: {$header}" );
	$readme_headers[ $header ] = trim( $match[1] );
}

docszine_readme_assert( preg_match( '/^[a-z0-9-]+$/', $readme_headers['Contributors'] ), 'readme has exactly one WordPress.org-style contributor token' );
docszine_readme_assert( $style_headers['Version'] === $readme_headers['Stable tag'], 'style.css Version matches readme Stable tag' );
docszine_readme_assert( $style_headers['Requires at least'] === $readme_headers['Requires at least'], 'WordPress minimum version matches' );
docszine_readme_assert( $style_headers['Tested up to'] === $readme_headers['Tested up to'], 'tested-up-to version matches' );
docszine_readme_assert( $style_headers['Requires PHP'] === $readme_headers['Requires PHP'], 'PHP minimum version matches' );
docszine_readme_assert( 'docszine' === $style_headers['Text Domain'], 'style.css text domain matches the theme slug' );

$readme_lines      = preg_split( '/\n/', $readme );
$tags_index        = null;
$short_description = '';
foreach ( $readme_lines as $index => $readme_line ) {
	if ( 0 === strpos( $readme_line, 'Tags:' ) ) {
		$tags_index = $index;
		break;
	}
}
if ( null !== $tags_index ) {
	for ( $index = $tags_index + 1; $index < count( $readme_lines ); $index++ ) {
		if ( '' !== trim( $readme_lines[ $index ] ) ) {
			$short_description = trim( $readme_lines[ $index ] );
			break;
		}
	}
}
$description_length = function_exists( 'mb_strlen' ) ? mb_strlen( $short_description ) : strlen( $short_description );
docszine_readme_assert( '' !== $short_description && $description_length <= 150, 'readme short description is present and at most 150 characters' );

$upgrade_notice = '';
if ( preg_match( '/== Upgrade Notice ==\R(.*?)(?:\R== |\z)/s', $readme, $upgrade_match ) ) {
	$upgrade_notice = trim( preg_replace( '/\s+/', ' ', $upgrade_match[1] ) );
}
$upgrade_length = function_exists( 'mb_strlen' ) ? mb_strlen( $upgrade_notice ) : strlen( $upgrade_notice );
docszine_readme_assert( $upgrade_length <= 300, 'readme upgrade notice is at most 300 characters' );

echo "PASS: Docszine readme contract\n";
