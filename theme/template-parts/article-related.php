<?php
/**
 * Related article list.
 *
 * @package Docszine
 */

$related_title = isset( $args['title'] ) ? $args['title'] : '';
$related_term  = isset( $args['term'] ) ? $args['term'] : null;
$related_posts = isset( $args['posts'] ) && is_array( $args['posts'] ) ? $args['posts'] : array();

if ( empty( $related_posts ) ) {
	return;
}
?>
<section class="related-block">
	<div class="related-head">
		<h2><?php echo esc_html( $related_title ); ?></h2>
		<?php
		if ( $related_term && ! is_wp_error( get_term_link( $related_term ) ) ) :
			?>
			<a class="link-arrow" href="<?php echo esc_url( get_term_link( $related_term ) ); ?>"><?php esc_html_e( 'View all', 'docszine' ); ?></a><?php endif; ?>
	</div>
	<div class="related-list">
		<?php foreach ( $related_posts as $related_post ) : ?>
			<?php $related_issue = docszine_get_post_issue( $related_post->ID ); ?>
			<a href="<?php echo esc_url( get_permalink( $related_post ) ); ?>"><strong><?php echo esc_html( get_the_title( $related_post ) ); ?></strong><span><?php echo esc_html( $related_issue ? docszine_get_issue_display_label( $related_issue ) : get_the_date( '', $related_post ) ); ?></span></a>
		<?php endforeach; ?>
	</div>
</section>
