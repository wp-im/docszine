<?php
/**
 * Issue taxonomy and term editing UI.
 *
 * @package Docszine_Editorial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the optional editorial Issue taxonomy. */
function docszine_register_issue_taxonomy() {
	$labels = array(
		'name'                  => __( 'Issues', 'docszine-editorial' ),
		'singular_name'         => __( 'Issue', 'docszine-editorial' ),
		'menu_name'             => __( 'Issues', 'docszine-editorial' ),
		'all_items'             => __( 'All Issues', 'docszine-editorial' ),
		'edit_item'             => __( 'Edit Issue', 'docszine-editorial' ),
		'view_item'             => __( 'View Issue', 'docszine-editorial' ),
		'update_item'           => __( 'Update Issue', 'docszine-editorial' ),
		'add_new_item'          => __( 'Add New Issue', 'docszine-editorial' ),
		'new_item_name'         => __( 'New Issue Name', 'docszine-editorial' ),
		'search_items'          => __( 'Search Issues', 'docszine-editorial' ),
		'not_found'             => __( 'No issues found.', 'docszine-editorial' ),
		'back_to_items'         => __( '← Back to Issues', 'docszine-editorial' ),
		'item_link'             => __( 'Issue link', 'docszine-editorial' ),
		'item_link_description' => __( 'A link to an Issue.', 'docszine-editorial' ),
	);

	register_taxonomy(
		'docszine_issue',
		array( 'post' ),
		array(
			'labels'            => $labels,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'rewrite'           => array(
				'slug'       => 'issues',
				'with_front' => false,
			),
			'query_var'         => 'docszine_issue',
			'capabilities'      => array(
				'assign_terms' => 'edit_posts',
				'manage_terms' => 'manage_categories',
				'edit_terms'   => 'manage_categories',
				'delete_terms' => 'manage_categories',
			),
		)
	);
}
add_action( 'init', 'docszine_register_issue_taxonomy', 5 );

/**
 * Add Issue-specific metadata fields to the term editor.
 *
 * @param WP_Term|null $term Existing term object, if editing.
 * @return void
 */
