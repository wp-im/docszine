<?php
/**
 * Small display helpers.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preserve a useful label without modifying an untitled native record.
 *
 * @param int|WP_Post $post Post ID or object.
 * @return string
 */
function docszine_get_title( $post = 0 ) {
	$title = get_the_title( $post );
	return '' !== trim( wp_strip_all_tags( $title ) ) ? $title : __( 'Untitled', 'docszine' );
}

/** Posted date markup. */
function docszine_posted_on() {
	printf(
		'<time class="entry-date published" datetime="%1$s">%2$s</time>',
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);
}

/** Posted author markup. */
function docszine_posted_by() {
	$author_id   = absint( get_the_author_meta( 'ID' ) );
	$author_name = get_the_author();
	if ( ! $author_id || '' === trim( $author_name ) ) {
		esc_html_e( 'Editorial team', 'docszine' );
		return;
	}

	printf(
		'<a class="author-link" href="%1$s">%2$s</a>',
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_html( get_the_author() )
	);
}

/**
 * Approximate read time from rendered-independent post text.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function docszine_get_read_time( $post_id = 0 ) {
	$content = get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() );
	$text    = wp_strip_all_tags( strip_shortcodes( $content ) );
	$chars   = function_exists( 'mb_strlen' ) ? mb_strlen( $text ) : strlen( $text );
	return max( 1, (int) ceil( $chars / 450 ) );
}

/**
 * Human issue label.
 *
 * @param WP_Term|null $issue Issue term.
 * @return string
 */
function docszine_get_issue_display_label( $issue ) {
	if ( ! $issue ) {
		return '';
	}
	$number = get_term_meta( $issue->term_id, '_docszine_issue_number', true );
	$label  = get_term_meta( $issue->term_id, '_docszine_issue_label', true );
	$parts  = array();
	if ( $number ) {
		/* translators: %s: issue number. */
		$parts[] = sprintf( __( 'Issue %s', 'docszine' ), $number );
	} else {
		$parts[] = $issue->name;
	}
	if ( $label ) {
		$parts[] = $label;
	}
	return implode( ' · ', $parts );
}

/**
 * Get an H2 ID exactly as stored, or generate the same ID used by injection.
 *
 * @param string $attributes Opening H2 attributes.
 * @param string $title      Heading text.
 * @param int    $index      Heading index.
 * @param array  $seen       Reference map of generated IDs.
 * @return string
 */
function docszine_article_heading_id( $attributes, $title, $index, &$seen ) {
	if ( preg_match( '/\sid=["\']([^"\']+)["\']/', $attributes, $id_match ) ) {
		$explicit_id       = trim( html_entity_decode( $id_match[1], ENT_QUOTES, get_bloginfo( 'charset' ) ) );
		$seen_key          = sanitize_title( $explicit_id );
		$seen_key          = $seen_key ? $seen_key : 'section-' . $index;
		$seen[ $seen_key ] = isset( $seen[ $seen_key ] ) ? $seen[ $seen_key ] + 1 : 1;
		return 1 === $seen[ $seen_key ] ? $explicit_id : $seen_key . '-' . $seen[ $seen_key ];
	}

	$base          = trim( sanitize_title( $title ), '-' );
	$base          = $base ? $base : 'section-' . $index;
	$seen[ $base ] = isset( $seen[ $base ] ) ? $seen[ $base ] + 1 : 1;
	return 1 === $seen[ $base ] ? $base : $base . '-' . $seen[ $base ];
}

/** Get raw content for the currently displayed page of a paginated post. */
function docszine_get_current_article_page_content() {
	global $page, $pages;

	if ( is_array( $pages ) && ! empty( $pages ) ) {
		$current_page = max( 1, absint( $page ) );
		$index        = $current_page - 1;
		if ( isset( $pages[ $index ] ) ) {
			return $pages[ $index ];
		}
	}

	return get_post_field( 'post_content', get_the_ID() );
}

