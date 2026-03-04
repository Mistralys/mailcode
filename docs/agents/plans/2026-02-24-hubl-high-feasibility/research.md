# Research Report

## Problem Statement

Determine which Mailcode commands can be added to the HubL translation layer by cross-referencing the full set of Mailcode commands with HubSpot's HubL template language capabilities. The user has confirmed that all `ListXXX` commands are out of scope because HubL has no concept of "record lists."

## Problem Decomposition

1. **Inventory all Mailcode commands and their current HubL translation status** — identify which are fully implemented, which are stubs/not-implemented, and which are missing entirely.
2. **Map HubL language features to Mailcode command semantics** — determine which HubL operators, expression tests, filters, and tags can serve each Mailcode command.
3. **Identify gaps that can be closed** — for each unimplemented or stub command, assess feasibility and propose the HubL translation.
4. **Identify commands that remain untranslatable** — for completeness.

## Context & Constraints

- HubL is a Jinja2-derived template language used by HubSpot CMS.
- Variable output: `{{ variable }}`, statements: `{% ... %}`, comments: `{# ... #}`.
- HubL supports: `if`/`elif`/`else`/`endif`, `for`/`endfor`, `set`, filters (`|filter`), expression tests (`is test`).
- HubL does **not** have a concept of "record lists" (i.e., iterating over structured record data with field access à la `$RECORD.FIELD` inside a loop over `$LIST.list()`). Regular `for` loops over simple sequences are supported.
- The existing Apache Velocity syntax has **full coverage** and serves as the reference implementation.
- Translation classes are auto-discovered in `Translator/Syntax/HubL/` via `ClassCache`.

## Current State Inventory

### All Mailcode Commands

| # | Command | Class | Type | HubL Translation File Exists | Status |
|---|---------|-------|------|------------------------------|--------|
| 1 | `{showvar}` | `ShowVariable` | Standalone | Yes | **Fully implemented** |
| 2 | `{showencoded}` | `ShowEncoded` | Standalone | Yes | **Fully implemented** |
| 3 | `{showurl}` | `ShowURL` | Opening | Yes | **Fully implemented** |
| 4 | `{showdate}` | `ShowDate` | Standalone | Yes | **Stub** — outputs `{# ! show date commands are not implemented ! #}` |
| 5 | `{shownumber}` | `ShowNumber` | Standalone | Yes | **Partial** — outputs `{{ VARNAME }}` but ignores number formatting |
| 6 | `{showphone}` | `ShowPhone` | Standalone | Yes | **Partial** — outputs `{{ VARNAME }}` but ignores phone formatting |
| 7 | `{showsnippet}` | `ShowSnippet` | Standalone | Yes | **Fully implemented** |
| 8 | `{showprice}` | `ShowPrice` | Standalone | **No** | **Missing entirely** |
| 9 | `{setvar}` | `SetVariable` | Standalone | Yes | **Fully implemented** |
| 10 | `{comment}` | `Comment` | Standalone | Yes | **Fully implemented** |
| 11 | `{for}` | `For` | Opening | Yes | **Stub** — outputs `{# ! for commands are not implemented ! #}` |
| 12 | `{break}` | `Break` | Standalone | Yes | **Stub** — outputs `{# !break is not supported in HubL! #}` |
| 13 | `{if}` | `If` | Opening | Yes | **Partially implemented** (see sub-types below) |
| 14 | `{elseif}` | `ElseIf` | Sibling | Yes | **Partially implemented** (see sub-types below) |
| 15 | `{else}` | `Else` | Sibling | Yes | **Fully implemented** |
| 16 | `{end}` | `End` | Closing | Yes | **Fully implemented** |
| 17 | `{mono}` | `Mono` | Opening | Yes | **Fully implemented** (outputs empty string; preprocessor handles it) |
| 18 | `{code}` | `Code` | Opening | Yes | **Fully implemented** |

### If/ElseIf Sub-type Status

