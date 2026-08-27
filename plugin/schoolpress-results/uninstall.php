<?php
/**
 * Fired when the plugin is deleted via the WordPress admin.
 *
 * IMPORTANT: This intentionally does NOT delete any options, posts,
 * post meta, taxonomies, or terms. The academic/result database
 * schema has not been finalized/approved yet, so there is no safe,
 * reviewed definition yet of what "this plugin's data" fully
 * includes. Once the schema (Milestone 2+) is approved, this file
 * should be updated to remove plugin-owned data ONLY, behind an
 * explicit opt-in setting — never destructively by default.
 *
 * @package SchoolPress\Results
 */

// If uninstall is not called from WordPress, bail.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// No-op by design. See note above.
