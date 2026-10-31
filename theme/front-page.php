<?php
/**
 * Magazine front page.
 *
 * @package Docszine
 */

if ( 'posts' === get_option( 'show_on_front' ) ) {
	require get_theme_file_path( '/home.php' );
	return;
}

get_header();
$current_issue         = docszine_get_current_issue();
$issue_posts           = $current_issue ? docszine_get_issue_posts( $current_issue->term_id, 8 ) : array();
$lead_post             = $current_issue ? docszine_get_issue_lead_post( $current_issue->term_id ) : null;
$secondary_issue_posts = $lead_post ? array_values(
	array_filter(
		$issue_posts,
		static function ( $item ) use ( $lead_post ) {
			return (int) $item->ID !== (int) $lead_post->ID;
		}
	)
) : $issue_posts;
$docs_root             = docszine_get_docs_root_page();
$docs_entries          = docszine_get_docs_directory_pages();
$shell_context         = docszine_get_shell_context(
	array(
		'route' => 'home',
		'issue' => $current_issue,
	)
);
?>
<?php
get_template_part(
	'template-parts/layout/shell-start',
	null,
	array(
		'main_class' => 'magazine-home',
		'context'    => $shell_context,
	)
);
?>
	<div class="docszine-home-content">
		<section class="docszine-home-intro">
			<p class="eyebrow"><?php esc_html_e( 'Documentation', 'docszine' ); ?></p>
			<h1 class="page-title"><?php echo esc_html( $docs_root ? docszine_get_title( $docs_root ) : get_bloginfo( 'name' ) ); ?></h1>
			<p class="page-deck"><?php echo esc_html( $docs_root && has_excerpt( $docs_root ) ? get_the_excerpt( $docs_root ) : get_bloginfo( 'description' ) ); ?></p>
			<p class="docszine-index-meta"><?php esc_html_e( 'Enter through the documentation tree, or browse the sections below.', 'docszine' ); ?></p>
			<div class="docszine-lead-search">
				<?php get_search_form(); ?>
			</div>
			<?php if ( ! $docs_entries ) : ?>
				<?php
				get_template_part(
					'template-parts/content',
					'none',
					array(
						'title'       => __( 'The documentation is taking shape.', 'docszine' ),
						'message'     => __( 'Publish a Page to give this reading room its first entry.', 'docszine' ),
						'heading_tag' => 'h2',
					)
				);
				?>
			<?php endif; ?>
		</section>
		<?php if ( $docs_entries ) : ?>
		<section class="docszine-directory" aria-labelledby="docszine-directory-title">
			<div class="section-head"><h2 id="docszine-directory-title"><?php esc_html_e( 'Start here', 'docszine' ); ?></h2><span><?php esc_html_e( 'Browse the documentation', 'docszine' ); ?></span></div>
			<div class="docszine-directory-list">
				<?php foreach ( array_slice( $docs_entries, 0, 8 ) as $index => $entry ) : ?>
					<a class="docszine-directory-row<?php echo 0 === $index ? ' is-primary' : ''; ?>" href="<?php echo esc_url( get_permalink( $entry ) ); ?>">
						<span class="docszine-directory-num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span><span class="docszine-directory-title"><?php echo esc_html( docszine_get_title( $entry ) ); ?></span><span class="docszine-directory-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt( $entry ), 24 ) ); ?></span></span>
						<span class="docszine-directory-arrow" aria-hidden="true">→</span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>
		<?php get_template_part( 'template-parts/front-page-editorial' ); ?>
