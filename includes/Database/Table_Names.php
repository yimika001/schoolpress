<?php
/**
 * Centralized table-name abstraction for the academic/results database.
 *
 * INTEGRATION NOTE:
 * This file is new in Milestone 3 Part 1. It assumes the existing
 * namespace-based autoloader in schoolpress-results.php resolves
 * SchoolPress\Results\Database\Table_Names to:
 *   plugin/schoolpress-results/includes/Database/Table_Names.php
 * This mirrors the verified convention used by existing classes such as
 * SchoolPress\Results\Admin\Admin_Menu -> includes/Admin/Admin_Menu.php.
 * If the actual autoloader maps differently, only this file's location
 * needs to move — no class code changes are required.
 *
 * @package SchoolPress\Results\Database
 */

namespace SchoolPress\Results\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Table_Names
 *
 * Single source of truth for academic/results table base names and their
 * fully prefixed ($wpdb->prefix) equivalents. No other class should
 * construct these table names manually.
 */
class Table_Names {

	/**
	 * Base (unprefixed) table names, without the WordPress prefix.
	 */
	const SESSIONS           = 'spsr_sessions';
	const TERMS               = 'spsr_terms';
	const CLASSES             = 'spsr_classes';
	const SUBJECTS            = 'spsr_subjects';
	const STUDENTS            = 'spsr_students';
	const ENROLLMENTS         = 'spsr_enrollments';
	const RESULTS             = 'spsr_results';
	const RESULT_ITEMS        = 'spsr_result_items';
	const RESULT_CREDENTIALS  = 'spsr_result_credentials';

	/**
	 * Get all base table name constants, keyed by short identifier.
	 *
	 * @return array<string,string>
	 */
	protected static function base_map() {
		return array(
			'sessions'           => self::SESSIONS,
			'terms'              => self::TERMS,
			'classes'            => self::CLASSES,
			'subjects'           => self::SUBJECTS,
			'students'           => self::STUDENTS,
			'enrollments'        => self::ENROLLMENTS,
			'results'            => self::RESULTS,
			'result_items'       => self::RESULT_ITEMS,
			'result_credentials' => self::RESULT_CREDENTIALS,
		);
	}

	/**
	 * Resolve a fully prefixed table name by short identifier.
	 *
	 * Example: Table_Names::get( 'sessions' ) => "{$wpdb->prefix}spsr_sessions"
	 *
	 * @param string $key One of: sessions, terms, classes, subjects, students,
	 *                    enrollments, results, result_items, result_credentials.
	 * @return string Fully prefixed table name.
	 */
	public static function get( $key ) {
		global $wpdb;

		$map = self::base_map();

		if ( ! isset( $map[ $key ] ) ) {
			// Fail loudly during development; never silently return an
			// unprefixed or incorrect table name.
			throw new \InvalidArgumentException(
				sprintf( 'Unknown academic table identifier: %s', esc_html( $key ) )
			);
		}

		return $wpdb->prefix . $map[ $key ];
	}

	/**
	 * Convenience accessors — one per table. These simply proxy to get()
	 * but read better at call sites (Table_Names::sessions() vs
	 * Table_Names::get( 'sessions' )).
	 */
	public static function sessions() {
		return self::get( 'sessions' );
	}

	public static function terms() {
		return self::get( 'terms' );
	}

	public static function classes() {
		return self::get( 'classes' );
	}

	public static function subjects() {
		return self::get( 'subjects' );
	}

	public static function students() {
		return self::get( 'students' );
	}

	public static function enrollments() {
		return self::get( 'enrollments' );
	}

	public static function results() {
		return self::get( 'results' );
	}

	public static function result_items() {
		return self::get( 'result_items' );
	}

	public static function result_credentials() {
		return self::get( 'result_credentials' );
	}

	/**
	 * Return all fully prefixed academic table names, e.g. for reverse
	 * ordering during a future (not-yet-implemented) uninstall routine.
	 *
	 * @return string[]
	 */
	public static function all() {
		$names = array();
		foreach ( array_keys( self::base_map() ) as $key ) {
			$names[ $key ] = self::get( $key );
		}
		return $names;
	}
}
