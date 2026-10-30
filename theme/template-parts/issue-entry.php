<?php
/**
 * One entry inside an Issue page.
 *
 * @package Docszine
 */

$issue_post = isset( $args['post'] ) ? $args['post'] : null;
if ( ! $issue_post instanceof WP_Post ) {
	return;
}
$excerpt       = get_the_excerpt( $issue_post );
$has_thumbnail = has_post_thumbnail( $issue_post );
?>
<article class="issue-entry <?php echo $has_thumbnail ? 'has-thumbnail' : 'no-thumbnail'; ?>">
	<div class="issue-entry-copy">
		<h3><a href="<?php echo esc_url( get_permalink( $issue_post ) ); ?>"><?php echo esc_html( get_the_title( $issue_post ) ); ?></a></h3>
		<?php
		if ( $excerpt ) :
			?>
			<p><?php echo esc_html( wp_trim_words( $excerpt, 34 ) ); ?></p><?php endif; ?>
		<?php /* translators: %d: estimated reading time in minutes. */ ?>
		<div class="entry-meta"><?php echo esc_html( get_the_author_meta( 'display_name', $issue_post->post_author ) ); ?> · <?php echo esc_html( sprintf( __( '%d min read', 'docszine' ), docszine_get_read_time( $issue_post->ID ) ) ); ?></div>
	</div>
	<?php if ( $has_thumbnail ) : ?>
		<a class="issue-thumb" href="<?php echo esc_url( get_permalink( $issue_post ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo get_the_post_thumbnail( $issue_post, 'medium_large', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
	<?php endif; ?>
</article>
