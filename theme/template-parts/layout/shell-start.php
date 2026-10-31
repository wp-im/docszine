<?php
/**
 * Start the shared reading shell.
 *
 * @package Docszine
 */

$context    = isset( $args['context'] ) && is_array( $args['context'] ) ? docszine_get_shell_context( $args['context'] ) : docszine_get_shell_context();
$main_class = 'docszine-main';
if ( isset( $args['main_class'] ) && is_string( $args['main_class'] ) ) {
	$main_class_tokens = preg_split( '/\s+/', trim( $args['main_class'] ) );
	$main_class_tokens = array_filter( array_map( 'sanitize_html_class', $main_class_tokens ) );
	$main_class        = $main_class_tokens ? implode( ' ', $main_class_tokens ) : $main_class;
}
$shell_classes = array( 'docszine-shell', 'is-' . sanitize_html_class( $context['route'] ) );
if ( ! empty( $context['issue'] ) ) {
	$shell_classes[] = 'has-issue-context';
}
if ( ! empty( $context['headings'] ) ) {
	$shell_classes[] = 'has-toc';
}
?>
<div class="docszine-local-bar">
	<div class="docszine-local-bar-inner">
		<button class="local-trigger" type="button" data-leftbar-open aria-controls="docszine-leftbar" aria-expanded="false"><span aria-hidden="true">☰</span><span class="local-trigger-label"><?php esc_html_e( 'Browse', 'docszine' ); ?></span></button>
		<button class="local-trigger" type="button" data-rightbar-open aria-controls="docszine-rightbar" aria-expanded="false"><span class="local-trigger-label"><?php esc_html_e( 'Sidebar', 'docszine' ); ?></span><span aria-hidden="true">→</span></button>
	</div>
</div>
<div class="docszine-drawer-backdrop" data-drawer-backdrop hidden></div>
<div class="<?php echo esc_attr( $main_class ); ?>">
	<div class="<?php echo esc_attr( implode( ' ', $shell_classes ) ); ?>">
		<aside id="docszine-leftbar" class="docszine-rail docszine-leftbar" aria-label="<?php esc_attr_e( 'Browse the publication', 'docszine' ); ?>" aria-hidden="false">
			<div class="docszine-rail-head"><strong><?php esc_html_e( 'Browse', 'docszine' ); ?></strong><button class="close-drawer" type="button" data-leftbar-close aria-label="<?php esc_attr_e( 'Close publication navigation', 'docszine' ); ?>">×</button></div>
			<?php get_template_part( 'template-parts/layout/leftbar', null, array( 'context' => $context ) ); ?>
		</aside>
		<main id="primary" class="docszine-main-column" tabindex="-1">
