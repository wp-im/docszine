<?php
/**
 * Small publication settings page.
 *
 * @package Docszine_Editorial
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the settings used by the Theme homepage. */
function docszine_register_settings() {
	register_setting(
		'docszine_editorial',
		'docszine_current_issue',
		array(
			'type'              => 'integer',
			'default'           => 0,
			'sanitize_callback' => 'docszine_sanitize_issue_setting',
		)
	);
	register_setting(
		'docszine_editorial',
		'docszine_featured_section',
		array(
			'type'              => 'integer',
			'default'           => 0,
			'sanitize_callback' => 'docszine_sanitize_category_setting',
		)
	);
	register_setting(
		'docszine_editorial',
		'docszine_featured_topic',
		array(
			'type'              => 'integer',
			'default'           => 0,
			'sanitize_callback' => 'docszine_sanitize_tag_setting',
		)
	);

	add_settings_section( 'docszine_editorial_main', __( 'Homepage editorial settings', 'docszine-editorial' ), '__return_false', 'docszine_editorial' );
	add_settings_field( 'docszine_current_issue', __( 'Current Issue', 'docszine-editorial' ), 'docszine_render_issue_setting', 'docszine_editorial', 'docszine_editorial_main' );
	add_settings_field( 'docszine_featured_section', __( 'Featured Section', 'docszine-editorial' ), 'docszine_render_category_setting', 'docszine_editorial', 'docszine_editorial_main' );
	add_settings_field( 'docszine_featured_topic', __( 'Featured Topic', 'docszine-editorial' ), 'docszine_render_tag_setting', 'docszine_editorial', 'docszine_editorial_main' );
}
add_action( 'admin_init', 'docszine_register_settings' );

/** Add the settings screen under Settings. */
function docszine_add_settings_page() {
	add_options_page( __( 'Docszine Editorial', 'docszine-editorial' ), __( 'Docszine', 'docszine-editorial' ), 'manage_options', 'docszine-editorial', 'docszine_render_settings_page' );
}
add_action( 'admin_menu', 'docszine_add_settings_page' );

/** Render the settings screen. */
function docszine_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Docszine Editorial', 'docszine-editorial' ); ?></h1>
		<p><?php esc_html_e( 'Choose the content sources used by the Docszine homepage.', 'docszine-editorial' ); ?></p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'docszine_editorial' );
			do_settings_sections( 'docszine_editorial' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Render a reusable taxonomy select.
 *
 * @param string $name        Field name.
 * @param string $taxonomy    Taxonomy name.
 * @param int    $selected    Selected term ID.
 * @param string $placeholder Empty-option label.
 * @return void
 */
function docszine_render_term_select( $name, $taxonomy, $selected, $placeholder ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number'     => 200,
		)
	);
	?>
	<select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>">
		<option value="0"><?php echo esc_html( $placeholder ); ?></option>
		<?php
		if ( ! is_wp_error( $terms ) ) :
			foreach ( $terms as $term ) :
				?>
			<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $selected, $term->term_id ); ?>><?php echo esc_html( $term->name ); ?></option>
					<?php
		endforeach;
endif;
		?>
	</select>
	<?php
}

/** Render the Current Issue setting field. */
function docszine_render_issue_setting() {
	docszine_render_term_select( 'docszine_current_issue', 'docszine_issue', absint( get_option( 'docszine_current_issue', 0 ) ), __( '— Select an Issue —', 'docszine-editorial' ) );
}

/** Render the Featured Section setting field. */
function docszine_render_category_setting() {
	docszine_render_term_select( 'docszine_featured_section', 'category', absint( get_option( 'docszine_featured_section', 0 ) ), __( '— Select a Section —', 'docszine-editorial' ) );
}

/** Render the Featured Topic setting field. */
function docszine_render_tag_setting() {
	docszine_render_term_select( 'docszine_featured_topic', 'post_tag', absint( get_option( 'docszine_featured_topic', 0 ) ), __( '— Select a Topic —', 'docszine-editorial' ) );
}

/**
 * Sanitize the Current Issue setting.
 *
 * @param int $value Proposed term ID.
 * @return int
 */
function docszine_sanitize_issue_setting( $value ) {
	$value = absint( $value );
	$term  = $value ? get_term( $value, 'docszine_issue' ) : null;
	return $term && ! is_wp_error( $term ) ? $value : 0;
}

/**
 * Sanitize the Featured Section setting.
 *
 * @param int $value Proposed term ID.
 * @return int
 */
function docszine_sanitize_category_setting( $value ) {
	$value = absint( $value );
	$term  = $value ? get_term( $value, 'category' ) : null;
	return $term && ! is_wp_error( $term ) ? $value : 0;
}

/**
 * Sanitize the Featured Topic setting.
 *
 * @param int $value Proposed term ID.
 * @return int
 */
function docszine_sanitize_tag_setting( $value ) {
	$value = absint( $value );
	$term  = $value ? get_term( $value, 'post_tag' ) : null;
	return $term && ! is_wp_error( $term ) ? $value : 0;
}
