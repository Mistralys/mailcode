# Plan

## Summary

Resolve all strategic observations and recommendations from the `2026-03-05-hubl-high-feasibility-rework-1` synthesis. This plan addresses five areas: (1) eliminate the 113 PHPUnit class-not-found warnings by standardising namespaces across the entire non-HubL test suite, (2) resolve all 7 remaining PHPStan violations, (3) audit `api-surface.md` for PHP 8 union type syntax and convert to PHP 7.4-compatible docblock form, (4) add an inline code comment to `TimezoneTrait.hasExplicitTimezone()` documenting the fresh-lookup rationale, and (5) fix the `ContainsStatementBuilder` constructor docblock legacy naming.

## Architectural Context

### Test Suite Namespace Convention (Established by WP-005)

The canonical pattern for test files is:

```
namespace MailcodeTests\{Suite}\{SubDir};
class {ClassName}Tests extends {BaseTestCase}
```

Example (from `tests/testsuites/Translator/HubL/ForTests.php`):

```php
namespace MailcodeTests\Translator\HubL;
final class ForTests extends HubLTestCase
```

Base test cases reside in `tests/assets/classes/`:
- `MailcodeTestCase` — global namespace (no namespace)
- `VelocityTestCase` — `MailcodeTestClasses` namespace
- `FactoryTestCase` — `MailcodeTestClasses` namespace
- `HubLTestCase` — `MailcodeTestClasses` namespace

### Current State of Non-HubL Test Files

| Category | Count | Description |
|---|---|---|
| **No namespace declaration** | 113 | Use old-style underscore-prefixed class names (e.g., `Factory_BreakTests`, `Translator_Velocity_ElseIfTests`). These cause the 113 PHPUnit warnings. |
| **Wrong namespace (`testsuites\...`)** | 20 | Use the non-canonical `testsuites\Commands\Types`, `testsuites\Parser`, etc. These are invisible to PHPUnit because their class names happen to match, but the namespace is still incorrect. |
| **Correct namespace (`MailcodeTests\...`)** | 18 | Already follow the canonical pattern. No changes needed. |
| **Other (`Mailcode`)** | 1 | `Isolation/IsolatedTests.php` uses `namespace Mailcode;`. Must become `MailcodeTests\Isolation`. |
| **Total non-HubL test files** | 152 | |

Files affected: 134 (113 + 20 + 1).

### File Naming Convention

In addition to namespace issues, 113 files lack the `*Tests.php` suffix (e.g., `Break.php` instead of `BreakTests.php`). These must be renamed to match the established convention and so PHPUnit can discover them correctly within namespaces.

### PHPStan Violations (7 remaining)

| # | File | Line | Error | Category |
|---|---|---|---|---|
| 1 | `src/Mailcode/ClassCache.php` | 36 | `$instanceOf` param: `string\|null` given, `class-string\|null` expected | Type refinement |
| 2 | `src/Mailcode/Commands/LogicKeywords.php` | 176 | Strict comparison `=== between string and false` always evaluates same way | Dead-code branch |
| 3 | `src/Mailcode/Commands/Normalizer/ProtectedContent.php` | 61 | Call to undefined method `getContent()` on `Mailcode_Commands_Command` | Missing type narrowing |
| 4 | `src/Mailcode/Factory/CommandSets/Set/Show.php` | 110 | `@param string\|null` not subtype of native type `string` | Docblock mismatch |
| 5 | `src/Mailcode/Parser.php` | 179 | Possibly invalid array key type `int\|string\|null` | Null-safety |
| 6 | `tools/extractPhoneCountries.php` | 137 | Possibly invalid array key type `mixed` | Type narrowing |
| 7 | `tools/extractPhoneCountries.php` | 140 | Return type mismatch: `array<mixed>` vs `array<string, string>` | Type annotation |

### api-surface.md PHP 8 Syntax

One confirmed instance: `ClassCache::findClassesInFolder()` documents `string|FolderInfo $folder` as a native union type parameter. The actual code uses an untyped `$folder` with a `@param string|FolderInfo` docblock (PHP 7.4 compatible). The manifest must match the real signature.

## Approach / Architecture

The work is divided into 5 major work packages, ordered by priority and risk:

1. **WP-001 — Namespace Standardisation Sweep** (High priority, high scope): Fix all 134 non-HubL test files. This is the largest work package. The approach mirrors WP-005 from the prior plan — add/correct namespace declarations and rename class names. Files without the `*Tests.php` suffix must be renamed. This must be done per test-suite directory to keep changes reviewable.

2. **WP-002 — PHPStan Violation Resolution** (High priority, moderate scope): Fix all 7 PHPStan violations to achieve a clean analysis at level 9.

