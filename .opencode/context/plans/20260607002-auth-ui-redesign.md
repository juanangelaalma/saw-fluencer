# Plan: Auth UI/UX Redesign to DESIGN.md

**ID**: 20260607002
**Status**: ready
**Created**: 2026-06-07
**Author**: @architect

---

## TL;DR

Redesign completed auth and user-management screens to match `DESIGN.md` and `index.html` visual system. Scope covers welcome, login, authenticated shell with dashboard-style sidebar, role dashboards, and Admin user management without changing auth behavior.

---

## Context

### Background

Auth feature is complete and verified, but UI still resembles Laravel Breeze defaults. User wants current completed features to look like the canonical high-fidelity prototype for academic/business demo quality.

### Current State

- Laravel 13 Breeze Blade + Tailwind/Vite.
- `resources/css/app.css` only has Tailwind directives.
- App layout uses Breeze top navigation.
- Guest/login layout uses centered Breeze card.
- Dashboards are simple placeholders.
- Admin user-management views use default Tailwind/Breeze styling.

### Target State

- Visual system follows `DESIGN.md`/`index.html`: near-greyscale, cobalt accent, hairline borders, no heavy shadows.
- Authenticated app uses `272px` sticky sidebar, sticky blurred topbar, responsive mobile pill nav.
- Sidebar shows only active completed features:
  - Admin: Dashboard, Manajemen Pengguna.
  - Manajer: Dashboard.
- Welcome/login/dashboard/user-management pages use design tokens, cards, buttons, inputs, pills, and tables from prototype.
- Login omits role select; backend role remains source of truth.

---

## Objectives

### Primary Goals

1. Port design tokens and core component classes to the Laravel app.
2. Redesign guest/welcome/login UI to match prototype visual system.
3. Redesign authenticated shell with sidebar/topbar/mobile tabs.
4. Redesign Admin/Manajer dashboard placeholders and Admin user-management views.
5. Preserve all existing auth/user-management behavior and test coverage.

### Success Criteria

- [ ] `:root` design tokens from `DESIGN.md` are present in app CSS.
- [ ] Login screen visually matches prototype style and still posts `username`/`password`.
- [ ] No login role select is submitted or required.
- [ ] Welcome page uses same design system and does not show public register/reset links.
- [ ] Authenticated layout uses sidebar + topbar on desktop.
- [ ] Mobile under `1080px` hides sidebar and shows role-aware pill nav.
- [ ] Admin sidebar shows Dashboard and Manajemen Pengguna.
- [ ] Manajer sidebar does not show Manajemen Pengguna.
- [ ] User-management table/cards/forms match prototype components.
- [ ] All forms preserve routes, methods, field names, CSRF, method spoofing, and validation errors.
- [ ] `./vendor/bin/sail artisan test` passes.
- [ ] `./vendor/bin/sail pint --test` passes.
- [ ] `./vendor/bin/sail npm run build` passes.

### Non-Goals

- Building Data Influencer, Kriteria, SAW calculation, or Ranking pages/routes.
- Adding future disabled/clickable prototype nav items.
- Changing auth behavior, roles, rate limiting, or user-management authorization.
- Adding new frontend dependencies.
- Reintroducing public register/password reset/email verification.

---

## Execution Waves

### Wave 1: Design System Foundation (Sequential)

| Task ID | Description | Agent | Dependencies | Est. Time |
|---|---|---|---|---|
| 1.1 | Port design tokens and component CSS | @laravel | none | 45m |
| 1.2 | Redesign shared Blade components | @laravel | 1.1 | 45m |

### Wave 2: Layout Shell and Guest UI (Sequential after Wave 1)

| Task ID | Description | Agent | Dependencies | Est. Time |
|---|---|---|---|---|
| 2.1 | Redesign authenticated app shell/sidebar/topbar | @laravel | 1.2 | 75m |
| 2.2 | Redesign welcome and login screens | @laravel | 2.1 | 60m |

### Wave 3: Auth Feature Pages (Sequential after Wave 2)

