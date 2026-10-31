<?php
/**
 * Search results.
 *
 * @package Docszine
 */

get_header();
$shell_context = docszine_get_shell_context( array( 'route' => 'search' ) );
?>
<?php
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'search-main',
		'context'    => $shell_context,
	)
);
?>
	<header class="search-header"><p class="eyebrow"><?php esc_html_e( 'Search', 'docszine' ); ?></p><?php /* translators: %s: search query. */ ?><h1 class="search-title"><?php printf( esc_html__( 'Results for “%s”', 'docszine' ), esc_html( get_search_query() ) ); ?></h1></header>
	<div class="post-list">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content' );
endwhile;
else :
	get_template_part( 'template-parts/content', 'none', array( 'heading_tag' => 'h2' ) );
endif;
?>
	</div>
	<?php the_posts_pagination(); ?>
<?php get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) ); ?>
<?php get_footer(); ?>