3. **WP-003 — api-surface.md Type Syntax Audit** (Medium priority, documentation-only): Correct the `ClassCache::findClassesInFolder()` signature and audit for any other PHP 8 syntax.

4. **WP-004 — TimezoneTrait Inline Comment** (Low priority, documentation-only): Add a code comment explaining why `hasExplicitTimezone()` does a fresh token lookup instead of using the `$timezoneToken` property.

5. **WP-005 — ContainsStatementBuilder Docblock Fix** (Low priority, trivial): Update the constructor PHPDoc `@description` from the legacy underscore-class name to the namespaced name.

## Rationale

- **WP-001 first** because the 113 warnings are the most visible technical debt and the same fix pattern has already been validated (WP-005 of the prior plan). Doing it suite-by-suite allows incremental verification.
- **WP-002 second** because PHPStan level 9 compliance is a project constraint (`constraints.md`). Fixing these prevents baseline drift.
- **WP-003 through WP-005** are low-risk documentation/comment-only changes that round out the cleanup.

## Detailed Steps

### WP-001 — Namespace Standardisation Sweep

For **each** test suite directory below, perform the following per-file transformation:

**Transformation rules:**

1. **Add or correct the namespace declaration** to `MailcodeTests\{SuiteDir}\{SubDir}`:
   - `tests/testsuites/Commands/` → `namespace MailcodeTests\Commands;`
   - `tests/testsuites/Commands/Types/` → `namespace MailcodeTests\Commands\Types;`
   - `tests/testsuites/Factory/` → `namespace MailcodeTests\Factory;`
   - `tests/testsuites/Factory/Commands/` → `namespace MailcodeTests\Factory\Commands;`
   - `tests/testsuites/Formatting/` → `namespace MailcodeTests\Formatting;`
   - `tests/testsuites/Highlighting/` → `namespace MailcodeTests\Highlighting;`
   - `tests/testsuites/Isolation/` → `namespace MailcodeTests\Isolation;`
   - `tests/testsuites/Mailcode/` → `namespace MailcodeTests\Mailcode;`
   - `tests/testsuites/Numbers/` → `namespace MailcodeTests\Numbers;`
   - `tests/testsuites/Parser/` → `namespace MailcodeTests\Parser;`
   - `tests/testsuites/PreProcessor/` → `namespace MailcodeTests\PreProcessor;`
   - `tests/testsuites/StringContainer/` → `namespace MailcodeTests\StringContainer;`
   - `tests/testsuites/Translator/` → `namespace MailcodeTests\Translator;`
   - `tests/testsuites/Translator/Velocity/` → `namespace MailcodeTests\Translator\Velocity;`
   - `tests/testsuites/Validator/` → `namespace MailcodeTests\Validator;`
   - `tests/testsuites/Variables/` → `namespace MailcodeTests\Variables;`

2. **Rename the class** to strip the suite prefix and use only the `{Name}Tests` suffix:
   - `Mailcode_CommandsTests` → `CommandsTests`
   - `Factory_BreakTests` → `BreakTests`
   - `Translator_Velocity_ElseIfTests` → `ElseIfTests`
   - `Formatting_GeneralTests` → `GeneralTests`
   - etc.

3. **Adjust the base class reference** where namespace changes require it:
   - Files extending `MailcodeTestCase` (global namespace) must use `\MailcodeTestCase` when inside a namespace.
   - Files extending `VelocityTestCase` or `FactoryTestCase` (in `MailcodeTestClasses`) need a `use MailcodeTestClasses\VelocityTestCase;` (or `FactoryTestCase`) import if not already present.

4. **Rename the file** to match the new class name with `Tests.php` suffix if the file doesn't already end in `Tests.php`:
   - `Break.php` → `BreakTests.php`
   - `ElseIf.php` → `ElseIfTests.php`
   - `Commands.php` → `CommandsTests.php`
   - `Factory.php` → `FactoryTests.php`
   - etc.

5. **Add `declare(strict_types=1);`** if missing (some older files omit it).

**Affected directories and file counts:**

