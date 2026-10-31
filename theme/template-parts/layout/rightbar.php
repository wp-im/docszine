<?php
/**
 * Shared right rail: native widgets and publication context, never article TOC.
 *
 * @package Docszine
 */

$context = isset( $args['context'] ) && is_array( $args['context'] ) ? $args['context'] : docszine_get_shell_context();
?>
<div class="docszine-rail-inner">
	<?php if ( is_active_sidebar( 'docszine-rightbar' ) ) : ?>
		<div class="docszine-widget-stack"><?php dynamic_sidebar( 'docszine-rightbar' ); ?></div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/navigation/contextual', null, array( 'context' => $context ) ); ?>
	<?php endif; ?>
</div>
