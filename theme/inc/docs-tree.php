<?php
/**
 * Native Pages-based documentation navigation.
 *
 * @package Docszine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the configured Docs Root page, if it is still a published Page.
 *
 * @return WP_Post|null
 */
function docszine_get_docs_root_page() {
	$page_id = absint( get_theme_mod( 'docszine_docs_root_page', 0 ) );
	$page    = $page_id ? get_post( $page_id ) : null;

	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
		return null;
	}

	return $page;
}

/**
 * Return published Pages that belong to the Docs Tree.
 *
 * The tree is deliberately built from native Page hierarchy and menu order.
 * When no root is configured, top-level Pages become the fallback tree.
 *
 * @return WP_Post[]
 */
function docszine_get_docs_tree_pages() {
	static $cache = null;

	if ( null !== $cache ) {
		return $cache;
	}

	$pages = get_pages(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'asc',
			'number'      => 0,
		)
	);

	if ( ! $pages ) {
		$cache = array();
		return $cache;
	}

	$root = docszine_get_docs_root_page();
	if ( ! $root ) {
		$cache = $pages;
		return $cache;
	}

	$children = get_page_children( $root->ID, $pages );
	$cache    = array_merge( array( $root ), $children );
	return $cache;
}

/**
 * Return the Pages used as directory entries on the Docs-first homepage.
 *
 * @return WP_Post[]
 */
function docszine_get_docs_directory_pages() {
	$pages = docszine_get_docs_tree_pages();
	$root  = docszine_get_docs_root_page();

	if ( ! $root ) {
		return array_values(
			array_filter(
				$pages,
				static function ( $page ) {
					return 0 === (int) $page->post_parent;
				}
			)
		);
	}

	$children = array_values(
		array_filter(
			$pages,
			static function ( $page ) use ( $root ) {
				return (int) $page->post_parent === (int) $root->ID;
			}
		)
	);

	return $children ? $children : array( $root );
}

/**
 * Determine whether a Page is the current queried Page.
 *
 * @param int $page_id Page ID.
 * @return bool
 */
function docszine_is_docs_page_current( $page_id ) {
	$page_id = absint( $page_id );

	if ( ! $page_id || ! is_page() ) {
		return false;
	}

	return get_queried_object_id() === $page_id;
}

/**
 * Determine whether a Page is an ancestor of the current queried Page.
 *
 * @param int $page_id Page ID.
 * @return bool
 */
function docszine_is_docs_page_ancestor( $page_id ) {
	$page_id = absint( $page_id );

	if ( ! $page_id || ! is_page() ) {
		return false;
	}

	$current_id = get_queried_object_id();
	return $page_id !== $current_id && in_array( $page_id, get_post_ancestors( $current_id ), true );
}

/**
 * Render one Docs Tree branch.
 *
 * @param array $pages     Pages indexed by parent ID.
 * @param int   $parent_id Parent Page ID.
 * @param int   $depth     Current tree depth.
 */
function docszine_render_docs_tree_branch( $pages, $parent_id = 0, $depth = 0 ) {
	if ( empty( $pages[ $parent_id ] ) ) {
		return;
	}

	echo '<ul class="docszine-tree-children">';
	foreach ( $pages[ $parent_id ] as $page ) {
		$children = ! empty( $pages[ $page->ID ] );
		$open     = $children && ( docszine_is_docs_page_current( $page->ID ) || docszine_is_docs_page_ancestor( $page->ID ) || ( is_front_page() && 0 === $depth ) );
		$current  = docszine_is_docs_page_current( $page->ID );
		$ancestor = docszine_is_docs_page_ancestor( $page->ID );
		$classes  = array( 'docszine-tree-node' );
		if ( $open ) {
			$classes[] = 'is-open';
		}
		?>
		<li class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="docszine-tree-row<?php echo $children ? '' : ' no-toggle'; ?>">
				<?php if ( $children ) : ?>
					<?php /* translators: %s: documentation page title. */ ?>
					<button class="docszine-tree-toggle" type="button" data-docszine-tree-toggle aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Expand or collapse %s', 'docszine' ), get_the_title( $page ) ) ); ?>"><span aria-hidden="true"></span></button>
				<?php else : ?>
					<span class="docszine-tree-spacer" aria-hidden="true"></span>
				<?php endif; ?>
				<a class="docszine-tree-link<?php echo $current ? ' is-current' : ( $ancestor ? ' is-ancestor' : '' ); ?>" href="<?php echo esc_url( get_permalink( $page ) ); ?>"<?php echo $current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( get_the_title( $page ) ); ?></a>
			</div>
			<?php
			if ( $children ) :
				docszine_render_docs_tree_branch( $pages, $page->ID, $depth + 1 );
endif;
			?>
		</li>
		<?php
	}
	echo '</ul>';
}

/**
 * Render the shared Docs Tree navigation.
 *
 * @param array $args Optional renderer arguments.
 */
function docszine_render_docs_tree( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'label' => __( 'Documentation', 'docszine' ),
		)
	);

	$tree_pages = docszine_get_docs_tree_pages();
	if ( ! $tree_pages ) {
		return;
	}

	$pages_by_parent = array();
	foreach ( $tree_pages as $page ) {
		$pages_by_parent[ (int) $page->post_parent ][] = $page;
	}

	$root = docszine_get_docs_root_page();
	$top  = $root ? array( $root ) : ( $pages_by_parent[0] ?? array() );
	if ( ! $top ) {
		return;
	}

	$pages_by_parent[0] = $top;
	?>
	<nav class="docszine-docs-tree" aria-label="<?php echo esc_attr( $args['label'] ); ?>">
		<p class="docszine-tree-label"><?php echo esc_html( $args['label'] ); ?></p>
		<?php docszine_render_docs_tree_branch( $pages_by_parent ); ?>
	</nav>
	<?php
}
