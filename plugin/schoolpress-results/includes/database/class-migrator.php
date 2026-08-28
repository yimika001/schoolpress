<?php
/**
 * Versioned migration runner for the academic/results database.
 *
 * @package SchoolPress\Results\Database
 */

namespace SchoolPress\Results\Database;

use SchoolPress\Results\Database\Migrations\Migration_0001_Initial_Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Migrator
 *
 * Builds on the existing "spsr_db_version" option (currently "0",
 * introduced by Activator in Milestone 1). Running migrate() applies,
 * in order, every migration whose target_version() is greater than the
 * currently stored version, stopping — and NOT advancing the stored
 * version — at the first failure.
 *
 * This class intentionally does not know about, or depend on, the
 * existing Activator/Plugin implementation. See
 * docs/milestone-3-part-1-activator-integration.md for the single
 * additive call needed to wire this into the existing activation flow.
 */
class Migrator {

	/**
	 * Option name already established in Milestone 1. Reused as-is —
	 * not replaced with a new/unrelated mechanism.
	 */
	const VERSION_OPTION = 'spsr_db_version';

	/**
	 * Registry of all known migrations, in ascending version order.
	 * Future Part 2+ migrations are appended here, never inserted
	 * before existing entries and never renumbered.
	 *
	 * @return Migration_Interface[]
	 */
	protected static function migrations() {
		return array(
			new Migration_0001_Initial_Schema(),
		);
	}

	/**
	 * Get the database version currently recorded for this site.
	 * Defaults to "0" to match the existing Milestone 1 Activator
	 * baseline if the option is somehow missing.
	 *
	 * @return string
	 */
	public static function current_version() {
		return (string) get_option( self::VERSION_OPTION, '0' );
	}

	/**
	 * Whether every known migration has already been applied.
	 *
	 * @return bool
	 */
	public static function is_up_to_date() {
		$migrations = self::migrations();

		if ( empty( $migrations ) ) {
			return true;
		}

		$latest = end( $migrations );

		return version_compare( self::current_version(), $latest->target_version(), '>=' );
	}

	/**
	 * Run all pending migrations in order.
	 *
	 * Safe to call on every activation/upgrade check: migrations whose
	 * target_version() is not greater than the stored version are
	 * skipped, and each individual migration is itself written to be
	 * idempotent (see Migration_0001_Initial_Schema).
	 *
	 * The stored version is advanced one migration at a time, only
	 * after that specific migration succeeds. If a migration fails,
	 * the loop stops immediately and the version option reflects the
	 * last successfully applied migration — never a version whose
	 * migration did not actually complete.
	 *
	 * @return true|\WP_Error True if fully up to date, WP_Error on the
	 *                        first failed migration.
	 */
	public static function migrate() {
		$current = self::current_version();

		foreach ( self::migrations() as $migration ) {
			if ( version_compare( $current, $migration->target_version(), '>=' ) ) {
				// Already applied; skip.
				continue;
			}

			$succeeded = false;

			try {
				$succeeded = (bool) $migration->run();
			} catch ( \Throwable $e ) {
				self::log_failure( $migration, $e->getMessage() );

				return new \WP_Error(
					'spsr_migration_failed',
					sprintf(
						'Academic schema migration to version %s failed: %s',
						$migration->target_version(),
						$e->getMessage()
					)
				);
			}

			if ( ! $succeeded ) {
				self::log_failure( $migration, 'run() returned false.' );

				return new \WP_Error(
					'spsr_migration_failed',
					sprintf(
						'Academic schema migration to version %s did not complete successfully.',
						$migration->target_version()
					)
				);
			}

			// Only now — after confirmed success — advance the stored
			// version. This is the crux of "never mark the database
			// version as migrated when schema creation failed".
			update_option( self::VERSION_OPTION, $migration->target_version() );
			$current = $migration->target_version();

			self::log_success( $migration );
		}

		return true;
	}

	/**
	 * Log a successful migration step via the existing Logger support
	 * class, if available. Falls back to no-op if Logger cannot be
	 * resolved (kept optional/defensive since Logger's exact public
	 * API was not part of the verified handoff).
	 *
	 * @param Migration_Interface $migration
	 */
	protected static function log_success( Migration_Interface $migration ) {
		self::log(
			sprintf(
				'Academic schema migrated to version %s: %s',
				$migration->target_version(),
				$migration->description()
			)
		);
	}

	/**
	 * @param Migration_Interface $migration
	 * @param string              $reason
	 */
	protected static function log_failure( Migration_Interface $migration, $reason ) {
		self::log(
			sprintf(
				'Academic schema migration to version %s FAILED: %s',
				$migration->target_version(),
				$reason
			),
			true
		);
	}

	/**
	 * Best-effort logging through SchoolPress\Results\Support\Logger.
	 * Its exact method signature was not part of the verified Milestone
	 * 1 handoff, so this is written defensively: it checks for the
	 * class and a couple of likely method names before falling back to
	 * error_log(). Replace this shim with a direct call once the real
	 * Logger API is confirmed.
	 *
	 * @param string $message
	 * @param bool   $is_error
	 */
	protected static function log( $message, $is_error = false ) {
		$logger_class = '\\SchoolPress\\Results\\Support\\Logger';

		if ( class_exists( $logger_class ) ) {
			foreach ( array( $is_error ? 'error' : 'info', 'log' ) as $method ) {
				if ( is_callable( array( $logger_class, $method ) ) ) {
					call_user_func( array( $logger_class, $method ), $message );
					return;
				}
			}
		}

		error_log( '[SchoolPress Results] ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
}
