# Milestone 3 Part 1 — Academic Database Schema

Nine `$wpdb`-backed tables, no CPTs, no `school_id` (single-school install).
No native MySQL `FOREIGN KEY` constraints; relationships are logical only
and enforced by application code in a later milestone. Every logical FK
column has a supporting index.

## Tables & keys

| Table | Primary Key | Unique | Indexes |
|---|---|---|---|
| `spsr_sessions` | id | name | — |
| `spsr_terms` | id | (session_id, name) | (session_id, ordering) |
| `spsr_classes` | id | name; slug | — |
| `spsr_subjects` | id | code (nullable-safe) | — |
| `spsr_students` | id | admission_no | (last_name, first_name) |
| `spsr_enrollments` | id | (student_id, session_id) | (session_id, class_id) |
| `spsr_results` | id | (enrollment_id, term_id) | (status); (term_id, status) |
| `spsr_result_items` | id | (result_id, subject_id) | (result_id) |
| `spsr_result_credentials` | id | result_id | — |

## Relationships (logical only, no FK constraints)

```
Session ─┬─< Terms
         └─< Enrollments
Class   ──< Enrollments
Student ──< Enrollments
Enrollment ──< Results >── Term
Result ──< Result Items >── Subject
Result ──1:1── Result Credential
```

- One Enrollment per `(student_id, session_id)`.
- One Result per `(enrollment_id, term_id)` — Results reference
  Enrollment, never Student directly.
- One Result Item per `(result_id, subject_id)`.
- One Credential per `result_id`.

## Nullable-unique `subjects.code`

MySQL/MariaDB treat multiple `NULL`s in a `UNIQUE KEY` as distinct, so a
plain `UNIQUE KEY code (code)` enforces uniqueness only when a value is
actually present — satisfying "unique when present" without relying on
MySQL 8-only functional/expression indexes, keeping it compatible with
older MariaDB versions still common in WordPress hosting.

## Historical snapshots

`spsr_results` stores `class_id_snapshot`, `class_name_snapshot`,
`assessment_snapshot`, `grading_snapshot`; `spsr_result_items` stores
`subject_name_snapshot`, `total`, `grade`. These are populated by a future
Result service — this milestone only provides the columns.

## JSON / configuration columns

`assessment_config` (sessions), `assessment_snapshot` and
`grading_snapshot` (results) are `LONGTEXT`, not native MySQL `JSON`.
Encoding/decoding is deferred to a controlled repository/service layer in
a later milestone — no JSON handling is implemented here.

## Timestamps

`created_at` / `updated_at` are plain `datetime NOT NULL` columns with no
default and no `ON UPDATE CURRENT_TIMESTAMP`. They are intended to be
application-managed using WordPress GMT conventions (e.g. `current_time(
'mysql', true )`) by the future repository layer — nothing in this
migration sets them automatically.

## Deliberate v1 limitations (approved, not bugs)

- No `spsr_enrollment_transfers` — a transfer overwrites
  `enrollments.class_id` in place; placement history is not preserved.
- No term-level `assessment_config` override — session-level only. The
  schema does not block adding this later (e.g. a future nullable
  override column on `spsr_terms`).
- No audit table.
- No credential generation/UI — only the `spsr_result_credentials` table
  shape exists.
- No hard-delete strategy for published Results is implemented yet; the
  schema does not include a `deleted_at`/archived flag in this part, since
  the archive-first deletion service itself is out of scope for Part 1.

## Migration versioning

- `spsr_db_version` option (pre-existing, was `"0"`) now advances to
  `"1"` after `Migration_0001_Initial_Schema` succeeds.
- `Migrator::migrate()` applies pending migrations in ascending version
  order, skips already-applied ones, and stops without advancing the
  option on the first failure.
- `Migration_0001_Initial_Schema::run()` calls `dbDelta()` for all nine
  tables, then explicitly verifies each table exists via `SHOW TABLES`
  before reporting success — `dbDelta()` itself does not return a
  reliable success/failure signal.
