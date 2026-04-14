# Plan

## Summary

Implement all eight strategic recommendations surfaced during the code review and QA phases of the `2026-02-24-hubl-high-feasibility` project. These are non-blocking housekeeping and interface-enhancement items that reduce future maintenance cost, improve code clarity, and eliminate the remaining pre-existing PHPStan violation. The items span two priority tiers (Medium × 2, Low × 6) and touch translator base classes, a validation interface/trait pair, a base translation docblock, one source file cleanup, and a test-namespace harmonisation pass.

## Architectural Context

The changes affect the **Translator** and **Validation** layers of Mailcode:

- **Translator base classes** — `src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php` and `src/Mailcode/Translator/Syntax/ApacheVelocity/Base/AbstractIfBase.php` define shared if/elseif translation logic. Both expose a `$caseSensitive` parameter that semantically carries the *opposite* meaning (value comes from `isCaseInsensitive()`).
- **Translator call-sites** — Four concrete classes call `_translateContains()` and `_translateSearch()`:
  - `src/Mailcode/Translator/Syntax/HubL/IfTranslation.php`
  - `src/Mailcode/Translator/Syntax/HubL/ElseIfTranslation.php`
  - `src/Mailcode/Translator/Syntax/ApacheVelocity/IfTranslation.php`
  - `src/Mailcode/Translator/Syntax/ApacheVelocity/ElseIfTranslation.php`
- **Apache Velocity ContainsStatementBuilder** — `src/Mailcode/Translator/Syntax/ApacheVelocity/Contains/ContainsStatementBuilder.php` stores and consumes the `$caseSensitive` property.
- **TimezoneInterface / TimezoneTrait** — `src/Mailcode/Interfaces/Commands/Validation/TimezoneInterface.php` and `src/Mailcode/Traits/Commands/Validation/TimezoneTrait.php` define timezone support. The only implementing command is `ShowDate`.
- **BaseHubLCommandTranslation** — `src/Mailcode/Translator/Syntax/BaseHubLCommandTranslation.php` defines `renderEncodings()`.
- **ForTranslation** — `src/Mailcode/Translator/Syntax/HubL/ForTranslation.php` has two unused imports.
- **HubL test files** — 24 files under `tests/testsuites/Translator/HubL/` use three inconsistent namespace styles.
- **ShowURLTests** — `tests/testsuites/Translator/HubL/ShowURLTests.php` has a pre-existing PHPStan `deadCode.unreachable` violation at line 77.

## Approach / Architecture

The eight recommendations map to five discrete work-package groups:

1. **WP-001: Rename `$caseSensitive` → `$caseInsensitive`** (Medium priority)
   Rename the parameter in both `AbstractIfBase` classes, the `ContainsStatementBuilder`, and remove all inline "misnamed" comments. The call-sites already pass `$command->isCaseInsensitive()`, so no call-site value changes are needed — only the parameter name in the method signature and internal usages.

2. **WP-002: Add `hasExplicitTimezone()` to TimezoneInterface** (Medium priority)
   Add the method to the interface. Implement it in `TimezoneTrait` using the same token-lookup pattern currently duplicated in `ShowDateTranslation`. Then refactor `ShowDateTranslation` to call the new method.

3. **WP-003: Document `renderEncodings()` wrapping contract** (Low priority)
   Add a `@note` docblock to `BaseHubLCommandTranslation::renderEncodings()` explaining that the method must be called on the inner expression (without `{{ }}`).

4. **WP-004: Source file cleanup** (Low priority, three sub-items)
   - **4a:** Replace `strtolower()` with `mb_strtolower()` in `_translateSearch()` of `HubL/Base/AbstractIfBase.php`.
   - **4b:** Remove the two unused imports from `HubL/ForTranslation.php`.
   - **4c:** Resolve the `deadCode.unreachable` PHPStan violation in `ShowURLTests.php` (line 77 — code after `markTestIncomplete()`).

5. **WP-005: Test namespace harmonisation & file-tree annotation policy** (Low priority)
   - **5a:** Change all HubL test file namespaces to the canonical `MailcodeTests\Translator\HubL` (lowercase 'c'). Files currently using `testsuites\Translator\HubL` (7 files) and `MailCodeTests\Translator\HubL` (4 files) must be updated.
   - **5b:** Strip all `★ Added` annotations from `file-tree.md` to establish the post-review cleanup policy.