| Task ID | Description | Agent | Dependencies | Est. Time |
|---|---|---|---|---|
| 3.1 | Redesign Admin/Manajer dashboard placeholders | @laravel | 2.2 | 45m |
| 3.2 | Redesign Admin user-management views | @laravel | 3.1 | 90m |

### Wave 4: Tests and Final Verification (Sequential after Wave 3)

| Task ID | Description | Agent | Dependencies | Est. Time |
|---|---|---|---|---|
| 4.1 | Update UI tests and run full verification | @laravel | 3.2 | 75m |

### Dependency Matrix

```
Wave 1: [1.1] -> [1.2]
                    |
Wave 2:          [2.1] -> [2.2]
                              |
Wave 3:                    [3.1] -> [3.2]
                                      |
Wave 4:                              [4.1]
```

### File Conflict Rule Check

All code-modifying tasks are sequential. No same-wave parallel file conflicts.

---

## Task Details

### TODO 1.1: Port design tokens and component CSS

**Agent**: @laravel
**Dispatch**: single
**Estimated Time**: 45m
**Dependencies**: none

#### Objective

Add app-level CSS design system matching `DESIGN.md`/`index.html`.

#### Acceptance Criteria

- [ ] `:root` tokens from `DESIGN.md` are added verbatim where possible.
- [ ] Base body typography uses system font stack and `15px / 1.5`.
- [ ] Component classes exist for shell/sidebar/topbar/content/cards/buttons/inputs/selects/labels/errors/pills/tables/grids.
- [ ] CSS supports responsive sidebar hide at `1080px` and stacked layout at `720px`.
- [ ] Tables use horizontal overflow wrappers.
- [ ] No heavy shadows, gradients, or decorative visuals added.

#### Implementation Notes

- Modify `resources/css/app.css` using Tailwind `@layer base/components` if useful.
- Preserve Tailwind directives.
- Use design tokens from `DESIGN.md` and prototype `index.html` as source of truth.
- Add fallback colors only if needed; do not dilute prototype look.

#### QA Scenarios

| Scenario | Input | Expected Output | Verification |
|---|---|---|---|
| Build CSS | `./vendor/bin/sail npm run build` | build succeeds | automated |
| Component class | rendered page with `.card` | hairline card, no shadow | manual/browser |
| Responsive shell | viewport <1080px | sidebar hidden, mobile tabs visible | manual/browser |

#### Files to Modify

- `resources/css/app.css` - tokens, base styles, component classes.

---

### TODO 1.2: Redesign shared Blade components

**Agent**: @laravel
**Dispatch**: single
**Estimated Time**: 45m
**Dependencies**: 1.1

#### Objective

Update shared Breeze components to use the new design system while preserving their APIs.

#### Acceptance Criteria

- [ ] Button components use prototype primary/secondary/danger styling.
- [ ] Text input, label, and error components match prototype input/label/error treatment.
- [ ] Nav/responsive/dropdown link components remain usable or are safely deprecated by new shell.
- [ ] Component props and slots remain compatible with existing views.
- [ ] No form field names, methods, or validation bindings changed.

#### Implementation Notes

- Prefer class aliases like `btn btn-primary`, `input`, `field-label`, `field-error`.
- Keep `{{ $slot }}` and `$attributes->merge()` behavior intact.
- Avoid broad changes to components not used by current scope unless needed.

#### QA Scenarios

| Scenario | Input | Expected Output | Verification |
|---|---|---|---|
| Input render | login form | styled input with label/error support | feature/manual |
| Button render | save user form | styled primary button | manual |
| Validation error | invalid user form | clear field error near field | feature/manual |

#### Files to Modify

- `resources/views/components/primary-button.blade.php` - primary button class.
- `resources/views/components/secondary-button.blade.php` - secondary button class.
- `resources/views/components/danger-button.blade.php` - danger button class.
- `resources/views/components/text-input.blade.php` - input class.
- `resources/views/components/input-label.blade.php` - label class.
- `resources/views/components/input-error.blade.php` - error class.
- `resources/views/components/nav-link.blade.php` - if still used.
- `resources/views/components/responsive-nav-link.blade.php` - if still used.
- `resources/views/components/dropdown-link.blade.php` - if account dropdown retained.

