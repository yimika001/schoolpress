<?php
/**
 * Contract for a single versioned database migration.
 *
 * @package SchoolPress\Results\Database
 */

namespace SchoolPress\Results\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface Migration_Interface
 *
 * Each migration represents one forward step in the academic database
 * schema. Migrations are never edited once released; schema changes are
 * expressed as new migrations with a higher version.
 */
interface Migration_Interface {

	/**
	 * Target version this migration upgrades the database TO.
	 * Stored in the spsr_db_version option once this migration succeeds.
	 *
	 * Versions are plain incrementing strings/integers (e.g. "1", "2"),
	 * compared numerically by the Migrator, so future migrations can be
	 * appended without renumbering existing ones.
	 *
	 * @return string
	 */
	public function target_version();

	/**
	 * Short human-readable description, used only in logging.
	 *
	 * @return string
	 */
	public function description();

	/**
	 * Execute the migration. Must be safe to run more than once
	 * (idempotent) since dbDelta()-based creation is inherently
	 * re-runnable, and the Migrator itself guards against re-running
	 * already-applied versions.
	 *
	 * Implementations should return true on success. Any failure should
	 * either return false or throw, so the Migrator does not advance
	 * spsr_db_version past a failed step.
	 *
	 * @return bool
	 */
	public function run();
}
