<?php
/**
 * Previous / next article navigation.
 *
 * @package Docszine
 */

$previous = isset( $args['previous'] ) ? $args['previous'] : null;
$next     = isset( $args['next'] ) ? $args['next'] : null;
$context  = isset( $args['context'] ) ? $args['context'] : 'issue';

if ( ! $previous && ! $next ) {
	return;
}
?>
<nav class="article-prevnext" aria-label="<?php esc_attr_e( 'Article navigation', 'docszine' ); ?>">
	<div class="prev-slot">
		<?php if ( $previous ) : ?>
			<a href="<?php echo esc_url( get_permalink( $previous ) ); ?>"><span class="nav-kicker"><?php echo esc_html( 'issue' === $context ? __( 'Previous in issue', 'docszine' ) : __( 'Previous', 'docszine' ) ); ?></span><span class="nav-title"><?php echo esc_html( get_the_title( $previous ) ); ?></span></a>
		<?php endif; ?>
	</div>
	<div class="next-slot">
		<?php if ( $next ) : ?>
			<a class="next" href="<?php echo esc_url( get_permalink( $next ) ); ?>"><span class="nav-kicker"><?php echo esc_html( 'issue' === $context ? __( 'Next in issue', 'docszine' ) : __( 'Next', 'docszine' ) ); ?></span><span class="nav-title"><?php echo esc_html( get_the_title( $next ) ); ?></span></a>
		<?php endif; ?>
	</div>
</nav>
