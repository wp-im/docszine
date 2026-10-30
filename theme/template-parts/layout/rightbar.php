<?php
/**
 * Shared right rail: TOC, widgets, and contextual fallback.
 *
 * @package Docszine
 */

$context  = isset( $args['context'] ) && is_array( $args['context'] ) ? $args['context'] : docszine_get_shell_context();
$headings = ! empty( $context['headings'] ) ? $context['headings'] : array();
?>
<div class="docszine-rail-inner">
	<?php if ( $headings ) : ?>
		<?php
		get_template_part(
			'template-parts/navigation/toc',
			null,
			array(
				'headings' => $headings,
				'variant'  => 'rail',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'docszine-rightbar' ) ) : ?>
		<div class="docszine-widget-stack"><?php dynamic_sidebar( 'docszine-rightbar' ); ?></div>
	<?php else : ?>
		<?php get_template_part( 'template-parts/navigation/contextual', null, array( 'context' => $context ) ); ?>
	<?php endif; ?>
</div>
