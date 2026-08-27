<?php
/**
 * Settings page foundation.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Establishes the Settings API plumbing (option group + option name)
 * without adding any real settings fields yet. Grading/assessment
 * configuration will register sections/fields against this same
 * option group in a later milestone — this class just needs to grow
 * `add_settings_section()` / `add_settings_field()` calls, not a new
 * page or a new save handler.
 */
class Settings_Page {

	public const OPTION_GROUP = 'spsr_settings_group';
	public const OPTION_NAME  = 'spsr_settings';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_settings(): void {
		register_setting(
			self::OPTION_GROUP,
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => array(),
			)
		);
	}

	/**
	 * Placeholder sanitizer. Real fields will each get explicit
	 * validation/sanitization when they are introduced; this keeps the
	 * option safe (an array, no arbitrary keys) even with zero fields.
	 *
	 * @param mixed $value Raw settings input.
	 * @return array<string, mixed>
	 */
	public function sanitize( mixed $value ): array {
		return is_array( $value ) ? $value : array();
	}

	public function render(): void {
		if ( ! current_user_can( Admin_Menu::CAPABILITY ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'SchoolPress Results — Settings', 'schoolpress-results' ); ?></h1>
			<p><?php esc_html_e( 'No configurable settings are available yet. Grading and assessment structure settings will appear here in a later milestone.', 'schoolpress-results' ); ?></p>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( self::OPTION_GROUP );
				?>
			</form>
		</div>
		<?php
	}
}
