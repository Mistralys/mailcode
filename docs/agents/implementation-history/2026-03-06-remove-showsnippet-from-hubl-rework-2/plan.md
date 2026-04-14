# Plan

## Summary

This plan implements all five strategic recommendations ("Gold Nuggets") surfaced by the rework-1 synthesis for the "Remove ShowSnippet from HubL" plan. The changes are housekeeping in nature: adding the missing `BreakTests.php` regression guard, annotating two dead `translate()` stubs, eliminating the duplicated not-supported format string across test files, fixing a missing PHPDoc block on `HubLSyntax`, and codifying the stub-test method convention in `constraints.md`. No runtime behaviour changes are introduced.

---

## Architectural Context

### Unsupported-commands registry (runtime)

`BaseSyntax::translateCommand()` (`src/Mailcode/Translator/BaseSyntax.php`) checks `getUnsupportedCommands()` before resolving a translation class. If the command ID is listed, it returns the canonical comment:

```
{# !<commandId> is not supported in HubL! #}
```

`HubLSyntax::getUnsupportedCommands()` (`src/Mailcode/Translator/Syntax/HubLSyntax.php`) returns `['Break', 'ShowSnippet']`. The two stub classes (`BreakTranslation.php`, `ShowSnippetTranslation.php`) are never reached at runtime; they exist solely to satisfy the `Mailcode_Translator_Command_*` interface contracts.

### Stub translation classes

- `src/Mailcode/Translator/Syntax/HubL/BreakTranslation.php`
- `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php`

Both have a live `translate()` method that is dead code under the current registry. The method bodies contain a hardcoded copy of the not-supported comment string.

### Existing regression guard

`tests/testsuites/Translator/HubL/ShowSnippetTests.php` — 6 separate test methods, one per command variant. This is the **reference pattern** for stub test files (confirmed as the preferred approach by the maintainer).

### HubL test base class

`tests/assets/classes/HubLTestCase.php` provides `runCommands()` used by all HubL test files.

### Existing convention

Other fully-translated HubL command tests (e.g., `ShowVariableTests.php`, `ForTests.php`) use a single `test_translateCommand()` method with an array of cases passed to `runCommands()`. This pattern is correct for commands where different variants produce different outputs. Stub tests are a distinct category.

---

## Approach / Architecture

### Item 1 — `BreakTests.php` regression guard (HIGH)

Create `tests/testsuites/Translator/HubL/BreakTests.php` following exactly the `ShowSnippetTests.php` pattern (separate `test_*()` methods, private `EXPECTED` constant). `Break` has no configurable variants (no URL encoding, no namespace, no HTML flag — confirmed by `Mailcode_Commands_Command_Break::supportsURLEncoding(): false` and `requiresParameters(): false`), so a single `test_basic()` method is sufficient.

Factory call: `Mailcode_Factory::misc()->break()`.
Expected output: `'{# !break is not supported in HubL! #}'`.

### Item 2 — Annotate dead `translate()` methods (MEDIUM)

Add `@codeCoverageIgnore` and `@internal` PHPDoc tags to `BreakTranslation::translate()` and `ShowSnippetTranslation::translate()`. This explicitly signals that these methods are unreachable at runtime and prevents confusion in coverage reports.

### Item 3 — Eliminate duplicated not-supported format string (LOW)

The format `{# !<command> is not supported in HubL! #}` is currently codified:
- As a dead hardcoded string in each stub translation class (`translate()` return value).
- As a private `EXPECTED` constant in `ShowSnippetTests.php`.
- As a new private `EXPECTED` constant that will be added to `BreakTests.php`.

The authoritative source-of-truth is the format string embedded in `BaseSyntax::translateCommand()`. To prevent silent drift if the canonical format ever changes, add a `protected static function buildNotSupportedComment(string $commandId): string` helper method to `HubLTestCase`. Both `ShowSnippetTests.php` and `BreakTests.php` will use this helper instead of their private `EXPECTED` constants.

The dead string literals in the stub translation class bodies remain (they are part of the dead-code path already marked `@codeCoverageIgnore` by Item 2) but are no longer duplicated within the live test path.

### Item 4 — Class-level PHPDoc block on `HubLSyntax.php` (LOW)

`HubLSyntax.php` is missing the `@package / @subpackage / @author` file-level doc block present on all other translator classes. Add the standard block.

### Item 5 — Document stub-test method convention in `constraints.md` (LOW)

Add a dedicated "HubL Stub Test Convention" subsection to the Testing section of `constraints.md`, recording the following rules:
- For **stub/unsupported** commands, use **separate `test_<variant>()` methods** (one per command variant), each containing a single `runCommands()` call. This makes test failures immediately locatable and keeps fixes granular.
- For **fully-translated** commands, continue to use a single `test_translateCommand()` method with an array of cases.
- `ShowSnippetTests.php` is the canonical reference for stub tests.

---

## Rationale

- Separate test methods for stubs were explicitly chosen by the maintainer over the `runCommands()`-array pattern to improve discoverability and granularity of failures.
- Moving the not-supported format into a `HubLTestCase` helper rather than a `BaseSyntax` constant keeps the format string accessible to tests without leaking test concerns into production code.
- `@codeCoverageIgnore` + `@internal` is the lowest-friction way to document unreachable stubs without removing them (removal would break the interface contract).

