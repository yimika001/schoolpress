<?php
/**
 * Gallery module foundation.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results\Modules\Gallery;

use SchoolPress\Results\Admin\Admin_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ARCHITECTURE NOTE (flagged for approval, see docs/architecture):
 *
 * Gallery items are modelled as a WordPress custom post type rather
 * than a bespoke $wpdb table. This is a deliberate, contained
 * decision — not the same category of change as the future
 * students/results schema:
 *
 *  - Images live in the Media Library already (attachment IDs via
 *    featured image), which the brief requires.
 *  - "Title", "caption/description", "publish/unpublish",
 *    "edit/delete/archive" are native WordPress post concepts —
 *    reusing them avoids re-implementing storage, revisions, and
 *    capability checks that WordPress already gives us for free.
 *  - It keeps Gallery fully decoupled from the (not yet designed)
 *    academic/result schema and its future $wpdb migrations.
 *
 * This class only registers the data model (post type, taxonomy,
 * meta). It does NOT build:
 *  - custom meta-box UI for "featured" / manual ordering,
 *  - a bespoke REST controller (the auto-generated core REST route
 *    is sufficient for Milestone 1 and can be replaced later),
 *  - any front-end rendering (that belongs to the theme).
 *
 * Full admin UX (drag-reorder, featured toggle UI, category picker
 * polish) is explicitly deferred to the Gallery milestone.
 */
class Gallery_Module {

	public const POST_TYPE     = 'spsr_gallery_item';
	public const TAXONOMY      = 'spsr_gallery_category';
	public const META_FEATURED = '_spsr_gallery_featured';

	public function register(): void {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_action( 'init', array( $this, 'register_meta' ) );
	}

	public function register_post_type(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => array(
					'name'          => __( 'Gallery Items', 'schoolpress-results' ),
					'singular_name' => __( 'Gallery Item', 'schoolpress-results' ),
					'add_new_item'  => __( 'Add Gallery Item', 'schoolpress-results' ),
					'edit_item'     => __( 'Edit Gallery Item', 'schoolpress-results' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => Admin_Menu::MENU_SLUG,
				'show_in_rest'        => true,
				'rest_base'           => 'gallery-items',
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'exclude_from_search' => true,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
				/*
				 * "editor" is used for the caption/description field —
				 * reusing the native content editor rather than inventing
				 * a separate custom field for it.
				 */
			)
		);
	}

	public function register_taxonomy(): void {
		register_taxonomy(
			self::TAXONOMY,
			self::POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Gallery Categories', 'schoolpress-results' ),
					'singular_name' => __( 'Gallery Category', 'schoolpress-results' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'rewrite'           => false,
			)
		);
	}

	/**
	 * Registers the one piece of custom meta this milestone needs
	 * ("featured"). Display order uses core `menu_order` rather than
	 * custom meta, so no separate order field is registered here.
	 */
	public function register_meta(): void {
		register_post_meta(
			self::POST_TYPE,
			self::META_FEATURED,
			array(
				'type'              => 'boolean',
				'single'            => true,
				'default'           => false,
				'show_in_rest'      => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => static fn(): bool => current_user_can( Admin_Menu::CAPABILITY ),
			)
		);
	}

	/**
	 * Foundation read helper for the theme. Returns a small, curated
	 * shape rather than raw WP_Post objects so the theme never needs
	 * to know about post types, meta keys, or internal post IDs.
	 *
	 * Intentionally minimal: no pagination/category filtering yet —
	 * that belongs to the full Gallery milestone. This exists now so
	 * the theme's Gallery pattern has something safe to call today,
	 * even while it returns an empty list.
	 *
	 * @return array<int, array{title: string, caption: string, image_url: string|false, featured: bool}>
	 */
	public static function get_published_items( int $limit = 12 ): array {
		if ( ! post_type_exists( self::POST_TYPE ) ) {
			return array();
		}

		$query = new \WP_Query(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);

		$items = array();

		foreach ( $query->posts as $post ) {
			$items[] = array(
				'title'     => get_the_title( $post ),
				'caption'   => wp_strip_all_tags( $post->post_content ),
				'image_url' => get_the_post_thumbnail_url( $post, 'large' ),
				'featured'  => (bool) get_post_meta( $post->ID, self::META_FEATURED, true ),
			);
		}

		return $items;
	}
}
