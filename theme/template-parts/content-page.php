<?php
/**
 * Page content.
 *
 * @package Docszine
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-entry' ); ?>>
	<header class="page-header"><h1 class="page-title-generic"><?php the_title(); ?></h1></header>
	<div class="page-content docszine-prose">
	<?php if ( '' === trim( get_the_content() ) ) : ?>
		<p class="empty-content-message"><?php esc_html_e( 'This page is ready for its first note.', 'docszine' ); ?></p>
	<?php else : ?>
		<?php the_content(); ?><?php wp_link_pages(); ?>
	<?php endif; ?>
	</div>
</article>
