<?php
/**
 * Dependency-free contract test for the public Classic Theme surface.
 *
 * Run with: php tests/test-theme-contract.php
 *
 * @package Docszine
 */

$theme_dir = dirname( __DIR__ ) . '/theme';

function docszine_contract_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

$required_templates = array(
	'404.php',
	'archive.php',
	'author.php',
	'category.php',
	'comments.php',
	'footer.php',
	'front-page.php',
	'functions.php',
	'header.php',
	'home.php',
	'index.php',
	'page.php',
	'search.php',
	'searchform.php',
	'single.php',
	'style.css',
	'tag.php',
	'taxonomy-docszine_issue.php',
);

foreach ( $required_templates as $template ) {
	docszine_contract_assert( is_file( $theme_dir . '/' . $template ), "required template exists: {$template}" );
}

$shell_templates = array(
	'404.php',
	'archive.php',
	'author.php',
	'category.php',
	'front-page.php',
	'home.php',
	'page.php',
	'search.php',
	'tag.php',
	'index.php',
	'single.php',
	'taxonomy-docszine_issue.php',
);

foreach ( $shell_templates as $template ) {
	$content = file_get_contents( $theme_dir . '/' . $template );
	docszine_contract_assert( false !== strpos( $content, 'template-parts/layout/shell-start' ), "shared reading shell is present: {$template}" );
}

$layout = file_get_contents( $theme_dir . '/template-parts/layout/leftbar.php' ) . file_get_contents( $theme_dir . '/template-parts/layout/rightbar.php' );
foreach ( array( 'docszine-leftbar', 'docszine-rightbar' ) as $contract ) {
	docszine_contract_assert( false !== strpos( $layout, $contract ), "shared rail contract is present: {$contract}" );
}
$template_functions = file_get_contents( $theme_dir . '/inc/template-functions.php' );
docszine_contract_assert( false !== strpos( $template_functions, 'docszine-editorial-active' ), 'body class uses Docszine namespace' );
docszine_contract_assert( false === strpos( $template_functions, 'fn-editorial-active' ), 'old editorial body class is removed' );

$header     = file_get_contents( $theme_dir . '/header.php' );
$footer     = file_get_contents( $theme_dir . '/footer.php' );
$style      = file_get_contents( $theme_dir . '/style.css' );
$search     = file_get_contents( $theme_dir . '/searchform.php' );
$css        = file_get_contents( $theme_dir . '/assets/css/magazine.css' );
$editor_css = file_get_contents( $theme_dir . '/assets/css/editor-style.css' );
$js         = file_get_contents( $theme_dir . '/assets/js/navigation.js' );

foreach ( array( 'wp_head()', 'wp_body_open()', 'body_class()', 'skip-link' ) as $contract ) {
	docszine_contract_assert( false !== strpos( $header, $contract ), "header contract is present: {$contract}" );
}
docszine_contract_assert( false !== strpos( $header, 'has_nav_menu( \'primary\' )' ), 'header omits empty primary navigation landmarks' );
docszine_contract_assert( false !== strpos( $header, 'Open search' ), 'header has a no-menu search fallback label' );
docszine_contract_assert( false !== strpos( $footer, 'wp_footer()' ), 'footer contract includes wp_footer()' );
docszine_contract_assert( false !== strpos( $footer, 'has_nav_menu( \'footer\' )' ), 'footer omits empty footer navigation landmarks' );

foreach ( array( 'Theme Name:', 'Text Domain:', 'License:', 'Domain Path:' ) as $contract ) {
	docszine_contract_assert( false !== strpos( $style, $contract ), "style.css header includes {$contract}" );
}

foreach ( array( 'role="search"', 'method="get"', 'type="search"', 'name="s"', 'screen-reader-text' ) as $contract ) {
	docszine_contract_assert( false !== strpos( $search, $contract ), "search form includes {$contract}" );
}

foreach ( array( 'height:100vh', 'height:100dvh', ':focus-visible', 'prefers-reduced-motion' ) as $contract ) {
	docszine_contract_assert( false !== strpos( $css, $contract ), "CSS fallback/accessibility contract includes {$contract}" );
}
foreach ( array( '--accent-text:#a85a0a', '--dz-accent-text:var(--accent-text)', 'color:var(--accent-text)' ) as $contract ) {
	docszine_contract_assert( false !== strpos( $css, $contract ), "CSS contrast token contract includes {$contract}" );
}
docszine_contract_assert( false === strpos( $css, 'color:var(--accent);' ), 'bright accent is not used as text color' );
docszine_contract_assert( false !== strpos( $css, 'outline:2px solid var(--accent-text);' ), 'focus outline uses the text accent' );
foreach ( array( '#929299', '#89898f', '#85858b', '#838389', '#808086', '#7b7b81', '#7b7b80', '#7a7a80', '#78787e', '#77777d', '#74747a' ) as $pale_text_color ) {
	$unsafe_color_pattern = '/(?:color|fill)\s*:\s*' . preg_quote( $pale_text_color, '/' ) . '\b/i';
	docszine_contract_assert( ! preg_match( $unsafe_color_pattern, $css . $editor_css ), "theme avoids low-contrast text color: {$pale_text_color}" );
}
foreach ( array( 'Escape', 'aria-hidden', 'matchMedia' ) as $contract ) {
	docszine_contract_assert( false !== strpos( $js, $contract ), "navigation JS contract includes {$contract}" );
}

$theme_php_source = '';
$theme_files      = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $theme_dir, FilesystemIterator::SKIP_DOTS )
);
foreach ( $theme_files as $theme_file ) {
	if ( $theme_file->isFile() && 'php' === strtolower( $theme_file->getExtension() ) ) {
		$theme_php_source .= file_get_contents( $theme_file->getPathname() );
	}
}

foreach (
	array(
		'register_taxonomy(',
		'register_post_type(',
		'add_shortcode(',
		'add_meta_box(',
		'wp_insert_post(',
		'wp_update_post(',
		'file_put_contents(',
		'fopen(',
		'\$wpdb',
	)
	as $forbidden_call
) {
	docszine_contract_assert( false === strpos( $theme_php_source, $forbidden_call ), "theme stays presentation-only: {$forbidden_call}" );
}

$front_page         = file_get_contents( $theme_dir . '/front-page.php' );
$directory_position = strpos( $front_page, 'docszine-directory' );
$editorial_position = strpos( $front_page, 'front-page-editorial' );
$issue_position     = strpos( $front_page, 'current-issue' );
docszine_contract_assert( false !== $directory_position, 'Docs-first homepage includes a directory' );
docszine_contract_assert( false !== $editorial_position && $directory_position < $editorial_position, 'homepage editorial content follows the Docs directory' );
docszine_contract_assert( false !== $issue_position && $editorial_position < $issue_position, 'current Issue follows the Docs-first homepage content' );

echo "PASS: Docszine theme contract\n";