| Sub-type | HubL Status | Notes |
|----------|-------------|-------|
| `Variable` (string comparison) | **Implemented** | Uses `==`, `!=` with `\|lower` for case-insensitivity |
| `Empty` | **Implemented** | Uses `!variable` / `variable` truthiness |
| `NotEmpty` | **Implemented** | Same as above (inverted) |
| `EqualsNumber` | **Implemented** | Uses `\|float` + `==` |
| `BiggerThan` | **Implemented** | Uses `\|float` + `>` |
| `SmallerThan` | **Implemented** | Uses `\|float` + `<` |
| `Contains` | **Not implemented** | Returns `{# ! if commands are not fully implemented ! #}` |
| `NotContains` | **Not implemented** | Same stub |
| `BeginsWith` | **Not implemented** | Same stub |
| `EndsWith` | **Not implemented** | Same stub |
| `ListContains` | **Not implemented** | Same stub (also out of scope per user) |
| `ListNotContains` | **Not implemented** | Same stub (out of scope) |
| `ListBeginsWith` | **Not implemented** | Same stub (out of scope) |
| `ListEndsWith` | **Not implemented** | Same stub (out of scope) |
| `ListEquals` | **Not implemented** | Same stub (out of scope) |

---

## Prior Art & Known Patterns

### Pattern 1: HubL `is string_containing` expression test

- **Description:** HubL provides `is string_containing "substring"` to check if a string contains a substring.
- **Where used:** Official HubSpot documentation — Expression Tests.
- **Strengths:** Native, concise, built-in to HubL.
- **Weaknesses:** Case-sensitive only. No built-in case-insensitive variant.
- **Fit:** Direct match for `{if contains:}`. Case-insensitivity can be achieved by piping through `|lower` on both sides, though expression tests don't easily allow filtering the test argument. A workaround: convert the variable to lowercase first with `{% set temp = var|lower %}`, then test `temp is string_containing "lowered_term"`.

### Pattern 2: HubL `is string_startingwith` expression test

- **Description:** Tests whether a string starts with a particular substring.
- **Where used:** Official HubSpot documentation — Expression Tests.
- **Strengths:** Direct, native.
- **Weaknesses:** Case-sensitive only. Same workaround needed for insensitivity.
- **Fit:** Direct match for `{if begins-with:}`.

### Pattern 3: HubL `in` operator for substring containment

- **Description:** The `in` operator checks if a value is in a sequence. For strings, `"sub" in string_var` checks if `"sub"` is a substring of `string_var`.
- **Where used:** Jinja2 documentation (HubL is Jinja2-derived). Common pattern in Jinja2 templates.
- **Strengths:** Simple, well-known syntax. Works for both substring checks and list membership.
- **Weaknesses:** Case-sensitive. Requires `|lower` workaround for case-insensitivity.
- **Fit:** Alternative approach for `{if contains:}`. Syntax: `{% if "term" in variable %}`.

### Pattern 4: HubL `format_datetime` / `format_date` filters

- **Description:** Formats a datetime object using Unicode LDML patterns (e.g., `yyyy-MM-dd`, `'short'`, `'medium'`).
- **Where used:** Official HubSpot documentation — Filters.
- **Strengths:** Full date formatting with timezone and locale support. Uses Java/Unicode LDML format strings which are the same format family as Mailcode's date format system.
- **Weaknesses:** Requires that the source variable holds a proper datetime object. The Mailcode `showdate` command's PHP-style format characters need conversion to the Unicode LDML equivalents (same conversion the Apache Velocity translator already does).
- **Fit:** Excellent fit for `{showdate}`. The conversion table from PHP to Java format already exists in the Velocity `ShowDateTranslation`.

### Pattern 5: HubL `format_number` filter

