<?php
/**
 * Shared left rail: Docs Tree, Issue navigation, and widgets.
 *
 * @package Docszine
 */

$context = isset( $args['context'] ) && is_array( $args['context'] ) ? $args['context'] : docszine_get_shell_context();
$issue   = ! empty( $context['issue'] ) ? $context['issue'] : null;
$terms   = docszine_get_issue_terms_ordered();
?>
<div class="docszine-rail-inner">
	<?php if ( docszine_get_docs_tree_pages() ) : ?>
		<?php get_template_part( 'template-parts/navigation/docs-tree', null, array( 'label' => __( 'Documentation', 'docszine' ) ) ); ?>
	<?php endif; ?>

	<?php if ( $terms ) : ?>
		<nav class="docszine-issue-index" aria-label="<?php esc_attr_e( 'Issue navigation', 'docszine' ); ?>">
			<p class="docszine-rail-label"><?php esc_html_e( 'Issues', 'docszine' ); ?></p>
			<?php foreach ( $terms as $issue_term ) : ?>
				<?php
				$current    = $issue && (int) $issue->term_id === (int) $issue_term->term_id;
				$issue_link = get_term_link( $issue_term );
				if ( is_wp_error( $issue_link ) ) {
					continue;
				}
				?>
				<a class="docszine-issue-link<?php echo $current ? ' is-current' : ''; ?>" href="<?php echo esc_url( $issue_link ); ?>"<?php echo $current ? ' aria-current="page"' : ''; ?>><span><?php echo esc_html( docszine_get_issue_display_label( $issue_term ) ); ?></span><small><?php echo esc_html( $issue_term->count ); ?></small></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<?php if ( $issue ) : ?>
		<?php
		get_template_part(
			'template-parts/navigation/issue-contents',
			null,
			array(
				'issue'           => $issue,
				'current_post_id' => isset( $context['post_id'] ) ? $context['post_id'] : 0,
			)
		);
		?>
	<?php endif; ?>

	<?php if ( is_active_sidebar( 'docszine-leftbar' ) ) : ?>
		<div class="docszine-widget-stack"><?php dynamic_sidebar( 'docszine-leftbar' ); ?></div>
	<?php endif; ?>
</div>
