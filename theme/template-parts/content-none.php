<?php
/**
 * Empty-state content.
 *
 * @package Docszine
 */

$args = isset( $args ) && is_array( $args ) ? $args : array();

$empty_title   = isset( $args['title'] ) && is_string( $args['title'] ) ? $args['title'] : __( 'Nothing found', 'docszine' );
$empty_message = isset( $args['message'] ) && is_string( $args['message'] ) ? $args['message'] : __( 'There is no content here yet.', 'docszine' );
$heading_tag   = isset( $args['heading_tag'] ) && 'h2' === $args['heading_tag'] ? 'h2' : 'h1';

?>
<section class="no-results not-found">
	<header class="page-header">
	<?php if ( 'h2' === $heading_tag ) : ?>
		<h2 class="page-title-generic"><?php echo esc_html( $empty_title ); ?></h2>
	<?php else : ?>
		<h1 class="page-title-generic"><?php echo esc_html( $empty_title ); ?></h1>
	<?php endif; ?>
	</header>
	<div class="page-content"><p><?php echo esc_html( $empty_message ); ?></p><?php get_search_form(); ?></div>
</section>