/**
 * Find the end of an HTML tag without treating a quoted `>` as the end.
 *
 * @param string $content HTML content.
 * @param int    $start   Position of the opening `<`.
 * @return int|false
 */
function docszine_find_html_tag_end( $content, $start ) {
	$length = strlen( $content );
	$quote  = '';

	for ( $index = $start + 1; $index < $length; $index++ ) {
		$character = $content[ $index ];

		if ( $quote ) {
			if ( $character === $quote ) {
				$quote = '';
			}
			continue;
		}

		if ( '"' === $character || "'" === $character ) {
			$quote = $character;
		} elseif ( '>' === $character ) {
			return $index;
		}
	}

	return false;
}

/**
 * Parse real H2 opening tags for the pre-HTML-Processor fallback.
 *
 * This scanner deliberately skips comments and quoted attributes. It is not
 * intended to repair arbitrary malformed HTML; it only prevents the fallback
 * from rewriting text that happens to contain an H2-looking string.
 *
 * @param string $content Article content.
 * @return array
 */
function docszine_get_fallback_article_heading_nodes( $content ) {
	$nodes    = array();
	$length   = strlen( $content );
	$position = 0;
	$current  = null;
	$index    = 0;

	while ( $position < $length ) {
		$tag_start = strpos( $content, '<', $position );
		if ( false === $tag_start ) {
			if ( null !== $current ) {
				$current['title'] .= substr( $content, $position );
			}
			break;
		}

		if ( null !== $current ) {
			$current['title'] .= substr( $content, $position, $tag_start - $position );
		}

		if ( '<!--' === substr( $content, $tag_start, 4 ) ) {
			$comment_end = strpos( $content, '-->', $tag_start + 4 );
			if ( false === $comment_end ) {
				break;
			}
			$position = $comment_end + 3;
			continue;
		}

		$tag_end = docszine_find_html_tag_end( $content, $tag_start );
		if ( false === $tag_end ) {
			break;
		}

		$tag = substr( $content, $tag_start, $tag_end - $tag_start + 1 );
		if ( preg_match( '/^<\s*(\/?)\s*([a-z][a-z0-9:-]*)\b/i', $tag, $tag_match ) && '/' !== $tag_match[1] ) {
			$raw_text_tag = strtolower( $tag_match[2] );
			if ( in_array( $raw_text_tag, array( 'script', 'style', 'textarea', 'title' ), true ) ) {
				$close_pattern = sprintf( '/<\/\s*%s\s*>/i', $raw_text_tag );
				if ( preg_match( $close_pattern, $content, $close_match, PREG_OFFSET_CAPTURE, $tag_end + 1 ) ) {
					$position = $close_match[0][1] + strlen( $close_match[0][0] );
					continue;
				}
				break;
			}
		}
		if ( preg_match( '/^<\s*(\/?)\s*h2\b((?:(?:"[^"]*"|\'[^\']*\'|[^\'">])*)\s*)>$/i', $tag, $match ) ) {
			if ( '/' === $match[1] ) {
				if ( null !== $current ) {
					$current['title'] = trim( wp_strip_all_tags( $current['title'] ) );
					$nodes[]          = $current;
					$current          = null;
				}
			} elseif ( null === $current ) {
				++$index;
				$current = array(
					'start'      => $tag_start,
					'length'     => $tag_end - $tag_start + 1,
					'attributes' => $match[2],
					'index'      => $index,
					'title'      => '',
				);
			}
		}

		$position = $tag_end + 1;
	}

	return $nodes;
}

/**
 * Extract H2 headings with the WordPress HTML API when it is available.
 *
 * @param string $content Filtered article content.
 * @return array|null
 */
function docszine_get_article_headings_html_api( $content ) {
	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return null;
	}
	return docszine_parse_article_headings( $content )['headings'];
}

/**
 * Decorate headings without rewriting arbitrary block HTML.
 *
 * The parsing approach is shared with Docspress, with no runtime dependency.
 * Reserve all content IDs before allocating generated anchors; retain unique
 * explicit IDs and omit empty/hidden headings and inert template content.
 *
 * @param string $content Filtered HTML.
 * @return array{html:string,headings:array}
 */
