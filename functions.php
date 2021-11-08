<?php
/**
 * American Lanterns Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package AmericanLanterns
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_VERSION', '1.0.0' );

/**
 * Enqueue styles, dequeue ASTRA stylesheet.
 */
function child_enqueue_styles() {

	wp_enqueue_style(
		'zpv-theme-css',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'astra-theme-css' ),
		CHILD_THEME_VERSION,
		'all'
	);

	wp_enqueue_style( 'dashicons' );

	wp_dequeue_style( 'astra-theme-css' );
}
add_action( 'wp_enqueue_scripts', 'child_enqueue_styles' );
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_theme_support( 'editor-style' );

/**
 * Registers an editor stylesheet in a sub-directory.
 */
function add_editor_styles_sub_dir() {
    add_editor_style( trailingslashit( get_template_directory_uri() ) . 'editor-style.css' );
}
add_action( 'after_setup_theme', 'add_editor_styles_sub_dir' );


function add_footer_info() {

	?>
</div>
<div class="ast-container" id="inspire-footer">
		<article>
			<div class="before-footer">
				<h2>Our lantern festivals light up the night and inspire fantasy, imagination and celebration!</h2>
				<p>People of all ages can wander through a dazzling array of larger-than-life illuminated silk lanterns and light festival displays while taking in the fresh air and unparalleled majestic scenery of the Finger Lakes in New York. Groups of all sizes are welcome</p>
			</div>
		</article>

</div>
<?php
}
add_action( 'astra_content_bottom', 'add_footer_info' );