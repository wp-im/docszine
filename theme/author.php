<?php
/**
 * Author archive.
 *
 * @package Docszine
 */

get_header();
$author        = get_queried_object();
$shell_context = docszine_get_shell_context( array( 'route' => 'archive' ) );
?>
<?php
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'archive-main author-archive',
		'context'    => $shell_context,
	)
);
?>
	<header class="archive-header author-header">
		<p class="eyebrow"><?php esc_html_e( 'Author', 'docszine' ); ?></p>
		<h1 class="archive-title"><?php echo esc_html( $author->display_name ); ?></h1>
		<?php
		if ( get_the_author_meta( 'description', $author->ID ) ) :
			?>
			<div class="archive-description"><?php echo wp_kses_post( wpautop( get_the_author_meta( 'description', $author->ID ) ) ); ?></div><?php endif; ?>
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
