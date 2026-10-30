<?php
/**
 * Post Issue order and registered metadata.
 *
 * @package Docszine_Editorial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register Theme-facing meta with object-level authorization. */
function docszine_register_meta() {
	register_term_meta(
		'docszine_issue',
		'_docszine_issue_number',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'absint',
			'auth_callback'     => 'docszine_can_manage_issue_terms',
		)
	);
	register_term_meta(
		'docszine_issue',
		'_docszine_issue_label',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => 'docszine_can_manage_issue_terms',
		)
	);
	register_term_meta(
		'docszine_issue',
		'_docszine_issue_cover_id',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'absint',
			'auth_callback'     => 'docszine_can_manage_issue_terms',
		)
	);
	register_term_meta(
		'docszine_issue',
		'_docszine_issue_editor_note',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'sanitize_textarea_field',
			'auth_callback'     => 'docszine_can_manage_issue_terms',
		)
	);
	register_post_meta(
		'post',
		'_docszine_issue_order',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => false,
			'sanitize_callback' => 'absint',
			'auth_callback'     => 'docszine_can_edit_post_meta',
		)
	);
}
add_action( 'init', 'docszine_register_meta', 6 );

/**
 * Authorize Issue term metadata using the taxonomy's own capability.
 *
 * @return bool
 */
function docszine_can_manage_issue_terms() {
	$taxonomy = get_taxonomy( 'docszine_issue' );
	return $taxonomy && current_user_can( $taxonomy->cap->manage_terms );
}

/**
 * Authorize Issue order using the current post object.
 *
 * @param bool   $allowed  Whether the current user may edit the metadata.
 * @param string $meta_key Metadata key.
 * @param int    $post_id  Post ID.
 * @return bool
 */
function docszine_can_edit_post_meta( $allowed, $meta_key, $post_id ) {
	return current_user_can( 'edit_post', $post_id );
}

/** Add the Issue order field to the native Post editor. */
function docszine_add_issue_order_meta_box() {
	add_meta_box(
		'docszine_issue_order',
		__( 'Docszine Editorial Order', 'docszine-editorial' ),
		'docszine_render_issue_order_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes_post', 'docszine_add_issue_order_meta_box' );

/**
 * Render the Issue order field.
 *
 * @param WP_Post $post Current post object.
 * @return void
 */
function docszine_render_issue_order_meta_box( $post ) {
	$order = absint( get_post_meta( $post->ID, '_docszine_issue_order', true ) );
	wp_nonce_field( 'docszine_save_issue_order', 'docszine_issue_order_nonce' );
	?>
	<p><label for="docszine_issue_order_field"><?php esc_html_e( 'Order inside the Issue', 'docszine-editorial' ); ?></label></p>
	<input class="small-text" type="number" min="0" step="1" name="docszine_issue_order" id="docszine_issue_order_field" value="<?php echo esc_attr( $order ); ?>" />
	<p class="description"><?php esc_html_e( 'Use 1 for the lead story. Leave blank or use 0 for unnumbered stories.', 'docszine-editorial' ); ?></p>
	<?php
}

/**
 * Save the Issue order field safely.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function docszine_save_issue_order( $post_id ) {
	if ( ! isset( $_POST['docszine_issue_order_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['docszine_issue_order_nonce'] ) ), 'docszine_save_issue_order' ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$order = isset( $_POST['docszine_issue_order'] ) ? absint( wp_unslash( $_POST['docszine_issue_order'] ) ) : 0;
	if ( $order > 0 ) {
		update_post_meta( $post_id, '_docszine_issue_order', $order );
	} else {
		delete_post_meta( $post_id, '_docszine_issue_order' );
	}
}
add_action( 'save_post_post', 'docszine_save_issue_order' );
