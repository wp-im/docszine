<?php
/**
 * Small dependency-free regression test for the article heading contract.
 *
 * Run with: php tests/test-heading-ids.php
 */

define( 'ABSPATH', __DIR__ );

function add_filter() {}
function get_bloginfo( $show ) {
	return 'UTF-8'; }
function sanitize_title( $title ) {
	return strtolower( preg_replace( '/[^a-z0-9]+/i', '-', trim( (string) $title ) ) ); }
function wp_strip_all_tags( $text ) {
	return strip_tags( (string) $text ); }
function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function is_singular() {
	return true; }
function in_the_loop() {
	return true; }
function is_main_query() {
	return true; }
function get_the_ID() {
	return 1; }
function get_post_field( $field, $post_id ) {
	return ''; }

require dirname( __DIR__ ) . '/theme/inc/template-tags.php';

function docszine_test_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

$content  = '<h2 id="Intro">One</h2><p>Text</p><h2>Intro</h2><h2 id="Intro">Three</h2><h2>中文</h2>';
$headings = docszine_get_article_headings( $content );

docszine_test_assert( 4 === count( $headings ), 'all H2 headings are collected' );
docszine_test_assert( 'Intro' === $headings[0]['id'], 'the first explicit ID is preserved' );
docszine_test_assert( 'intro-2' === $headings[1]['id'], 'an automatic duplicate receives a suffix' );
docszine_test_assert( 'intro-3' === $headings[2]['id'], 'a duplicate explicit ID receives a suffix' );
docszine_test_assert( 'section-4' === $headings[3]['id'], 'a non-Latin heading receives a stable fallback ID' );

$injected = docszine_add_article_heading_ids( $content );
docszine_test_assert( 1 === substr_count( $injected, 'id="Intro"' ), 'the first explicit ID remains unchanged' );
docszine_test_assert( 1 === substr_count( $injected, 'id="intro-2"' ), 'the automatic duplicate ID is injected' );
docszine_test_assert( 1 === substr_count( $injected, 'id="intro-3"' ), 'the duplicate explicit ID is normalized' );
docszine_test_assert( 1 === substr_count( $injected, 'id="section-4"' ), 'the fallback ID is injected' );

$attribute_fixture = '<p data-example="<h2>not a heading</h2>">Copy</p><h2>Real heading</h2>';
$prepared_fixture  = docszine_prepare_article_content( $attribute_fixture );
docszine_test_assert( $attribute_fixture === $prepared_fixture, 'the no-API fallback never rewrites HTML inside attributes' );
$fallback_headings = docszine_get_article_headings( $attribute_fixture );
docszine_test_assert( 1 === count( $fallback_headings ), 'the fallback ignores H2-looking text inside attributes' );
docszine_test_assert( 'Real heading' === $fallback_headings[0]['title'], 'the fallback keeps nested heading text readable' );
$fallback_injected = docszine_add_article_heading_ids( $attribute_fixture );
docszine_test_assert( false !== strpos( $fallback_injected, 'data-example="<h2>not a heading</h2>"' ), 'the fallback leaves attribute values byte-stable' );
docszine_test_assert( false !== strpos( $fallback_injected, '<h2 id="real-heading">Real heading</h2>' ), 'the fallback injects the real heading ID' );
$raw_text_fixture  = '<script>var example = "<h2>not a heading</h2>";</script><h2>Actual heading</h2>';
$raw_text_headings = docszine_get_article_headings( $raw_text_fixture );
docszine_test_assert( 1 === count( $raw_text_headings ), 'the fallback ignores H2-looking text inside raw-text elements' );
docszine_test_assert( false !== strpos( docszine_add_article_heading_ids( $raw_text_fixture ), '<script>var example = "<h2>not a heading</h2>";</script>' ), 'the fallback leaves raw-text elements byte-stable' );

echo "PASS: Docszine heading ID contract\n";
