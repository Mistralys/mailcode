# Project Synthesis Report

**Plan:** `2026-03-06-remove-showsnippet-from-hubl-rework-1`  
**Date:** 2026-03-06  
**Status:** COMPLETE  
**Work Packages:** 4 / 4 COMPLETE  
**Pipeline Stages:** 16 / 16 PASS  

---

## Executive Summary

This plan addressed five strategic recommendations surfaced by the prior synthesis cycle for the "Remove ShowSnippet from HubL" plan. All four work packages completed the full implementation → QA → code-review → documentation pipeline with zero failures.

The session delivered:

1. **WP-001 — HubL Manifest Audit (HIGH):** Rewrote the Translation Coverage section in `constraints.md` with a verified 17-command table (15 Fully Translated, 2 Stub/Not Supported). Corrected a stale HubL entry in `README.md` Translation Targets table.

2. **WP-002 — BreakTranslation docblock + file-tree.md cleanup (LOW):** Back-filled a class-level docblock in `BreakTranslation.php` explaining the HubL for-loop early-exit limitation. Stripped all 5 residual `★` annotation markers from `file-tree.md`.

3. **WP-003 — ShowSnippetTests.php regression guard (MEDIUM):** Created `tests/testsuites/Translator/HubL/ShowSnippetTests.php` with 6 test methods covering all ShowSnippet command variants. Test suite grew from 519 to 525.

4. **WP-004 — BaseSyntax unsupported-commands registry (MEDIUM):** Introduced `BaseSyntax::getUnsupportedCommands()` hook and `HubLSyntax::getUnsupportedCommands()` override returning `['Break', 'ShowSnippet']`. `translateCommand()` now short-circuits before `createTranslator()` for unsupported commands, making the stub files intentionally inert (retained for interface compliance). Updated `constraints.md`, `api-surface.md`, and `docs/user-guide/translate-hubl.md`.

---

## Metrics

| Metric | Value |
|--------|-------|
| Tests passing (baseline → final) | 519 → **525** |
| Tests failing | **0** |
| PHPStan level | **9** |
| PHPStan errors | **0** |
| Pipeline stages passed | **16 / 16** |
| Blocker issues | **0** |
| Security concerns | **0** |

---

## Files Modified

| File | WP |
|------|----|
| `docs/agents/project-manifest/constraints.md` | WP-001, WP-004 |
| `README.md` | WP-001 |
| `src/Mailcode/Translator/Syntax/HubL/BreakTranslation.php` | WP-002 |
| `docs/agents/project-manifest/file-tree.md` | WP-002, WP-003 |
| `tests/testsuites/Translator/HubL/ShowSnippetTests.php` | WP-003 (new) |
| `src/Mailcode/Translator/BaseSyntax.php` | WP-004 |
| `src/Mailcode/Translator/Syntax/HubLSyntax.php` | WP-004 |
| `docs/agents/project-manifest/api-surface.md` | WP-004 |
| `docs/user-guide/translate-hubl.md` | WP-004 |

---

## Strategic Recommendations ("Gold Nuggets")

### 1. Add `HubLBreakTests.php` regression guard (HIGH)
No test currently guards against `Break` being accidentally removed from the unsupported registry. If a future refactor drops the entry, the regression would be silent. A `tests/testsuites/Translator/HubL/BreakTests.php` mirroring `ShowSnippetTests.php` closes this gap. Flagged independently by both QA and Reviewer.

### 2. Annotate dead stub `translate()` methods (MEDIUM)
`BreakTranslation::translate()` and implicitly `ShowSnippetTranslation::translate()` are now unreachable — `BaseSyntax::translateCommand()` short-circuits before `createTranslator()` is invoked. A `@codeCoverageIgnore` + `@internal` (or `@deprecated`) annotation makes the dead-code intent explicit to future maintainers and prevents confusion in coverage reports.

### 3. Introduce a shared `NOT_SUPPORTED_COMMENT` constant (LOW)
The not-supported string literal (e.g., `{# !showsnippet is not supported in HubL! #}`) is currently duplicated: once in `ShowSnippetTranslation.php` and once as `EXPECTED` in `ShowSnippetTests.php`. A constant on `BaseSyntax` (or on each translation class) would prevent silent drift if the canonical format changes.

### 4. Add class-level PHPDoc block to `HubLSyntax.php` (LOW)
`HubLSyntax.php` lacks the `@package/@subpackage/@author` block present on `BaseSyntax`. Not introduced by this plan, but worth fixing in the next housekeeping pass for documentation consistency.

### 5. Clarify stub test method convention (LOW)
`ShowSnippetTests.php` uses 6 separate test methods (one per variant); other HubL test files use a single `runCommands()` with an array of cases. The preferred pattern for future stub test files should be documented explicitly — ideally as a note in `constraints.md` or the test base class docblock.

---

## Next Steps

| Priority | Action |
|----------|--------|
| HIGH | Add `tests/testsuites/Translator/HubL/BreakTests.php` mirroring `ShowSnippetTests.php` |
| MEDIUM | Annotate `BreakTranslation::translate()` with `@codeCoverageIgnore` + `@internal` |
| LOW | Annotate `ShowSnippetTranslation::translate()` with `@codeCoverageIgnore` + `@internal` |
| LOW | Add class-level PHPDoc block to `HubLSyntax.php` |
| LOW | Evaluate a shared `NOT_SUPPORTED_COMMENT` constant on `BaseSyntax` or translation classes |
| LOW | Document the preferred stub test method pattern in `constraints.md` |
