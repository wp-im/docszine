<?php
/** Native optional-plugin degradation test; loopback fixtures only. */
if ( ! defined( 'ABSPATH' ) || ! in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) ) {
	throw new RuntimeException( 'Loopback WordPress QA only.' );
}
if ( taxonomy_exists( 'docszine_issue' ) || docszine_get_current_issue() || docszine_get_issue_terms_ordered() ) {
	throw new RuntimeException( 'Deactivate Editorial and load a fresh WordPress request.' );
}
$_SERVER['SERVER_NAME'] = wp_parse_url( home_url(), PHP_URL_HOST );
global $wp_query;
foreach ( array(
	'front-page.php' => array( 'page_id' => (int) get_option( 'page_on_front' ) ),
	'single.php' => array( 'name' => 'reading-en', 'post_type' => 'post' ),
	'page.php' => array( 'page_id' => (int) get_option( 'page_on_front' ) ),
) as $template => $query ) {
	$wp_query = new WP_Query( $query );
	ob_start();
	include get_template_directory() . '/' . $template;
	$html = ob_get_clean();
	foreach ( array( 'id="docszine-leftbar"', 'id="docszine-rightbar"', 'id="primary"' ) as $landmark ) {
		if ( 1 !== substr_count( $html, $landmark ) ) {
			throw new RuntimeException( $template . ' must preserve the shared reading shell.' );
		}
	}
}
if ( ! docszine_get_docs_tree_pages() ) {
	throw new RuntimeException( 'Docs Tree remains native without Editorial.' );
}
echo "PASS: plugin-off home, article, Page and native Docs navigation\n";
