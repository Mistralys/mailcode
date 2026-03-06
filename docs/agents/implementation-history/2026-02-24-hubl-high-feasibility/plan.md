# Plan: Implement High-Feasibility HubL Translation Commands

## Summary

Implement all **high feasibility** HubL translation commands identified in the [research paper](../../research/2026-02-24-hubl-translation-coverage.md). This covers five commands/sub-types that currently output stub comments or are missing entirely:

1. **`{if contains:}` / `{if not-contains:}`** — string containment checks using HubL's `in` operator
2. **`{if begins-with:}`** — string prefix checks using HubL's `is string_startingwith` expression test
3. **`{if ends-with:}`** — string suffix checks using Jinjava string slicing (`variable[-N:]`)
4. **`{for}` loop** — iteration using `{% for X in Y %}` with `break_at:` via loop slicing
5. **`{showdate}`** — date formatting using HubL's `format_datetime` filter with PHP-to-LDML character conversion

All five apply equally to `{elseif}` variants where applicable (contains, not-contains, begins-with, ends-with share the same base class `AbstractIfBase`).

## Architectural Context

### Translation Layer Structure

Each target syntax lives in `src/Mailcode/Translator/Syntax/{SyntaxName}/`. Translation classes are auto-discovered by `ClassCache::findClassesInFolder()` — no manual registration needed.

**Key files involved:**

| File | Role |
|------|------|
| [src/Mailcode/Translator/Syntax/BaseHubLCommandTranslation.php](../../../../src/Mailcode/Translator/Syntax/BaseHubLCommandTranslation.php) | Base class for all HubL translation classes; provides `formatVariableName()`, `renderEncodings()`, `renderStringToNumber()` |
| [src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php](../../../../src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php) | Shared base for `IfTranslation` and `ElseIfTranslation`; contains `_translateContains()`, `_translateSearch()`, `_translateEmpty()`, `_translateVariable()`, `_translateNumberComparison()` |
| [src/Mailcode/Translator/Syntax/HubL/IfTranslation.php](../../../../src/Mailcode/Translator/Syntax/HubL/IfTranslation.php) | Per-subtype dispatch for `{if}`; delegates to `AbstractIfBase` methods |
| [src/Mailcode/Translator/Syntax/HubL/ElseIfTranslation.php](../../../../src/Mailcode/Translator/Syntax/HubL/ElseIfTranslation.php) | Per-subtype dispatch for `{elseif}`; delegates to `AbstractIfBase` methods |
| [src/Mailcode/Translator/Syntax/HubL/ForTranslation.php](../../../../src/Mailcode/Translator/Syntax/HubL/ForTranslation.php) | Currently a stub returning `{# ! for commands are not implemented ! #}` |
| [src/Mailcode/Translator/Syntax/HubL/ShowDateTranslation.php](../../../../src/Mailcode/Translator/Syntax/HubL/ShowDateTranslation.php) | Currently a stub returning `{# ! show date commands are not implemented ! #}` |

**Reference implementation (Apache Velocity — full coverage):**

| File | Relevance |
|------|-----------|
| [src/Mailcode/Translator/Syntax/ApacheVelocity/Base/AbstractIfBase.php](../../../../src/Mailcode/Translator/Syntax/ApacheVelocity/Base/AbstractIfBase.php) | Reference for `_translateContains()`, `_translateSearch()` |
| [src/Mailcode/Translator/Syntax/ApacheVelocity/Contains/ContainsStatementBuilder.php](../../../../src/Mailcode/Translator/Syntax/ApacheVelocity/Contains/ContainsStatementBuilder.php) | Reference for contains/not-contains rendering with multiple search terms |
| [src/Mailcode/Translator/Syntax/ApacheVelocity/ForTranslation.php](../../../../src/Mailcode/Translator/Syntax/ApacheVelocity/ForTranslation.php) | Reference for `{for}` with `break_at:` |
| [src/Mailcode/Translator/Syntax/ApacheVelocity/ShowDateTranslation.php](../../../../src/Mailcode/Translator/Syntax/ApacheVelocity/ShowDateTranslation.php) | Reference for PHP-to-LDML date character conversion table (reusable) |

### Pattern Summary

