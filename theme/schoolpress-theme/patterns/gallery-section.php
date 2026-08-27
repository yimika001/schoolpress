<?php
/**
 * Title: Gallery Section
 * Slug: schoolpress-theme/gallery-section
 * Categories: schoolpress
 * Description: Homepage gallery section. Reads published gallery items
 *              from the SchoolPress Results plugin when active; renders
 *              a graceful placeholder otherwise. Contains no gallery
 *              business logic — data comes entirely from the plugin.
 *
 * @package SchoolPress\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$spst_items = spst_get_gallery_items( 8 );
?>
<!-- wp:group {"tagName":"section","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}}} -->
<section class="wp-block-group" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'Gallery', 'schoolpress-theme' ); ?></h2>
	<!-- /wp:heading -->

	<?php if ( empty( $spst_items ) ) : ?>
		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">
			<?php
			echo spst_gallery_plugin_available()
				? esc_html__( 'No gallery items have been published yet.', 'schoolpress-theme' )
				: esc_html__( 'Gallery content will appear here once the SchoolPress Results plugin is active and gallery items are published.', 'schoolpress-theme' );
			?>
		</p>
		<!-- /wp:paragraph -->
	<?php else : ?>
		<div class="spst-gallery-grid">
			<?php foreach ( $spst_items as $spst_item ) : ?>
				<figure class="spst-gallery-item<?php echo $spst_item['featured'] ? ' spst-gallery-item--featured' : ''; ?>">
					<?php if ( $spst_item['image_url'] ) : ?>
						<img src="<?php echo esc_url( $spst_item['image_url'] ); ?>" alt="<?php echo esc_attr( $spst_item['title'] ); ?>" loading="lazy" />
					<?php endif; ?>
					<figcaption>
						<strong><?php echo esc_html( $spst_item['title'] ); ?></strong>
						<?php if ( $spst_item['caption'] ) : ?>
							<span><?php echo esc_html( $spst_item['caption'] ); ?></span>
						<?php endif; ?>
					</figcaption>
				</figure>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
<!-- /wp:group -->