## Rationale

- **`$caseSensitive` rename** eliminates a persistent mental inversion tax. The call-sites already use `isCaseInsensitive()`, so the rename is a pure clarity improvement with zero behavioral change.
- **`hasExplicitTimezone()`** encapsulates a fragile introspection pattern that `ShowDateTranslation` currently does manually. Any future translator for a timezone-aware command would need to rediscover this pattern.
- **`renderEncodings()` docblock** prevents a documented trap (applying it to `{{ ... }}` output produces invalid HubL).
- **`mb_strtolower` alignment** is a defensive measure for multi-byte search terms.
- **Unused imports / dead code** are standard hygiene to maintain PHPStan level-9 compliance.
- **Namespace harmonisation** eliminates a three-way inconsistency that confuses autoloaders and IDEs.

## Detailed Steps

### WP-001: Rename `$caseSensitive` → `$caseInsensitive`

1. In `src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php`:
   - Rename the `$caseSensitive` parameter to `$caseInsensitive` in `_translateContains()` (line ~176) and `_translateSearch()` (line ~231).
   - Remove the two `// $caseSensitive is misnamed: true means case-insensitive.` comments.
   - Update all internal references from `$caseSensitive` to `$caseInsensitive`.

2. In `src/Mailcode/Translator/Syntax/ApacheVelocity/Base/AbstractIfBase.php`:
   - Rename `$caseSensitive` to `$caseInsensitive` in `_translateContains()` (line ~192) and `_translateSearch()` (line ~202).
   - Update all internal references.

3. In `src/Mailcode/Translator/Syntax/ApacheVelocity/Contains/ContainsStatementBuilder.php`:
   - Rename the `$caseSensitive` property (line 19) and constructor parameter (line 35, 46) to `$caseInsensitive`.
   - Update the usage in `renderRegex()` (line ~176).
   - Update the `@param` docblock.

4. **No call-site changes needed** — the four `IfTranslation.php` / `ElseIfTranslation.php` files already pass `$command->isCaseInsensitive()` by value. The parameter name in the method signature changes, but the passed argument and its semantics remain identical.

5. Run `composer test` and `composer analyze` to confirm zero breakage.

### WP-002: Add `hasExplicitTimezone()` to TimezoneInterface

1. In `src/Mailcode/Interfaces/Commands/Validation/TimezoneInterface.php`:
   - Add method signature: `public function hasExplicitTimezone(): bool;`

2. In `src/Mailcode/Traits/Commands/Validation/TimezoneTrait.php`:
   - Implement `hasExplicitTimezone()` using the existing token-lookup pattern:
     ```php
     public function hasExplicitTimezone(): bool
     {
         $params = $this->requireParams();
         $token = $params->getInfo()->getTokenByParamName(TimezoneInterface::PARAMETER_NAME);
         return $token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
             || $token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable;
     }
     ```

3. In `src/Mailcode/Translator/Syntax/HubL/ShowDateTranslation.php`:
   - Replace the manual introspection block (lines 46–58) with a call to `$command->hasExplicitTimezone()`.
   - When explicit timezone is present, use `$command->getTimezoneToken()` to retrieve the token (safe to call now because we confirmed one exists).
   - Remove the now-unnecessary `TimezoneInterface` import if no longer directly referenced.

4. Run `composer test` and `composer analyze`.

### WP-003: Document `renderEncodings()` wrapping contract

1. In `src/Mailcode/Translator/Syntax/BaseHubLCommandTranslation.php`:
   - Add a `@note` to the `renderEncodings()` method docblock:
     ```
     @note The $statement parameter must be the INNER expression only (e.g., `var|filter`),
     without the surrounding `{{ }}` wrapper. Applying this method to a fully wrapped
     output (e.g., `{{ var }}`) will produce invalid HubL like `{{ {{ var }}|urlencode }}`.
     ```

2. No tests needed — documentation-only change.

### WP-004: Source file cleanup