- **Contains/Search translations** are centralized in `AbstractIfBase` — both `IfTranslation` and `ElseIfTranslation` delegate to the same shared methods (`_translateContains()`, `_translateSearch()`). Implementing these once in `AbstractIfBase` covers both.
- **Standalone command translations** (For, ShowDate) each have their own translation class file.
- **Variable names** in HubL output must be lowercase (handled by `formatVariableName()` in `BaseHubLCommandTranslation`).
- **Encodings** (urlencode, urldecode) are handled by `renderEncodings()` wrapping the statement in `|urlencode` or `|urldecode`.
- **Tests** extend `HubLTestCase` (in `tests/assets/classes/HubLTestCase.php`) and use `runCommands()` with arrays of `{mailcode, expected, label}`.

## Approach / Architecture

### 1. Contains / NotContains — Modify `AbstractIfBase._translateContains()`

Replace the stub in `AbstractIfBase._translateContains()` with an implementation using HubL's `in` / `not in` operator:

```hubl
{# contains, single term, case-sensitive: #}
{% if "search_term" in variable %}

{# contains, single term, case-insensitive: #}
{% if "search_term" in variable|lower %}

{# contains, multiple terms (OR logic): #}
{% if "term1" in variable or "term2" in variable %}

{# not-contains, single term: #}
{% if "search_term" not in variable %}

{# not-contains, multiple terms (AND logic): #}
{% if "term1" not in variable and "term2" not in variable %}
```

**Key details:**
- The `$caseSensitive` parameter in `_translateContains()` is misnamed: it actually receives `$command->isCaseInsensitive()`, meaning `true` = case-insensitive. Treatment must match the existing convention.
- When case-insensitive, apply `|lower` to the variable and lowercase the search term at translation time.
- Multiple search terms are joined with `or` for `contains` and `and` for `not-contains`.
- The `$containsType` parameter determines whether it's `contains` or `not-contains` (check for `not-contains` substring).
- List variants (`list-contains`, `list-not-contains`, etc.) should continue returning the "not implemented" stub since HubL has no record-list concept.

### 2. BeginsWith — Modify `AbstractIfBase._translateSearch()`

Replace the stub in `AbstractIfBase._translateSearch()` for `mode === 'starts'`:

```hubl
{# case-sensitive: #}
{% if variable is string_startingwith "prefix" %}

{# case-insensitive: #}
{% if variable|lower is string_startingwith "prefix" %}
```

**Key details:**
- The `$caseSensitive` parameter is misnamed (same as above — means case-insensitive when `true`).
- When case-insensitive, apply `|lower` to the variable name and lowercase the search term at translation time.
- The search term from `getSearchTerm()` returns the quoted/normalized string — must trim quotes.

### 3. EndsWith — Modify `AbstractIfBase._translateSearch()`

Add handling for `mode === 'ends'` using Jinjava string slicing:

```hubl
{# case-sensitive: #}
{% if variable[-6:] == "suffix" %}

{# case-insensitive: #}
{% if variable|lower[-6:] == "suffix" %}
```

**Key details:**
- The suffix length `N` is computed at translation time from `mb_strlen()` of the search term.
- When case-insensitive, apply `|lower` to the variable and lowercase the search term.
- The search term must be trimmed of quotes from the tokenizer output.

### 4. For Loop — Rewrite `ForTranslation.translate()`

Replace the stub with a proper `{% for %}` implementation:

```hubl
{# simple loop: #}
{% for loop_var in source_var %}

{# with break_at (loop slicing): #}
{% for loop_var in source_var[:N] %}
```

**Key details:**
- Variable names must be lowercased via `formatVariableName()`.
- The `break_at:` keyword is emulated via Jinja2 loop slicing `[:N]` which limits iteration.
- The `break_at` value can be a number literal or a variable — when it's a variable, formatting applies.
- Unlike Velocity's `.list()` suffix, HubL iterates directly over the variable.

### 5. ShowDate — Rewrite `ShowDateTranslation.translate()`

Replace the stub with `format_datetime` filter usage:

```hubl
{# with variable and timezone: #}
{{ variable|format_datetime("yyyy-MM-dd", "Europe/Paris") }}

{# with variable, no timezone: #}
{{ variable|format_datetime("yyyy-MM-dd") }}

{# current date (no variable): #}
{{ local_dt|format_datetime("yyyy-MM-dd") }}
```