<?php if ( $current_issue ) : ?>
	<?php
	$issue_link = get_term_link( $current_issue );
	$cover_id   = absint( get_term_meta( $current_issue->term_id, '_docszine_issue_cover_id', true ) );
	$intro      = $current_issue->description;
	?>
	<section class="current-issue<?php echo $cover_id ? ' has-cover' : ' no-cover'; ?>">
		<div class="current-copy">
			<?php /* translators: %s: current Issue label. */ ?>
			<p class="issue-number"><?php echo esc_html( sprintf( __( 'Current Issue · %s', 'docszine' ), docszine_get_issue_display_label( $current_issue ) ) ); ?></p>
			<h2 class="issue-name"><?php echo esc_html( $current_issue->name ); ?></h2>
			<?php
			if ( $intro ) :
				?>
				<div class="issue-intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div><?php endif; ?>
			<?php
			if ( ! is_wp_error( $issue_link ) ) :
				?>
				<a class="link-arrow" href="<?php echo esc_url( $issue_link ); ?>"><?php esc_html_e( 'Explore this issue', 'docszine' ); ?></a><?php endif; ?>
		</div>
		<?php
		if ( $cover_id && ! is_wp_error( $issue_link ) ) :
			?>
			<a class="cover" href="<?php echo esc_url( $issue_link ); ?>"><?php echo wp_get_attachment_image( $cover_id, 'large', false, array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php endif; ?>
	</section>

	<?php if ( ! $issue_posts ) : ?>
		<?php
		get_template_part(
			'template-parts/content',
			'none',
			array(
				'title'       => __( 'This Issue is ready for its first story.', 'docszine' ),
				'message'     => __( 'Add a published post to the current Issue to begin the editorial sequence.', 'docszine' ),
				'heading_tag' => 'h2',
			)
		);
		?>
	<?php elseif ( $lead_post ) : ?>
	<section class="home-section">
		<div class="section-head"><h2><?php esc_html_e( 'From the current issue', 'docszine' ); ?></h2></div>
		<div class="feature-row">
			<article class="feature-story">
				<?php
				if ( has_post_thumbnail( $lead_post ) ) :
					?>
					<a class="feature-image" href="<?php echo esc_url( get_permalink( $lead_post ) ); ?>"><?php echo get_the_post_thumbnail( $lead_post, 'large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><?php endif; ?>
				<?php $lead_section = docszine_get_post_section( $lead_post->ID ); ?>
				<?php
				if ( $lead_section ) :
					?>
					<p class="eyebrow"><?php echo esc_html( $lead_section->name ); ?></p><?php endif; ?>
				<h3><a href="<?php echo esc_url( get_permalink( $lead_post ) ); ?>"><?php echo esc_html( docszine_get_title( $lead_post ) ); ?></a></h3>
				<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $lead_post ), 38 ) ); ?></p>
			<?php /* translators: %d: estimated reading time in minutes. */ ?>
			<div class="meta-line"><?php echo esc_html( get_the_author_meta( 'display_name', $lead_post->post_author ) ); ?> · <?php echo esc_html( sprintf( __( '%d min read', 'docszine' ), docszine_get_read_time( $lead_post->ID ) ) ); ?></div>
			</article>
			<div class="story-list">
				<?php
				foreach ( array_slice( $secondary_issue_posts, 0, 5 ) as $issue_post ) :
					$section = docszine_get_post_section( $issue_post->ID );
					?>
					<a class="story-row" href="<?php echo esc_url( get_permalink( $issue_post ) ); ?>"><span class="story-kicker"><?php echo esc_html( $section ? $section->name : __( 'Story', 'docszine' ) ); ?></span><span><span class="story-title"><?php echo esc_html( docszine_get_title( $issue_post ) ); ?></span><span class="story-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt( $issue_post ), 18 ) ); ?></span></span></a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
	$featured_section_id = absint( get_option( 'docszine_featured_section', 0 ) );
	$featured_topic_id   = absint( get_option( 'docszine_featured_topic', 0 ) );
	$featured_section    = $featured_section_id ? get_term( $featured_section_id, 'category' ) : null;
	$featured_topic      = $featured_topic_id ? get_term( $featured_topic_id, 'post_tag' ) : null;
	$section_posts       = $featured_section && ! is_wp_error( $featured_section ) ? docszine_get_taxonomy_posts( 'category', $featured_section->term_id, 3, $current_issue->term_id ) : array();
	$topic_posts         = $featured_topic && ! is_wp_error( $featured_topic ) ? docszine_get_taxonomy_posts( 'post_tag', $featured_topic->term_id, 3, $current_issue->term_id ) : array();
	$latest_posts        = docszine_get_latest_posts( 3, $current_issue->term_id );
	?>
	<?php if ( $section_posts || $topic_posts || $latest_posts ) : ?>
		<?php $cross_column_count = ( $section_posts ? 1 : 0 ) + ( $topic_posts ? 1 : 0 ) + ( $latest_posts ? 1 : 0 ); ?>
	<section class="home-section">
		<div class="section-head"><h2><?php esc_html_e( 'Across issues', 'docszine' ); ?></h2><p><?php esc_html_e( 'Browse the magazine beyond the boundaries of a single issue.', 'docszine' ); ?></p></div>
		<div class="cross-grid cross-grid--<?php echo esc_attr( (string) $cross_column_count ); ?>">
			<?php
			if ( $section_posts ) :
				?>
				<section class="cross-column"><h3><a href="<?php echo esc_url( get_term_link( $featured_section ) ); ?>"><?php echo esc_html( $featured_section->name ); ?></a></h3>
				<?php
				foreach ( $section_posts as $item ) :
					?>
				<a class="cross-item" href="<?php echo esc_url( get_permalink( $item ) ); ?>"><strong><?php echo esc_html( docszine_get_title( $item ) ); ?></strong><span><?php echo esc_html( get_the_date( '', $item ) ); ?></span></a><?php endforeach; ?></section><?php endif; ?>
			<?php
			if ( $topic_posts ) :
				?>
				<section class="cross-column"><h3><a href="<?php echo esc_url( get_term_link( $featured_topic ) ); ?>"><?php echo esc_html( $featured_topic->name ); ?></a></h3>
				<?php
				foreach ( $topic_posts as $item ) :
					?>
				<a class="cross-item" href="<?php echo esc_url( get_permalink( $item ) ); ?>"><strong><?php echo esc_html( docszine_get_title( $item ) ); ?></strong><span><?php echo esc_html( get_the_date( '', $item ) ); ?></span></a><?php endforeach; ?></section><?php endif; ?>
			<?php
			if ( $latest_posts ) :
				?>
				<section class="cross-column"><h3><?php esc_html_e( 'Latest', 'docszine' ); ?></h3>
				<?php
				foreach ( $latest_posts as $item ) :
					?>
				<a class="cross-item" href="<?php echo esc_url( get_permalink( $item ) ); ?>"><strong><?php echo esc_html( docszine_get_title( $item ) ); ?></strong><span><?php echo esc_html( get_the_date( '', $item ) ); ?></span></a><?php endforeach; ?></section><?php endif; ?>
		</div>
	</section>
	<?php endif; ?>
