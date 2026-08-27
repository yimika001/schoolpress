# Milestone 1 — Development Foundation

## Scope

This milestone establishes the skeleton for both SchoolPress Suite
packages. It intentionally contains **no** academic/result business
logic, **no** custom database tables, and **no** sample data.

## Plugin (`schoolpress-results`)

- Namespace: `SchoolPress\Results`. Procedural/global identifiers use
  the `spsr_` prefix.
- A small namespace-scoped autoloader is used instead of Composer,
  since there are no third-party dependencies yet. Composer can be
  introduced later if/when we add real ones — not before.
- `Requirements` gates everything else behind a PHP/WP version check,
  so an incompatible host gets an admin notice instead of a fatal
  error.
- `Activator` only records a `spsr_db_version` option (currently
  `"0"`) so a future migration routine has something to compare
  against. It does not create tables or seed data.
- `Deactivator` only flushes rewrite rules. `uninstall.php` is a
  documented no-op until the schema is approved.
- `Plugin` is the single composition root; future modules register
  themselves there the same way the Gallery module does.
- `Support\Logger` is a debug-log wrapper with three levels and no
  storage backend, dependencies, or sensitive-data logging.
- Admin UI: one top-level menu ("SchoolPress Results") with a
  Dashboard page (environment/health status) and a Settings page that
  registers the Settings API option group with zero fields yet.

## Theme (`schoolpress-theme`)

- Modern block theme: `theme.json` (v3) defines color, typography,
  and spacing tokens; no page-builder, no React app.
- Templates: `index`, `front-page`, `single`, `page`, `404`.
- Template parts: `header`, `footer`.
- Patterns (category `schoolpress`): `hero`, `gallery-section`. More
  (About, Principal's Message, Programs, Statistics, News, Admissions
  CTA) are expected later and were intentionally not built now.
- `inc/plugin-integration.php` is the *only* place the theme touches
  plugin code, and every function there degrades to an empty/safe
  value if the plugin is inactive.

## Decisions flagged for review

1. **Gallery data model uses a WordPress custom post type**
   (`spsr_gallery_item`) rather than a custom `$wpdb` table. See
   `gallery-module-foundation.md` for the reasoning. This is scoped
   only to Gallery — it does not set precedent for the
   students/classes/results schema, which still requires a separate
   approved ERD.
2. The Gallery custom post type currently uses WordPress's standard
   `post` capability type (e.g. `edit_posts`) rather than bespoke
   capabilities, so any role that can normally publish posts can also
   manage gallery items. This may need tightening to
   administrator-only once real schools are involved — flagging for a
   decision rather than assuming.

## Explicitly not done in this milestone

- No students/classes/subjects/sessions/terms/results tables or CRUD.
- No grading/assessment configuration.
- No public result checking, PDF, QR, or CSV import.
- No full Gallery admin UX (custom meta boxes, drag-reorder UI,
  bespoke REST controller) — see `gallery-module-foundation.md`.
- No React admin application.
- No final marketing/school homepage content.
