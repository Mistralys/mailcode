# Synthesis Report — PHP 8.4 Union Type Fix (ClassCache)

**Plan:** `2026-03-05-wp003-php84-union-type-fix`
**Date:** 2026-03-05
**Status:** COMPLETE
**Work Packages:** 2 / 2 COMPLETE

---

## Executive Summary

This plan resolved a docblock/type-system inconsistency in `ClassCache::findClassesInFolder()`. The `$folder` parameter had a `@param string|FolderInfo $folder` docblock annotation but no native PHP type declaration. The fix replaced the docblock-only annotation with a native `string|FolderInfo` union type (available since PHP 8.0, consistent with the project's PHP ≥ 8.4 baseline), removed the now-redundant `@param` line, and codified the pattern in the project manifest so the convention is enforced going forward.

No new commands, translators, or architectural components were introduced. The change is minimal, targeted, and backward-compatible.

---

## Work Packages

| WP | Title | Status | Assigned To |
|----|-------|--------|-------------|
| WP-001 | Apply native union type to `ClassCache::findClassesInFolder()` + manifest update | COMPLETE | Documentation |
| WP-002 | Quality gate validation (PHPStan + PHPUnit) | COMPLETE | Documentation |

---

## Metrics

| Metric | Value |
|--------|-------|
| PHPStan level | 9 |
| PHPStan files scanned | 528 |
| PHPStan errors | **0** |
| PHPUnit tests passed | **519 / 519** |
| PHPUnit assertions | 2 321 |
| Test failures | 0 |
| Test errors | 0 |
| Pre-existing incomplete tests | 1 (out of scope) |
| Pre-existing PHPUnit deprecations | 1 (out of scope) |
| Files modified | 4 |

### Files Modified

| File | Change |
|------|--------|
| `src/Mailcode/ClassCache.php` | Native `string\|FolderInfo` union type on `$folder`; redundant `@param` removed; `@phpstan-param` retained for `class-string` narrowing |
| `docs/agents/project-manifest/api-surface.md` | ClassCache block updated to reflect native union type signature |
| `docs/agents/project-manifest/constraints.md` | PHP 8.4 native union type rule added to Strict Typing section |
| `changelog.md` | v3.6.0 entry added documenting the fix |

---

## Quality Gate Results

All four acceptance criteria for WP-001 were confirmed met by implementation, QA, code-review, and documentation pipelines. WP-002 (validation-only) independently verified the same gates.

- **PHPStan level 9:** PASS — 528 files, 0 errors.
- **PHPUnit:** PASS — 519/519 tests, 2 321 assertions, 0 failures, 0 errors.

---

## Strategic Recommendations (Gold Nuggets)

### 1. Runtime Safety Improvement (Security/Correctness)

Replacing docblock-only type annotations with native union types is a **runtime safety upgrade**. Before this change, passing an invalid type to `findClassesInFolder()` would silently propagate; now PHP throws a `TypeError` at the method boundary. This pattern should be applied wherever docblock `@param` annotations carry type information that native PHP can express.

### 2. Manifest-Driven Convention Enforcement

Adding the native union type rule to `constraints.md` (with the `@phpstan-param` carve-out for class-string narrowing) ensures future contributors apply the pattern consistently. This is the preferred enforcement mechanism: rules in the manifest prevent both over-annotation and under-typing.

### 3. Non-blocking: `$instanceOf` Parameter Description

The `$instanceOf` parameter in `ClassCache::findClassesInFolder()` has a `@phpstan-param class-string|null` annotation but no prose `@param` description. Since `ClassCache` is an internal utility, this is acceptable; however, adding a brief description (e.g., *"Filter results to instances of this class"*) would improve IDE tooling for developers who navigate to the method.

---

## Pre-existing Issues (Out of Scope)

| Issue | Status |
|-------|--------|
| 1 PHPUnit deprecation notice | Pre-existing; not introduced by this plan |
| 1 PHPUnit incomplete test | Pre-existing; not introduced by this plan |

These are unchanged from before this plan and are outside its scope.

---

## Next Steps for Planner / Manager

1. **Broader union-type audit:** Apply the same native union type pattern to other methods across the codebase where `@param` docblocks carry type information expressible in PHP 8.0+ native syntax. This plan established the rule; a follow-up plan could automate or systematically apply it.
2. **$instanceOf description (low priority):** Add a prose `@param` description to `ClassCache::findClassesInFolder()::$instanceOf` for IDE benefit.
3. **Pre-existing incomplete test:** Track and address the one pre-existing incomplete PHPUnit test in a future quality hygiene plan.
4. **Version bump:** `changelog.md` has a v3.6.0 section. Ensure the version in `composer.json` and `tech-stack.md` is updated when releasing v3.6.0.
