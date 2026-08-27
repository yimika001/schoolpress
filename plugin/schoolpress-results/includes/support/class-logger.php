<?php
/**
 * Minimal logging foundation.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results\Support;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thin wrapper around WordPress's own debug log so call sites don't
 * depend on error_log() directly. Intentionally has no dependencies,
 * no storage backend, and no severity/channel system yet — those can
 * be layered on later without changing call sites.
 *
 * Never pass user input, credentials, PII, or full request data to
 * this logger. Messages only; keep any dynamic detail generic.
 */
class Logger {

	private const PREFIX = '[SchoolPress Results] ';

	public static function info( string $message ): void {
		self::write( 'INFO', $message );
	}

	public static function warning( string $message ): void {
		self::write( 'WARNING', $message );
	}

	public static function error( string $message ): void {
		self::write( 'ERROR', $message );
	}

	private static function write( string $level, string $message ): void {
		if ( ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
			return;
		}

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- intentional debug-only log sink.
		error_log( self::PREFIX . '[' . $level . '] ' . $message );
	}
}