---

### TODO 2.1: Redesign authenticated app shell/sidebar/topbar

**Agent**: @laravel
**Dispatch**: single
**Estimated Time**: 75m
**Dependencies**: 1.2

#### Objective

Replace Breeze top-nav shell with role-aware dashboard shell matching prototype layout.

#### Acceptance Criteria

- [ ] Authenticated layout uses `aside` sidebar, `header` topbar, `main` content landmarks.
- [ ] Desktop sidebar width is `272px` and sticky.
- [ ] Topbar is sticky with translucent background/blur.
- [ ] Mobile under `1080px` hides sidebar and shows horizontal pill nav.
- [ ] Admin sidebar/mobile nav shows Dashboard and Manajemen Pengguna.
- [ ] Manajer sidebar/mobile nav shows Dashboard only.
- [ ] Active route state appears for dashboard and admin user routes.
- [ ] Account area shows username and role label.
- [ ] Logout remains a POST form with CSRF.
- [ ] Profile link is not reintroduced.
- [ ] Nonfunctional future links/search/Hitung SAW CTA are not added.

#### Implementation Notes

- Modify `app.blade.php` and `navigation.blade.php` only; preserve route names.
- Use existing `Auth::user()->isAdmin()` and `roleLabel()` helpers.
- If `$header` slot becomes obsolete, preserve it safely or style it in shell.

#### QA Scenarios

| Scenario | Input | Expected Output | Verification |
|---|---|---|---|
| Admin nav | Admin GET dashboard | Dashboard + Manajemen Pengguna visible | feature/manual |
| Manajer nav | Manajer GET dashboard | Manajemen Pengguna absent | feature/manual |
| Logout | click/submit logout | POST logout route used | feature/manual |
| Mobile nav | <1080px viewport | pill nav visible, sidebar hidden | manual |

#### Files to Modify

- `resources/views/layouts/app.blade.php` - authenticated shell structure.
- `resources/views/layouts/navigation.blade.php` - sidebar/topbar/mobile nav partial.

---

### TODO 2.2: Redesign welcome and login screens

**Agent**: @laravel
**Dispatch**: single
**Estimated Time**: 60m
**Dependencies**: 2.1

#### Objective

Apply prototype visual system to public welcome and login screen while preserving auth behavior.

#### Acceptance Criteria

- [ ] Welcome page uses prototype-style brand, hero/card layout, Bahasa copy, and login CTA.
- [ ] Welcome page does not show Register or forgot-password links.
- [ ] Guest layout uses design tokens/cards, not Breeze grey shadow card.
- [ ] Login page has Username and Password fields with prototype styling.
- [ ] Login form posts to `route('login')` with `username`, `password`, optional `remember`, and CSRF.
- [ ] Forgot password link remains absent.
- [ ] Role select is not rendered as a submitted login field.
- [ ] Lockout warning and generic error guidance appear in Bahasa.
- [ ] Validation/session status still render accessibly.

#### Implementation Notes

- Keep backend login behavior unchanged.
- Lightweight welcome only; do not build future modules or routes.
- If other inactive Breeze auth views still exist, shared guest layout should keep them coherent without reintroducing routes.

#### QA Scenarios

| Scenario | Input | Expected Output | Verification |
|---|---|---|---|
| Welcome | GET `/` | design-system landing, login CTA, no Register | feature/manual |
| Login page | GET `/login` | Username/Password, no role select, no forgot link | feature/manual |
| Login submit | valid credentials | same redirect behavior as before | feature test |

#### Files to Modify

- `resources/views/layouts/guest.blade.php` - guest shell design.
- `resources/views/auth/login.blade.php` - login screen design.
- `resources/views/welcome.blade.php` - lightweight public redesign.
- `resources/views/components/auth-session-status.blade.php` - session status styling if needed.

---

### TODO 3.1: Redesign Admin/Manajer dashboard placeholders

