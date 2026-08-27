<?php
/**
 * Safe, optional integration with the SchoolPress Results plugin.
 *
 * The theme must keep working with the plugin disabled, so every
 * function here is defensive: it checks the plugin class exists
 * before calling into it, and always returns a safe empty value
 * otherwise. No result/gallery business logic lives in this theme —
 * this file only reads data the plugin already decided to expose.
 *
 * @package SchoolPress\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the SchoolPress Results plugin is active and its Gallery
 * module foundation is available to read from.
 */
function spst_gallery_plugin_available(): bool {
	return class_exists( '\SchoolPress\Results\Modules\Gallery\Gallery_Module' );
}

/**
 * Returns published gallery items in the small, curated shape the
 * plugin exposes, or an empty array if the plugin/module is not
 * available. The theme never queries `spsr_gallery_item` posts
 * directly — it only ever goes through this helper — so the plugin
 * remains the single owner of gallery data and its shape.
 *
 * @return array<int, array{title: string, caption: string, image_url: string|false, featured: bool}>
 */
function spst_get_gallery_items( int $limit = 12 ): array {
	if ( ! spst_gallery_plugin_available() ) {
		return array();
	}

	return \SchoolPress\Results\Modules\Gallery\Gallery_Module::get_published_items( $limit );
}
