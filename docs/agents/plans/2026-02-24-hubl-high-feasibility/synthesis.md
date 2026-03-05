# Synthesis Report — HubL High-Feasibility Translations

**Plan:** `2026-02-24-hubl-high-feasibility`  
**Date:** 2026-03-04  
**Status:** COMPLETE  
**Work Packages:** 7 / 7 COMPLETE

---

## Executive Summary

This project delivered four new HubL command translations that were previously unsupported (stubs or missing), expanded the HubL test suite from 4 to 10 dedicated test files, and brought all user-facing and agent-facing documentation fully up to date.

**Commands implemented:**

| Command | HubL Output Pattern | WP |
|---|---|---|
| `{if contains}` / `{if not-contains}` | `{% if "term" in var %}` / `{% if "term" not in var %}` | WP-001 |
| `{if begins-with}` / `{if ends-with}` | `{% if var is string_startingwith "pre" %}` / `{% if var[-N:] == "suffix" %}` | WP-002 |
| `{for: $RECORD in: $SOURCE}` | `{% for record in source %}` with optional `[:N]` / `[:var]` slice | WP-003 |
| `{showdate}` | `{{ var\|format_datetime("ldml") }}` with PHP-to-LDML conversion | WP-004 |

All four commands support their full feature set: `{if}` + `{elseif}` variants, case-insensitive flags, multi-term joining, and numeric/variable `break_at` and timezone arguments where applicable.

**Source files changed:**

- `src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php` — `_translateContains()` and `_translateSearch()` added
- `src/Mailcode/Translator/Syntax/HubL/ForTranslation.php` — complete rewrite from stub
- `src/Mailcode/Translator/Syntax/HubL/ShowDateTranslation.php` — complete rewrite from stub
- 10 test files under `tests/testsuites/Translator/HubL/` (6 new, 4 expanded pre-existing)
- `docs/user-guide/translate-hubl.md`
- `docs/agents/project-manifest/constraints.md`
- `docs/agents/project-manifest/file-tree.md`

---

## Metrics

| Metric | Before Project | After Project | Delta |
|---|---|---|---|
| PHPUnit tests | 309 | 315 | +6 |
| PHPUnit assertions | 1 352 | 1 376 | +24 |
| Test failures | 0 | 0 | 0 |
| PHPStan level 9 new violations | — | 0 | — |
| PHPStan pre-existing violations | 8 | 8 | 0 |
| HubL test files | 4 | 10 | +6 |
| Acceptance criteria met | — | 27 / 27 | 100 % |

All four pipeline stages (implementation, QA, code-review, documentation) passed with `PASS` on first attempt across all seven work packages. Zero rework cycles were required.

---

## Strategic Recommendations (Gold Nuggets)

The following cross-cutting observations were surfaced during code review and QA. None were blocking, but each carries a measurable future maintenance cost.

### Priority: Medium

**1. `$caseSensitive` parameter is semantically inverted**  
In `HubL/Base/AbstractIfBase.php` and the mirrored `ApacheVelocity/Base/AbstractIfBase.php`, the `$caseSensitive` parameter carries the value of `isCaseInsensitive()` — i.e., `true` means *case-insensitive*. Both implementations add inline comments to document this quirk, but the mental inversion tax accumulates with every future touch. A dedicated cleanup ticket should rename the parameter to `$caseInsensitive` across both files and all four call-sites (`IfTranslation.php`, `ElseIfTranslation.php` in both syntaxes) simultaneously.

**2. `TimezoneInterface` is missing `hasExplicitTimezone()`**  
`getTimezoneToken()` creates a default token on demand, mutating the command's params. To detect whether a timezone was *explicitly* provided, `ShowDateTranslation` must inspect `$command->getParams()->getInfo()->getTokenByParamName(TimezoneInterface::PARAMETER_NAME)` directly — a fragile introspection. Adding `hasExplicitTimezone()` to `TimezoneInterface` (implemented via the same token lookup) would encapsulate the pattern and prevent mis-implementations in future translators.

### Priority: Low

**3. `renderEncodings()` wrapping contract is undocumented at the base class level**  
The method must be called on the *inner* expression (without the `{{ }}` wrapper). This is non-obvious — an initial naive implementation applied it to the full `{{ ... }}` output, which would produce invalid HubL. A single `@note` in `BaseHubLCommandTranslation::renderEncodings()` would prevent the same trap for future translator authors.

**4. `strtolower` / `mb_strtolower` inconsistency in `AbstractIfBase`**  
`_translateContains()` uses `mb_strtolower()` while `_translateSearch()` uses `strtolower()`. Both are functionally correct for current inputs, but alignment on `mb_strtolower` throughout would be safer for multi-byte search terms.

**5. Two unused imports in `ForTranslation.php`**  
`Mailcode_Parser_Statement_Tokenizer_Token_Number` and `Mailcode_Parser_Statement_Tokenizer_Token_Variable` are leftover from a draft using token introspection. They should be removed.

**6. Test namespace inconsistency**  
Legacy ElseIf test files (`ElseIfVariableTests.php`, `ElseIfEmptyTests.php`, etc.) use the bare namespace `testsuites\Translator\HubL`. All WP-005 files correctly use `MailcodeTests\Translator\HubL`. A housekeeping pass should harmonise the legacy files.

**7. Pre-existing `ShowURLTests.php` PHPStan violation**  
`tests/testsuites/Translator/HubL/ShowURLTests.php` line 77 carries a `deadCode.unreachable` violation that predates this project. It is the sole cause of the project not reaching a clean PHPStan level-9 zero-error state. Resolve in a short cleanup pass.

**8. ★ Added annotation policy for `file-tree.md`**  
The file-tree now mixes annotated new files with unannotated pre-existing files in the same directory. Establishing a policy — e.g., strip `★ Added` annotations after each plan's review cycle — will keep the manifest readable long-term.

---

## Next Steps

The following items are recommended for the next planning cycle, in priority order:

1. **Cleanup ticket (Code):** Rename `$caseSensitive` → `$caseInsensitive` in both HubL and Velocity `AbstractIfBase` implementations and all call-sites.
2. **Interface enhancement:** Add `hasExplicitTimezone()` to `TimezoneInterface`; implement in participating command classes.
3. **Base class docblock:** Add `@note` to `BaseHubLCommandTranslation::renderEncodings()` to document the inner-expression wrapping contract.
4. **Housekeeping:** Fix `strtolower` inconsistency, remove unused imports from `ForTranslation.php`, harmonise test namespaces, and resolve `ShowURLTests.php` PHPStan violation.
5. **HubL medium-feasibility commands:** If a medium-feasibility plan exists, it is now unblocked — the high-feasibility gap is fully closed.

---

## Coverage Status After This Project

The following HubL commands were **newly added** to the supported set:

- `{if contains}` / `{if not-contains}` ✓  
- `{if begins-with}` / `{if ends-with}` ✓  
- `{elseif contains}` / `{elseif not-contains}` / `{elseif begins-with}` / `{elseif ends-with}` ✓  
- `{for}` with numeric and variable `break_at` ✓  
- `{showdate}` with PHP-to-LDML conversion, all timezone paths, and URL encoding ✓  

`{break}` inside `{for}` blocks and list-contains sub-types (`{if list-contains}`) remain intentionally unsupported and are documented as such in `translate-hubl.md`.
