<?php
/**
 * dbDelta-compatible schema definitions for the academic/results tables.
 *
 * @package SchoolPress\Results\Database
 */

namespace SchoolPress\Results\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Schema
 *
 * Produces CREATE TABLE statements for all nine approved Milestone 2
 * tables, formatted per WordPress dbDelta() requirements:
 *  - Two spaces after PRIMARY KEY
 *  - Field types lower-case
 *  - Each key/field on its own line
 *  - No backticks around identifiers (dbDelta does not parse them reliably)
 *
 * No native FOREIGN KEY constraints are used per approved architecture;
 * logical relationships are enforced by application code in a later
 * milestone. Every logical FK column has a supporting index.
 */
class Schema {

	/**
	 * Get an ordered map of table_name => CREATE TABLE SQL for every
	 * academic table. Order matters only for documentation purposes —
	 * dbDelta does not require FK-safe ordering since no FKs are used —
	 * but it is kept parent-before-child for readability.
	 *
	 * @return array<string,string>
	 */
	public static function get_all_sql() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		return array(
			Table_Names::sessions()            => self::sessions_sql( $charset_collate ),
			Table_Names::terms()               => self::terms_sql( $charset_collate ),
			Table_Names::classes()             => self::classes_sql( $charset_collate ),
			Table_Names::subjects()            => self::subjects_sql( $charset_collate ),
			Table_Names::students()            => self::students_sql( $charset_collate ),
			Table_Names::enrollments()         => self::enrollments_sql( $charset_collate ),
			Table_Names::results()             => self::results_sql( $charset_collate ),
			Table_Names::result_items()        => self::result_items_sql( $charset_collate ),
			Table_Names::result_credentials()  => self::result_credentials_sql( $charset_collate ),
		);
	}

	protected static function sessions_sql( $charset_collate ) {
		$table = Table_Names::sessions();

		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  status varchar(20) NOT NULL,
  is_current tinyint(1) NOT NULL DEFAULT 0,
  assessment_config longtext NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY name (name)
) ENGINE=InnoDB {$charset_collate};";
	}

	protected static function terms_sql( $charset_collate ) {
		$table = Table_Names::terms();

		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  session_id bigint(20) unsigned NOT NULL,
  name varchar(50) NOT NULL,
  ordering smallint(5) unsigned NOT NULL,
  is_current tinyint(1) NOT NULL DEFAULT 0,
  status varchar(20) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY session_name (session_id,name),
  KEY session_ordering (session_id,ordering)
) ENGINE=InnoDB {$charset_collate};";
	}

	protected static function classes_sql( $charset_collate ) {
		$table = Table_Names::classes();

		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  slug varchar(120) NOT NULL,
  ordering smallint(5) unsigned NOT NULL,
  description text NULL,
  status varchar(20) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY name (name),
  UNIQUE KEY slug (slug)
) ENGINE=InnoDB {$charset_collate};";
	}

	protected static function subjects_sql( $charset_collate ) {
		$table = Table_Names::subjects();

		// "code" must be unique when present but may repeat as NULL.
		// A standard UNIQUE KEY on a nullable column is the most
		// compatible implementation across supported MySQL/MariaDB
		// versions: both treat multiple NULLs as distinct values under
		// a UNIQUE index, so uniqueness is only enforced when a value
		// is actually present. This avoids relying on MySQL 8-only
		// features (e.g. functional/expression unique indexes).
		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL,
  code varchar(20) NULL,
  ordering smallint(5) unsigned NOT NULL,
  status varchar(20) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY code (code)
) ENGINE=InnoDB {$charset_collate};";
	}

	protected static function students_sql( $charset_collate ) {
		$table = Table_Names::students();

		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  admission_no varchar(50) NOT NULL,
  first_name varchar(100) NOT NULL,
  middle_name varchar(100) NULL,
  last_name varchar(100) NOT NULL,
  gender varchar(10) NULL,
  date_of_birth date NULL,
  photo_attachment_id bigint(20) unsigned NULL,
  status varchar(20) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY admission_no (admission_no),
  KEY name_index (last_name,first_name)
) ENGINE=InnoDB {$charset_collate};";
	}

	protected static function enrollments_sql( $charset_collate ) {
		$table = Table_Names::enrollments();

		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  student_id bigint(20) unsigned NOT NULL,
  session_id bigint(20) unsigned NOT NULL,
  class_id bigint(20) unsigned NOT NULL,
  status varchar(20) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY student_session (student_id,session_id),
  KEY session_class (session_id,class_id)
) ENGINE=InnoDB {$charset_collate};";
	}

	protected static function results_sql( $charset_collate ) {
		$table = Table_Names::results();

		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  enrollment_id bigint(20) unsigned NOT NULL,
  term_id bigint(20) unsigned NOT NULL,
  class_id_snapshot bigint(20) unsigned NOT NULL,
  class_name_snapshot varchar(100) NOT NULL,
  assessment_snapshot longtext NOT NULL,
  grading_snapshot longtext NOT NULL,
  status varchar(20) NOT NULL,
  published_at datetime NULL,
  created_by bigint(20) unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY enrollment_term (enrollment_id,term_id),
  KEY status_index (status),
  KEY term_status (term_id,status)
) ENGINE=InnoDB {$charset_collate};";
	}

	protected static function result_items_sql( $charset_collate ) {
		$table = Table_Names::result_items();

		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  result_id bigint(20) unsigned NOT NULL,
  subject_id bigint(20) unsigned NOT NULL,
  subject_name_snapshot varchar(100) NOT NULL,
  ca_score decimal(5,2) NOT NULL,
  exam_score decimal(5,2) NOT NULL,
  total decimal(5,2) NOT NULL,
  grade varchar(5) NOT NULL,
  remark varchar(100) NULL,
  ordering smallint(5) unsigned NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY result_subject (result_id,subject_id),
  KEY result_index (result_id)
) ENGINE=InnoDB {$charset_collate};";
	}

	protected static function result_credentials_sql( $charset_collate ) {
		$table = Table_Names::result_credentials();

		return "CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  result_id bigint(20) unsigned NOT NULL,
  pin_hash varchar(255) NOT NULL,
  status varchar(20) NOT NULL,
  generated_at datetime NOT NULL,
  regenerated_at datetime NULL,
  expires_at datetime NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY result_id (result_id)
) ENGINE=InnoDB {$charset_collate};";
	}
}
