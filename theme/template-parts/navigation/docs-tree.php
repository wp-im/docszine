<?php
/**
 * Shared Docs Tree navigation.
 *
 * @package Docszine
 */

$args = isset( $args ) && is_array( $args ) ? $args : array();
?>
<div class="docszine-docs-navigation">
	<div class="docszine-tree-search">
		<?php get_search_form(); ?>
	</div>
	<?php docszine_render_docs_tree( $args ); ?>
</div>