| Directory | No namespace | Wrong namespace | Files to rename | Total affected |
|---|---|---|---|---|
| `Commands/` | 3 | 1 | 3 | 4 |
| `Commands/Types/` | 17 | 6 | 17 | 23 |
| `Factory/` | 2 | 0 | 2 | 2 |
| `Factory/Commands/` | 34 | 2 | 34 | 36 |
| `Formatting/` | 2 | 1 | 2 | 3 |
| `Highlighting/` | 1 | 0 | 1 | 1 |
| `Isolation/` | 0 | 0 (uses `Mailcode`) | 0 | 1 |
| `Mailcode/` | 1 | 0 | 1 | 1 |
| `Numbers/` | 0 | 1 | 0 | 1 |
| `Parser/` | 0 | 5 | 0 | 5 |
| `PreProcessor/` | 1 | 0 | 1 | 1 |
| `StringContainer/` | 1 | 0 | 1 | 1 |
| `Translator/` | 1 | 0 | 1 | 1 |
| `Translator/Velocity/` | 39 | 3 | 39 | 42 |
| `Validator/` | 0 | 1 | 0 | 1 |
| `Variables/` | 1 | 0 | 1 | 1 |

**Verification (after each directory batch):**
- Run `composer test` — all 315 tests must pass, warning count must decrease.
- Confirm no new PHPUnit deprecation warnings are introduced.

**Final verification:**
- Run `composer test` — 0 PHPUnit test runner warnings.

### WP-002 — PHPStan Violation Resolution

Fix each of the 7 violations:

1. **`ClassCache.php:36`** — Add a `@phpstan-param class-string|null $instanceOf` annotation to the `findClassesInFolder()` method docblock, or cast the parameter with an `@var` assertion.

