<?php
/**
 * Single Issue archive.
 *
 * @package Docszine
 */

get_header();
$issue         = get_queried_object();
$editor_note   = $issue instanceof WP_Term ? get_term_meta( $issue->term_id, '_docszine_issue_editor_note', true ) : '';
$shell_context = docszine_get_shell_context(
	array(
		'route' => 'issue',
		'issue' => $issue instanceof WP_Term ? $issue : null,
	)
);
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'issue-main',
		'context'    => $shell_context,
	)
);
?>
	<?php if ( $issue instanceof WP_Term ) : ?>
		<?php $cover_id = absint( get_term_meta( $issue->term_id, '_docszine_issue_cover_id', true ) ); ?>
		<section class="issue-hero<?php echo $cover_id ? ' has-cover' : ' no-cover'; ?>">
			<?php if ( $cover_id ) : ?>
				<div class="issue-cover"><?php echo wp_get_attachment_image( $cover_id, 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>
			<div class="issue-hero-copy">
				<p class="eyebrow"><?php echo esc_html( docszine_get_issue_display_label( $issue ) ); ?></p>
				<h1><?php echo esc_html( $issue->name ); ?></h1>
				<?php
				if ( $issue->description ) :
					?>
					<div class="issue-description docszine-prose"><?php echo wp_kses_post( wpautop( $issue->description ) ); ?></div><?php endif; ?>
				<?php
				if ( $editor_note ) :
					?>
					<aside class="editor-note"><strong><?php esc_html_e( 'Editor’s note', 'docszine' ); ?></strong><?php echo wp_kses_post( wpautop( $editor_note ) ); ?></aside><?php endif; ?>
			</div>
		</section>

		<?php $issue_groups = docszine_get_issue_posts_grouped( $issue->term_id ); ?>
		<?php if ( $issue_groups ) : ?>
			<?php foreach ( $issue_groups as $group ) : ?>
				<section class="issue-section">
					<h2 class="issue-section-title"><?php echo esc_html( $group['term'] ? $group['term']->name : __( 'Other', 'docszine' ) ); ?></h2>
					<?php foreach ( $group['posts'] as $issue_post ) : ?>
						<?php get_template_part( 'template-parts/issue-entry', null, array( 'post' => $issue_post ) ); ?>
					<?php endforeach; ?>
				</section>
			<?php endforeach; ?>
		<?php else : ?>
			<?php
			get_template_part(
				'template-parts/content',
				'none',
				array(
					'title'       => __( 'No stories in this Issue yet.', 'docszine' ),
					'message'     => __( 'Publish a post and assign it to this Issue to make the sequence visible here.', 'docszine' ),
					'heading_tag' => 'h2',
				)
			);
			?>
		<?php endif; ?>

		<?php
		$previous_issue = docszine_get_adjacent_issue_term( $issue->term_id, 'previous' );
		$next_issue     = docszine_get_adjacent_issue_term( $issue->term_id, 'next' );
		?>
		<?php if ( $previous_issue || $next_issue ) : ?>
			<nav class="issue-pagination" aria-label="<?php esc_attr_e( 'Issue navigation', 'docszine' ); ?>">
				<div>
				<?php
				if ( $previous_issue ) :
					?>
					<a href="<?php echo esc_url( get_term_link( $previous_issue ) ); ?>"><span><?php esc_html_e( 'Previous Issue', 'docszine' ); ?></span><?php echo esc_html( $previous_issue->name ); ?></a><?php endif; ?></div>
				<div>
				<?php
				if ( $next_issue ) :
					?>
					<a href="<?php echo esc_url( get_term_link( $next_issue ) ); ?>"><span><?php esc_html_e( 'Next Issue', 'docszine' ); ?></span><?php echo esc_html( $next_issue->name ); ?></a><?php endif; ?></div>
			</nav>
		<?php endif; ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none', array( 'heading_tag' => 'h1' ) ); ?>
	<?php endif; ?>
<?php get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) ); ?>
<?php get_footer(); ?>
