<?php
/**
 * Page content.
 *
 * @package Docszine
 */

$docszine_page_html = docszine_add_article_heading_ids( apply_filters( 'the_content', get_the_content() ) );
$docszine_headings  = post_password_required() ? array() : docszine_get_article_headings( $docszine_page_html );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-entry' ); ?>>
	<header class="page-header"><h1 class="page-title-generic"><?php echo wp_kses_post( docszine_get_title() ); ?></h1></header>
	<?php get_template_part( 'template-parts/navigation/toc', null, array( 'headings' => $docszine_headings ) ); ?>
	<div class="page-content docszine-prose">
	<?php if ( '' === trim( $docszine_page_html ) ) : ?>
		<p class="empty-content-message"><?php esc_html_e( 'This page is ready for its first note.', 'docszine' ); ?></p>
	<?php else : ?>
		<?php echo $docszine_page_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Native the_content filters have run once. ?><?php wp_link_pages(); ?>
	<?php endif; ?>
	</div>
</article>
