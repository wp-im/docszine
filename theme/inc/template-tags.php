<?php
/**
 * Small display helpers.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
	if ( ! class_exists( 'WP_HTML_Processor' ) ) {
		return null;
	}

	$processor = WP_HTML_Processor::create_fragment( $content );
	if ( ! $processor ) {
		return null;
	}

	$headings = array();
	$seen     = array();
	$index    = 0;
	$current  = null;

	while ( $processor->next_token() ) {
		$token_type = $processor->get_token_type();
		$token_name = $processor->get_token_name();

		if ( '#tag' === $token_type && 'H2' === $token_name ) {
			if ( $processor->is_tag_closer() ) {
				if ( null !== $current ) {
					$title      = trim( wp_strip_all_tags( $current['title'] ) );
					$id         = docszine_article_heading_id( $current['attributes'], $title, $current['index'], $seen );
					$headings[] = array(
						'id'    => $id,
						'title' => $title,
					);
					$current    = null;
				}
			} else {
				++$index;
				$explicit_id = $processor->get_attribute( 'id' );
				$attributes  = is_string( $explicit_id ) ? ' id="' . esc_attr( $explicit_id ) . '"' : '';
				$current     = array(
					'attributes' => $attributes,
					'index'      => $index,
					'title'      => '',
				);
			}
		} elseif ( '#text' === $token_type && null !== $current ) {
			$text = $processor->get_modifiable_text();
			if ( is_string( $text ) ) {
				$current['title'] .= $text;
			}
		}
	}

	return $headings;
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
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if ( class_exists( 'WP_HTML_Tag_Processor' ) ) {
		$headings = docszine_get_article_headings( $content );
		if ( $headings ) {
			$processor = new WP_HTML_Tag_Processor( $content );
			$index     = 0;
			while ( $processor->next_tag( array( 'tag_name' => 'H2' ) ) && isset( $headings[ $index ] ) ) {
				$processor->set_attribute( 'id', $headings[ $index ]['id'] );
				++$index;
			}
			return (string) $processor;
		}
		return $content;
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
	foreach ( $groups as $group ) {
		$label = $group['term'] ? $group['term']->name : __( 'Other', 'docszine' );
		echo '<div class="contents-group">';
		echo '<div class="contents-group-title">' . esc_html( $label ) . '</div>';
		foreach ( $group['posts'] as $post ) {
			$class = (int) $current_post_id === (int) $post->ID ? ' class="active current-post" aria-current="page"' : '';
			echo '<a' . $class . ' href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $class is fixed theme markup.
		}
		echo '</div>';
	}
}
