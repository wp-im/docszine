<?php
/**
 * Reusable article table of contents.
 *
 * @package Docszine
 */

$headings = isset( $args['headings'] ) && is_array( $args['headings'] ) ? $args['headings'] : array();
$variant  = isset( $args['variant'] ) && 'rail' === $args['variant'] ? 'rail' : 'inline';
if ( ! $headings ) {
	return;
}
?>
<nav class="docszine-toc docszine-toc--<?php echo esc_attr( $variant ); ?>" aria-label="<?php esc_attr_e( 'On this page', 'docszine' ); ?>">
	<strong><?php esc_html_e( 'On this page', 'docszine' ); ?></strong>
	<?php foreach ( $headings as $heading ) : ?>
		<a href="#<?php echo esc_attr( $heading['id'] ); ?>"><?php echo esc_html( $heading['title'] ); ?></a>
	<?php endforeach; ?>
</nav>
