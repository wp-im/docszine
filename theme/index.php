<?php
/**
 * Main fallback template.
 *
 * @package Docszine
 */

get_header();
$shell_context = docszine_get_shell_context( array( 'route' => 'archive' ) );
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'site-main generic-main',
		'context'    => $shell_context,
	)
);
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', get_post_type() );
	endwhile;
	the_posts_pagination();
else :
	get_template_part( 'template-parts/content', 'none' );
endif;
get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) );
get_footer();
