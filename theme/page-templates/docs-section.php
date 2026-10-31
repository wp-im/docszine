<?php
/**
 * Template Name: Docs Section Index
 * Template Post Type: page
 *
 * @package Docszine
 */

get_header();
$shell_context = docszine_get_shell_context( array( 'route' => 'docs-section' ) );
get_template_part( 'template-parts/layout/shell-start', null, array( 'context' => $shell_context ) );
while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content', 'page' );
	$children = get_pages(
		array(
			'parent'      => get_the_ID(),
			'post_status' => 'publish',
			'sort_column' => 'menu_order,post_title,ID',
		)
	);
	?>
	<section class="docszine-directory docszine-section-directory" aria-labelledby="docszine-section-heading">
		<div class="section-head"><h2 id="docszine-section-heading"><?php esc_html_e( 'In this section', 'docszine' ); ?></h2></div>
		<?php if ( $children ) : ?>
			<div class="docszine-directory-list">
				<?php foreach ( $children as $index => $child ) : ?>
					<a class="docszine-directory-row" href="<?php echo esc_url( get_permalink( $child ) ); ?>">
						<span class="docszine-directory-num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span><span class="docszine-directory-title"><?php echo esc_html( docszine_get_title( $child ) ); ?></span><span class="docszine-directory-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt( $child ), 24 ) ); ?></span></span>
						<span class="docszine-directory-arrow" aria-hidden="true">→</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'This section has no child Pages yet.', 'docszine' ); ?></p>
		<?php endif; ?>
	</section>
	<?php
endwhile;
get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) );
get_footer();