**Key details:**
- **Reuse the PHP-to-LDML conversion table** from `ApacheVelocity\ShowDateTranslation::$charTable`. This is a `public static` array, so it can be directly referenced.
- The conversion logic (iterating format characters and mapping them) should be extracted into a shared utility or the table referenced directly.
- The timezone token can be a string literal or a variable. For string literals, pass as a second argument to `format_datetime()`. For variable tokens, interpolate the variable.
- URL encoding support should be wrapped via `renderEncodings()`.

## Rationale

1. **Centralized Contains/Search in `AbstractIfBase`**: Both `IfTranslation` and `ElseIfTranslation` already delegate to `_translateContains()` and `_translateSearch()`. Implementing once covers both command types — exactly the same pattern used by the Velocity translator.

2. **`in` operator over `is string_containing`**: The `in` operator is simpler, well-documented, and handles both containment and negation (`not in`) naturally. The `is string_containing` test does not easily compose with negation or `|lower`.

3. **String slicing for EndsWith**: Multiple independent sources confirm Jinjava supports Python-style negative string indexing. This is simpler and more reliable than regex-based alternatives.

4. **Loop slicing for `break_at:`**: HubL has no `break` statement. Loop slicing (`[:N]`) is the standard Jinja2 approach and cleanly limits iteration count.

5. **Reusing the Velocity date conversion table**: The character table is already public and static. Duplicating it would create a maintenance burden.

## Detailed Steps

### Step 1: Implement `_translateContains()` in `AbstractIfBase`

**File:** `src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php`

Replace the current stub:
```php
protected function _translateContains(...) : string
{
    return $this->translateNotImplemented();
}
```

With a full implementation that:
1. Determines if this is a `not-contains` type (check `strpos($containsType, 'not-contains')`)
2. Determines if this is a `list-*` type — if so, return `$this->translateNotImplemented()` (no HubL equivalent)
3. For each search term token, builds `"term" in variable` or `"term" not in variable`
4. Applies `|lower` to the variable if case-insensitive and lowercases the search term
5. Joins multiple terms with `or` (for contains) or `and` (for not-contains)

### Step 2: Implement `_translateSearch()` in `AbstractIfBase`

**File:** `src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php`

Replace the current stub:
```php
protected function _translateSearch(...) : string
{
    return $this->translateNotImplemented();
}
```

With:
1. For `$mode === 'starts'`: Use `variable is string_startingwith "term"`
2. For `$mode === 'ends'`: Use `variable[-N:] == "term"` where N = `mb_strlen(trim($searchTerm, '"'))`
3. Both modes: Apply `|lower` and lowercase the term when case-insensitive

### Step 3: Implement `ForTranslation.translate()`

**File:** `src/Mailcode/Translator/Syntax/HubL/ForTranslation.php`

Replace the stub with:
1. Format both loop and source variable names via `formatVariableName()`
2. If `break_at:` is enabled, apply loop slicing `[:N]` to the source variable
3. Output `{% for loopvar in sourcevar %}` or `{% for loopvar in sourcevar[:N] %}`

### Step 4: Implement `ShowDateTranslation.translate()`

**File:** `src/Mailcode/Translator/Syntax/HubL/ShowDateTranslation.php`

Replace the stub with:
1. Convert the PHP date format string to LDML format using `ApacheVelocity\ShowDateTranslation::$charTable`
2. Build the `format_datetime` filter call
3. Handle the timezone parameter (string literal or variable)
4. Handle the "no variable" case (use `local_dt` or equivalent)
5. Wrap with `renderEncodings()` for URL encoding support

### Step 5: Create unit tests for all new translations

Create the following new test files in `tests/testsuites/Translator/HubL/`:

| Test File | Tests |
|-----------|-------|
| `IfContainsTests.php` | Single term contains, multiple terms, case-insensitive, special characters |
| `IfNotContainsTests.php` | Single term not-contains, multiple terms, case-insensitive |
| `IfBeginsWithTests.php` | Case-sensitive begins-with, case-insensitive begins-with |
| `IfEndsWithTests.php` | Case-sensitive ends-with, case-insensitive ends-with |
| `ElseIfContainsTests.php` | Mirrors if-contains tests for the `{elseif}` variant |
| `ElseIfNotContainsTests.php` | Mirrors if-not-contains tests for the `{elseif}` variant |
| `ElseIfBeginsWithTests.php` | Mirrors if-begins-with tests |
| `ElseIfEndsWithTests.php` | Mirrors if-ends-with tests |
| `ForTests.php` | Simple for loop, for with `break_at:` numeric, for with `break_at:` variable |
| `ShowDateTests.php` | Date with format, date with timezone string, date with timezone variable, date without variable (current date), date with URL encoding |

