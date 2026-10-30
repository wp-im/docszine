<?php
/**
 * Site footer.
 *
 * @package Docszine
 */

?>
<footer class="site-footer" id="colophon">
	<div class="footer-inner">
		<div class="footer-copy">
			<?php
			$footer_text = get_theme_mod( 'docszine_footer_text', '' );
			echo $footer_text ? esc_html( $footer_text ) : esc_html( get_bloginfo( 'name' ) );
			?>
		</div>
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
		<nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'docszine' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'fallback_cb'    => false,
					'depth'          => 1,
				)
			);
			?>
		</nav>
		<?php endif; ?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