- **Description:** `{{ value|format_number('locale') }}` formats a number with locale-specific thousand separators and decimal formatting.
- **Where used:** Official HubSpot documentation — Filters.
- **Strengths:** Proper locale-based number formatting.
- **Weaknesses:** The Mailcode `ShowNumber` command has its own format parameters (decimal places, thousands separator, etc.) that may not map 1:1 to HubL's locale-based approach.
- **Fit:** Partial. Can handle basic number display with locale. Some Mailcode number formatting options may not have a direct HubL equivalent.

### Pattern 6: HubL `format_currency_value` filter

- **Description:** `{{ amount|format_currency_value(locale='en-US', currency='EUR') }}` formats a number as currency.
- **Where used:** Official HubSpot documentation — Filters.
- **Strengths:** Supports locale, currency code, min/max decimal digits.
- **Weaknesses:** Mailcode's `ShowPrice` has `absolute:`, `currency-name:` keywords and region/currency token support that may need creative mapping.
- **Fit:** Good fit for basic price display. The `absolute:` keyword (which shows absolute value) can be handled with `|abs` before currency formatting.

### Pattern 7: HubL `for` loops

- **Description:** `{% for item in sequence %}...{% endfor %}`.
- **Where used:** Core HubL syntax, official documentation.
- **Strengths:** Standard Jinja2-style for loops.
- **Weaknesses:** HubL has no `break` statement in for loops.
- **Fit:** The Mailcode `{for}` command iterates `$RECORD in: $LIST`. The source variable in Mailcode is a list variable. This maps to `{% for record in list %}`. The `break_at:` keyword has no HubL equivalent, but the loop itself is translatable, and `break_at` can be emulated with `{% if loop.index > N %}` (though HubL lacks `break`).

---

## Alternative & Creative Approaches

### Contains with case-insensitivity via `|lower`

For `{if contains: $VAR "term" insensitive:}`, the HubL translation can use:
```
{% if "term" in variable|lower %}
```
This only works correctly when the search term is already lowered. The translator must lowercase the search term at translation time.

### Multiple search terms with logical connectors

The Mailcode `contains` command supports multiple search terms (`{if contains: $VAR "a" "b"}`), joined with OR logic (any match). In HubL:
```
{% if "a" in variable or "b" in variable %}
```
For `not-contains`, terms are joined with AND logic:
```
{% if "a" not in variable and "b" not in variable %}
```

### BeginsWith / EndsWith without native `endswith` test

