<?php
/**
 * Generic archive template.
 *
 * @package Docszine
 */

get_header();
$shell_context = docszine_get_shell_context( array( 'route' => 'archive' ) );
?>
<?php
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'archive-main',
		'context'    => $shell_context,
	)
);
?>
	<header class="archive-header">
		<p class="eyebrow"><?php esc_html_e( 'Archive', 'docszine' ); ?></p>
		<?php the_archive_title( '<h1 class="archive-title">', '</h1>' ); ?>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
	</header>
	<div class="post-list">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
endwhile;
else :
	get_template_part( 'template-parts/content', 'none', array( 'heading_tag' => 'h2' ) );
endif;
?>
	</div>
	<?php the_posts_pagination(); ?>
<?php get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) ); ?>
<?php get_footer(); ?>
