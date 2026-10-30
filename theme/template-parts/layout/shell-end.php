<?php
/**
 * End the shared reading shell.
 *
 * @package Docszine
 */

$context = isset( $args['context'] ) && is_array( $args['context'] ) ? docszine_get_shell_context( $args['context'] ) : docszine_get_shell_context();
?>
		</div><!-- .docszine-main-column -->
		<aside id="docszine-rightbar" class="docszine-rail docszine-rightbar" aria-label="<?php esc_attr_e( 'More from Docszine', 'docszine' ); ?>" aria-hidden="false">
			<div class="docszine-rail-head"><strong><?php esc_html_e( 'More reading', 'docszine' ); ?></strong><button class="close-drawer" type="button" data-rightbar-close aria-label="<?php esc_attr_e( 'Close additional reading', 'docszine' ); ?>">×</button></div>
			<?php get_template_part( 'template-parts/layout/rightbar', null, array( 'context' => $context ) ); ?>
		</aside>
	</div>
</main>
