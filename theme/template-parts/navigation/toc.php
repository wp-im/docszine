<?php
/**
 * Reusable article table of contents.
 *
 * @package Docszine
 */

$headings = isset( $args['headings'] ) && is_array( $args['headings'] ) ? $args['headings'] : array();
if ( count( $headings ) < 2 ) {
	return;
}
?>
<details class="docszine-toc docszine-toc--article">
	<summary><?php esc_html_e( 'On this page', 'docszine' ); ?></summary>
	<nav aria-label="<?php esc_attr_e( 'On this page', 'docszine' ); ?>">
	<?php foreach ( $headings as $heading ) : ?>
		<a href="#<?php echo esc_attr( rawurlencode( $heading['id'] ) ); ?>"><?php echo esc_html( $heading['title'] ); ?></a>
	<?php endforeach; ?>
	</nav>
</details>
