<?php
/** Native WordPress regression; run with Studio wp eval-file on loopback QA only. */

if ( ! defined( 'ABSPATH' ) || ! in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) ) {
	throw new RuntimeException( 'Loopback WordPress QA only.' );
}
if ( ! taxonomy_exists( 'docszine_issue' ) ) {
	throw new RuntimeException( 'Activate the optional Editorial plugin for this test.' );
}

global $wpdb;
// WP-CLI does not populate the HTTP server context used by core header helpers.
$_SERVER['SERVER_NAME'] = wp_parse_url( home_url(), PHP_URL_HOST );
$check = static function ( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
};

// Keep the preview database unchanged, including term counts and options.
$wpdb->query( 'START TRANSACTION' );
try {
	$term = wp_insert_term( 'QA transactional Issue ' . wp_generate_uuid4(), 'docszine_issue' );
	$check( ! is_wp_error( $term ), 'Create temporary Issue.' );
	$issue_id = $term['term_id'];
	$ids      = array();
	wp_defer_term_counting( true );
	for ( $i = 0; $i < 121; ++$i ) {
		$id = wp_insert_post( array(
			'post_type' => 'post', 'post_status' => 'publish',
			'post_title' => 'Transactional story ' . $i,
			'post_date' => '2000-01-01 00:00:00',
			'post_content' => '<p>Disposable fixture.</p>',
		), true );
		$check( ! is_wp_error( $id ), 'Create temporary story.' );
		wp_set_object_terms( $id, array( (int) $issue_id ), 'docszine_issue' );
		$ids[] = $id;
	}
	// The last-created story must win even though it was outside the old SQL cap.
	update_post_meta( $ids[120], '_docszine_issue_order', 1 );
	update_post_meta( $ids[119], '_docszine_issue_order', 2 );
	wp_defer_term_counting( false );
	$first = docszine_get_issue_posts( $issue_id, 1 );
	$check( $first[0]->ID === $ids[120], 'Editorial order applies before display limit.' );
	$queries = $wpdb->num_queries;
	$all     = docszine_get_issue_posts( $issue_id, 0 );
	$check( 121 === count( $all ), 'Complete Issue includes more than 100 stories.' );
	$check( $queries === $wpdb->num_queries, 'Different limits share the same per-request query.' );
	$check( $all[1]->ID === $ids[119] && $all[2]->ID === $ids[0], 'Explicit order precedes unnumbered stories; ties use ID.' );
	foreach ( $all as $index => $story ) {
		foreach ( array( 'previous' => $index - 1, 'next' => $index + 1 ) as $direction => $target ) {
			$actual = docszine_get_issue_adjacent_post( $story->ID, $direction );
			$check( ( $actual ? $actual->ID : 0 ) === ( isset( $all[$target] ) ? $all[$target]->ID : 0 ), 'Navigation reaches the full sequence in both directions.' );
		}
	}
	$groups = docszine_get_issue_posts_grouped( $issue_id );
	$check( 121 === array_sum( array_map( static function ( $group ) { return count( $group['posts'] ); }, $groups ) ), 'Grouping preserves all stories.' );
	ob_start();
	docszine_render_issue_contents_list( get_term( $issue_id ), $all[120]->ID );
	$sidebar = ob_get_clean();
	$check( 102 === substr_count( $sidebar, '<a' ), 'Sidebar has at most 100 stories, the current story and complete contents link.' );
	$check( false !== strpos( $sidebar, 'aria-current="page"' ), 'Current story remains visible beyond sidebar limit.' );
	global $wp_query;
	$original_query = $wp_query;
	foreach ( array( array( 10, 1, 10 ), array( 10, 13, 1 ), array( 30, 1, 30 ), array( 30, 2, 30 ), array( 30, 5, 1 ), array( 100, 1, 100 ), array( 100, 2, 21 ), array( -1, 1, 121 ) ) as $case ) {
		list( $page_size, $page, $expected_entries ) = $case;
		$wp_query = new WP_Query( array( 'post_type' => 'post', 'docszine_issue' => get_term( $issue_id )->slug, 'paged' => $page, 'posts_per_page' => $page_size ) );
		$check( $wp_query->post_count > 0, 'Every Issue contents page also has native query results.' );
		ob_start();
		include get_template_directory() . '/taxonomy-docszine_issue.php';
		$archive = ob_get_clean();
		$check( $expected_entries === substr_count( $archive, '<article class="issue-entry ' ), 'Native Issue template paginates all stories.' );
		$check( ( $page_size > 0 ) === ( false !== strpos( $archive, 'Issue contents pages' ) ), 'Pagination follows native page-size and no-paging settings.' );
	}
	$wp_query = $original_query;
	wp_reset_postdata();

	$html = '<p id="future"></p><h2>Future</h2><h2 id="a:b">Punctuation</h2><h2 id="a:b">Duplicate</h2><h2>日本語の見出し</h2><h2 hidden>Hidden</h2>';
	$parsed = docszine_parse_article_headings( $html );
	$check( 4 === count( $parsed['headings'] ), 'Modern parser omits hidden headings.' );
	$anchors = array_column( $parsed['headings'], 'id' );
	$check( count( $anchors ) === count( array_unique( $anchors ) ), 'All visible heading anchors are unique.' );
	$check( 'future' !== $anchors[0] && 'a:b' === $anchors[1], 'Existing document IDs are reserved; explicit punctuation ID preserved.' );
	echo "PASS: native library order, 121-story navigation, request cache, bounded sidebar and modern heading IDs\n";
} finally {
	wp_defer_term_counting( false );
	$wpdb->query( 'ROLLBACK' );
	wp_cache_flush();
}
$remaining = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->posts . ' WHERE ID IN (' . implode( ',', array_map( 'intval', $ids ) ) . ')' );
$check( 0 === (int) $remaining, 'Fixture transaction must leave no test posts behind.' );
echo "PASS: fixture database rollback\n";
