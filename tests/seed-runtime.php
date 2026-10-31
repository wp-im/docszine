<?php
/** Disposable native WordPress fixtures. Never run on production. */
if ( ! defined( 'ABSPATH' ) || ! in_array( wp_parse_url( home_url(), PHP_URL_HOST ), array( 'localhost', '127.0.0.1' ), true ) ) {
	throw new RuntimeException( 'Loopback QA sites only.' );
}
update_option( 'blogname', 'Docszine' );
update_option( 'blogdescription', 'Documents, issues, and durable editorial reading.' );
update_option( 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/' );
$qa_page = static function ( $slug, $title, $parent = 0 ) {
	$old = get_page_by_path( $slug );
	return wp_insert_post( array( 'ID' => $old ? $old->ID : 0, 'post_type' => 'page', 'post_status' => 'publish', 'post_name' => $slug, 'post_title' => $title, 'post_parent' => $parent, 'post_excerpt' => 'Native Pages keep the collection understandable.', 'post_content' => '<h2>Reading the collection</h2><p>A quiet reading surface for a publication and its documentation.</p><h2>Maintaining the archive</h2><p>Stable paths, clear sources and native WordPress content.</p>' ) );
};
$qa_root = $qa_page( 'documentation', 'Docszine documentation' );
$qa_about = $qa_page( 'about-docszine', 'About Docszine', $qa_root );
$qa_methods = $qa_page( 'methods', 'Editorial methods', $qa_root );
$qa_detail = $qa_page( 'references', 'Stable references', $qa_methods );
update_post_meta( $qa_methods, '_wp_page_template', 'page-templates/docs-section.php' );
$qa_notes = $qa_page( 'notes', 'Notes' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $qa_root );
update_option( 'page_for_posts', $qa_notes );
set_theme_mod( 'docszine_docs_root_page', $qa_root );
$qa_issue = wp_insert_term( 'The Reading Surface', 'docszine_issue', array( 'slug' => 'reading-surface', 'description' => 'An issue about editorial reading and durable archives.' ) );
$qa_issue_id = is_wp_error( $qa_issue ) ? (int) $qa_issue->get_error_data() : $qa_issue['term_id'];
update_term_meta( $qa_issue_id, '_docszine_issue_number', 1 );
update_option( 'docszine_current_issue', $qa_issue_id );
$qa_section = wp_insert_term( 'Essays', 'category', array( 'slug' => 'essays' ) );
$qa_section_id = is_wp_error( $qa_section ) ? (int) $qa_section->get_error_data() : $qa_section['term_id'];
$qa_topic = wp_insert_term( 'Reading systems', 'post_tag', array( 'slug' => 'reading-systems' ) );
$qa_topic_id = is_wp_error( $qa_topic ) ? (int) $qa_topic->get_error_data() : $qa_topic['term_id'];
$qa_languages = array(
	'en' => array( 'Reading a durable publication', 'A reference library preserves the path to a document as carefully as the document itself. Sources, context, and stable navigation help future readers.' ),
	'zh' => array( '长期资料库：阅读、检索与维护', '资料库不仅保存内容，也要保存找到它的路径。稳定的章节、完整的引用与清楚的更新时间，让过去的资料仍能被理解。' ),
	'ja' => array( '資料を読み続けるための設計', '資料を保存することと、後から読み返せることは同じではありません。見出し、出典、更新日と関連するページへのリンクを整えます。' ),
);
$qa_posts = array();
foreach ( $qa_languages as $lang => $copy ) {
	$html = '<div lang="' . $lang . '"><h2 id="stable-reference">' . $copy[0] . '</h2>' . str_repeat( '<p>' . $copy[1] . '</p>', 8 );
	$html .= '<div class="wp-block-group"><h2>Sources and context</h2><p>' . $copy[1] . '</p></div>';
	$html .= '<h2>Tables and code</h2><figure class="wp-block-table"><table><caption>Reference archive</caption><tr><th>Record</th><th>Source</th></tr><tr><td>001</td><td>' . str_repeat( 'long-reference/', 24 ) . '</td></tr></table></figure><pre><code>' . str_repeat( 'reference-path/', 35 ) . '</code></pre>';
	$html .= '<figure><img src="' . esc_url( get_theme_file_uri( '/screenshot.png' ) ) . '" alt="Theme image ratio fixture" width="1200" height="900"><figcaption>' . $copy[1] . '</figcaption></figure>';
	$html .= '<h2>Maintenance</h2>' . str_repeat( '<p>' . $copy[1] . '</p>', 8 ) . '[docszine_note title="Reference note"]Native editorial shortcode output.[/docszine_note]</div>';
	$id = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_name' => 'reading-' . $lang, 'post_title' => $copy[0], 'post_excerpt' => $copy[1], 'post_content' => $html, 'comment_status' => 'open' ) );
	update_post_meta( $id, '_docszine_issue_order', count( $qa_posts ) + 1 );
	wp_set_object_terms( $id, array( (int) $qa_issue_id ), 'docszine_issue' );
	wp_set_object_terms( $id, array( (int) $qa_section_id ), 'category' );
	wp_set_object_terms( $id, array( (int) $qa_topic_id ), 'post_tag' );
	$qa_posts[ $lang ] = get_permalink( $id );
}
$qa_protected = wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Protected fixture', 'post_name' => 'protected-fixture', 'post_password' => wp_generate_password( 24 ), 'post_excerpt' => 'PROTECTED_EXCERPT_DO_NOT_LEAK', 'post_content' => '<h2>PROTECTED_HEADING_DO_NOT_LEAK</h2><p>PROTECTED_BODY_DO_NOT_LEAK</p><h2>Another secret</h2>' ) );
$qa_private = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'private', 'post_parent' => $qa_root, 'post_title' => 'PRIVATE_TREE_DO_NOT_LEAK', 'post_content' => '<p>Private body.</p>' ) );
$qa_menu = wp_create_nav_menu( 'QA Primary' );
foreach ( array( $qa_root, $qa_about, $qa_methods, $qa_notes ) as $id ) {
	wp_update_nav_menu_item( $qa_menu, 0, array( 'menu-item-object-id' => $id, 'menu-item-object' => 'page', 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish' ) );
}
set_theme_mod( 'nav_menu_locations', array( 'primary' => $qa_menu ) );
flush_rewrite_rules();
file_put_contents( ABSPATH . 'docszine-qa-fixtures.json', wp_json_encode( array( 'php' => PHP_VERSION, 'wordpress' => get_bloginfo( 'version' ), 'pages' => array( $qa_root, $qa_about, $qa_methods, $qa_detail, $qa_notes ), 'articles' => $qa_posts, 'issue' => get_term_link( $qa_issue_id, 'docszine_issue' ), 'protected' => get_permalink( $qa_protected ) ), JSON_PRETTY_PRINT ) );
echo "Docszine native fixtures ready.\n";
