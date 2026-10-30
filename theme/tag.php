<?php
/**
 * Native Tag archive used as a cross-issue Topic.
 *
 * @package Docszine
 */

get_header();
$queried_term  = get_queried_object();
$shell_context = docszine_get_shell_context( array( 'route' => 'archive' ) );
?>
<?php
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'archive-main topic-archive',
		'context'    => $shell_context,
	)
);
?>
	<header class="archive-header">
		<p class="eyebrow"><?php esc_html_e( 'Topic', 'docszine' ); ?></p>
		<h1 class="archive-title"><?php single_tag_title(); ?></h1>
		<?php /* translators: %d: number of stories in this topic. */ ?>
		<p class="small-label"><?php echo esc_html( sprintf( _n( '%d story across issues', '%d stories across issues', (int) $queried_term->count, 'docszine' ), (int) $queried_term->count ) ); ?></p>
		<?php
		if ( term_description() ) :
			?>
			<div class="archive-description"><?php echo wp_kses_post( term_description() ); ?></div><?php endif; ?>
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
