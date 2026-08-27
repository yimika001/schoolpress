<?php
/**
 * Migration 0001: Initial academic/results schema.
 *
 * Creates all nine approved Milestone 2 tables. This is the first real
 * schema migration; it upgrades spsr_db_version from "0" (the existing
 * Milestone 1 placeholder) to "1".
 *
 * @package SchoolPress\Results\Database\Migrations
 */

namespace SchoolPress\Results\Database\Migrations;

use SchoolPress\Results\Database\Migration_Interface;
use SchoolPress\Results\Database\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Migration_0001_Initial_Schema implements Migration_Interface {

	/**
	 * {@inheritDoc}
	 */
	public function target_version() {
		return '1';
	}

	/**
	 * {@inheritDoc}
	 */
	public function description() {
		return 'Create initial academic schema: sessions, terms, classes, subjects, students, enrollments, results, result_items, result_credentials.';
	}

	/**
	 * {@inheritDoc}
	 *
	 * dbDelta() is inherently additive/idempotent: it compares each
	 * CREATE TABLE definition against the live schema and only applies
	 * the difference. Running this migration again (e.g. because the
	 * Migrator is re-invoked) will not duplicate tables or data.
	 */
	public function run() {
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		$statements = Schema::get_all_sql();

		// dbDelta() reports, per table, which queries it actually ran.
		// We treat the migration as successful only if dbDelta did not
		// throw/fatal and every expected table now exists — dbDelta
		// itself does not return a simple boolean, so existence is
		// verified explicitly below rather than assumed.
		foreach ( $statements as $sql ) {
			dbDelta( $sql );
		}

		return $this->verify_tables_exist( array_keys( $statements ) );
	}

	/**
	 * Confirm every expected table is present after dbDelta() runs.
	 *
	 * @param string[] $tables Fully prefixed table names.
	 * @return bool
	 */
	protected function verify_tables_exist( array $tables ) {
		global $wpdb;

		foreach ( $tables as $table ) {
			$found = $wpdb->get_var(
				$wpdb->prepare( 'SHOW TABLES LIKE %s', $table )
			);

			if ( $found !== $table ) {
				return false;
			}
		}

		return true;
	}
}
