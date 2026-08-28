<?php
/**
 * Activation routine.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results;

use SchoolPress\Results\Database\Migrator;
use SchoolPress\Results\Support\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs on plugin activation.
 */
class Activator {

	public static function activate(): void {
		if ( ! Requirements::met() ) {
			deactivate_plugins( SPSR_PLUGIN_BASENAME );

			wp_die(
				esc_html__( 'SchoolPress Results could not be activated because this server does not meet the minimum PHP/WordPress version requirements.', 'schoolpress-results' ),
				esc_html__( 'Plugin Activation Error', 'schoolpress-results' ),
				array( 'back_link' => true )
			);
		}

		// Tracks the schema/data version separately from SPSR_VERSION so
		// future migrations can run only when the DB actually needs it.
		if ( false === get_option( 'spsr_db_version' ) ) {
			add_option( 'spsr_db_version', '0' );
		}

		$migration_result = Migrator::migrate();

		if ( is_wp_error( $migration_result ) ) {
			Logger::error(
				'SchoolPress Results database migration failed during activation.',
				array(
					'error' => $migration_result->get_error_message(),
				)
			);

			deactivate_plugins( SPSR_PLUGIN_BASENAME );

			wp_die(
				esc_html__( 'SchoolPress Results could not be activated because the database migration failed.', 'schoolpress-results' ),
				esc_html__( 'Plugin Activation Error', 'schoolpress-results' ),
				array( 'back_link' => true )
			);
		}

		flush_rewrite_rules();
	}
}