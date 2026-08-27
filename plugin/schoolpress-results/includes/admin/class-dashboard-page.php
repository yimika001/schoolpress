<?php
/**
 * Dashboard admin page.
 *
 * @package SchoolPress\Results
 */

namespace SchoolPress\Results\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the plugin's landing page: confirmation the foundation is
 * installed, plus a small health/status block that later milestones
 * can extend (e.g. DB migration status, active academic session).
 */
class Dashboard_Page {

	public function render(): void {
		if ( ! current_user_can( Admin_Menu::CAPABILITY ) ) {
			return;
		}

		global $wp_version;
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'SchoolPress Results', 'schoolpress-results' ); ?></h1>
			<p>
				<?php esc_html_e( 'The plugin foundation is installed successfully. Result-management features are not yet available in this milestone.', 'schoolpress-results' ); ?>
			</p>

			<h2><?php esc_html_e( 'Environment status', 'schoolpress-results' ); ?></h2>
			<table class="widefat striped" style="max-width: 480px;">
				<tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Plugin version', 'schoolpress-results' ); ?></th>
						<td><?php echo esc_html( SPSR_VERSION ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'WordPress version', 'schoolpress-results' ); ?></th>
						<td><?php echo esc_html( $wp_version ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'PHP version', 'schoolpress-results' ); ?></th>
						<td><?php echo esc_html( PHP_VERSION ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Data schema version', 'schoolpress-results' ); ?></th>
						<td><?php echo esc_html( get_option( 'spsr_db_version', '0' ) ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Environment status', 'schoolpress-results' ); ?></th>
						<td>
							<span style="color:#1a7e2e;">&#9679;</span>
							<?php esc_html_e( 'Compatible', 'schoolpress-results' ); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
}