<?php else : ?>
	<section class="home-section home-fallback">
		<div class="section-head"><h1 class="page-title"><?php bloginfo( 'name' ); ?></h1><p><?php bloginfo( 'description' ); ?></p></div>
		<div class="story-list">
			<?php
			$latest_posts = docszine_get_latest_posts( 8 );
			if ( $latest_posts ) :
				foreach ( $latest_posts as $latest_post ) :
					?>
				<a class="story-row" href="<?php echo esc_url( get_permalink( $latest_post ) ); ?>"><span class="story-kicker"><?php echo esc_html( get_the_date( 'Y.m.d', $latest_post ) ); ?></span><span><span class="story-title"><?php echo esc_html( docszine_get_title( $latest_post ) ); ?></span><span class="story-desc"><?php echo esc_html( wp_trim_words( get_the_excerpt( $latest_post ), 22 ) ); ?></span></span></a><?php endforeach; ?>
			<?php else : ?>
				<?php
				get_template_part(
					'template-parts/content',
					'none',
					array(
						'title'       => __( 'The magazine is ready for its first story.', 'docszine' ),
						'message'     => __( 'Publish a post to begin the reading sequence.', 'docszine' ),
						'heading_tag' => 'h2',
					)
				);
				?>
			<?php endif; ?>
		</div>
	</section>
<?php endif; ?>
	</div><!-- .docszine-home-content -->
<?php get_template_part( 'template-parts/layout/shell-end', null, array( 'context' => $shell_context ) ); ?>
<?php get_footer(); ?>