function docszine_parse_article_headings( $content ) {
	$content = (string) $content;
	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return array(
			'html'     => $content,
			'headings' => array(),
		);
	}

	$reader    = new WP_HTML_Tag_Processor( $content );
	$reserved  = array();
	$foreign   = array();
	$headings  = array();
	$current   = null;
	$inert_tag = 0;
	while ( $reader->next_token() ) {
		$tag = $reader->get_tag();
		if ( 'TEMPLATE' === $tag ) {
			$inert_tag += $reader->is_tag_closer() ? -1 : 1;
		}
		if ( $reader->is_tag_closer() ) {
			if ( null !== $current && $tag === $current['tag'] ) {
				$headings[] = $current;
				$current    = null;
			}
			continue;
		}
		if ( null !== $tag ) {
			$id = $reader->get_attribute( 'id' );
			if ( is_string( $id ) && '' !== $id ) {
				$reserved[ $id ] = true;
				if ( ! in_array( $tag, array( 'H2' ), true ) ) {
					$foreign[ $id ] = true;
				}
			}
		}
		if ( in_array( $tag, array( 'H2' ), true ) ) {
			if ( null !== $current ) {
				$headings[] = $current;
			}
			$current = array(
				'tag'    => $tag,
				'title'  => '',
				'id'     => $reader->get_attribute( 'id' ),
				'hidden' => $inert_tag > 0 || null !== $reader->get_attribute( 'hidden' ) || 'true' === $reader->get_attribute( 'aria-hidden' ),
			);
		} elseif ( null !== $current && '#text' === $reader->get_token_type() ) {
			$current['title'] .= $reader->get_modifiable_text();
		}
	}
	if ( null !== $current ) {
		$headings[] = $current;
	}

	$writer   = new WP_HTML_Tag_Processor( $content );
	$used     = array();
	$toc      = array();
	$position = 0;
	while ( $writer->next_tag() ) {
		$tag = $writer->get_tag();
		if ( ! in_array( $tag, array( 'H2' ), true ) ) {
			continue;
		}
		$heading = isset( $headings[ $position ] ) ? $headings[ $position ] : null;
		++$position;
		if ( ! $heading ) {
			continue;
		}
		$title = trim( preg_replace( '/\\s+/u', ' ', $heading['title'] ) );
		if ( '' === $title || $heading['hidden'] ) {
			continue;
		}
		$given   = is_string( $heading['id'] ) ? trim( $heading['id'] ) : '';
		$base_id = '' !== $given ? $given : rawurldecode( sanitize_title( $title ) );
		if ( '' === $base_id ) {
			$base_id = 'section-' . $position;
		}
		$id     = $base_id;
		$suffix = 2;
		while ( isset( $used[ $id ] ) || isset( $foreign[ $id ] ) || ( ( '' === $given || $id !== $given ) && isset( $reserved[ $id ] ) ) ) {
			$id = $base_id . '-' . $suffix;
			++$suffix;
		}
		$used[ $id ] = true;
		$writer->set_attribute( 'id', $id );
		foreach ( (array) $writer->get_attribute_names_with_prefix( 'on' ) as $attribute ) {
			$writer->remove_attribute( $attribute );
		}
		$toc[] = array(
			'id'    => $id,
			'title' => $title,
		);
	}
	return array(
		'html'     => $writer->get_updated_html(),
		'headings' => $toc,
	);
}

/**
 * Extract H2 headings using the same ID algorithm as content injection.
 *
 * @param string $content Article content.
 * @return array
 */
function docszine_get_article_headings( $content ) {
	$html_api_headings = docszine_get_article_headings_html_api( $content );
	if ( null !== $html_api_headings ) {
		return $html_api_headings;
	}

	$headings = array();
	$seen     = array();
	foreach ( docszine_get_fallback_article_heading_nodes( $content ) as $node ) {
		$headings[] = array(
			'id'    => docszine_article_heading_id( $node['attributes'], $node['title'], $node['index'], $seen ),
			'title' => $node['title'],
		);
	}
	return $headings;
}