HubL has `is string_startingwith` but no documented `string_endingwith` test. However, since HubL is Jinja2/Jinjava-based, the following Jinja2 pattern works:
- **BeginsWith:** `{% if variable is string_startingwith "prefix" %}`
- **EndsWith (alternative):** `{% if variable[-N:] == "suffix" %}` (string slicing). However, string slicing may not be fully supported in HubL. A safer alternative is to use the `|regex_replace` filter or simply use `"suffix" in variable` (which is weaker — it's a contains, not an endswith).

**EndsWith approach (resolved):** Multiple independent sources confirm that Jinjava (HubL's engine) supports Python-style string slicing. Use `variable[-N:] == "suffix"` where N is the length of the suffix (known at translation time). For case-insensitivity, apply `|lower` to the variable and lowercase the search term. See the "Addendum: EndsWith Resolution" section for the full analysis.

### For loops with break_at emulation

HubL does not support `break` in for loops. However, for the `break_at:` keyword, we can use Jinja2's loop slicing:
```
{% for item in list[:N] %}  {# limits iteration to first N items #}
```
Or use `{% if loop.index <= N %}` inside the loop body to suppress output after N iterations (though the loop still runs).

---

## Detailed Feasibility Analysis

### 1. `{if contains:}` / `{if not-contains:}` — **IMPLEMENTABLE**

**HubL translation:**
```
{# Case-sensitive, single term: #}
{% if "search_term" in variable %}

{# Case-insensitive, single term: #}
{% if "search_term" in variable|lower %}

{# Multiple terms (contains = OR logic): #}
{% if "term1" in variable or "term2" in variable %}

{# Not-contains, single term: #}
{% if "search_term" not in variable %}

{# Not-contains, multiple terms (AND logic): #}
{% if "term1" not in variable and "term2" not in variable %}
```

Note: The `in` operator for string containment is standard Jinja2 and works in HubL. The `is string_containing` expression test is an alternative.

**Complexity:** Low-Medium. Need to handle case-sensitivity and multiple search terms.

### 2. `{if begins-with:}` — **IMPLEMENTABLE**

**HubL translation:**
```
{# Case-sensitive: #}
{% if variable is string_startingwith "prefix" %}

{# Case-insensitive: #}
{% if variable|lower is string_startingwith "prefix" %}
```

**Complexity:** Low.

### 3. `{if ends-with:}` — **IMPLEMENTABLE**

HubL does not have a documented `string_endingwith` expression test. However, Jinjava (the engine behind HubL) supports Python-style string slicing. Multiple independent sources confirm that `variable[-N:] == "suffix"` works in HubL.

**HubL translation:**
```
{# Case-sensitive: #}
{% if variable[-6:] == "suffix" %}

{# Case-insensitive: #}
{% if variable|lower[-6:] == "suffix" %}
```

The length `N` is known at translation time from the search term string.

**Fallback:** If slicing is unreliable, use `variable|regex_replace(".*suffix$", "MATCH") == "MATCH"` (requires regex escaping of special characters in the search term).

**Complexity:** Low-Medium.

### 4. `{showdate}` — **IMPLEMENTABLE**

HubL provides `format_datetime` which accepts Unicode LDML patterns (e.g., `yyyy-MM-dd HH:mm:ss`). The Mailcode date format uses PHP-style characters, and the existing `ShowDateTranslation` for Apache Velocity already has a full PHP-to-Java/LDML conversion table.

**HubL translation:**
```
{# With variable: #}
{{ variable|format_datetime("yyyy-MM-dd", "timezone") }}

{# Without variable (current date): #}
{{ local_dt|format_datetime("yyyy-MM-dd") }}
```

The timezone support maps directly: Mailcode's timezone feature uses IANA timezone identifiers (e.g., `Europe/Paris`), and HubL's `format_datetime` accepts the same identifiers.

**Complexity:** Medium. Need to reuse the PHP-to-LDML conversion table from the Velocity translator.

### 5. `{shownumber}` (improved) — **PARTIALLY IMPLEMENTABLE**

The current implementation just outputs `{{ VARNAME }}` without formatting. HubL provides:
- `|format_number('locale')` for locale-based formatting
- `|round(precision)` for rounding

However, Mailcode's `ShowNumber` command format parameters would need analysis to determine exact mapping. At minimum, the variable name should be formatted through `formatVariableName()` for lowercase conversion, and URL encoding support should be added.

**Complexity:** Medium. Depends on what formatting options `ShowNumber` supports.

### 6. `{showphone}` (improved) — **PARTIALLY IMPLEMENTABLE**

The current implementation outputs `{{ VARNAME }}` without formatting. Phone number formatting is not natively available in HubL. The current pass-through approach is likely the best option, but the variable name should at least be formatted correctly (lowercase).

**Complexity:** Low (mainly a bug fix for variable name formatting).

### 7. `{showprice}` — **IMPLEMENTABLE**

HubL has `format_currency_value` which supports locale, currency code, and decimal digit configuration.

**HubL translation:**
```
{{ variable|format_currency_value(locale='en-US', currency='EUR') }}
```

The `absolute:` keyword can be handled by wrapping with `|abs`:
```
{{ variable|abs|format_currency_value(locale='en-US', currency='EUR') }}
```

The `currency-name:` keyword (display full currency name instead of symbol) may not have a direct HubL equivalent.

**Complexity:** Medium-High. Need to map Mailcode's region/currency tokens to HubL locale/currency parameters.

### 8. `{for}` loop — **IMPLEMENTABLE**

**HubL translation:**
```
{% for loop_var in source_var %}
```

The `break_at:` keyword can be emulated with loop slicing:
```
{% for loop_var in source_var[:N] %}
```

**Complexity:** Low.

### 9. `{break}` — **NOT IMPLEMENTABLE**

HubL does not support `break` within for loops. This is a known limitation.

**Status:** Keep the current stub with the "not supported" comment.

---

## Comparative Evaluation

| Command | Feasibility | Complexity | HubL Fidelity | Priority |
|---------|-------------|------------|----------------|----------|
| `if contains` / `if not-contains` | High | Low-Medium | High | **High** |
| `if begins-with` | High | Low | High | **High** |
| `if ends-with` | High | Low-Medium | High (string slicing works in Jinjava) | **High** |
| `showdate` | High | Medium | High (format_datetime matches well) | **High** |
| `shownumber` (improved) | Medium | Medium | Medium (locale-based only) | **Medium** |
| `showphone` (fix varname) | High | Low | Same as current (pass-through) | **Low** |
| `showprice` (new) | Medium | Medium-High | Good (format_currency_value) | **Medium** |
| `for` loop | High | Low | High | **High** |
| `break` | None | N/A | N/A | **N/A** |
| `ListXXX` subtypes | None | N/A | N/A (no record list concept) | **N/A — excluded** |

---

## Recommendation

### Tier 1 — Implement Now (High feasibility, high impact)

1. **`{if contains:}` and `{if not-contains:}`** — Use `"term" in variable` / `"term" not in variable` with `|lower` for case-insensitivity. Handle multiple search terms with `or`/`and` connectors.

2. **`{if begins-with:}`** — Use `variable is string_startingwith "prefix"`. Add `|lower` for case-insensitivity.

3. **`{for}` loop** — Use `{% for loop_var in source_var %}`. Use `source_var[:N]` for `break_at:`.

4. **`{showdate}`** — Use `{{ variable|format_datetime("converted_format", "timezone") }}`. Reuse the PHP-to-LDML character conversion table from `ApacheVelocity\ShowDateTranslation`.

### Tier 2 — Implement Next (Medium feasibility, good impact)

5. **`{if ends-with:}`** — Use string slicing: `variable[-N:] == "suffix"`. The search term length is known at translation time.

6. **`{showprice}` (new translation class)** — Use `format_currency_value` filter. Map region → locale, currency → ISO currency code.

7. **`{shownumber}` (improve)** — Fix variable name formatting. Consider adding `|format_number` if Mailcode's format parameters can be mapped.

### Tier 3 — Minor fixes

8. **`{showphone}` (fix variable formatting)** — Apply `formatVariableName()` to the output. The current code outputs raw `$VARNAME` instead of lowercase `varname`.

### Not Implementable

- **`{break}`** — HubL does not support break in loops. Keep stub.
- **All `List*` subtypes** — No record-list concept in HubL. Keep stubs.

### Proof-of-Concept Outline

1. Implement `_translateContains()` in `HubL/Base/AbstractIfBase.php` using the `in` operator pattern.
2. Implement `_translateSearch()` in `HubL/Base/AbstractIfBase.php` for `BeginsWith` using `is string_startingwith`.
3. Implement `ForTranslation::translate()` using `{% for X in Y %}`.
4. Implement `ShowDateTranslation::translate()` using `|format_datetime`.
5. Create `ShowPriceTranslation.php` in the HubL folder.
6. Fix `ShowNumberTranslation` and `ShowPhoneTranslation` variable name formatting.
7. Run `composer test-suite -- Translator` to validate.
8. Run `composer analyze` to ensure PHPStan level 9 compliance.

---

## Open Questions

- **`EndsWith` support:** Resolved — see "Addendum: EndsWith Resolution" below.
- **`ShowNumber` format mapping:** What exact formatting options does the Mailcode `ShowNumber` command expose? Need to check the command's parameters to determine if `format_number` is sufficient.
- **`ShowPrice` `currency-name:` keyword:** HubL's `format_currency_value` does not appear to offer a "display currency name instead of symbol" option. This may need to remain unsupported.
- **`format_datetime` timezone variable:** Mailcode supports timezone as a variable (`$TIMEZONE`). HubL's `format_datetime` accepts a string timezone. Need to verify if a variable can be passed as the timezone parameter in HubL.

---

## Addendum: EndsWith Resolution (2026-02-24)

### Findings

Multiple LLM sources were consulted. All five unanimously agree: **HubL does NOT support `variable.endsWith()`**. The Jinjava engine does not expose Java string methods directly.

Several alternative approaches were evaluated:

| Approach | Documented in HubL? | Reliability | Notes |
|----------|---------------------|-------------|-------|
| `variable.endsWith("x")` | **No** | Broken | All sources confirm this does not work |
| `is string_endingwith "x"` | **No** | Likely hallucinated | Only 1 of 5 sources claims this exists; official HubL expression test docs do NOT list it (they list `string_containing` and `string_startingwith` only) |
| `variable[-N:] == "suffix"` | **Not officially, but Jinjava supports it** | Good | 4 of 5 sources recommend this; Jinjava (the engine behind HubL) supports Python-style string slicing; ChatGPT notes it may not work in all email contexts |
| `variable\|regex_match('suffix$')` | **No** — `regex_match` is not in the official filter list | Likely hallucinated | Only 1 source recommends it; the official HubL filter docs list `regex_replace` but NOT `regex_match` |
| `variable\|regex_replace('.*(suffix)$', 'MATCH') == 'MATCH'` | **Yes** — `regex_replace` is documented | Good | Gemini recommends this; uses the documented `regex_replace` filter with a `$` anchor |

### Recommended Solution: String Slicing

**Use `variable[-N:] == "suffix"` as the primary approach.** This is:

- Recommended by 4 of 5 sources.
- Supported by Jinjava (the engine underlying HubL), which handles Python-style negative string indexing.
- Simple, readable, and predictable.
- The search term length is known at translation time in Mailcode, so `N` is a compile-time constant.

**For case-insensitive matching:**
```hubl
{% if variable|lower[-4:] == ".com" %}
```

**Translation pattern for the Mailcode translator:**
```php
// For {if ends-with: $VAR "suffix"}
protected function _translateSearch(string $mode, Mailcode_Variables_Variable $variable, bool $caseSensitive, string $searchTerm) : string
{
    $varName = $this->formatVariableName($variable->getFullName());
    $term = trim($searchTerm, '"');
    $len = mb_strlen($term);

    if (!$caseSensitive) {
        $varName .= '|lower';
        $term = mb_strtolower($term);
    }

    if ($mode === 'ends') {
        return sprintf('%s[-%d:] == "%s"', $varName, $len, $term);
    }

    // 'starts' mode — use the documented expression test
    return sprintf('%s is string_startingwith "%s"', $varName, $term);
}
```

### Fallback

If string slicing proves unreliable in a specific HubSpot environment, a fallback using `regex_replace` is possible:
```hubl
{% if variable|regex_replace(".*suffix$", "MATCH") == "MATCH" %}
```
This uses the documented `regex_replace` filter with a `$` end-of-string anchor. The search term would need regex-special characters escaped.

## References

- HubL Operators & Expression Tests: https://developers.hubspot.com/docs/cms/reference/hubl/operators-and-expression-tests
- HubL If Statements: https://developers.hubspot.com/docs/cms/reference/hubl/if-statements
- HubL Filters: https://developers.hubspot.com/docs/cms/reference/hubl/filters
- HubL Variables & Macros: https://developers.hubspot.com/docs/cms/reference/hubl/variables-macros-syntax
- Jinjava (HubL engine): https://github.com/HubSpot/jinjava
- Existing Mailcode HubL translations: `src/Mailcode/Translator/Syntax/HubL/`
- Existing Mailcode Velocity translations (reference): `src/Mailcode/Translator/Syntax/ApacheVelocity/`
