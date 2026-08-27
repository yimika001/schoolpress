<?php
/**
 * Title: Hero
 * Slug: schoolpress-theme/hero
 * Categories: schoolpress
 * Description: Homepage hero/introduction section.
 *
 * @package SchoolPress\Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- wp:group {"tagName":"section","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"backgroundColor":"surface"} -->
<section class="wp-block-group has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"xx-large"} -->
	<h1 class="wp-block-heading has-text-align-center has-xx-large-font-size">Welcome to Our School</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","fontSize":"large"} -->
	<p class="has-text-align-center has-large-font-size">Replace this hero text with your school's introduction from the Site Editor.</p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"primary"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-primary-background-color has-background wp-element-button">Learn More</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</section>
<!-- /wp:group -->
