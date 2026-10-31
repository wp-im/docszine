<?php
/**
 * Generic post list item.
 *
 * @package Docszine
 */

$entry_issue = docszine_get_post_issue( get_the_ID() );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'archive-entry' ); ?>>
	<header class="entry-header">
		<h2><a href="<?php the_permalink(); ?>" rel="bookmark"><?php echo wp_kses_post( docszine_get_title() ); ?></a></h2>
		<div class="entry-meta">
		<?php
		if ( $entry_issue ) :
			?>
			<?php echo esc_html( docszine_get_issue_display_label( $entry_issue ) ); ?> · <?php endif; ?><?php docszine_posted_on(); ?> · <?php docszine_posted_by(); ?></div>
	</header>
	<div class="entry-summary"><?php the_excerpt(); ?></div>
</article>
