<?php
/**
 * Small editorial shortcodes for the Classic Editor.
 *
 * @package Docszine_Editorial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return shortcode content through normal WordPress filters.
 *
 * @param string $content Nested shortcode content.
 * @return string
 */
function docszine_shortcode_content( $content ) {
	return wp_kses_post( wpautop( do_shortcode( (string) $content ) ) );
}

/**
 * Render a note aside.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 * @return string
 */
function docszine_shortcode_note( $atts, $content = '' ) {
	$atts = shortcode_atts( array( 'title' => __( 'Note', 'docszine-editorial' ) ), $atts, 'docszine_note' );
	return '<aside class="docszine-note"><strong>' . esc_html( sanitize_text_field( $atts['title'] ) ) . '</strong>' . docszine_shortcode_content( $content ) . '</aside>';
}
add_shortcode( 'docszine_note', 'docszine_shortcode_note' );

/**
 * Render a context aside.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 * @return string
 */
function docszine_shortcode_context( $atts, $content = '' ) {
	$atts = shortcode_atts( array( 'title' => __( 'Context', 'docszine-editorial' ) ), $atts, 'docszine_context' );
	return '<aside class="docszine-context"><strong>' . esc_html( sanitize_text_field( $atts['title'] ) ) . '</strong>' . docszine_shortcode_content( $content ) . '</aside>';
}
add_shortcode( 'docszine_context', 'docszine_shortcode_context' );

/**
 * Render a definition aside.
 *
 * @param array  $atts    Shortcode attributes.
 * @param string $content Shortcode content.
 * @return string
 */
function docszine_shortcode_definition( $atts, $content = '' ) {
	$atts = shortcode_atts( array( 'term' => __( 'Definition', 'docszine-editorial' ) ), $atts, 'docszine_definition' );
	return '<aside class="docszine-definition"><strong>' . esc_html( sanitize_text_field( $atts['term'] ) ) . '</strong>' . docszine_shortcode_content( $content ) . '</aside>';
}
add_shortcode( 'docszine_definition', 'docszine_shortcode_definition' );

/**
 * Render a verified image Attachment as a margin or breakout figure.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function docszine_shortcode_figure( $atts ) {
	$atts   = shortcode_atts(
		array(
			'id'      => 0,
			'layout'  => 'breakout',
			'caption' => '',
		),
		$atts,
		'docszine_figure'
	);
	$id     = absint( $atts['id'] );
	$layout = in_array( $atts['layout'], array( 'margin', 'breakout' ), true ) ? $atts['layout'] : 'breakout';
	if ( ! $id || 'attachment' !== get_post_type( $id ) || ! wp_attachment_is_image( $id ) ) {
		return '';
	}
	$image              = wp_get_attachment_image( $id, 'large', false, array( 'loading' => 'lazy' ) );
	$attachment_caption = wp_get_attachment_caption( $id );
	$caption            = '' !== trim( $atts['caption'] ) ? '<figcaption>' . esc_html( sanitize_text_field( $atts['caption'] ) ) . '</figcaption>' : ( $attachment_caption ? '<figcaption>' . wp_kses_post( $attachment_caption ) . '</figcaption>' : '' );
	return '<figure class="docszine-media--' . esc_attr( $layout ) . '">' . $image . $caption . '</figure>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress image and caption helpers are escaped.
}
add_shortcode( 'docszine_figure', 'docszine_shortcode_figure' );
