# Project Synthesis — HubL High-Feasibility Rework 1

**Plan:** `2026-03-05-hubl-high-feasibility-rework-1`
**Date:** 2026-03-05
**Status:** COMPLETE

---

## Executive Summary

This session delivered five targeted clean-up and correctness improvements to the HubL translator layer and its supporting infrastructure. All work was low-risk, high-impact: three source-code corrections, one documentation-only improvement, and one test/manifest hygiene sweep. No new features were introduced; no APIs were broken. The codebase leaves the session cleaner, more Unicode-safe, and better documented than it entered.

---

## Work Package Summary

| WP | Title | Type | Result |
|---|---|---|---|
| WP-001 | Rename `$caseSensitive` → `$caseInsensitive` | Bugfix / Rename | ✅ PASS |
| WP-002 | Extract `hasExplicitTimezone()` to interface + trait | Refactor / API | ✅ PASS |
| WP-003 | Document `renderEncodings()` inner-expression precondition | Documentation | ✅ PASS |
| WP-004 | `mb_strtolower`, unused imports, PHPStan annotation | Correctness / Cleanup | ✅ PASS |
| WP-005 | Standardise HubL test namespaces + strip ★ annotations | Hygiene / Manifest | ✅ PASS |

---

## Metrics

| Metric | Value |
|---|---|
| Work packages completed | 5 / 5 |
| Tests passing | **315 / 315** |
| Tests failing | 0 |
| PHPStan new violations | **0** |
| PHPStan pre-existing violations (start) | 8 |
| PHPStan pre-existing violations (end) | **7** (WP-004 resolved one) |
| Source files modified | 11 |
| Test files modified | 11 |
| Manifest / doc files modified | 3 (`api-surface.md`, `constraints.md`, `file-tree.md`) |
| Changelog entries added | 5 (one per WP, all in v3.6.0) |

---

## Per-WP Detail

### WP-001 — Rename `$caseSensitive` → `$caseInsensitive`

Corrected an inverted parameter name in three translator files: `HubL/Base/AbstractIfBase.php`, `AV/Base/AbstractIfBase.php`, and `AV/Contains/ContainsStatementBuilder.php`. The old name was semantically incorrect (the flag signals case-insensitivity, not case-sensitivity). All 28+ call-sites pass values positionally via `$command->isCaseInsensitive()` and required no changes.

**Files:** `HubL/Base/AbstractIfBase.php`, `AV/Base/AbstractIfBase.php`, `AV/Contains/ContainsStatementBuilder.php`, `changelog.md`

---

### WP-002 — Extract `hasExplicitTimezone()` to `TimezoneInterface` / `TimezoneTrait`

Added `hasExplicitTimezone(): bool` to `TimezoneInterface` and implemented it in `TimezoneTrait` using a lifecycle-safe, side-effect-free token lookup (does not rely on the `$timezoneToken` property populated during validation). Refactored `ShowDateTranslation.translate()` to replace a 14-line manual introspection block with a single ternary delegation. Updated `api-surface.md` to document the new method; the `setTimezone()` signature was corrected from PHP 8 union syntax to PHP 7.4-compatible docblock form.

**Files:** `TimezoneInterface.php`, `TimezoneTrait.php`, `HubL/ShowDateTranslation.php`, `api-surface.md`, `changelog.md`

---

### WP-003 — Document `renderEncodings()` Inner-Expression Precondition

Documentation-only change. Added a full PHPDoc block to `BaseHubLCommandTranslation::renderEncodings()` including a `@note` that states the precondition (the method must receive an inner expression, not the final output) and gives a concrete counterexample of the failure mode (`{{ {{ var }}|urlencode }}`). No functional code changes.

**Files:** `BaseHubLCommandTranslation.php`, `changelog.md`

---

### WP-004 — `mb_strtolower`, Unused Imports, PHPStan Annotation

Three independent sub-fixes:
1. **Unicode safety:** Replaced `strtolower` with `mb_strtolower` at all three lowercasing sites in `HubL/Base/AbstractIfBase::_translateSearch()`.
2. **Dead code:** Removed unused `Token_Number` and `Token_Variable` imports from `HubL/ForTranslation.php`.
3. **PHPStan:** Added a `@phpstan-ignore-next-line deadCode.unreachable` annotation before the first unreachable statement in `ShowURLTests::test_nestedCommands()`, reducing the total PHPStan error count from 8 to 7.

The `mb_strtolower` convention was codified in `constraints.md` (new "String Functions" section) to prevent regression.

**Files:** `HubL/Base/AbstractIfBase.php`, `HubL/ForTranslation.php`, `ShowURLTests.php`, `constraints.md`, `changelog.md`

---

### WP-005 — Standardise HubL Test Namespaces + Strip ★ Annotations

Corrected namespace declarations in all 11 affected HubL test files to the canonical `MailcodeTests\Translator\HubL`. Two incorrect variants had been in use: `testsuites\Translator\HubL` (7 files) and `MailCodeTests\Translator\HubL` (4 files). Stripped all 10 `★ Added` temporary file-entry annotations from `file-tree.md` and documented the stripping policy in both `file-tree.md` (header note) and `constraints.md` (new "Manifest Maintenance — Annotation Policy" section naming both incorrect namespace variants).

**Files:** 11 HubL test files, `file-tree.md`, `constraints.md`, `changelog.md`

---

## Strategic Observations & Recommendations

### High Priority — Pre-existing Technical Debt

1. **113 PHPUnit class-not-found warnings** are emitted on every test run for test files outside the HubL directory (Variables, Velocity, and other translator subsystems). These warnings do not cause test failures but indicate missing or incorrect namespace declarations in the broader test suite — the same class of problem that WP-005 fixed for HubL. A follow-on rework pass targeting the remaining test suites is strongly recommended.

2. **7 remaining PHPStan violations** in `Parser.php`, `ClassCache.php`, `LogicKeywords.php`, `ProtectedContent.php`, `Show.php`, and `tools/extractPhoneCountries.php`. These are pre-existing and were untouched by this session. They should be resolved before the PHPStan baseline drifts further.

### Medium Priority — Improvement Opportunities

3. **`TimezoneTrait.hasExplicitTimezone()` vs `validateSyntax_check_timezone()`:** Both methods perform token lookup for the timezone parameter. The duplication is intentional (different lifecycle roles: translate-time vs parse-time with side effects), but the architectural rationale is now only documented in a ledger comment. Adding an inline code comment to `TimezoneTrait.hasExplicitTimezone()` explaining why a fresh lookup is used (rather than the property) would aid future maintainers.

4. **`ContainsStatementBuilder` constructor docblock** uses the legacy underscore-class naming style in its description text. Minor but worth addressing in a future cleanup pass to align with the rest of the file.

5. **`api-surface.md` PHP 7.4 / PHP 8 type syntax inconsistency** (fixed for `setTimezone()` in WP-002): A broader audit of `api-surface.md` for any remaining PHP 8 union type syntax in documented signatures would ensure the manifest is self-consistent.

---

## Next Steps for Planner / Manager

1. **Immediate:** Plan a namespace-standardisation sweep for the ~113 PHPUnit class-not-found warnings across non-HubL test suites (same pattern as WP-005).
2. **Near-term:** Resolve the 7 remaining PHPStan violations; several are in core classes (`Parser.php`, `ClassCache.php`).
3. **Near-term:** Audit `api-surface.md` for any remaining PHP 8 union type syntax in documented signatures.
4. **Low priority:** Add an inline comment to `TimezoneTrait.hasExplicitTimezone()` explaining the fresh-lookup rationale.
