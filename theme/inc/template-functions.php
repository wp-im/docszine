<?php
/**
 * Theme hooks and body classes.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add magazine-aware body classes without assuming the plugin is active.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function docszine_body_classes( $classes ) {
	if ( taxonomy_exists( 'docszine_issue' ) ) {
		$classes[] = 'docszine-editorial-active';
	}
	if ( is_singular( 'post' ) && docszine_get_post_issue() ) {
		$classes[] = 'has-issue-context';
	}
	return $classes;
}
add_filter( 'body_class', 'docszine_body_classes' );

/**
 * Normalize the context shared by every public reading shell.
 *
 * @param array $context Optional route-specific context.
 * @return array
 */
function docszine_get_shell_context( $context = array() ) {
	$context = wp_parse_args(
		$context,
		array(
			'route'    => 'generic',
			'issue'    => null,
			'post_id'  => 0,
			'headings' => array(),
		)
	);

	if ( ! $context['post_id'] && is_singular( 'post' ) ) {
		$context['post_id'] = get_queried_object_id();
	}
	if ( ! $context['issue'] && is_tax( 'docszine_issue' ) ) {
		$queried_issue    = get_queried_object();
		$context['issue'] = $queried_issue instanceof WP_Term ? $queried_issue : null;
	}
	if ( 'generic' === $context['route'] ) {
		if ( is_front_page() ) {
			$context['route'] = 'home';
		} elseif ( is_singular( 'post' ) ) {
			$context['route'] = 'article';
		} elseif ( is_page() ) {
			$context['route'] = 'page';
		} elseif ( is_tax( 'docszine_issue' ) ) {
			$context['route'] = 'issue';
		} elseif ( is_search() ) {
			$context['route'] = 'search';
		} elseif ( is_404() ) {
			$context['route'] = 'error';
		} elseif ( is_archive() ) {
			$context['route'] = 'archive';
		}
	}
	if ( 'home' === $context['route'] && ! $context['issue'] ) {
		$context['issue'] = docszine_get_current_issue();
	}

	return apply_filters( 'docszine_shell_context', $context );
}
