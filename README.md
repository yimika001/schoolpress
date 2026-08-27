# SchoolPress Suite

SchoolPress Suite is a commercial WordPress product made of two
independently usable packages for schools:

1. **SchoolPress Theme** — a modern, responsive WordPress block theme
   for a school's public website. Contains no result/academic
   business logic.
2. **SchoolPress Results** — a plugin providing academic session,
   class, subject, student, and result-management functionality. Works
   standalone with any theme, and integrates optionally (never
   required) with the SchoolPress Theme.

## Repository structure

```
schoolpress-suite/
├── plugin/
│   └── schoolpress-results/   # WordPress plugin
├── theme/
│   └── schoolpress-theme/     # WordPress block theme
└── docs/
    └── architecture/          # Design/architecture notes
```

## Plugin vs. theme responsibilities

| Concern                          | Owner  |
|-----------------------------------|--------|
| Academic data (sessions, classes, results, gallery, etc.) | Plugin |
| Public website presentation/design | Theme |
| Admin screens for managing that data | Plugin |
| Editable general site content (pages, posts) | Theme + WordPress core |

The plugin never assumes the theme is active. The theme never
contains result/gallery business logic — it only reads data the
plugin chooses to expose, and only through the small helper functions
in `theme/schoolpress-theme/inc/plugin-integration.php`.

## Development prerequisites

- PHP 8.2+
- WordPress 6.4+
- A local WordPress environment (e.g. `wp-env`, Local, or similar)
- Node.js/npm (for future admin tooling — not required for this
  milestone)
- Composer (not currently required — no third-party PHP dependencies
  yet)

## Installing the plugin locally

1. Copy or symlink `plugin/schoolpress-results/` into your WordPress
   installation's `wp-content/plugins/` directory.
2. Activate **SchoolPress Results** from the WordPress admin Plugins
   screen.
3. You should see a **SchoolPress Results** menu item with a
   Dashboard and a Settings page.

## Installing the theme locally

1. Copy or symlink `theme/schoolpress-theme/` into your WordPress
   installation's `wp-content/themes/` directory.
2. Activate **SchoolPress Theme** from the WordPress admin Themes
   screen.
3. Open the Site Editor to confirm it is recognized as a block theme.

## Building frontend assets

Not applicable yet. This milestone has no build step — CSS/JS in
`assets/` are plain files loaded directly. A build step (and the
`package.json` to drive it) will be introduced only when a real admin
UI (React/TypeScript) is added in a later milestone.

## Checks / linting

No linting/type-checking tooling is configured yet (no
`composer.json`/`phpcs.xml`/`package.json` exist in this milestone).
Validation performed for Milestone 1 was:

- `php -l` (lint) against every PHP file in the plugin and theme.
- Manual review against WordPress plugin/theme header requirements.

These will be replaced with proper `composer.json` (PHP_CodeSniffer /
WPCS) and `package.json` tooling in a later milestone rather than
being added ad hoc now.

## Current milestone status

**Milestone 1 — Development Foundation: complete.**

Implemented:
- Plugin bootstrap, namespacing, autoloading, activation/deactivation,
  environment checks, admin menu (Dashboard + Settings foundation),
  logging foundation, i18n readiness, documented no-op `uninstall.php`.
- Theme bootstrap: `theme.json` design tokens, core templates and
  template parts, two initial patterns (Hero, Gallery Section), safe
  plugin-detection integration layer.
- Gallery module **foundation only**: custom post type, taxonomy, one
  meta field, and a read helper. See
  `docs/architecture/gallery-module-foundation.md`.

## Features intentionally NOT implemented yet

- Academic sessions, terms, classes, subjects, students, enrollment.
- Result entry, grading configuration, result calculation, result
  publishing, public result checking, report cards/PDF, QR
  verification, CSV import.
- Full Gallery admin UX (meta-box UI, drag-reorder, bespoke REST
  controller) and any gallery page beyond the homepage section.
- Teacher roles, licensing, payments, or any SaaS functionality.
- Any custom `$wpdb` database tables — none exist yet; the schema is
  pending a separate approved ERD (Milestone 2).
- React/TypeScript admin application.
- Final marketing copy/design for the school homepage.
