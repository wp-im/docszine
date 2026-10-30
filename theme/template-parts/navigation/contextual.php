<?php
/**
 * Contextual fallback for an empty Rightbar.
 *
 * @package Docszine
 */

$context = isset( $args['context'] ) && is_array( $args['context'] ) ? $args['context'] : docszine_get_shell_context();
$issue   = ! empty( $context['issue'] ) ? $context['issue'] : null;
?>
<section class="docszine-contextual">
	<?php if ( 'article' === $context['route'] && ! empty( $context['post_id'] ) ) : ?>
		<?php $related = docszine_get_related_section_posts( $context['post_id'], 3 ); ?>
		<?php if ( $related ) : ?>
			<h2 class="docszine-rail-label"><?php esc_html_e( 'Continue reading', 'docszine' ); ?></h2>
			<div class="docszine-context-list">
				<?php foreach ( $related as $related_post ) : ?>
					<a href="<?php echo esc_url( get_permalink( $related_post ) ); ?>"><?php echo esc_html( get_the_title( $related_post ) ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	<?php elseif ( $issue ) : ?>
		<?php $issue_link = get_term_link( $issue ); ?>
		<p class="docszine-rail-label"><?php esc_html_e( 'This Issue', 'docszine' ); ?></p>
		<h2><?php echo esc_html( $issue->name ); ?></h2>
		<?php
		if ( ! is_wp_error( $issue_link ) ) :
			?>
			<a class="docszine-context-link" href="<?php echo esc_url( $issue_link ); ?>"><?php esc_html_e( 'View Issue', 'docszine' ); ?> <span aria-hidden="true">→</span></a><?php endif; ?>
	<?php else : ?>
		<p class="docszine-rail-label"><?php esc_html_e( 'Reading room', 'docszine' ); ?></p>
		<p><?php esc_html_e( 'Follow a section, topic, or Issue to continue through the publication.', 'docszine' ); ?></p>
	<?php endif; ?>
</section>
