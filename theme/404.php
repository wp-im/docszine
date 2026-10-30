<?php
/**
 * 404 template.
 *
 * @package Docszine
 */

get_header();
$shell_context = docszine_get_shell_context( array( 'route' => 'error' ) );
?>
<?php
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'error-main',
		'context'    => $shell_context,
	)
);
?>
	<header class="error-header"><p class="eyebrow">404</p><h1 class="error-title"><?php esc_html_e( 'Page not found', 'docszine' ); ?></h1></header>
	<p><?php esc_html_e( 'The page may have moved, or the address may be incorrect. Search the magazine instead.', 'docszine' ); ?></p>
	<?php get_search_form(); ?>
<?php get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) ); ?>
<?php get_footer(); ?>
