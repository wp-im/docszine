<?php
/**
 * Single article template.
 *
 * @package Docszine
 */

get_header();
if ( have_posts() ) :
	the_post();
	$article_id    = get_the_ID();
	$issue         = docszine_get_post_issue( $article_id );
	$section       = docszine_get_post_section( $article_id );
	$topics        = docszine_get_post_topics( $article_id );
	$toc_content   = docszine_get_current_article_page_content();
	$headings      = docszine_get_article_headings( $toc_content );
	$shell_context = docszine_get_shell_context(
		array(
			'route'    => 'article',
			'issue'    => $issue,
			'post_id'  => $article_id,
			'headings' => $headings,
		)
	);
	get_template_part(
		'template-parts/layout/shell-start',
		null,
		array(
			'main_class' => 'article-main',
			'context'    => $shell_context,
		)
	);
	?>
	<article <?php post_class( 'docszine-reading-entry' ); ?>>
		<?php if ( $issue || $section ) : ?>
			<p class="article-kicker"><?php echo esc_html( implode( ' · ', array_filter( array( $issue ? docszine_get_issue_display_label( $issue ) : '', $section ? $section->name : '' ) ) ) ); ?></p>
		<?php endif; ?>
		<h1><?php the_title(); ?></h1>
		<?php if ( has_excerpt() ) : ?>
			<p class="article-deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
		<?php /* translators: %d: estimated reading time in minutes. */ ?>
		<div class="article-meta"><?php esc_html_e( 'By', 'docszine' ); ?> <?php docszine_posted_by(); ?> · <?php docszine_posted_on(); ?> · <?php echo esc_html( sprintf( __( '%d min read', 'docszine' ), docszine_get_read_time( $article_id ) ) ); ?></div>
		<div class="docszine-prose article-body">
			<?php
			ob_start();
			the_content();
			$content = ob_get_clean();
			if ( '' === trim( $content ) ) {
				$content = '<p class="empty-content-message">' . esc_html__( 'This article is ready for its first paragraph.', 'docszine' ) . '</p>';
			}
			echo docszine_prepare_article_content( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- content already passed through the_content filters or is theme-generated escaped markup.
			wp_link_pages(
				array(
					'before' => '<nav class="post-page-links" aria-label="' . esc_attr__( 'Article pages', 'docszine' ) . '"><span>' . esc_html__( 'Pages:', 'docszine' ) . '</span>',
					'after'  => '</nav>',
				)
			);
			?>
		</div>
		<?php
		$previous = $issue ? docszine_get_issue_adjacent_post( $article_id, 'previous' ) : get_previous_post();
		$next     = $issue ? docszine_get_issue_adjacent_post( $article_id, 'next' ) : get_next_post();
		get_template_part(
			'template-parts/article-navigation',
			null,
			array(
				'previous' => $previous,
				'next'     => $next,
				'context'  => $issue ? 'issue' : 'generic',
			)
		);

		if ( $section ) {
			get_template_part(
				'template-parts/article-related',
				null,
				array(
					/* translators: %s: section name. */
					'title' => sprintf( __( 'More from %s', 'docszine' ), $section->name ),
					'term'  => $section,
					'posts' => docszine_get_related_section_posts( $article_id, 3 ),
				)
			);
		}
		if ( $topics ) {
			$topic_names = wp_list_pluck( $topics, 'name' );
			get_template_part(
				'template-parts/article-related',
				null,
				array(
					/* translators: %s: one or more topic names. */
					'title' => sprintf( __( 'Related topics: %s', 'docszine' ), implode( ' · ', $topic_names ) ),
					'term'  => 1 === count( $topics ) ? $topics[0] : null,
					'posts' => docszine_get_related_topic_posts( $article_id, 3 ),
				)
			);
		}
		?>
	</article>
	<?php if ( comments_open() || get_comments_number() ) : ?>
		<div class="docszine-reading-entry article-comments"><?php comments_template(); ?></div>
	<?php endif; ?>
	<?php get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) ); ?>
	<?php
endif;
get_footer();
