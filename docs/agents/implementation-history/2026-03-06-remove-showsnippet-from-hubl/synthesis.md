# Project Synthesis Report

**Plan:** Remove `{showsnippet}` from HubL Translation  
**Date:** 2026-03-06  
**Status:** COMPLETE  
**Work Packages:** 5 / 5 COMPLETE  

---

## Executive Summary

The project corrected a semantic bug in the HubL translation layer where `{showsnippet}` was being translated to `{{ var }}` — a plain variable reference — rather than signalling that the command is unsupported. HubL has no server-side dictionary infrastructure equivalent to Apache Velocity's `DictionaryTool`, making a real translation impossible.

An important architectural constraint was discovered during implementation: `BaseSyntax::createTranslator()` throws a hard `Mailcode_Translator_Exception` for any missing translation class, so the file could not simply be deleted. Instead, the file was converted to a "not supported" stub following the established `BreakTranslation` pattern, emitting the HubL comment `{# !showsnippet is not supported in HubL! #}` for any `{showsnippet}` command variant.

Alongside the code change, all relevant documentation layers were updated: the HubL user guide, the general command reference, and the project manifest.

---

## Deliverables

| WP | Title | Outcome |
|----|-------|---------|
| WP-001 | Replace HubL `ShowSnippetTranslation.php` with a not-supported stub | ✅ PASS |
| WP-002 | Update `translate-hubl.md` — move `{showsnippet}` to Not Supported | ✅ PASS |
| WP-003 | Add prerequisite note to `mailcode-documentation.md` | ✅ PASS |
| WP-004 | Verify `constraints.md` HubL coverage table | ✅ PASS |
| WP-005 | Full test suite and static analysis validation | ✅ PASS |

### Files Modified

| File | Change |
|------|--------|
| `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php` | Replaced incorrect `{{ var }}` translation with not-supported stub |
| `docs/user-guide/translate-hubl.md` | Moved `{showsnippet}` from Fully Supported to Not Supported list |
| `docs/user-guide/mailcode-documentation.md` | Added Prerequisite callout (DictionaryTool + HubL limitation) before first `{showsnippet}` code example |
| `docs/agents/project-manifest/constraints.md` | Updated HubL coverage section to document the stub-class pattern and the `BaseSyntax::createTranslator()` architectural constraint |

---

## Metrics

| Metric | Value |
|--------|-------|
| Tests Passed | 519 / 519 |
| Tests Failed | 0 |
| PHPStan Level | 9 |
| PHPStan Errors | 0 |
| Security Issues | 0 |
| PHPStan Files Checked | 528 |

---

## Strategic Recommendations (Gold Nuggets)

### 1. 🔴 HIGH — Schedule a HubL Manifest Audit WP (Technical Debt)

`docs/agents/project-manifest/constraints.md` lists only **6 HubL commands** in its Translation Coverage section (line 64), while `src/Mailcode/Translator/Syntax/HubL/` contains **17 translation class files**. The missing commands include `shownumber`, `showphone`, `mono`, `code`, `comment`, `else`, `end`, and `break`. Any agent performing manifest-first lookups will incorrectly conclude HubL does not support these commands.

**Recommended action:** Create a dedicated manifest audit WP that:
- Enumerates all 17 HubL translation files
- Splits the coverage table into two clear columns: **(1) Fully Translated** and **(2) Stub / Not Supported** (currently: `break`, `showsnippet`)
- Completes this before the next manifest-driven agent cycle to prevent false negatives in "does HubL support X?" queries

This was flagged independently across 4 pipelines and every WP from WP-004 onwards.

---

### 2. 🟡 MEDIUM — Add `HubLShowSnippetTests.php` Regression Guard

No dedicated test file exists for HubL `{showsnippet}` translation. The stub behavior (emitting `{# !showsnippet is not supported in HubL! #}` for all command variants) is now the **permanent intended behavior**. Without a test, it could be silently overwritten by a future translator refactor.

**Recommended action:** Create `tests/testsuites/Translator/HubL/ShowSnippetTests.php` asserting the exact stub string for all 6 known `ShowSnippet` command variants (basic, URL-encoding, URL-decoding, no-HTML, with-namespace, no-HTML + URL-encoded). This was flagged in 3 consecutive pipelines (Developer → QA → Reviewer → Documentation on WP-001 and WP-005).

---

### 3. 🟡 MEDIUM — Architectural: Add `isCommandSupported()` Hook to `BaseSyntax`

`BaseSyntax::createTranslator()` (lines 47–49) throws a hard `Mailcode_Translator_Exception` when a translation class file is missing. This forces the creation of empty stub files for every unsupported command in every syntax (currently two such stubs in HubL: `BreakTranslation.php` and `ShowSnippetTranslation.php`).

**Recommended action:** Add an optional `isCommandSupported()` method or an unsupported-commands registry to `BaseSyntax`, allowing unsupported commands to fall back to a stub comment automatically. This eliminates the conceptual friction of files whose sole purpose is to exist, and removes the risk that a future developer accidentally adds real translation logic to a stub.

---

### 4. 🟢 LOW — Back-fill `BreakTranslation.php` Docblock

`ShowSnippetTranslation.php` now has a richer docblock than its template (`BreakTranslation.php`) — it explicitly states *why* the command cannot be translated (no server-side dictionary infrastructure in HubL) and cross-references the canonical pattern. This is a positive quality delta.

**Recommended action:** Back-fill `BreakTranslation.php` with a similar rationale paragraph (e.g., *"HubL for-loops do not support early termination"*) to establish a consistent convention for all future stub translation classes.

---

### 5. 🟢 LOW — Housekeeping: Clear Residual `★ Added` Markers in `file-tree.md`

`file-tree.md` contains residual `★ Added` annotation markers on lines 33, 56, 98, 105, and 154 from earlier plan cycles. Per the annotation policy in `constraints.md`, these should be removed in a dedicated housekeeping pass.

---

## Next Steps for the Planner / Project Manager

1. **Immediately:** Schedule the **HubL Manifest Audit WP** (see Recommendation #1). This is a prerequisite for any future manifest-driven agent cycle.
2. **Short-term:** Create a follow-up WP to add `HubLShowSnippetTests.php` (Recommendation #2).
3. **Medium-term:** Evaluate the `BaseSyntax::isCommandSupported()` architectural improvement (Recommendation #3) as part of the next translator refactor cycle.
4. **Backlog:** `BreakTranslation.php` docblock improvement and `file-tree.md` marker cleanup (Recommendations #4–5) can be batched into a general housekeeping WP.
