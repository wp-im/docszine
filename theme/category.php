<?php
/**
 * Native Category archive used as a long-term magazine Section.
 *
 * @package Docszine
 */

get_header();
$queried_section = get_queried_object();
$shell_context   = docszine_get_shell_context( array( 'route' => 'archive' ) );
?>
<?php
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'archive-main section-archive',
		'context'    => $shell_context,
	)
);
?>
	<header class="archive-header">
		<p class="eyebrow"><?php esc_html_e( 'Section', 'docszine' ); ?></p>
		<h1 class="archive-title"><?php echo esc_html( single_cat_title( '', false ) ); ?></h1>
		<?php
		if ( term_description() ) :
			?>
			<div class="archive-description"><?php echo wp_kses_post( term_description() ); ?></div><?php endif; ?>
	</header>
	<?php if ( have_posts() ) : ?>
		<?php /* translators: %d: number of published posts in this section. */ ?>
		<div class="docszine-section-summary"><strong><?php esc_html_e( 'Section index', 'docszine' ); ?></strong> <?php echo esc_html( sprintf( _n( '%d story in this section.', '%d stories in this section.', (int) $queried_section->count, 'docszine' ), (int) $queried_section->count ) ); ?></div>
		<section class="docszine-section-index" aria-labelledby="docszine-section-index-title">
			<div class="section-head"><h2 id="docszine-section-index-title"><?php esc_html_e( 'Browse this section', 'docszine' ); ?></h2><span><?php esc_html_e( 'Read in order or choose a single entry', 'docszine' ); ?></span></div>
			<div class="docszine-directory-list">
				<?php
				$section_page  = max( 1, absint( get_query_var( 'paged' ) ) );
				$section_limit = max( 1, absint( get_query_var( 'posts_per_page' ) ) );
				$section_index = ( $section_page - 1 ) * $section_limit;
				while ( have_posts() ) :
					the_post();
					++$section_index;
					?>
					<a class="docszine-directory-row<?php echo 1 === $section_index ? ' is-primary' : ''; ?>" href="<?php the_permalink(); ?>">
						<span class="docszine-directory-num"><?php echo esc_html( str_pad( (string) $section_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span><span class="docszine-directory-title"><?php the_title(); ?></span><span class="docszine-directory-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></span><span class="docszine-directory-meta"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?> · <?php echo esc_html( get_the_author() ); ?></span></span>
						<span class="docszine-directory-arrow" aria-hidden="true">→</span>
					</a>
				<?php endwhile; ?>
			</div>
		</section>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none', array( 'heading_tag' => 'h2' ) ); ?>
	<?php endif; ?>
	<?php the_posts_pagination(); ?>
<?php get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) ); ?>
<?php get_footer(); ?>
