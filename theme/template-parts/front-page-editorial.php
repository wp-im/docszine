<?php
/**
 * Native static-front-page editorial content.
 *
 * Uses the main WordPress Loop and the_content() so content filtering,
 * shortcodes, embeds and capability/trust behavior remain WordPress-native.
 *
 * @package Docszine
 */

if ( 'page' !== get_option( 'show_on_front' ) || ! have_posts() ) {
	return;
}

while ( have_posts() ) :
	the_post();
	if ( '' === trim( get_the_content() ) ) {
		continue;
	}
	?>
	<section class="home-section front-page-editorial">
		<div class="front-page-editorial-content"><?php the_content(); ?></div>
	</section>
	<?php
endwhile;

rewind_posts();
