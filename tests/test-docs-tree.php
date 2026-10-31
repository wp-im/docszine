<?php
/**
 * Dependency-free regression test for the native Docs Tree data contract.
 *
 * Run with: php tests/test-docs-tree.php
 */

define( 'ABSPATH', __DIR__ );

class WP_Post {
	public $ID;
	public $post_type   = 'page';
	public $post_status = 'publish';
	public $post_parent = 0;

	public function __construct( $id, $parent = 0 ) {
		$this->ID          = $id;
		$this->post_parent = $parent;
	}
}

$docszine_test_pages = array(
	new WP_Post( 1 ),
	new WP_Post( 2, 1 ),
	new WP_Post( 3, 1 ),
	new WP_Post( 4, 2 ),
	new WP_Post( 5 ),
);

function absint( $value ) {
	return abs( (int) $value ); }
function get_current_blog_id() {
	return 1; }
function get_locale() {
	global $docszine_test_locale;
	return $docszine_test_locale ?? 'en_US'; }
function get_theme_mod( $name, $default = false ) {
	return 'docszine_docs_root_page' === $name ? 1 : $default; }
function get_post( $post_id ) {
	return 1 === (int) $post_id ? new WP_Post( 1 ) : null; }
function get_pages( $args = array() ) {
	global $docszine_test_pages;
	return $docszine_test_pages; }
function get_page_children( $page_id, $pages ) {
	$children = array();
	foreach ( $pages as $page ) {
		if ( (int) $page->post_parent === (int) $page_id ) {
			$children[] = $page;
			$children   = array_merge( $children, get_page_children( $page->ID, $pages ) );
		}
	}
	return $children;
}
function is_page() {
	return true; }
function get_queried_object_id() {
	return 4; }
function get_post_ancestors( $post_id ) {
	return 2 === (int) $post_id ? array( 1 ) : array( 2, 1 ); }

require dirname( __DIR__ ) . '/theme/inc/docs-tree.php';

function docszine_test_assert_tree( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

$tree      = docszine_get_docs_tree_pages();
$directory = docszine_get_docs_directory_pages();

docszine_test_assert_tree( 4 === count( $tree ), 'the configured root and descendants are returned' );
docszine_test_assert_tree( 1 === $tree[0]->ID && 2 === $tree[1]->ID && 4 === $tree[2]->ID && 3 === $tree[3]->ID, 'native page order is preserved' );
docszine_test_assert_tree( 2 === count( $directory ), 'the first directory level contains root children' );
docszine_test_assert_tree( 2 === $directory[0]->ID && 3 === $directory[1]->ID, 'directory entries are direct children of the root' );
docszine_test_assert_tree( ! docszine_is_docs_page_current( 1 ), 'the root is not marked as the current Page' );
docszine_test_assert_tree( docszine_is_docs_page_ancestor( 1 ), 'the root is marked as a current Page ancestor' );
docszine_test_assert_tree( docszine_is_docs_page_ancestor( 2 ), 'the Section is marked as a current Page ancestor' );
docszine_test_assert_tree( docszine_is_docs_page_current( 4 ), 'the current Page is marked current' );

$docszine_test_locale = 'ja';
$docszine_test_pages  = array( new WP_Post( 1 ) );
for ( $id = 2; $id <= 1000; ++$id ) {
	$docszine_test_pages[] = new WP_Post( $id, $id - 1 );
}
$large_tree = docszine_get_docs_tree_pages();
docszine_test_assert_tree( 1000 === count( $large_tree ) && 1000 === end( $large_tree )->ID, 'deep 1000-page tree is complete and isolated by locale' );

$docszine_test_locale = 'zh_CN';
$docszine_test_pages  = array( new WP_Post( 1, 3 ), new WP_Post( 2, 1 ), new WP_Post( 3, 2 ) );
$cycle_tree = docszine_get_docs_tree_pages();
docszine_test_assert_tree( 3 === count( $cycle_tree ), 'cyclic parent data terminates with each Page once' );

echo "PASS: Docszine Docs Tree contract\n";
