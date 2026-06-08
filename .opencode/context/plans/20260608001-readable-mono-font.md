# Simple Plan: Readable Mono Font

**ID**: 20260608001
**Status**: partial
**Created**: 2026-06-08
**Scope**: trivial

---

## Goal

Replace current monospace font stack with more readable system monospace stack for app UI and canonical prototype artifact.

## Context

**Current**: `--font-mono` prefers `JetBrains Mono` and `IBM Plex Mono` in shared CSS and `index.html`.
**Target**: `--font-mono` prefers widely available readable system monospace faces without adding dependencies.

## Tasks

### 1. Update monospace token
- Change `--font-mono` in app CSS and prototype CSS.
- Keep existing design system usage intact.
- **Files**: `resources/css/app.css`, `index.html`

### 2. Verify UI build and PHP checks
- Run relevant build and PHP checks.
- Update graph after code changes.

## Verify

- `./vendor/bin/sail npm run build`
- `./vendor/bin/sail artisan test`
- `./vendor/bin/sail pint --test`
- `graphify update .`