**4a: `strtolower` → `mb_strtolower` in HubL `_translateSearch()`**

1. In `src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php`, `_translateSearch()` method (~line 241):
   - Replace `strtolower($rawTerm)` with `mb_strtolower($rawTerm)`.

**4b: Remove unused imports from `ForTranslation.php`**

1. In `src/Mailcode/Translator/Syntax/HubL/ForTranslation.php`:
   - Remove `use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Number;` (line 12).
   - Remove `use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;` (line 13).

**4c: Resolve `ShowURLTests.php` PHPStan violation**

1. In `tests/testsuites/Translator/HubL/ShowURLTests.php`, method `test_nestedCommands()` (line 77):
   - The code after `$this->markTestIncomplete(...)` is unreachable. Move the `markTestIncomplete()` call to the very beginning of the method (before any variable assignments), or add `return;` immediately after it to satisfy PHPStan's dead-code analysis.
   - Preferred approach: place `$this->markTestIncomplete(...)` as the first statement (the method body after it will remain for future reference but PHPStan recognises `markTestIncomplete` as `@noreturn`).

2. Run `composer analyze` to confirm the pre-existing violation is resolved.

### WP-005: Test namespace harmonisation & file-tree annotation cleanup

**5a: Namespace harmonisation**

The canonical namespace is `MailcodeTests\Translator\HubL` (lowercase 'c' in "code"). Update these files:

Files using `testsuites\Translator\HubL` (7 files):
- `tests/testsuites/Translator/HubL/ShowURLTests.php`
- `tests/testsuites/Translator/HubL/ElseIfBiggerThanTests.php`
- `tests/testsuites/Translator/HubL/ElseIfEmptyTests.php`
- `tests/testsuites/Translator/HubL/ElseIfSmallerThanTests.php`
- `tests/testsuites/Translator/HubL/ShowVariableTests.php`
- `tests/testsuites/Translator/HubL/ElseIfVariableTests.php`
- `tests/testsuites/Translator/HubL/ElseIfEqualsNumberTests.php`

Files using `MailCodeTests\Translator\HubL` (uppercase 'C', 4 files):
- `tests/testsuites/Translator/HubL/ShowEncodedTests.php`
- `tests/testsuites/Translator/HubL/IfEqualsNumberTests.php`
- `tests/testsuites/Translator/HubL/IfBiggerThanTests.php`
- `tests/testsuites/Translator/HubL/IfEmptyTests.php`

All 11 files → change namespace to `MailcodeTests\Translator\HubL`.

**5b: Strip `★ Added` annotations from `file-tree.md`**

1. In `docs/agents/project-manifest/file-tree.md`:
   - Remove all `★ Added: ...` suffixes from file entries.
   - Add a brief note at the top of the file establishing the convention: annotations are stripped after each plan's review cycle.

2. Run `composer test` to confirm namespace changes don't break tests.

## Dependencies

- WP-001 through WP-005 are fully independent and can be executed in any order or in parallel.
- WP-004c (ShowURLTests fix) and WP-005a (namespace fix for ShowURLTests) both touch the same file. If executed in the same pass, combine the edits.

## Required Components

### Modified source files
- `src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php` — WP-001, WP-004a
- `src/Mailcode/Translator/Syntax/ApacheVelocity/Base/AbstractIfBase.php` — WP-001
- `src/Mailcode/Translator/Syntax/ApacheVelocity/Contains/ContainsStatementBuilder.php` — WP-001
- `src/Mailcode/Interfaces/Commands/Validation/TimezoneInterface.php` — WP-002
- `src/Mailcode/Traits/Commands/Validation/TimezoneTrait.php` — WP-002
- `src/Mailcode/Translator/Syntax/HubL/ShowDateTranslation.php` — WP-002
- `src/Mailcode/Translator/Syntax/BaseHubLCommandTranslation.php` — WP-003
- `src/Mailcode/Translator/Syntax/HubL/ForTranslation.php` — WP-004b

### Modified test files
- `tests/testsuites/Translator/HubL/ShowURLTests.php` — WP-004c, WP-005a
- 10 additional HubL test files — WP-005a (namespace only)

