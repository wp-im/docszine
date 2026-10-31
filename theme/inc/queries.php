<?php
/**
 * Magazine query helpers.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the single Issue assigned to a post.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function docszine_get_post_issue( $post_id = 0 ) {
	if ( ! taxonomy_exists( 'docszine_issue' ) ) {
		return null;
	}
	$terms = wp_get_post_terms( $post_id ? $post_id : get_the_ID(), 'docszine_issue' );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}
	return $terms[0];
}

/**
 * Get the magazine Section from native WordPress Categories.
 *
 * Docszine treats Category as the editorial Section. Editors should use
 * one main Category for an issue article; Tags carry secondary topics.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function docszine_get_post_section( $post_id = 0 ) {
	$categories = get_the_category( $post_id ? $post_id : get_the_ID() );
	return ! empty( $categories ) ? $categories[0] : null;
}

/**
 * Get all native Tags used as cross-issue Topics.
 *
 * @param int $post_id Post ID.
 * @return WP_Term[]
 */
function docszine_get_post_topics( $post_id = 0 ) {
	$tags = get_the_tags( $post_id ? $post_id : get_the_ID() );
	return ( $tags && ! is_wp_error( $tags ) ) ? array_values( $tags ) : array();
}

/**
 * Backward-compatible first-topic helper. Prefer docszine_get_post_topics().
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function docszine_get_post_topic( $post_id = 0 ) {
	$topics = docszine_get_post_topics( $post_id );
	return ! empty( $topics ) ? $topics[0] : null;
}

/**
 * Return all Issue terms in descending editorial order.
 *
 * The optional Editorial plugin owns the taxonomy and metadata. The theme
 * only reads it, and falls back cleanly when the taxonomy is unavailable.
 *
 * @return WP_Term[]
 */
function docszine_get_issue_terms_ordered() {
	static $cache = array();
	$cache_key    = get_current_blog_id() . ':' . get_locale();

	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}
	if ( ! taxonomy_exists( 'docszine_issue' ) ) {
		return array();
	}

	$found = get_terms(
		array(
			'taxonomy'   => 'docszine_issue',
			'hide_empty' => false,
			'number'     => 0,
		)
	);
	if ( is_wp_error( $found ) || empty( $found ) ) {
		$cache[ $cache_key ] = array();
		return $cache[ $cache_key ];
	}

	usort(
		$found,
		static function ( $a, $b ) {
			$a_number = absint( get_term_meta( $a->term_id, '_docszine_issue_number', true ) );
			$b_number = absint( get_term_meta( $b->term_id, '_docszine_issue_number', true ) );
			$a_key    = $a_number ? $a_number : 0;
			$b_key    = $b_number ? $b_number : 0;
			if ( $a_key === $b_key ) {
				return (int) $b->term_id <=> (int) $a->term_id;
			}
			return $b_key <=> $a_key;
		}
	);

	$cache[ $cache_key ] = array_values( $found );
	return $cache[ $cache_key ];
}

/** Get configured current Issue, falling back to the newest Issue. */
function docszine_get_current_issue() {
	if ( ! taxonomy_exists( 'docszine_issue' ) ) {
		return null;
	}
	$term_id = absint( get_option( 'docszine_current_issue', 0 ) );
	if ( $term_id ) {
		$term = get_term( $term_id, 'docszine_issue' );
		if ( $term && ! is_wp_error( $term ) ) {
			return $term;
		}
	}

	$terms = docszine_get_issue_terms_ordered();
	return ! empty( $terms ) ? $terms[0] : null;
}

/**
 * Query posts in one issue in editorial order, cached for this request.
 *
 * @param int $issue_term_id Issue term ID.
 * @param int $limit        Maximum number of posts to return; zero returns the complete Issue.
 * @return WP_Post[]
 */
