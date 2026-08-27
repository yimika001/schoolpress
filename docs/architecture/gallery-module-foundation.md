# Gallery Module — Foundation Notes

## What exists today

- Custom post type `spsr_gallery_item` (owned by the plugin), registered
  with `title`, `editor` (used as caption/description), `thumbnail`
  (Media Library image), and `page-attributes` (gives a native "Order"
  field via `menu_order`) support.
- Taxonomy `spsr_gallery_category`, hierarchical, attached to the CPT.
- Post meta `_spsr_gallery_featured` (boolean, REST-exposed, gated by
  `manage_options`).
- Both are registered `show_in_rest`, so the core REST API already
  exposes `/wp-json/wp/v2/gallery-items` — no custom REST controller
  was written for this milestone.
- `Gallery_Module::get_published_items()` — a plugin-side read helper
  returning a small curated array (`title`, `caption`, `image_url`,
  `featured`), used by the theme so it never has to know about post
  types, meta keys, or raw post IDs.
- Theme side: `inc/plugin-integration.php` exposes
  `spst_gallery_plugin_available()` and `spst_get_gallery_items()`,
  both safe to call whether or not the plugin is active. The
  `gallery-section` pattern uses only these two functions.

## Why a CPT instead of a custom table

- Publish/unpublish, edit, delete/archive, and revisions are native
  WordPress post concepts — reusing them avoids reimplementing
  storage and capability checks the brief already asks for.
- Images must come from the Media Library (per requirements); a CPT
  with a featured image does that with zero extra code.
- It is fully decoupled from the still-unapproved academic/result
  schema and its future `$wpdb` migrations — no shared tables, no
  shared migration path.

This is a **contained, reviewable** decision. It does not imply the
students/classes/subjects/results domain will also use CPTs — that
schema is still pending its own ERD approval in Milestone 2.

## Explicitly deferred to the full Gallery milestone

- Custom meta-box UI for the "featured" toggle and for
  category/order (today these are only reachable via the REST API or
  raw post meta, not a polished admin screen).
- A bespoke REST controller/response shape, if the default core
  response turns out to be insufficient.
- Category-filtered / paginated retrieval in
  `get_published_items()` (currently a flat, unfiltered list capped
  at a caller-supplied limit).
- Any public-facing gallery UI beyond the single homepage grid in the
  `gallery-section` pattern (e.g. a dedicated gallery archive page,
  lightbox, filtering by category on the front end).
- Capability tightening (see `milestone-1.md`, decision #2).
