<?php
/**
 * Comments template.
 *
 * @package Docszine
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<?php /* translators: %1$s: number of comments. */ ?>
		<h2 class="comments-title"><?php printf( esc_html( _nx( '%1$s comment', '%1$s comments', get_comments_number(), 'comments title', 'docszine' ) ), esc_html( number_format_i18n( get_comments_number() ) ) ); ?></h2>
		<ol class="comment-list">
		<?php
		wp_list_comments(
			array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 42,
			)
		);
		?>
									</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php
	if ( ! comments_open() && get_comments_number() ) :
		?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'docszine' ); ?></p><?php endif; ?>
	<?php comment_form(); ?>
</section>