function docszine_get_issue_posts( $issue_term_id, $limit = 100 ) {
	static $cache = array();

	$issue_term_id = absint( $issue_term_id );
	$limit         = absint( $limit );
	if ( ! taxonomy_exists( 'docszine_issue' ) || ! $issue_term_id ) {
		return array();
	}

	$cache_key = get_current_blog_id() . ':' . get_locale() . ':' . $issue_term_id;
	if ( isset( $cache[ $cache_key ] ) ) {
		return $limit ? array_slice( $cache[ $cache_key ], 0, $limit ) : $cache[ $cache_key ];
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			// Read one complete Issue once: slicing before sorting loses editorial order.
			'posts_per_page'         => -1,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Only one Issue is loaded and cached for this request.
			'tax_query'              => array(
				array(
					'taxonomy' => 'docszine_issue',
					'field'    => 'term_id',
					'terms'    => array( $issue_term_id ),
				),
			),
			'orderby'                => array( 'date' => 'ASC' ),
			'order'                  => 'ASC',
			'update_post_term_cache' => true,
			'update_post_meta_cache' => true,
		)
	);
	$posts = $query->posts;
	wp_reset_postdata();

	usort(
		$posts,
		static function ( $a, $b ) {
			$a_order = absint( get_post_meta( $a->ID, '_docszine_issue_order', true ) );
			$b_order = absint( get_post_meta( $b->ID, '_docszine_issue_order', true ) );
			$a_key   = $a_order > 0 ? $a_order : PHP_INT_MAX;
			$b_key   = $b_order > 0 ? $b_order : PHP_INT_MAX;
			if ( $a_key === $b_key ) {
				$date_order = strcmp( $a->post_date, $b->post_date );
				return $date_order ? $date_order : ( (int) $a->ID <=> (int) $b->ID );
			}
			return $a_key <=> $b_key;
		}
	);

	$cache[ $cache_key ] = $posts;
	return $limit ? array_slice( $posts, 0, $limit ) : $posts;
}

/**
 * Get the current Issue lead story.
 *
 * Native Sticky Posts act as the editorial "cover story" flag. If one or
 * more sticky posts belong to this Issue, the first one in Issue order wins.
 * Otherwise the first ordered Issue post is used.
 *
 * @param int            $issue_term_id Issue term ID.
 * @param WP_Post[]|null $issue_posts  Optional preloaded Issue posts.
 * @return WP_Post|null
 */
function docszine_get_issue_lead_post( $issue_term_id, $issue_posts = null ) {
	$posts = is_array( $issue_posts ) ? $issue_posts : docszine_get_issue_posts( $issue_term_id, 0 );
	if ( empty( $posts ) ) {
		return null;
	}

	$sticky_ids = array_map( 'intval', (array) get_option( 'sticky_posts', array() ) );
	if ( $sticky_ids ) {
		foreach ( $posts as $post ) {
			if ( in_array( (int) $post->ID, $sticky_ids, true ) ) {
				return $post;
			}
		}
	}

	return $posts[0];
}

/**
 * Group issue posts by native Category, cached for this request.
 *
 * @param int $issue_term_id Issue term ID.
 * @return array
 */
function docszine_get_issue_posts_grouped( $issue_term_id ) {
	static $cache = array();

	$issue_term_id = absint( $issue_term_id );
	$cache_key     = get_current_blog_id() . ':' . get_locale() . ':' . $issue_term_id;
	if ( isset( $cache[ $cache_key ] ) ) {
		return $cache[ $cache_key ];
	}

	$groups = array();
	foreach ( docszine_get_issue_posts( $issue_term_id, 0 ) as $post ) {
		$section = docszine_get_post_section( $post->ID );
		$key     = $section ? 'term-' . $section->term_id : 'unsectioned';
		if ( ! isset( $groups[ $key ] ) ) {
			$groups[ $key ] = array(
				'term'  => $section,
				'posts' => array(),
			);
		}
		$groups[ $key ]['posts'][] = $post;
	}

	$cache[ $cache_key ] = $groups;
	return $groups;
}

/**
 * Get previous or next article inside the same issue.
 *
 * @param int    $post_id   Post ID.
 * @param string $direction Navigation direction.
 * @return WP_Post|null
 */
function docszine_get_issue_adjacent_post( $post_id, $direction = 'next' ) {
	$issue = docszine_get_post_issue( $post_id );
	if ( ! $issue ) {
		return null;
	}
	$posts = docszine_get_issue_posts( $issue->term_id, 0 );
	$ids   = wp_list_pluck( $posts, 'ID' );
	$index = array_search( (int) $post_id, array_map( 'intval', $ids ), true );
	if ( false === $index ) {
		return null;
	}
	$target = 'previous' === $direction ? $index - 1 : $index + 1;
	return isset( $posts[ $target ] ) ? $posts[ $target ] : null;
}

/**
 * Get related posts from the same native Category.
 *
 * @param int $post_id Post ID.
 * @param int $limit   Maximum number of posts to return.
 * @return WP_Post[]
 */
