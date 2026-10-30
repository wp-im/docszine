<?php
/**
 * Dependency-free contract test for the Docszine seed content.
 *
 * Run with: php tests/test-content-contract.php
 *
 * @package Docszine
 */

$content_dir = dirname( __DIR__ ) . '/content';

function docszine_content_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

$required_pages = array(
	'home.md',
	'about-docszine.md',
	'editorial-principles.md',
	'archive.md',
	'contact.md',
);

foreach ( $required_pages as $page_file ) {
	$path = $content_dir . '/pages/' . $page_file;
	docszine_content_assert( is_file( $path ), "seed page exists: {$page_file}" );
	docszine_content_assert( '' !== trim( file_get_contents( $path ) ), "seed page is not empty: {$page_file}" );
}

$post_files = glob( $content_dir . '/posts/*.md' );
sort( $post_files );
docszine_content_assert( 6 === count( $post_files ), 'seed contains exactly six posts' );

$expected_sections = array(
	'01-theme-reading-instrument.md'    => 'Essays',
	'02-archive-comes-first.md'         => 'Essays',
	'03-smallest-useful-plugin.md'      => 'Methods',
	'04-rules-rails-white-space.md'     => 'Field Notes',
	'05-short-history-classic-theme.md' => 'Reviews',
	'06-release-notes-010.md'           => 'Methods',
);
$expected_issue    = 'Issue 01 — The Reading Surface';
$orders            = array();
$all_topics        = array();

foreach ( $post_files as $post_file ) {
	$basename  = basename( $post_file );
	$contents  = file_get_contents( $post_file );
	$front_end = preg_match( '/\A---\R(.*?)\R---\R/s', $contents, $front_match );
	docszine_content_assert( 1 === $front_end, "front matter exists: {$basename}" );
	$front_matter = $front_match[1];

	foreach ( array( 'title', 'section', 'topics', 'issue', 'issue_order', 'excerpt' ) as $field ) {
		docszine_content_assert( preg_match( '/^' . preg_quote( $field, '/' ) . ':\s*(.+)$/m', $front_matter, $field_match ), "front matter field exists: {$basename} / {$field}" );
		docszine_content_assert( '' !== trim( $field_match[1] ), "front matter field is not empty: {$basename} / {$field}" );
	}

	preg_match( '/^section:\s*(.+)$/m', $front_matter, $section_match );
	docszine_content_assert( isset( $expected_sections[ $basename ] ) && $expected_sections[ $basename ] === trim( $section_match[1] ), "expected Section: {$basename}" );

	preg_match( '/^issue:\s*(.+)$/m', $front_matter, $issue_match );
	docszine_content_assert( $expected_issue === trim( $issue_match[1] ), "all posts belong to the first Issue: {$basename}" );

	preg_match( '/^issue_order:\s*(\d+)$/m', $front_matter, $order_match );
	$order = isset( $order_match[1] ) ? (int) $order_match[1] : 0;
	docszine_content_assert( $order >= 1 && $order <= 6, "Issue order is within 1..6: {$basename}" );
	docszine_content_assert( ! in_array( $order, $orders, true ), "Issue order is unique: {$basename}" );
	$orders[] = $order;

	preg_match( '/^topics:\s*\[([^\]]+)\]$/m', $front_matter, $topics_match );
	$topics = isset( $topics_match[1] ) ? explode( ',', $topics_match[1] ) : array();
	foreach ( $topics as $topic ) {
		$all_topics[] = trim( $topic, " \t\"'" );
	}
}

sort( $orders );
docszine_content_assert( range( 1, 6 ) === $orders, 'Issue order covers 1..6 without gaps' );

$all_topics = array_values( array_unique( $all_topics ) );
sort( $all_topics );
$expected_topics = array( 'Classic Themes', 'Editorial Design', 'Publishing', 'WordPress' );
sort( $expected_topics );
docszine_content_assert( $expected_topics === $all_topics, 'seed covers the four documented Topics' );

$all_content = file_get_contents( $content_dir . '/README.md' );
foreach ( $required_pages as $page_file ) {
	$all_content .= file_get_contents( $content_dir . '/pages/' . $page_file );
}
foreach ( $post_files as $post_file ) {
	$all_content .= file_get_contents( $post_file );
}
foreach ( array( 'password', 'access_token', 'api_key', 'private_key' ) as $secret_marker ) {
	docszine_content_assert( false === stripos( $all_content, $secret_marker ), "seed contains no secret marker: {$secret_marker}" );
}

echo "PASS: Docszine content contract\n";
