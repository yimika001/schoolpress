<?php
/**
 * Admin menu registration.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the top-level "SchoolPress Results" menu and its two
 * Milestone-1 subpages (Dashboard, Settings). Later milestones add
 * subpages here (Sessions, Classes, Students, Results, Gallery, ...)
 * rather than creating separate top-level menus.
 */
class Admin_Menu {

	public const CAPABILITY = 'manage_options';
	public const MENU_SLUG  = 'schoolpress-results';

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
	}

	public function add_menu(): void {
		add_menu_page(
			__( 'SchoolPress Results', 'schoolpress-results' ),
			__( 'SchoolPress Results', 'schoolpress-results' ),
			self::CAPABILITY,
			self::MENU_SLUG,
			array( new Dashboard_Page(), 'render' ),
			'dashicons-welcome-learn-more',
			26
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Dashboard', 'schoolpress-results' ),
			__( 'Dashboard', 'schoolpress-results' ),
			self::CAPABILITY,
			self::MENU_SLUG,
			array( new Dashboard_Page(), 'render' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Settings', 'schoolpress-results' ),
			__( 'Settings', 'schoolpress-results' ),
			self::CAPABILITY,
			self::MENU_SLUG . '-settings',
			array( new Settings_Page(), 'render' )
		);
	}
}