function docszine_get_related_section_posts( $post_id, $limit = 3 ) {
	$section = docszine_get_post_section( $post_id );
	if ( ! $section ) {
		return array();
	}
	$args  = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => min( 6, max( 1, absint( $limit ) ) ),
		'post__not_in'        => array( absint( $post_id ) ),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'cat'                 => (int) $section->term_id,
	);
	$issue = docszine_get_post_issue( $post_id );
	if ( $issue && taxonomy_exists( 'docszine_issue' ) ) {
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Related content is bounded to six posts and avoids the current Issue.
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'docszine_issue',
				'field'    => 'term_id',
				'terms'    => array( (int) $issue->term_id ),
				'operator' => 'NOT IN',
			),
		);
	}
	$query = new WP_Query( $args );
	$posts = $query->posts;
	wp_reset_postdata();
	return $posts;
}

/**
 * Get related posts sharing any native Tag with the current article.
 *
 * @param int $post_id Post ID.
 * @param int $limit   Maximum number of posts to return.
 * @return WP_Post[]
 */
function docszine_get_related_topic_posts( $post_id, $limit = 3 ) {
	$topics = docszine_get_post_topics( $post_id );
	if ( empty( $topics ) ) {
		return array();
	}
	$args  = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => min( 6, max( 1, absint( $limit ) ) ),
		'post__not_in'        => array( absint( $post_id ) ),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'tag__in'             => array_map( 'intval', wp_list_pluck( $topics, 'term_id' ) ),
	);
	$issue = docszine_get_post_issue( $post_id );
	if ( $issue && taxonomy_exists( 'docszine_issue' ) ) {
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Related content is bounded to six posts and avoids the current Issue.
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'docszine_issue',
				'field'    => 'term_id',
				'terms'    => array( (int) $issue->term_id ),
				'operator' => 'NOT IN',
			),
		);
	}
	$query = new WP_Query( $args );
	$posts = $query->posts;
	wp_reset_postdata();
	return $posts;
}

/**
 * Get a small cross-issue selection for a Category or Tag term.
 *
 * @param string $taxonomy         Taxonomy name.
 * @param int    $term_id          Term ID.
 * @param int    $limit            Maximum number of posts to return.
 * @param int    $exclude_issue_id Issue term ID to exclude.
 * @return WP_Post[]
 */
function docszine_get_taxonomy_posts( $taxonomy, $term_id, $limit = 4, $exclude_issue_id = 0 ) {
	if ( ! taxonomy_exists( $taxonomy ) || ! $term_id ) {
		return array();
	}

	$tax_query = array(
		array(
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => array( absint( $term_id ) ),
		),
	);
	if ( $exclude_issue_id && taxonomy_exists( 'docszine_issue' ) ) {
		$tax_query[] = array(
			'taxonomy' => 'docszine_issue',
			'field'    => 'term_id',
			'terms'    => array( absint( $exclude_issue_id ) ),
			'operator' => 'NOT IN',
		);
	}

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => min( 8, max( 1, absint( $limit ) ) ),
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Cross-issue selections are bounded to eight posts.
			'tax_query'           => $tax_query,
		)
	);
	$posts = $query->posts;
	wp_reset_postdata();
	return $posts;
}

/**
 * Get a latest-post fallback for sites without magazine metadata.
 *
 * @param int $limit            Maximum number of posts to return.
 * @param int $exclude_issue_id Issue term ID to exclude.
 * @return WP_Post[]
 */
function docszine_get_latest_posts( $limit = 6, $exclude_issue_id = 0 ) {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => min( 12, max( 1, absint( $limit ) ) ),
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);
	if ( $exclude_issue_id && taxonomy_exists( 'docszine_issue' ) ) {
		// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- The latest-post fallback is bounded to twelve posts.
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'docszine_issue',
				'field'    => 'term_id',
				'terms'    => array( absint( $exclude_issue_id ) ),
				'operator' => 'NOT IN',
			),
		);
	}
	$query = new WP_Query( $args );
	$posts = $query->posts;
	wp_reset_postdata();
	return $posts;
}

/**
 * Get the neighboring Issue term ordered by issue number, falling back to term ID.
 *
 * @param int    $current_term_id Current Issue term ID.
 * @param string $direction       Navigation direction.
 * @return WP_Term|null
 */
function docszine_get_adjacent_issue_term( $current_term_id, $direction = 'next' ) {
	$terms = array_reverse( docszine_get_issue_terms_ordered() );
	if ( empty( $terms ) ) {
		return null;
	}
	$ids   = array_map( static fn( $term ) => (int) $term->term_id, $terms );
	$index = array_search( (int) $current_term_id, $ids, true );
	if ( false === $index ) {
		return null;
	}
	$target = 'previous' === $direction ? $index - 1 : $index + 1;
	return isset( $terms[ $target ] ) ? $terms[ $target ] : null;
}
