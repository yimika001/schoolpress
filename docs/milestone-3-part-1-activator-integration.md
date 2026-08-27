# Milestone 3 Part 1 — Activator Integration Point

The real `SchoolPress\Results\Activator` class was **not available** in this
session (per the handoff, only its verified behavior was: it creates/reads
the `spsr_db_version` option, currently `"0"`). Rather than fabricate or
rewrite that class, this migration layer is fully self-contained under
`includes/Database/` and exposes a single static entry point:

```php
SchoolPress\Results\Database\Migrator::migrate();
```

## Required change to the real Activator (smallest possible edit)

Inside the existing `Activator::activate()` method (or wherever it
currently sets/checks `spsr_db_version`), add one call:

```php
use SchoolPress\Results\Database\Migrator;

// ... existing spsr_db_version bootstrap logic stays as-is ...

$result = Migrator::migrate();

if ( is_wp_error( $result ) ) {
    // Surface the failure using whatever the existing Activator/Requirements
    // pattern already uses for activation errors (e.g. Requirements class,
    // deactivate_plugins() + wp_die(), or Logger). Do not proceed as if
    // activation succeeded.
}
```

Nothing else in `Activator` needs to change. `Migrator::migrate()`:

- Reads the **existing** `spsr_db_version` option (defaults to `"0"` if
  somehow absent, matching the current baseline).
- Is safe to call on every activation (idempotent) — already-applied
  migrations are skipped via `version_compare()`.
- Only advances `spsr_db_version` after a migration's `run()` has been
  confirmed successful (see `Migration_0001_Initial_Schema::run()`, which
  verifies table existence via `SHOW TABLES` rather than trusting `dbDelta()`
  silently).
- Never claims rollback. If a migration fails, `spsr_db_version` simply
  stops at the last successfully applied version, and the failure is
  reported via `WP_Error` for the Activator to handle using its existing
  error-reporting convention.

## Why this wasn't done as a direct edit to Activator.php

The actual file contents were not present in this conversation (no
repository was provided). Editing it directly would mean guessing at
unrelated logic (`Requirements` checks, hook registration order, etc.) and
risking a fabricated rewrite of approved, working Milestone 1 code. The
integration is therefore expressed as this documented one-call addition,
which the maintainer can apply directly to the real file.