function docszine_issue_term_fields( $term = null ) {
	$term        = $term instanceof WP_Term ? $term : null;
	$number      = $term ? get_term_meta( $term->term_id, '_docszine_issue_number', true ) : '';
	$label       = $term ? get_term_meta( $term->term_id, '_docszine_issue_label', true ) : '';
	$cover_id    = $term ? absint( get_term_meta( $term->term_id, '_docszine_issue_cover_id', true ) ) : 0;
	$editor_note = $term ? get_term_meta( $term->term_id, '_docszine_issue_editor_note', true ) : '';
	$cover_src   = $cover_id ? wp_get_attachment_image_url( $cover_id, 'thumbnail' ) : '';
	?>
	<?php wp_nonce_field( 'docszine_save_issue_meta', 'docszine_issue_meta_nonce' ); ?>
	<?php if ( $term ) : ?>
	<tr class="form-field">
		<th scope="row"><label for="docszine_issue_number"><?php esc_html_e( 'Issue number', 'docszine-editorial' ); ?></label></th>
		<td><input name="docszine_issue_number" id="docszine_issue_number" type="number" min="1" step="1" value="<?php echo esc_attr( $number ); ?>" /><p class="description"><?php esc_html_e( 'Optional numeric order for this Issue.', 'docszine-editorial' ); ?></p></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="docszine_issue_label"><?php esc_html_e( 'Issue label', 'docszine-editorial' ); ?></label></th>
		<td><input name="docszine_issue_label" id="docszine_issue_label" type="text" value="<?php echo esc_attr( $label ); ?>" /><p class="description"><?php esc_html_e( 'Optional season or date label, such as Autumn 2026.', 'docszine-editorial' ); ?></p></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="docszine_issue_cover_id"><?php esc_html_e( 'Issue cover', 'docszine-editorial' ); ?></label></th>
		<td>
			<input name="docszine_issue_cover_id" id="docszine_issue_cover_id" type="number" min="0" step="1" value="<?php echo esc_attr( $cover_id ); ?>" />
			<button type="button" class="button docszine-media-button" data-target="#docszine_issue_cover_id"><?php esc_html_e( 'Select image', 'docszine-editorial' ); ?></button>
			<?php
			if ( $cover_src ) :
				?>
				<img class="docszine-cover-preview" src="<?php echo esc_url( $cover_src ); ?>" alt="" style="display:block;max-width:120px;height:auto;margin-top:8px" /><?php endif; ?>
			<p class="description"><?php esc_html_e( 'Only image Attachments are accepted.', 'docszine-editorial' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="docszine_issue_editor_note"><?php esc_html_e( 'Editor note', 'docszine-editorial' ); ?></label></th>
		<td><textarea name="docszine_issue_editor_note" id="docszine_issue_editor_note" rows="5" class="large-text"><?php echo esc_textarea( $editor_note ); ?></textarea><p class="description"><?php esc_html_e( 'Optional note displayed on the Issue page.', 'docszine-editorial' ); ?></p></td>
	</tr>
	<?php else : ?>
	<div class="form-field">
		<label for="docszine_issue_number"><?php esc_html_e( 'Issue number', 'docszine-editorial' ); ?></label>
		<input name="docszine_issue_number" id="docszine_issue_number" type="number" min="1" step="1" value="" />
	</div>
	<div class="form-field">
		<label for="docszine_issue_label"><?php esc_html_e( 'Issue label', 'docszine-editorial' ); ?></label>
		<input name="docszine_issue_label" id="docszine_issue_label" type="text" value="" />
	</div>
	<div class="form-field">
		<label for="docszine_issue_editor_note"><?php esc_html_e( 'Editor note', 'docszine-editorial' ); ?></label>
		<textarea name="docszine_issue_editor_note" id="docszine_issue_editor_note" rows="5" class="large-text"></textarea>
	</div>
	<?php endif; ?>
	<?php
}
add_action( 'docszine_issue_add_form_fields', 'docszine_issue_term_fields' );
add_action( 'docszine_issue_edit_form_fields', 'docszine_issue_term_fields' );

/**
 * Save and validate Issue metadata.
 *
 * @param int $term_id Issue term ID.
 * @return void
 */
function docszine_save_issue_term_meta( $term_id ) {
	if ( ! isset( $_POST['docszine_issue_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['docszine_issue_meta_nonce'] ) ), 'docszine_save_issue_meta' ) ) {
		return;
	}

	$taxonomy = get_taxonomy( 'docszine_issue' );
	if ( ! $taxonomy || ! current_user_can( $taxonomy->cap->manage_terms ) ) {
		return;
	}

	$number      = isset( $_POST['docszine_issue_number'] ) ? absint( wp_unslash( $_POST['docszine_issue_number'] ) ) : 0;
	$label       = isset( $_POST['docszine_issue_label'] ) ? sanitize_text_field( wp_unslash( $_POST['docszine_issue_label'] ) ) : '';
	$editor_note = isset( $_POST['docszine_issue_editor_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['docszine_issue_editor_note'] ) ) : '';
	$cover_id    = isset( $_POST['docszine_issue_cover_id'] ) ? absint( wp_unslash( $_POST['docszine_issue_cover_id'] ) ) : 0;

	if ( $number > 0 ) {
		update_term_meta( $term_id, '_docszine_issue_number', $number );
	} else {
		delete_term_meta( $term_id, '_docszine_issue_number' );
	}
	if ( '' !== $label ) {
		update_term_meta( $term_id, '_docszine_issue_label', $label );
	} else {
		delete_term_meta( $term_id, '_docszine_issue_label' );
	}
	if ( '' !== $editor_note ) {
		update_term_meta( $term_id, '_docszine_issue_editor_note', $editor_note );
	} else {
		delete_term_meta( $term_id, '_docszine_issue_editor_note' );
	}

	if ( $cover_id && 'attachment' === get_post_type( $cover_id ) && wp_attachment_is_image( $cover_id ) ) {
		update_term_meta( $term_id, '_docszine_issue_cover_id', $cover_id );
	} else {
		delete_term_meta( $term_id, '_docszine_issue_cover_id' );
	}
}
add_action( 'created_docszine_issue', 'docszine_save_issue_term_meta' );
add_action( 'edited_docszine_issue', 'docszine_save_issue_term_meta' );

/**
 * Enqueue the native Media Library only on Issue term screens.
 *
 * @param string $hook_suffix Current admin page hook suffix.
 * @return void
 */
function docszine_issue_admin_assets( $hook_suffix ) {
	$screen   = get_current_screen();
	$taxonomy = $screen && isset( $screen->taxonomy ) ? sanitize_key( $screen->taxonomy ) : '';
	if ( ! in_array( $hook_suffix, array( 'edit-tags.php', 'term.php' ), true ) || 'docszine_issue' !== $taxonomy ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'docszine-issue-media', DOCSZINE_EDITORIAL_URL . 'assets/js/issue-media.js', array( 'jquery' ), DOCSZINE_EDITORIAL_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'docszine_issue_admin_assets' );