**Agent**: @laravel
**Dispatch**: single
**Estimated Time**: 45m
**Dependencies**: 2.2

#### Objective

Make role dashboards look finished and credible using prototype cards while avoiding fake functionality.

#### Acceptance Criteria

- [ ] Admin dashboard uses design cards/metrics/empty states.
- [ ] Manajer dashboard uses design cards/metrics/read-only guidance.
- [ ] Copy is Bahasa Indonesia, formal, and suitable for demo.
- [ ] No fake unavailable SAW metrics are presented as real.
- [ ] Existing dashboard routes and redirects remain unchanged.
- [ ] Tests updated later can assert new dashboard text.

#### Implementation Notes

- Use honest placeholders such as `Belum tersedia` for future modules.
- PRD-known values like `6 kriteria` and `<5 detik` may be displayed as target/spec, not live metric.
- Keep admin/manajer role distinction clear.

#### QA Scenarios

| Scenario | Input | Expected Output | Verification |
|---|---|---|---|
| Admin dashboard | Admin GET route | design-system cards visible | feature/manual |
| Manajer dashboard | Manajer GET route | read-only dashboard copy visible | feature/manual |
| Generic redirect | GET `/dashboard` | role redirect unchanged | feature test |

#### Files to Modify

- `resources/views/admin/dashboard.blade.php` - Admin dashboard redesign.
- `resources/views/manager/dashboard.blade.php` - Manajer dashboard redesign.
- `resources/views/dashboard.blade.php` - leave untouched unless still routed/reachable; if modified, only style/redirect-safe content.

---

### TODO 3.2: Redesign Admin user-management views

**Agent**: @laravel
**Dispatch**: single
**Estimated Time**: 90m
**Dependencies**: 3.1

#### Objective

Apply prototype table/form/card components to Admin user management while preserving all behavior.

#### Acceptance Criteria

- [ ] User index uses card + table-wrap + prototype table styling.
- [ ] Active/inactive status uses semantic pills.
- [ ] Create/edit forms use prototype fields, inputs, selects, buttons, errors.
- [ ] Deactivate section uses clear danger/warning treatment and says `Nonaktifkan`.
- [ ] Admin create/edit/deactivate routes, field names, CSRF, and methods are unchanged.
- [ ] Self role change and self-deactivate UI protections remain.
- [ ] Manajer access remains server-side denied by middleware.
- [ ] Table remains horizontally scrollable on small screens.

#### Implementation Notes

- Do not rename form inputs: `name`, `username`, `password`, `role`, `is_active`.
- Do not convert deactivate into hard delete or label as destructive hard delete.
- Preserve pagination.

#### QA Scenarios

| Scenario | Input | Expected Output | Verification |
|---|---|---|---|
| User list | Admin GET index | table/card, names/usernames/status visible | feature/manual |
| Create form | Admin GET create | fields styled, required names preserved | feature/manual |
| Edit form | Admin GET edit | role/status disabled for self where required | feature/manual |
| Deactivate | Admin deactivates user | `is_active=false` unchanged | feature test |

#### Files to Modify

- `resources/views/admin/users/index.blade.php` - table/card redesign.
- `resources/views/admin/users/create.blade.php` - create page redesign.
- `resources/views/admin/users/edit.blade.php` - edit/deactivate page redesign.
- `resources/views/admin/users/_form.blade.php` - shared form redesign.

---

### TODO 4.1: Update UI tests and run full verification

**Agent**: @laravel
**Dispatch**: single
**Estimated Time**: 75m
**Dependencies**: 3.2

#### Objective

Align feature tests with intentional UI copy/layout changes and verify no behavior regression.

#### Acceptance Criteria

- [ ] `AuthUiTest` updated for redesigned welcome/login/sidebar/dashboard copy.
- [ ] Admin user-management tests still pass or are updated only for copy changes.
- [ ] Auth behavior tests still pass unchanged unless copy-only assertions require update.
- [ ] `./vendor/bin/sail artisan test` passes.
- [ ] `./vendor/bin/sail pint --test` passes.
- [ ] `./vendor/bin/sail npm run build` passes.
- [ ] `graphify update .` runs after changes.