All tests extend `HubLTestCase` and use the `runCommands()` pattern.

### Step 6: Run test suite and static analysis

1. Run `composer test-suite -- Translator` to validate all translator tests pass
2. Run `composer test` to validate no regressions in other suites
3. Run `composer analyze` to validate PHPStan level 9 compliance

### Step 7: Update project manifest documentation

Update the following manifest files to reflect the newly implemented translations:

- **`constraints.md`**: Update the "Translation Coverage" section — HubL now covers `contains`, `not-contains`, `begins-with`, `ends-with`, `for`, and `showdate`
- **`file-tree.md`**: Add any new test files created

## Dependencies

- **`ApacheVelocity\ShowDateTranslation::$charTable`** — the PHP-to-LDML date format conversion table. This is already `public static`, so it can be referenced directly from the HubL `ShowDateTranslation`.
- All other changes are self-contained within the HubL translation layer.

## Required Components

### Modified files (existing):
- `src/Mailcode/Translator/Syntax/HubL/Base/AbstractIfBase.php` — implement `_translateContains()` and `_translateSearch()`
- `src/Mailcode/Translator/Syntax/HubL/ForTranslation.php` — implement `translate()`
- `src/Mailcode/Translator/Syntax/HubL/ShowDateTranslation.php` — implement `translate()`

### New files:
- `tests/testsuites/Translator/HubL/IfContainsTests.php`
- `tests/testsuites/Translator/HubL/IfNotContainsTests.php`
- `tests/testsuites/Translator/HubL/IfBeginsWithTests.php`
- `tests/testsuites/Translator/HubL/IfEndsWithTests.php`
- `tests/testsuites/Translator/HubL/ElseIfContainsTests.php`
- `tests/testsuites/Translator/HubL/ElseIfNotContainsTests.php`
- `tests/testsuites/Translator/HubL/ElseIfBeginsWithTests.php`
- `tests/testsuites/Translator/HubL/ElseIfEndsWithTests.php`
- `tests/testsuites/Translator/HubL/ForTests.php`
- `tests/testsuites/Translator/HubL/ShowDateTests.php`

### Modified documentation:
- `docs/agents/project-manifest/constraints.md`
- `docs/agents/project-manifest/file-tree.md`

## Assumptions

- **`isCaseInsensitive()` parameter naming**: The existing `_translateContains($caseSensitive)` and `_translateSearch($caseSensitive)` parameters are misnamed — they receive `$command->isCaseInsensitive()` (i.e., `true` means case-insensitive). The implementation must follow this existing convention consistently rather than renaming parameters (to avoid breaking `IfTranslation` and `ElseIfTranslation` call sites).
- **`getSearchTerm()` returns quoted strings**: The `getNormalized()` method on string literal tokens returns the value with surrounding double quotes. The implementation must `trim($searchTerm, '"')` before using the value.
- **Jinjava string slicing works**: As confirmed by 4 of 5 independent sources in the research, `variable[-N:]` works in HubL/Jinjava. If this proves unreliable, a `regex_replace` fallback is available.
- **`format_datetime` accepts variable timezone**: The timezone parameter in `format_datetime` accepts string values; when the Mailcode timezone is a variable, it should be interpolated as a HubL variable expression.
- **`local_dt` is available**: For "current date" (no variable) scenarios, HubL provides `local_dt` as a built-in variable representing the current datetime. If this is incorrect, an alternative approach may be needed.

## Constraints

- **PHPStan level 9**: All new code must pass static analysis at level 9.
- **`declare(strict_types=1)`**: Required in all source files.
- **No List-type command translations**: `ListContains`, `ListNotContains`, `ListBeginsWith`, `ListEndsWith`, `ListEquals` subtypes remain as stubs — HubL has no record-list concept.
- **No `{break}` implementation**: HubL does not support break in for loops. The existing stub remains unchanged.
- **Underscore-delimited naming**: Follow existing naming convention for any new classes.

## Out of Scope

- **`{showprice}` (new translation class)** — Medium feasibility; requires mapping Mailcode's region/currency tokens to HubL `format_currency_value` parameters. Deferred to a separate work package.
- **`{shownumber}` improvements** — Medium feasibility; current pass-through approach is functional. Improving number formatting requires further analysis of format parameter mapping.
- **`{showphone}` variable name fix** — Low priority; the variable name casing bug is minor.
- **All `List*` subtypes** — HubL has no record-list concept; these remain as stubs.
- **`{break}` command** — HubL does not support break in for loops; the stub remains.
- **Version bumping / changelog updates** — To be handled as a separate concern.

