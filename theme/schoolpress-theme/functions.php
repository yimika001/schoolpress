<?php
/**
 * SchoolPress Theme functions and definitions.
 *
 * @package SchoolPress\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_theme_file_path( 'inc/plugin-integration.php' );

/**
 * Core theme setup: text domain, and the feature supports a modern
 * block theme is expected to declare explicitly. Kept intentionally
 * small for the foundation milestone.
 */
function spst_setup(): void {
	load_theme_textdomain( 'schoolpress-theme', get_template_directory() . '/languages' );

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'spst_setup' );

/**
 * Registers a dedicated pattern category so SchoolPress patterns are
 * easy to find in the inserter as more are added (About, Principal's
 * Message, Programs, Statistics, News, Admissions CTA, ...).
 */
function spst_register_pattern_category(): void {
	register_block_pattern_category(
		'schoolpress',
		array( 'label' => __( 'SchoolPress', 'schoolpress-theme' ) )
	);
}
add_action( 'init', 'spst_register_pattern_category' );

/**
 * Minimal stylesheet for the gallery grid placeholder markup used in
 * the gallery-section pattern. Kept separate from style.css so it can
 * be dropped/replaced without touching the theme's main stylesheet
 * header, and only loaded on the front end.
 */
function spst_enqueue_assets(): void {
	wp_enqueue_style(
		'schoolpress-theme-style',
		get_stylesheet_uri(),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_style(
		'schoolpress-theme-gallery',
		get_theme_file_uri( 'assets/css/gallery.css' ),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'spst_enqueue_assets' );
