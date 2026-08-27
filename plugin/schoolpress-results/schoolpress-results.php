<?php
/**
 * Plugin Name:       SchoolPress Results
 * Plugin URI:        https://example.com/schoolpress-suite
 * Description:       Academic session, result and school-data management for WordPress. Works standalone or alongside the SchoolPress Theme.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.2
 * Author:            SchoolPress Suite
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       schoolpress-results
 * Domain Path:       /languages
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Constants are defined here (not in a class) so they are available to
 * activation/deactivation callbacks and uninstall.php, which run before
 * the main class autoloading below.
 */
define( 'SPSR_VERSION', '0.1.0' );
define( 'SPSR_PLUGIN_FILE', __FILE__ );
define( 'SPSR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPSR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SPSR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'SPSR_MIN_PHP', '8.2' );
define( 'SPSR_MIN_WP', '6.4' );

/*
 * A tiny PSR-4-ish autoloader keyed to our namespace only, so we never
 * touch class names outside SchoolPress\Results. This avoids pulling in
 * Composer purely for autoloading in Milestone 1; Composer can be
 * introduced later if/when we add real third-party dependencies.
 */
spl_autoload_register(
	static function ( string $class ): void {
		$prefix = __NAMESPACE__ . '\\';

		if ( ! str_starts_with( $class, $prefix ) ) {
			return;
		}

		$relative = substr( $class, strlen( $prefix ) );
		$parts    = explode( '\\', $relative );
		$file     = 'class-' . strtolower( str_replace( '_', '-', array_pop( $parts ) ) ) . '.php';

		$path_parts = array_map(
			static fn( string $part ): string => strtolower( $part ),
			$parts
		);

		$path = SPSR_PLUGIN_DIR . 'includes/' . implode( '/', $path_parts );
		$path = rtrim( $path, '/' ) . '/' . $file;

		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}
);

register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivator::class, 'deactivate' ) );

/**
 * Boots the plugin once all plugins are loaded, so we can safely check
 * for environment compatibility before touching any WordPress APIs
 * that might not exist on unsupported versions.
 */
function spsr_boot(): void {
	if ( ! Requirements::met() ) {
		Requirements::add_admin_notice();
		return;
	}

	Plugin::instance()->run();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\spsr_boot' );
