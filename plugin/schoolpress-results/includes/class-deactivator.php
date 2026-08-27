<?php
/**
 * Deactivation routine.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs on plugin deactivation. Only clears transient/runtime state
 * (rewrite rules) — never touches options or any future stored data,
 * since deactivation is not the same as uninstall.
 */
class Deactivator {

	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
