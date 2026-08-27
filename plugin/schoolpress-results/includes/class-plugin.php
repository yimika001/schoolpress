<?php
/**
 * Core plugin runner.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results;

use SchoolPress\Results\Admin\Admin_Menu;
use SchoolPress\Results\Modules\Gallery\Gallery_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composition root. Wires together the small number of areas that
 * exist in Milestone 1 (admin UI, i18n, and the Gallery foundation).
 * Future modules (sessions, classes, results, etc.) will register
 * themselves here the same way GalleryModule does, so this class
 * should not need to grow much beyond a list of `new X()->register()`
 * calls as functionality is added.
 */
final class Plugin {

	private static ?Plugin $instance = null;

	/** @var array<int, object> Registered module instances, kept for future introspection. */
	private array $modules = array();

	private function __construct() {}

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function run(): void {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		if ( is_admin() ) {
			( new Admin_Menu() )->register();
		}

		// Gallery is registered regardless of is_admin() because it also
		// exposes a public read path (REST field) for the theme.
		$gallery = new Gallery_Module();
		$gallery->register();
		$this->modules[] = $gallery;
	}

	public function load_textdomain(): void {
		load_plugin_textdomain(
			'schoolpress-results',
			false,
			dirname( SPSR_PLUGIN_BASENAME ) . '/languages'
		);
	}
}
