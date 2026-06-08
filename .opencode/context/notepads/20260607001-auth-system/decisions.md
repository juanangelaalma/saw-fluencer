# Decisions

## 2026-06-07 00:00 - Initialization

**Task**: plan setup

- Executing `.opencode/context/plans/20260607001-auth-system.md`.
- Plan assumes pre-production DB refresh/migration rewrite acceptable.
- Email auth/profile/reset/verification removed for username-only auth.

## 2026-06-07 00:01 - Task 1.1 Implementation Choices Reported

**Task**: 1.1

- User role values implemented as `admin` and `manajer` with labels `Admin` and `Manajer`.
- Seeder reads `ADMIN_NAME`, `ADMIN_USERNAME`, `ADMIN_PASSWORD`, lowercases username, enforces password min 8, seeds active Admin.
- Factory states added for `admin`, `manajer`, and `inactive`.

## 2026-06-07 00:02 - Proceed Despite Environment Blocker

**Task**: 1.1

- Reviewer returned PASS_WITH_ENV_BLOCKER.
- Continue to Task 1.2 while tracking unresolved DB/Sail verification requirement.

## 2026-06-07 00:46 - Task 1.2 Implementation Choices Reported

**Task**: 1.2

- Auth attempt uses lowercase trimmed username, password, and `is_active = true` credential constraint.
- Rate limiter key uses lowercase username + IP, max 5, decay 600 seconds.
- Admin redirects to `admin.dashboard`; Manajer redirects to `manager.dashboard`.
- Middleware aliases `active` and `admin` registered.

## 2026-06-07 00:48 - Proceed to Wave 2 Despite Stale Tests

**Task**: 1.2

- Reviewer returned PASS_WITH_STALE_TESTS.
- Full test failures are deferred to Task 3.1 because they are stale Breeze expectations.
- Login page still showing email is expected until Task 2.1.

## 2026-06-07 00:49 - Wave 1 Passed Review

**Task**: Wave 1

- Reviewer returned PASS for Wave 1.
- Proceed to Wave 2 Task 2.1.

## 2026-06-07 00:50 - Task 2.1 Implementation Choices Reported

**Task**: 2.1

- Login view now posts `username`, label `Username`, no forgot-password link.
- Welcome register link removed.
- Navigation shows `username · roleLabel()`.
- Profile links/routes removed and profile Blade views deleted.
- Admin user management nav link is conditional on admin role and route existence.

## 2026-06-07 00:51 - Proceed to Task 2.2 Despite Stale Tests

**Task**: 2.1

- Reviewer returned PASS_WITH_STALE_TESTS.
- Targeted Auth UI tests pass; full stale test cleanup deferred to Task 3.1.

## 2026-06-07 00:52 - Task 2.2 Implementation Choices Reported

**Task**: 2.2

- Admin user management routes added under `auth`, `active`, `admin` middleware.
- Manajer direct access returns 403 via `admin` middleware.
- Create validates `name`, `username`, `password`, `role`; creates active user.
- Edit supports optional password and preserves current hash when blank.
- Self role change and self deactivate blocked server-side.
- Deactivate sets `is_active = false`; no hard delete.

## 2026-06-07 00:54 - Wave 2 Passed Review

**Task**: Wave 2

- Reviewer returned PASS for Wave 2.
- Proceed to Wave 3 Task 3.1.

## 2026-06-07 00:55 - Task 3.1 Test Alignment Reported

**Task**: 3.1

- Auth tests now use username instead of email.
- Public registration, reset, verification, profile, password confirmation/update expectations rewritten as inaccessible route tests.
- Seeder env test setup fixed for full suite.

## 2026-06-07 00:56 - Wave 3 Passed Review

**Task**: Wave 3

- Reviewer returned PASS for Wave 3.
- Execution complete.
