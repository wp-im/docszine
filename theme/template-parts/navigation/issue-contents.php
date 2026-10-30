<?php
/**
 * Reusable Issue contents navigation.
 *
 * @package Docszine
 */

$issue           = isset( $args['issue'] ) ? $args['issue'] : null;
$current_post_id = isset( $args['current_post_id'] ) ? absint( $args['current_post_id'] ) : 0;
if ( ! $issue ) {
	return;
}
?>
<nav class="docszine-issue-contents" aria-label="<?php esc_attr_e( 'Issue contents', 'docszine' ); ?>">
	<p class="docszine-rail-label"><?php echo esc_html( docszine_get_issue_display_label( $issue ) ); ?></p>
	<?php docszine_render_issue_contents_list( $issue, $current_post_id ); ?>
</nav>
