# Execution Guidelines

This document provides guidance for executing implementation plans in build mode.

This workspace is configured for a root Laravel project using:

- Laravel 13
- Blade
- Tailwind CSS v4
- Vite
- npm
- Pest
- Laravel Pint
- Laravel Sail

---

## Execution Order

Tasks should follow a dependency order appropriate to Laravel applications.

Typical Laravel execution flow:

```txt
@laravel (foundation: models, migrations, database shape)
    ↓
@laravel (backend: routes, controllers, requests, policies, services/actions)
    ↓
@laravel (presentation: Blade views, forms, Tailwind UI)
    ↓
@laravel (quality: Pest tests, Pint, build checks)
    ↓
@curator (documentation alignment)
    ↓
@scribe (archive learnings and execution summary)
````

**Rule:** Never start a layer until its dependencies are complete.

For example:

* Do not build Blade forms before the route/controller contract is known.
* Do not write feature tests against routes that do not exist yet.
* Do not update documentation before behavior is finalized.
* Do not proceed to the next wave before `@reviewer` returns `PASS`.

---

## Agent Scope

Implementation work is performed by stack-specific agents. In this Laravel workspace, `@laravel` owns Laravel application implementation.

Cross-cutting roles remain specialized.

| Agent           | Scope                                                    |
| --------------- | -------------------------------------------------------- |
| `@architect`    | Creates implementation plans for non-trivial work        |
| `@orchestrator` | Executes plans through delegation and tracks progress    |
| `@laravel`      | Laravel application implementation                       |
| `@build`        | Workflow, `.opencode`, tooling, and fallback maintenance |
| `@reviewer`     | Post-implementation validation                           |
| `@auditor`      | Plan validation                                          |
| `@strategist`   | Pre-plan risk and architecture analysis                  |
| `@analyst`      | Plan optimality review                                   |
| `@explore`      | Read-only codebase exploration                           |
| `@librarian`    | External documentation research                          |
| `@curator`      | Documentation alignment                                  |
| `@scribe`       | Cross-session archival and learnings                     |

Project-specific guidance lives in the root `AGENTS.md`.

---

## Agent Selection

Use `@laravel` for:

* controllers
* models
* routes
* Blade views
* FormRequests
* policies
* gates
* services
* actions
* jobs
* events
* listeners
* notifications
* factories
* seeders
* Pest tests
* Laravel feature implementation
* Laravel bug fixes
* Tailwind/Vite changes tied to Blade UI

Use `@build` for:

* `.opencode/**`
* agent definitions
* commands
* skills
* workflow files
* setup instructions
* repository maintenance
* tooling/config changes after approval

Use `@curator` for:

* `README.md`
* `docs/**`
* project documentation updates
* documentation alignment after implementation
* keeping `AGENTS.md` documentation sections current

Use `@scribe` for:

* archiving completed plans
* recording durable learnings
* summarizing decisions
* cross-session memory

Use `@reviewer` for:

* checking implementation against plan
* checking Laravel conventions
* checking Blade/UI consistency
* checking verification reports
* finding scope creep

---

## Delegation Format

When delegating implementation work, use the 8-section structure.

```md
@laravel

## PLAN CONTEXT

**Plan**: `.opencode/context/plans/{slug}.md`
**TL;DR**: {copy from plan}
**Target State**: {copy from plan}
**Non-Goals**: {copy from plan}
**Wave**: {N} of {M} — {Wave Title}

## TASK

**Task ID**: {ID}
**Objective**: {copy from plan task}

## ACCEPTANCE CRITERIA

{copy acceptance criteria exactly}

## IMPLEMENTATION NOTES

{copy implementation notes exactly}

### Files to Modify

{copy files to modify exactly}

## QA SCENARIOS

{copy QA scenarios exactly}

If the plan has no explicit QA scenarios, write:

No explicit QA scenarios were provided in the plan. Follow the acceptance criteria and Laravel verification commands.

## CONSTRAINTS

### MUST DO

- Follow Laravel 13 conventions.
- Use Blade for UI.
- Use Pest for tests.
- Use Sail commands when available.
- Validate all external input.
- Enforce authorization server-side.
- Keep controllers thin.
- Use FormRequest for non-trivial validation.
- Use Policy/Gate for protected resource actions.
- Follow root `AGENTS.md`.
- Follow `DESIGN.md` for UI/Blade/Tailwind changes when present.

### MUST NOT DO

- Do not modify `.env*`.
- Do not modify migrations without approval.
- Do not add dependencies without approval.
- Do not refactor unrelated code.
- Do not change auth, billing, queue, Docker, or deployment architecture without approval.
- Do not introduce Livewire, Inertia, React, or SPA routing unless explicitly requested.

## PRIOR WORK

{include dependency outputs from progress.md, decisions.md, learnings.md}

If no dependencies:

No prior dependencies.

## VERIFICATION

Run relevant commands:

- `./vendor/bin/sail artisan test`
- `./vendor/bin/sail pint --test`

If frontend assets changed:

- `./vendor/bin/sail npm run build`

If Sail is unavailable, use local fallback and report it:

- `php artisan test`
- `./vendor/bin/pint --test`
- `npm run build`
```

Never delegate with:

```txt
See the plan for details.
```

Subagents may not have the plan context unless the orchestrator includes it.

---

## Execution Modes

| Mode       | When                                            | How                                      |
| ---------- | ----------------------------------------------- | ---------------------------------------- |
| Sequential | Laravel code work, migrations, same-agent tasks | One implementation agent at a time       |
| Parallel   | Read-only exploration or research               | Multiple read-only agents simultaneously |
| Batched    | Small related same-agent tasks in one wave      | One subagent session with multiple tasks |

**Default:** Sequential for safety.

Laravel application work should usually be sequential because many tasks share:

```txt
routes/**
app/**
resources/views/**
database/**
tests/**
```

---

## Parallelism Policy

Code-modifying agents hold workspace state that can conflict under concurrent edits.

| Agent class      | Max parallel | Why                                                 |
| ---------------- | ------------ | --------------------------------------------------- |
| `@laravel`       | 1            | Laravel app files, database state, route/test state |
| `@build`         | 1            | Tooling, lockfiles, workflow files                  |
| `@curator`       | 1            | Documentation consistency                           |
| `@scribe`        | 1            | Archive consistency                                 |
| Read-only agents | unlimited    | No workspace state                                  |

Examples:

* OK: `@explore` + `@librarian` in parallel.
* OK: `@explore` runs while `@architect` is planning.
* Forbidden: two `@laravel` tasks in parallel.
* Forbidden: `@laravel` and `@build` editing the same file in parallel.
* Forbidden: `@curator` updating docs while `@laravel` is still changing the behavior those docs describe.

When in doubt, serialize.

---

## Batching Policy

Multiple small same-agent tasks in the same wave may be batched into one subagent session only when the plan explicitly includes:

```txt
Dispatch: batched:{batch-id}
```

Batching is allowed when:

* all members use the same agent
* all members are in the same wave
* all members touch the same area
* there is no review checkpoint between them
* batch size is 2–8 tasks

Good Laravel batch example:

```txt
Dispatch: batched:todo-validation

Task 1.1: Create StoreTodoRequest
Task 1.2: Update TodoController store method
Task 1.3: Add Pest validation test
```

Do not auto-batch just because tasks look related. The plan should declare batching intent.

---

## Per-Wave Verification

Implementation agents run their own verification.

For Laravel work, `@laravel` should normally run:

```bash
./vendor/bin/sail artisan test
./vendor/bin/sail pint --test
```

If frontend assets changed:

```bash
./vendor/bin/sail npm run build
```

If Sail is unavailable, `@laravel` may use local fallback:

```bash
php artisan test
./vendor/bin/pint --test
npm run build
```

The orchestrator should not re-run the same verification commands if the agent already ran them. The orchestrator should read the agent report, update progress, then invoke `@reviewer`.

---

## Progress Tracking

Track execution status in:

```txt
.opencode/context/notepads/{plan-slug}/progress.md
```

Use this format:

```md
# Execution Progress

**Plan**: .opencode/context/plans/{slug}.md
**Started**: {date}
**Updated**: {date time}
**Status**: executing | blocked | complete | aborted

---

## Wave 1: Foundation

- [x] Task 1.1 - @laravel - create model and migration plan ✓ verified
  - Files: `app/Models/Todo.php`
  - Verification: `./vendor/bin/sail artisan test` passed
- [ ] Task 1.2 - @laravel - create controller and routes - pending

---

## Verification Summary

| Check | Result |
|---|---|
| Agent verification | pending |
| Reviewer verdict | pending |

---

## Key Discoveries

- None yet
```

Avoid emoji icons in progress files. Use text statuses:

```txt
pending
in-progress
complete
failed
blocked
skipped
```

---

## Notepad Files

Each plan should have:

```txt
.opencode/context/notepads/{plan-slug}/
  learnings.md
  decisions.md
  issues.md
  progress.md
  rework-log.md
```

Use:

| File            | Purpose                            |
| --------------- | ---------------------------------- |
| `progress.md`   | Current execution status           |
| `decisions.md`  | Decisions made during execution    |
| `learnings.md`  | Discoveries that affect later work |
| `issues.md`     | Problems and resolutions           |
| `rework-log.md` | Reviewer rework attempts           |

---

## Error Handling

If an agent fails:

1. Record the failure in `issues.md`.
2. Assess whether the failure blocks dependent tasks.
3. Stop the wave if blocking.
4. Ask user or send rework depending on the issue.

Options:

```txt
1. Retry the failed task
2. Rework with narrower instructions
3. Return to @architect to revise the plan
4. Skip task if user approves
5. Abort execution
```

Never continue past a blocking failure without user approval.

---

## Blocking vs Non-Blocking Failures

| Failure Type                             | Blocking?   | Action                             |
| ---------------------------------------- | ----------- | ---------------------------------- |
| Migration approval needed                | Yes         | Stop and ask                       |
| Migration fails                          | Yes         | Stop and fix before continuing     |
| Route/controller fatal error             | Yes         | Stop and rework                    |
| Pest test failure for changed behavior   | Yes         | Rework                             |
| Pint failure                             | Usually yes | Rework or ask before formatting    |
| npm build failure after UI/assets change | Yes         | Rework                             |
| Missing optional documentation update    | No          | Assign to `@curator` later         |
| Scribe archival missing                  | No          | Run after implementation completes |

In earlier generic docs, test failures may be marked non-blocking. For this Laravel workflow, test failures related to the changed behavior are blocking.

---

## Reviewer Gate

After every wave, invoke `@reviewer`.

The wave cannot proceed until reviewer returns:

```txt
PASS
```

Reviewer verdict handling:

| Verdict          | Action                                       |
| ---------------- | -------------------------------------------- |
| PASS             | Mark wave complete and continue              |
| REWORK attempt 1 | Send rework to the same implementation agent |
| REWORK attempt 2 | Escalate to user                             |
| ESCALATE         | Stop and ask user                            |

---

## Rework Flow

When `@reviewer` returns `REWORK`:

1. Log the issue in `rework-log.md`.
2. Send rework to the same agent that implemented the task.
3. Limit rework to reviewer findings.
4. Require verification again.
5. Run `@reviewer` again.
6. Escalate after the second failed rework.

Rework delegation shape:

```md
@laravel

## REWORK REQUIRED

**Plan**: `.opencode/context/plans/{slug}.md`
**Task**: {ID}
**Attempt**: 1 of 2

## REVIEWER FINDINGS

{copy reviewer findings}

## REQUIRED FIX

{specific fix needed}

## CONSTRAINTS

- Fix only identified issues.
- Do not refactor unrelated code.
- Keep previous passing behavior.
- Run verification again.

## VERIFICATION

- `./vendor/bin/sail artisan test`
- `./vendor/bin/sail pint --test`
```

---

## Curator Handoff

After implementation behavior is stable, use `@curator` for documentation alignment.

Use `@curator` for:

* README updates
* `docs/**`
* `AGENTS.md` documentation sections
* feature documentation
* design docs
* workflow documentation intended for project readers

Example:

```md
@curator

## DOCUMENTATION ALIGNMENT

Feature:
Todo CRUD

Implemented behavior:
- Users can create, edit, delete, and mark todos complete.
- Validation uses FormRequest.
- Tests use Pest.
- UI follows DESIGN.md.

Docs to update:
- `docs/CRUD.md`
- `docs/BLADE.md`

Constraints:
- Do not modify application code.
- Keep docs concise.
```

---

## Scribe Handoff

Use `@scribe` after significant execution completes.

Use `@scribe` for:

* archive summary
* durable learnings
* decisions worth remembering
* plan completion summary

Example:

```md
@scribe

## ARCHIVE REQUEST

Plan:
`.opencode/context/plans/{slug}.md`

Notepad:
`.opencode/context/notepads/{slug}/`

Summarize:
- completed tasks
- key decisions
- learnings
- unresolved issues
- future follow-ups
```

---

## Completion Checklist

When all implementation waves are done:

```md
## Execution Complete

**Feature:** {name}

**Summary:**
- {N} tasks completed
- {Files changed}

**Reviewer Status:**
- Wave 1: PASS
- Wave 2: PASS

**Verification:**
- `./vendor/bin/sail artisan test` — pass | fail | skipped
- `./vendor/bin/sail pint --test` — pass | fail | skipped
- `./vendor/bin/sail npm run build` — pass | fail | skipped

**Documentation:**
- `@curator` needed: yes | no
- Docs updated: yes | no

**Archive:**
- `@scribe` needed: yes | no

**Notepad:**
`.opencode/context/notepads/{slug}/`
```

---

## Quick Reference

### Starting Execution

```txt
User: "Execute the plan"
Action:
1. Read plan
2. Initialize notepad
3. Start first wave
4. Delegate to @laravel or relevant agent
```

### Checking Status

```txt
User: "What's the status?"
Action:
1. Read progress.md
2. Show current wave
3. Show completed/pending/blocked tasks
```

### Handling Laravel Implementation

```txt
Task affects app/routes/resources/tests
→ delegate to @laravel
```

### Handling Docs

```txt
Task affects README/docs/AGENTS documentation sections
→ delegate to @curator
```

### Handling Workflow or Agent Setup

```txt
Task affects .opencode/agent, commands, skills
→ delegate to @build
```

### Handling Archive or Memory

```txt
Task asks to preserve learnings/decisions
→ delegate to @scribe
```

---

## Tips

* Read the plan carefully before starting.
* Ask if a migration is needed but not approved.
* Use one Laravel implementation agent at a time.
* Keep progress visible in notepads.
* Do not rush past reviewer gates.
* Prefer `@laravel` for Laravel application code.
* Prefer `@curator` for documentation.
* Prefer `@scribe` for memory/archive.
* Ask when unsure.

````