2. **`LogicKeywords.php:176`** — `substr()` with `0, $pos` where `$pos >= 0` (it's the result of `strpos()` which only reaches this point if non-false) can never return `false`. Remove the dead `if($store === false)` branch, or add a `@phpstan-ignore-next-line` annotation if the defensive check is intentionally kept.

3. **`ProtectedContent.php:61`** — The `$command` property is typed as `Mailcode_Commands_Command`, but `getContent()` exists on `Mailcode_Interfaces_Commands_ProtectedContent`. Add a type assertion or narrow the constructor parameter type to `Mailcode_Commands_Command&ProtectedContent` intersection (PHPStan supports this in PHPDoc).

4. **`Show.php:110`** — The `@param string|null $snippetName` docblock contradicts the native `string` type hint. Remove the `|null` from the docblock (or if `null` is intentional, add the native `?string` hint).

5. **`Parser.php:179`** — `array_key_last()` can return `null` on an empty array. Add a guard check or assertion that `$this->stack` is non-empty before calling `array_key_last()`.

6. **`extractPhoneCountries.php:137`** — Add proper type annotations to the array iteration variable to resolve `mixed` key type.

7. **`extractPhoneCountries.php:140`** — Add explicit type casts or assertions in the return statement to ensure the return type matches `array<string, string>`.

**Verification:**
- Run `composer analyze` — 0 errors at level 9.

### WP-003 — api-surface.md Type Syntax Audit

1. Correct the `ClassCache::findClassesInFolder()` documented signature from:
   ```php
   string|FolderInfo $folder,
   ```
   to:
   ```php
   /** @param string|FolderInfo $folder */
   $folder,
   ```
   (Matching the actual PHP 7.4 code style where the union is in the docblock, not a native type.)

2. Scan the entire `api-surface.md` for any other PHP 8 union type parameters in documented signatures and convert them to the docblock-annotated form.

**Verification:**
- Manual review confirms all signatures in `api-surface.md` are PHP 7.4 compatible.

### WP-004 — TimezoneTrait Inline Comment

Add an inline comment above the `hasExplicitTimezone()` method body in `src/Mailcode/Traits/Commands/Validation/TimezoneTrait.php` explaining:
- The method performs a fresh token lookup via `getTokenByParamName()` rather than reading the `$timezoneToken` property.
- This is intentional because `$timezoneToken` is only populated during `validateSyntax_check_timezone()` (parse-time), and `hasExplicitTimezone()` must be safe to call at translate-time before or without validation having run.

**File:** `src/Mailcode/Traits/Commands/Validation/TimezoneTrait.php`

### WP-005 — ContainsStatementBuilder Docblock Fix

Update the constructor PHPDoc in `src/Mailcode/Translator/Syntax/ApacheVelocity/Contains/ContainsStatementBuilder.php` from:
```
Mailcode_Translator_Syntax_ApacheVelocity_Contains_StatementBuilder constructor.
```
to:
```
ContainsStatementBuilder constructor.
```

**File:** `src/Mailcode/Translator/Syntax/ApacheVelocity/Contains/ContainsStatementBuilder.php`

## Dependencies

- WP-001 must complete before final verification (full test suite run with 0 warnings).
- WP-002 is independent of WP-001 and can proceed in parallel.
- WP-003 through WP-005 are independent of all other WPs.

## Required Components

### Modified Files (by WP)

**WP-001:**
- 134 test files across `tests/testsuites/` (113 no-namespace + 20 wrong-namespace + 1 other-namespace)
- 113 files additionally need renaming to `*Tests.php` suffix

**WP-002:**
- `src/Mailcode/ClassCache.php`
- `src/Mailcode/Commands/LogicKeywords.php`
- `src/Mailcode/Commands/Normalizer/ProtectedContent.php`
- `src/Mailcode/Factory/CommandSets/Set/Show.php`
- `src/Mailcode/Parser.php`
- `tools/extractPhoneCountries.php`

**WP-003:**
- `docs/agents/project-manifest/api-surface.md`

**WP-004:**
- `src/Mailcode/Traits/Commands/Validation/TimezoneTrait.php`

**WP-005:**
- `src/Mailcode/Translator/Syntax/ApacheVelocity/Contains/ContainsStatementBuilder.php`

### Manifest Updates

- `constraints.md` — Document the canonical test namespace convention for all suites (extend the existing HubL-specific rule to cover the full test suite).
- `file-tree.md` — Update the file tree to reflect any renamed test files.
- `changelog.md` — Add entries for WP-001, WP-002.

## Assumptions

- The 18 test files already using `MailcodeTests\...` namespaces are correct and need no changes.
- `MailcodeTestCase` intentionally has no namespace (it lives in `tests/assets/classes/` and is autoloaded via classmap). Tests in a namespace will reference it as `\MailcodeTestCase`.
- The `Isolation/IsolatedTests.php` file's use of `namespace Mailcode;` is incorrect, not intentional.
- PHPUnit's classmap autoloading (`tests/assets/` in `composer.json`) will discover the renamed test files after a `composer dump-autoload`.
- All 315 tests currently pass; the namespace/rename changes are declaration-only and will not affect test logic.

## Constraints

- PHP >= 7.4 compatibility must be maintained (no native union types).
- PHPStan level 9 compliance must be achieved after WP-002.
- All 315 tests must continue to pass after every WP.
- `declare(strict_types=1)` must be present in all modified source files.
- The `MailcodeTests\` namespace prefix is the canonical standard (not `testsuites\`, not `MailCodeTests\`).

## Out of Scope

- Adding new test cases or test coverage.
- Modifying any non-test source code beyond the 6 PHPStan-violation files and the 2 documentation-comment files.
- Addressing the single PHPUnit deprecation or the 1 incomplete test.
- Namespacing the `MailcodeTestCase` base class itself (it is in the global namespace by convention).
- HubL test files (already standardised in the prior plan's WP-005).

## Acceptance Criteria

- `composer test` produces **0 PHPUnit test runner warnings** and **315 tests passing**.
- `composer analyze` produces **0 errors** at level 9.
- All test files under `tests/testsuites/` (excluding `Isolation/`) use the `MailcodeTests\{Suite}\{SubDir}` namespace pattern.
- All test files end in `*Tests.php`.
- `api-surface.md` contains no PHP 8 native union type syntax.
- `TimezoneTrait.hasExplicitTimezone()` has an inline comment explaining the fresh-lookup design decision.
- `ContainsStatementBuilder` constructor docblock uses the namespaced class name.
- `constraints.md` documents the full-test-suite canonical namespace convention.
- Changelog has entries for WP-001 and WP-002.

## Testing Strategy

- **Per-directory incremental testing** during WP-001: After each directory batch of namespace changes, run `composer test` and verify the warning count decreases and no tests break.
- **PHPStan after WP-002**: Run `composer analyze` and confirm 0 errors.
- **Final full pass**: After all WPs, run both `composer test` (0 warnings, 315 pass) and `composer analyze` (0 errors).

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **File renames break PHPUnit discovery** | The `composer.json` autoload-dev uses classmap on `tests/assets/` (not `tests/testsuites/`). PHPUnit discovers test files via `phpunit.xml` directory configuration. File renames with matching class names will be auto-discovered. Run `composer dump-autoload` after all renames. |
| **Namespace changes cause "class not found" at runtime** | Use `\MailcodeTestCase` (backslash-prefixed) when extending from a namespaced context. Add explicit `use` imports for `VelocityTestCase`, `FactoryTestCase`, `HubLTestCase`. Verify incrementally. |
| **PHPStan fixes introduce regressions** | Each fix is small and targeted. Run full test suite after each individual fix. |
| **Large number of files (134) increases merge conflict risk** | The changes are mechanical (namespace + class rename), not logic changes. Batch by directory and commit per-suite to keep diffs reviewable. |
| **Some test files may have internal class references that break** | Grep for old class names across the test suite after renaming to catch cross-references. |