#### Implementation Notes

- Do not weaken behavior assertions.
- If tests fail due route/form behavior, fix UI regression rather than changing tests.
- Manual browser checklist should be reported for desktop and mobile widths.

#### QA Scenarios

| Scenario | Input | Expected Output | Verification |
|---|---|---|---|
| Full suite | `./vendor/bin/sail artisan test` | pass | automated |
| Build | `./vendor/bin/sail npm run build` | pass | automated |
| Admin/Manajer nav | feature tests | correct role visibility | automated |
| Mobile layout | viewport <1080px / <720px | sidebar hidden, mobile nav/table overflow OK | manual |

#### Files to Modify

- `tests/Feature/AuthUiTest.php` - update redesigned UI assertions.
- `tests/Feature/Admin/UserManagementTest.php` - update copy-only assertions if needed.
- Other `tests/Feature/Auth/*.php` - only if copy-only UI assertions fail.

---

## Verification Protocol

### Automated Checks

- [ ] `./vendor/bin/sail artisan test` passes.
- [ ] `./vendor/bin/sail pint --test` passes.
- [ ] `./vendor/bin/sail npm run build` passes.
- [ ] `graphify update .` runs successfully.

### Manual Review

- [ ] Welcome page visually follows prototype style and has no Register link.
- [ ] Login page visually follows prototype style and has no role select/forgot link.
- [ ] Admin shell has sidebar + topbar and correct nav links.
- [ ] Manajer shell excludes Manajemen Pengguna.
- [ ] User-management table/forms retain behavior and match design system.
- [ ] Mobile <1080px shows pill nav; <720px stacks content.
- [ ] Keyboard focus states visible; labels/errors accessible.

### Integration Testing

- [ ] Admin can login, see sidebar, create/edit/deactivate user.
- [ ] Manajer can login, see dashboard, cannot see/access user management.
- [ ] Logout works via POST form.

---

## Documentation Updates

| Document | Update Required | Owner |
|---|---|---|
| `DESIGN.md` | No update required; source design doc | none |
| `index.html` | No update required; source prototype | none |

---

## Risks and Mitigations

| Risk | Likelihood | Impact | Mitigation |
|---|---|---|---|
| UI redesign changes form behavior | Medium | High | Preserve field names/actions/methods/CSRF; full feature tests |
| Sidebar hides logout or admin access rules | Medium | Medium | Keep POST logout and role-gated nav; tests |
| Shared component restyle breaks untouched views | Medium | Medium | Keep component APIs; run full suite |
| Fake dashboard metrics reduce credibility | Medium | Low | Use honest placeholders/PRD targets only |
| Responsive table readability regresses | Medium | Medium | Use `.table-wrap` overflow and min-width tables |
| CSS token support varies | Low | Medium | Use tokens verbatim and verify build/manual browser |

---

## Assumptions

- Visual system should match prototype tokens/components, not recreate every prototype section.
- Dashboards may look finished while showing placeholders/spec targets only.
- Sidebar shows active completed features only.
- Account actions remain available even though feature nav is limited.
- Visible redesigned UI copy should be Bahasa Indonesia.

---

## Open Questions

- [ ] None blocking. Future question: when Epic 2-5 is implemented, sidebar can add those modules.

---

## Appendix

### Related Documents

- `DESIGN.md`
- `index.html`
- `.opencode/context/drafts/20260607002-auth-ui-redesign.md`
- `.opencode/context/plans/20260607001-auth-system.md`

### Research Findings

- Main files: `resources/css/app.css`, `resources/views/layouts/app.blade.php`, `resources/views/layouts/guest.blade.php`, `resources/views/layouts/navigation.blade.php`, `resources/views/auth/login.blade.php`, dashboards, and admin user views.
- Tests most likely impacted: `tests/Feature/AuthUiTest.php`, possibly `tests/Feature/Admin/UserManagementTest.php`.
- Routes to preserve: `login`, `logout`, `dashboard`, `admin.dashboard`, `manager.dashboard`, `admin.users.*`.
