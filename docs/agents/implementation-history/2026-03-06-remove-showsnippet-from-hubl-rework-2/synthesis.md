# Project Synthesis Report
**Plan:** `2026-03-06-remove-showsnippet-from-hubl-rework-2`
**Date:** 2026-03-06
**Status:** COMPLETE

---

## Executive Summary

This plan completed five housekeeping items targeting HubL translator code quality and test-infrastructure
consistency. The central goal was to establish a single canonical DRY mechanism — `HubLTestCase::buildNotSupportedComment()` — for all HubL stub-command test assertions, eliminating the ad-hoc private constant pattern that had accumulated in `ShowSnippetTests.php`. A companion
`BreakTests.php` was also created, giving the `{break}` HubL translation a proper regression guard for the
first time. Stub `translate()` methods were annotated with `@internal`/`@codeCoverageIgnore` to silence
false coverage-gap alerts, and `constraints.md` was extended with a formal **HubL Stub Test Convention**
section so future contributors know the exact pattern to follow.

All six work packages passed every pipeline stage (implementation → QA → code-review → documentation)
in a single pass with no rework cycles.

---

## Deliverables

| WP | Description | Files Changed |
|----|-------------|---------------|
| WP-001 | Add `HubLTestCase::buildNotSupportedComment()` helper | `tests/assets/classes/HubLTestCase.php` |
| WP-002 | Create `BreakTests.php` HubL translation test | `tests/testsuites/Translator/HubL/BreakTests.php` |
| WP-003 | Remove private `EXPECTED` constant from `ShowSnippetTests.php` | `tests/testsuites/Translator/HubL/ShowSnippetTests.php` |
| WP-004 | Add `@internal`/`@codeCoverageIgnore` to stub `translate()` methods; add class docblock to `HubLSyntax.php` | `src/Mailcode/Translator/Syntax/HubL/BreakTranslation.php`, `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php`, `src/Mailcode/Translator/Syntax/HubLSyntax.php` |
| WP-005 | Add `HubL Stub Test Convention` section to `constraints.md`; verify `file-tree.md` | `docs/agents/project-manifest/constraints.md` |
| WP-006 | End-to-end validation gate | `docs/agents/project-manifest/constraints.md` (test baseline bump 525→526) |

---

## Metrics

| Metric | Value |
|--------|-------|
| **Work Packages** | 6 / 6 COMPLETE |
| **Rework Cycles** | 0 |
| **Tests Passed** | 526 / 526 |
| **Tests Failed** | 0 |
| **PHPStan Level** | 9 |
| **PHPStan Errors** | 0 |
| **Pipeline Health** | All 6 WPs — all 4 stages PASS |
| **Pre-existing issues** | 1 PHPUnit deprecation, 1 incomplete test (both unrelated to this plan) |

---

## Strategic Recommendations ("Gold Nuggets")

### 1 — Rename `HubL_BreakTests` → `BreakTests` (Low Priority)
`tests/testsuites/Translator/HubL/BreakTests.php` declares class `HubL_BreakTests` to avoid a
perceived FQCN collision, but PHP namespacing fully isolates `MailcodeTests\Translator\HubL\BreakTests`
from `MailcodeTests\Commands\Types\BreakTests` and `MailcodeTests\Factory\Commands\BreakTests`.
The `HubL_` prefix is unnecessary, misleading, and inconsistent with the `ShowSnippetTests` reference
pattern already cited in the new `constraints.md` convention section.
**Recommended action:** Create a small cleanup WP to rename the class to plain `BreakTests`.

### 2 — Add file-level docblock to `HubLSyntax.php` (Low Priority)
All sibling translator files (`ApacheVelocitySyntax.php`, etc.) carry a `/** @package Mailcode @subpackage Translator */` block immediately after `<?php`. `HubLSyntax.php` received a class-level docblock in WP-004 but still lacks the file-level block. Pre-existing omission.
**Recommended action:** Add the file-level docblock in a future consistency pass.

### 3 — Use FQCN in `@see` tag of `HubLTestCase::buildNotSupportedComment()` (Low Priority)
The `@see BaseSyntax::translateCommand()` tag in the new helper uses a short class name.
IDEs in the `MailcodeTestClasses` namespace cannot resolve it without an import or FQCN.
**Recommended action:** Update to `@see \Mailcode\Translator\BaseSyntax::translateCommand()` in a future pass.

### 4 — DRY Pattern Now Enforced (Architecture Win)
The format string `{# !%s is not supported in HubL! #}` is now owned in exactly one place:
`HubLTestCase::buildNotSupportedComment()`. The convention is formally documented in `constraints.md`.
If the HubL comment format ever changes, a single method update covers all stub test files.
This is the correct minimal-surface-area design for test infrastructure.

---

## Next Steps for Planner / Manager

1. **Optional cleanup WP:** Rename `HubL_BreakTests` → `BreakTests` and update `file-tree.md` count (no functional impact — this is purely a naming hygiene task).
2. **Optional consistency WP:** Add file-level `/** @package @subpackage */` docblock to `HubLSyntax.php` alongside the FQCN `@see` fix in `HubLTestCase`.
3. **No functional regressions introduced.** The test suite baseline is now 526 passing tests at PHPStan level 9 — this should be treated as the new floor for future work.
