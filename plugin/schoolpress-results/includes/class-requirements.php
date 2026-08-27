<?php
/**
 * Environment requirement checks.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verifies PHP/WordPress version compatibility before the rest of the
 * plugin is loaded, so an incompatible environment degrades to an
 * admin notice instead of a fatal error.
 */
class Requirements {

	/**
	 * Whether the current environment satisfies our minimum requirements.
	 */
	public static function met(): bool {
		return self::php_ok() && self::wp_ok();
	}

	public static function php_ok(): bool {
		return version_compare( PHP_VERSION, SPSR_MIN_PHP, '>=' );
	}

	public static function wp_ok(): bool {
		global $wp_version;
		return version_compare( $wp_version, SPSR_MIN_WP, '>=' );
	}

	/**
	 * Registers an admin notice explaining why the plugin did not load.
	 * Deliberately does not call load_plugin_textdomain() or any other
	 * plugin bootstrap logic, since we may be running on an environment
	 * too old to trust.
	 */
	public static function add_admin_notice(): void {
		add_action(
			'admin_notices',
			static function (): void {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				$message = sprintf(
					/* translators: 1: required PHP version, 2: required WP version, 3: current PHP version, 4: current WP version */
					esc_html__( 'SchoolPress Results requires PHP %1$s+ and WordPress %2$s+. This site is running PHP %3$s and WordPress %4$s, so the plugin has been left inactive.', 'schoolpress-results' ),
					esc_html( SPSR_MIN_PHP ),
					esc_html( SPSR_MIN_WP ),
					esc_html( PHP_VERSION ),
					esc_html( $GLOBALS['wp_version'] ?? '' )
				);

				printf(
					'<div class="notice notice-error"><p>%s</p></div>',
					$message // phpcs:ignore -- already escaped above.
				);
			}
		);
	}
}
