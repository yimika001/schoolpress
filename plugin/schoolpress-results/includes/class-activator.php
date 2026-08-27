<?php
/**
 * Activation routine.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs on plugin activation.
 *
 * Deliberately does NOT create any custom database tables or seed any
 * data yet — the academic/result schema has not been approved. This
 * only records the installed version so a future migration routine
 * (Milestone 2+) can compare "installed version" against "code version"
 * and know whether a migration is needed.
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

		flush_rewrite_rules();
	}
}