## Acceptance Criteria

1. **Contains/NotContains**: `{if contains: $VAR "term"}` translates to `{% if "term" in var %}`. Multiple terms, case-insensitivity (`|lower`), and `not-contains` (`not in`) all work correctly. Both `{if}` and `{elseif}` variants produce correct output.

2. **BeginsWith**: `{if begins-with: $VAR "prefix"}` translates to `{% if var is string_startingwith "prefix" %}`. Case-insensitive variant uses `|lower`. Both `{if}` and `{elseif}` produce correct output.

3. **EndsWith**: `{if ends-with: $VAR "suffix"}` translates to `{% if var[-6:] == "suffix" %}` (where 6 = length of "suffix"). Case-insensitive variant uses `|lower`. Both `{if}` and `{elseif}` produce correct output.

4. **For loop**: `{for: $RECORD in: $SOURCE}` translates to `{% for record in source %}`. With `break_at: 5`, translates to `{% for record in source[:5] %}`.

5. **ShowDate**: `{showdate: $VAR "d/m/Y"}` translates to `{{ var|format_datetime("dd/MM/yyyy") }}` with correct PHP-to-LDML character conversion. Timezone support works for both string and variable tokens.

6. **List subtypes remain stubs**: `{if list-contains: $VAR "term"}` still outputs `{# ! if commands are not fully implemented ! #}`.

7. **All existing tests pass**: No regressions in the full test suite (`composer test`).

8. **PHPStan clean**: `composer analyze` passes at level 9 with no new errors.

9. **Logic keywords work**: Compound conditions like `{if contains: $VAR "a" and: contains: $BAZ "b"}` translate correctly with `and`/`or` connectors.

## Testing Strategy

### Unit Tests

Each new translation is tested via dedicated test classes extending `HubLTestCase`. Tests use the factory methods to create command instances and compare against expected HubL output strings.

**Test coverage matrix:**

| Command | Case-sensitive | Case-insensitive | Multiple terms | Logic keywords | Edge cases |
|---------|---------------|------------------|----------------|---------------|------------|
| If Contains | ✓ | ✓ | ✓ | ✓ (via existing framework) | Special chars in term |
| If NotContains | ✓ | ✓ | ✓ | ✓ | — |
| If BeginsWith | ✓ | ✓ | N/A (single term) | ✓ | — |
| If EndsWith | ✓ | ✓ | N/A (single term) | ✓ | Short suffix (1 char) |
| ElseIf Contains | ✓ | ✓ | ✓ | ✓ | — |
| ElseIf NotContains | ✓ | ✓ | ✓ | ✓ | — |
| ElseIf BeginsWith | ✓ | ✓ | N/A | ✓ | — |
| ElseIf EndsWith | ✓ | ✓ | N/A | ✓ | — |
| For | ✓ (simple) | N/A | N/A | N/A | break_at numeric, break_at variable |
| ShowDate | ✓ (default fmt) | N/A | N/A | N/A | Custom format, timezone string, timezone variable, no variable, URL encoding |

### Regression Tests

Run the full test suite (`composer test`) to ensure no existing functionality is broken.

### Static Analysis

Run `composer analyze` to ensure PHPStan level 9 compliance for all modified and new files.

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **Jinjava string slicing `[-N:]` may not work in all HubSpot environments** | Research confirms 4/5 sources support this; a fallback using `regex_replace` is documented in the research paper |
| **`isCaseInsensitive()` parameter naming confusion** | Document the convention clearly in code comments; keep consistent with existing usage |
| **`format_datetime` timezone variable interpolation** | If variable interpolation doesn't work in the filter argument, fall back to using `{% set %}` for timezone assignment |
| **`local_dt` availability for current date** | Verify against HubL documentation; if unavailable, the "no variable" case can output a stub with a comment |
| **Date format edge cases** | Reuse the proven Velocity conversion table; test with multiple format combinations |
| **Referencing `ApacheVelocity\ShowDateTranslation::$charTable` creates a cross-syntax dependency** | The table is pure data (no behavior); this is acceptable. If the coupling is a concern, extract the table to a shared location (e.g., `Mailcode_Date_FormatInfo`) |
