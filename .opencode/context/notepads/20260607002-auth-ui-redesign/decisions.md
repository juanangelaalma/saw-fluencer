# Decisions

## 2026-06-07 00:00 - Initialization

**Task**: plan setup

- Executing `.opencode/context/plans/20260607002-auth-ui-redesign.md`.
- Scope includes welcome, login, authenticated shell/sidebar, admin/manajer dashboards, and admin user management.
- Sidebar shows active completed features only.
- Login omits role select; backend role remains source of truth.

## 2026-06-07 00:01 - Design CSS Foundation

**Task**: 1.1

- Design tokens from `DESIGN.md` added to `resources/css/app.css`.
- Component CSS now covers shell, sidebar, topbar, content, cards, buttons, inputs, labels, errors, pills, tables, and grids.

## 2026-06-07 00:02 - Shared Component Styling

**Task**: 1.2

- Shared components now use design aliases: `btn`, `btn-primary`, `btn-secondary`, `btn-danger`, `input`, `field-label`, `field-error`, and nav/dropdown classes.
- Component APIs, props, slots, and `$attributes->merge()` behavior preserved.

## 2026-06-07 00:03 - Wave 1 Passed Review

**Task**: Wave 1

- Reviewer returned PASS for Wave 1.
- Proceed to Wave 2 Task 2.1.

## 2026-06-07 00:04 - Authenticated Shell Redesign

**Task**: 2.1

- Breeze top navigation replaced with dashboard shell.
- Admin nav shows Dashboard and Manajemen Pengguna.
- Manajer nav shows Dashboard only.
- Logout remains POST with CSRF.
- No profile link or future module links reintroduced.

## 2026-06-08 00:00 - Guest Auth UI Partial

**Task**: 2.2

- Welcome redesigned with Bahasa copy, brand, CTA, cards, no Register/forgot links.
- Guest layout now design-token card shell, not Breeze grey shadow card.
- Login keeps POST `route('login')`, CSRF, `username`, `password`, optional `remember`.
- No role select rendered.
- Lockout warning and generic error guidance now Bahasa.
- Validation/session status remain accessible with `role`/`aria-live`.
