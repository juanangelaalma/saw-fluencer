# Agent Guidelines - Laravel Project

## Quick Reference

| Task | Doc | Key Points |
|------|-----|------------|
| Add CRUD | [CRUD.md](docs/CRUD.md) | Controller, FormRequest, Policy, Pest |
| Add Blade page | [BLADE.md](docs/BLADE.md) | Named routes, CSRF, validation errors |
| Add migration | [DATABASE.md](docs/DATABASE.md) | Must ask first |
| Add test | [TESTING.md](docs/TESTING.md) | Pest style |
| Format code | AGENTS.md#commands | Use Sail Pint check |

## Commands

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail pint --test
./vendor/bin/sail npm run build
````

## Before Committing

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail pint --test
```

## Architecture Summary

* Laravel 13 root project.
* UI uses Blade, not Livewire/Inertia/SPA.
* Controllers should stay thin.
* Use FormRequest for non-trivial validation.
* Use Policy/Gate for authorization.
* Use Service/Action classes for reusable business logic.
* Use Pest for tests.
* Use Sail for commands.

## Environment Variables

| Variable      | Description             | Required |
| ------------- | ----------------------- | -------- |
| APP_ENV       | Application environment | Yes      |
| DB_CONNECTION | Database driver         | Yes      |
| DB_DATABASE   | Database name           | Yes      |

## Design System

All UI work must follow [`DESIGN.md`](DESIGN.md).

Rules:
- Read `DESIGN.md` before planning or implementing UI changes.
- Follow defined layout, spacing, typography, colors, components, and interaction patterns.
- Do not introduce new visual patterns unless explicitly approved.
- If a requested UI conflicts with `DESIGN.md`, stop and ask for clarification.
- If `DESIGN.md` is incomplete for the requested UI, ask before inventing a new pattern.

## See Also

* [CRUD Guide](docs/CRUD.md)
* [Blade Guide](docs/BLADE.md)
* [Database Guide](docs/DATABASE.md)
* [Testing Guide](docs/TESTING.md)