/**
 * Inject stable IDs into article H2s for TOC links.
 *
 * @param string $content Filtered article content.
 * @return string
 */
function docszine_add_article_heading_ids( $content ) {
	if ( ! is_singular( array( 'post', 'page' ) ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return docszine_parse_article_headings( $content )['html'];
	}

	$seen         = array();
	$replacements = array();
	foreach ( docszine_get_fallback_article_heading_nodes( $content ) as $node ) {
		$id         = docszine_article_heading_id( $node['attributes'], $node['title'], $node['index'], $seen );
		$attributes = $node['attributes'];
		if ( preg_match( '/\sid\s*=\s*(["\'])[^"\']*\1/i', $attributes ) ) {
			$attributes = preg_replace( '/\sid\s*=\s*(["\'])[^"\']*\1/i', ' id="' . esc_attr( $id ) . '"', $attributes, 1 );
		} else {
			$attributes .= ' id="' . esc_attr( $id ) . '"';
		}
		$replacements[] = array(
			'start'  => $node['start'],
			'length' => $node['length'],
			'html'   => '<h2' . $attributes . '>',
		);
	}

	foreach ( array_reverse( $replacements ) as $replacement ) {
		$content = substr_replace( $content, $replacement['html'], $replacement['start'], $replacement['length'] );
	}
	return $content;
}
add_filter( 'the_content', 'docszine_add_article_heading_ids', 8 );

/**
 * Prepare filtered article content for the editorial CSS Grid.
 *
 * Add the rail class to H2 elements with the WordPress HTML Tag Processor.
 * The section rule is a CSS pseudo-element so this function never inserts or
 * splits arbitrary editor HTML containers.
 *
 * @param string $content Filtered article content.
 * @return string
 */
function docszine_prepare_article_content( $content ) {
	if ( ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$processor = new WP_HTML_Tag_Processor( $content );
	while ( $processor->next_tag( array( 'tag_name' => 'H2' ) ) ) {
		$processor->add_class( 'section-title' );
	}

	return $processor->get_updated_html();
}

/**
 * Render the reusable Issue contents list.
 *
 * @param WP_Term|null $issue           Issue term.
 * @param int          $current_post_id Current post ID.
 * @param array|null   $groups          Optional pre-grouped posts.
 * @return void
 */
function docszine_render_issue_contents_list( $issue, $current_post_id = 0, $groups = null ) {
	if ( ! $issue ) {
		return;
	}
	if ( null === $groups ) {
		$groups = docszine_get_issue_posts_grouped( $issue->term_id );
	}
	$shown = 0;
	$total = array_sum(
		array_map(
			static function ( $group ) {
				return count( $group['posts'] );
			},
			$groups
		)
	);
	foreach ( $groups as $group ) {
		$visible_posts = array();
		foreach ( $group['posts'] as $post ) {
			if ( $shown < 100 || (int) $current_post_id === (int) $post->ID ) {
				$visible_posts[] = $post;
			}
			++$shown;
		}
		if ( ! $visible_posts ) {
			continue;
		}
		$label = $group['term'] ? $group['term']->name : __( 'Other', 'docszine' );
		echo '<div class="contents-group">';
		echo '<div class="contents-group-title">' . esc_html( $label ) . '</div>';
		foreach ( $visible_posts as $post ) {
			$class = (int) $current_post_id === (int) $post->ID ? ' class="active current-post" aria-current="page"' : '';
			echo '<a' . $class . ' href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( docszine_get_title( $post ) ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $class is fixed theme markup.
		}
		echo '</div>';
	}
	if ( $total > 100 ) {
		$link = get_term_link( $issue );
		if ( ! is_wp_error( $link ) ) {
			echo '<a href="' . esc_url( $link ) . '">' . esc_html__( 'View complete Issue contents', 'docszine' ) . '</a>';
		}
	}
}