### Modified documentation files
- `docs/agents/project-manifest/file-tree.md` — WP-005b
- `docs/agents/project-manifest/api-surface.md` — WP-002 (new `hasExplicitTimezone()` method)
- `docs/agents/project-manifest/constraints.md` — WP-005b (annotation policy note)

## Assumptions

- The canonical test namespace is `MailcodeTests\Translator\HubL` (matching the convention used by the most recently added test files in the HubL feasibility project).
- `markTestIncomplete()` is annotated as `@noreturn` in PHPUnit ≥ 9.6, so PHPStan will stop flagging dead code after it once it appears as the first statement.
- No external consumer depends on the `$caseSensitive` parameter *by name* (it is `protected`, not `public`).
- The `ContainsStatementBuilder` property rename is safe because the class is internal to the Apache Velocity translator and not extended externally.

## Constraints

- All changes must maintain PHPStan level-9 compliance (target: zero violations, down from the current 8 pre-existing).
- All changes must maintain `declare(strict_types=1)` in every file.
- The existing test suite must continue to pass with zero failures after each WP.
- No behavioral changes to translation output — all changes are rename/refactor/documentation only.

## Out of Scope

- HubL medium-feasibility command translations (separate plan).
- Broader test-namespace harmonisation beyond the `Translator/HubL/` directory.
- Any changes to the Apache Velocity test files' namespaces.
- Version bump or changelog entry (handled post-merge).

## Acceptance Criteria

- [ ] `$caseSensitive` parameter renamed to `$caseInsensitive` in all 3 source files, with all "misnamed" comments removed.
- [ ] `TimezoneInterface` declares `hasExplicitTimezone(): bool`.
- [ ] `TimezoneTrait` implements `hasExplicitTimezone()`.
- [ ] `ShowDateTranslation` uses `$command->hasExplicitTimezone()` instead of manual token inspection.
- [ ] `renderEncodings()` has a `@note` docblock documenting the inner-expression contract.
- [ ] `_translateSearch()` in HubL `AbstractIfBase` uses `mb_strtolower()`.
- [ ] Two unused imports removed from `ForTranslation.php`.
- [ ] `ShowURLTests::test_nestedCommands()` no longer triggers PHPStan `deadCode.unreachable`.
- [ ] All 24 HubL test files use namespace `MailcodeTests\Translator\HubL`.
- [ ] `file-tree.md` has no `★ Added` annotations and includes a cleanup-policy note.
- [ ] `composer test` passes with 0 failures.
- [ ] `composer analyze` reports fewer violations than the pre-project baseline of 8.
- [ ] `api-surface.md` and `constraints.md` updated to reflect new method and annotation policy.

## Testing Strategy

- **Unit tests:** Run the full PHPUnit suite (`composer test`) after each WP to catch regressions.
- **Static analysis:** Run `composer analyze` after WP-001 and WP-004 to verify PHPStan compliance.
- **Manual review:** Confirm that the `$caseInsensitive` rename does not change any translation output by inspecting the existing contains/search test assertions (they test output strings, not parameter names).
- **Namespace verification:** After WP-005a, confirm all 24 test files load and execute by running `composer test-suite -- Translator`.

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **`$caseSensitive` rename breaks a subclass or reflection-based code** | The parameter is `protected` on internal base classes with no external consumers. Grep confirms no reflection-based access. |
| **`hasExplicitTimezone()` returns false positive after `getTimezoneToken()` was called (which creates a default)** | The implementation checks the raw token store via `getTokenByParamName()` *before* any lazy creation occurs, matching the existing pattern in `ShowDateTranslation`. Add a test case for this edge case. |
| **Namespace change breaks PHPUnit autoloading** | PHPUnit uses classmap autoloading (`tests/assets/` classmap). Test classes are discovered by file path via `phpunit.xml` suite definitions, not by namespace. Verify with `composer test`. |
| **`markTestIncomplete()` reorder changes test semantics** | `markTestIncomplete()` is a hard stop — moving it earlier only removes dead setup code. The test still reports as incomplete. |
| **`mb_strtolower` locale-dependent behavior** | `mb_strtolower` without explicit encoding uses the default internal encoding (UTF-8). This matches the existing `_translateContains()` usage in the same file. |