---

## Detailed Steps

1. **Create `BreakTests.php`** — `tests/testsuites/Translator/HubL/BreakTests.php` with one `test_basic()` method. Use `Mailcode_Factory::misc()->break()`. Derive expected string from the `HubLTestCase` helper introduced in step 2.

2. **Add `buildNotSupportedComment()` helper to `HubLTestCase`** — `protected static function buildNotSupportedComment(string $commandId): string` returning `sprintf('{# !%s is not supported in HubL! #}', strtolower($commandId))`.

3. **Refactor `ShowSnippetTests.php`** — Replace the private `EXPECTED` constant with a call to `self::buildNotSupportedComment('showsnippet')` in each `test_*()` method (or assign it in `setUp()`). This removes the duplication while keeping the separate test methods intact.

4. **Annotate `BreakTranslation::translate()`** — Add `@codeCoverageIgnore` and `@internal` to the method's docblock in `src/Mailcode/Translator/Syntax/HubL/BreakTranslation.php`.

5. **Annotate `ShowSnippetTranslation::translate()`** — Same annotations in `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php`.

6. **Add PHPDoc block to `HubLSyntax.php`** — Insert standard `@package Mailcode / @subpackage Translator / @author` file-level block into `src/Mailcode/Translator/Syntax/HubLSyntax.php`.

7. **Document convention in `constraints.md`** — Add a "HubL Stub Test Convention" subsection under the Testing section of `docs/agents/project-manifest/constraints.md`.

8. **Update `file-tree.md`** — Add `BreakTests.php` entry to the `tests/testsuites/Translator/HubL/` directory listing in `docs/agents/project-manifest/file-tree.md`.

9. **Run tests and PHPStan** — Verify 526+ passing tests (one new), zero failures, PHPStan level 9 clean.

---

## Dependencies

- `HubLTestCase` helper (`buildNotSupportedComment`) must exist before `BreakTests.php` and refactored `ShowSnippetTests.php` are finalized (step 2 before steps 1 and 3).

---

## Required Components

### New files
- `tests/testsuites/Translator/HubL/BreakTests.php`

### Modified files
- `tests/assets/classes/HubLTestCase.php` — add `buildNotSupportedComment()` helper
- `tests/testsuites/Translator/HubL/ShowSnippetTests.php` — replace private const with helper call
- `src/Mailcode/Translator/Syntax/HubL/BreakTranslation.php` — annotate `translate()` method
- `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php` — annotate `translate()` method
- `src/Mailcode/Translator/Syntax/HubLSyntax.php` — add PHPDoc block
- `docs/agents/project-manifest/constraints.md` — add stub-test convention subsection
- `docs/agents/project-manifest/file-tree.md` — add `BreakTests.php` entry

---

## Assumptions

- `HubLTestCase` is the test base class for all HubL translator tests and is a suitable location for a shared helper method.
- `Break` has exactly one meaningful test variant (the bare command itself), since it supports neither URL encoding nor any other configurable parameter.
- The canonical not-supported comment format (`{# !<command> is not supported in HubL! #}`) will not change as part of this plan.

---

## Constraints

- `declare(strict_types=1)` in all modified PHP source files.
- PHPStan level 9 compliance must be maintained.
- Stub translation classes (`BreakTranslation.php`, `ShowSnippetTranslation.php`) must not be deleted.
- No runtime behaviour changes.

---

## Out of Scope

- Adding unsupported-command support for any syntax other than HubL.
- Changing the canonical not-supported comment format.
- Modifying `BaseSyntax::translateCommand()`.

---

## Acceptance Criteria

- `tests/testsuites/Translator/HubL/BreakTests.php` exists and passes with at least one test method.
- `ShowSnippetTests.php` no longer contains a private `EXPECTED` constant; expected strings are derived from `HubLTestCase::buildNotSupportedComment()`.
- `BreakTranslation::translate()` and `ShowSnippetTranslation::translate()` carry `@codeCoverageIgnore` + `@internal` annotations.
- `HubLSyntax.php` has a class-level `@package / @subpackage / @author` PHPDoc block.
- `constraints.md` contains a "HubL Stub Test Convention" subsection documenting the separate-method rule.
- `file-tree.md` lists `BreakTests.php`.
- `composer test` passes with a test count ≥ 526 and zero failures.
- `composer analyze` reports zero errors at level 9.

---

## Testing Strategy

All changes are either test additions, annotation-only edits, or documentation updates. The test suite itself is the validation mechanism:
- `composer test` verifies the new `BreakTests.php` method and the refactored `ShowSnippetTests.php` assertions pass.
- `composer analyze` confirms PHPStan level 9 compliance after annotation changes.

---

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **`buildNotSupportedComment()` format drifts from `BaseSyntax`** | The helper is a single `sprintf` mirroring the exact format in `BaseSyntax::translateCommand()`. Document the dependency in the helper docblock. |
| **Refactored `ShowSnippetTests.php` loses coverage** | Run `composer test` immediately after refactoring; all 6 test methods must still pass. |
| **`@internal` annotation confuses future maintainers** | The class-level docblock in `BreakTranslation.php` already explains the dead-code rationale; `@internal` reinforces it. |
