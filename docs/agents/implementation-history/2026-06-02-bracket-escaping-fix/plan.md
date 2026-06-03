# Plan

## Plan Audit Cycles
- Audits: 1 — Plan Auditor v1.4.0
- Architectural Reviews: none — Plan Architect Reviewer v1.5.0

## Summary

Complete the existing but non-functional bracket escaping mechanism so that `\{NAME\}` in template-level text produces literal `{NAME}` in the final output. The infrastructure already exists (`PreParser::safeguardBrackets()`, `StringPreProcessor::encodeBrackets()`, `PreParser::unescapeBrackets()`) but the final unescape step is never applied to the output of the three consumer pipelines (Safeguard, Translator, PreProcessor). This plan adds that missing restoration step at each pipeline's output boundary.

## Architectural Context

The bracket escaping flows through three layers:

1. **PreParser** (`src/Mailcode/Parser/PreParser.php`) — `safeguardBrackets()` converts `\{` → `__BRACKET_OPEN__` before command detection; `restoreBrackets()` converts back to `\{` after. The `unescapeBrackets()` method (converts `\{` → `{`) exists but is never called.

2. **StringPreProcessor** (`src/Mailcode/Parser/StringPreProcessor.php`) — `encodeBrackets()` converts `\{` → `__BRACKET_OPEN__` before the main regex, preventing escaped sequences from matching command patterns.

3. **Parser Match** (`src/Mailcode/Parser/Match.php`) — `decodeBrackets()` restores `__BRACKET_OPEN__` → `\{` in matched command strings so they retain proper internal state.

Three output pipelines consume parsed results:
- **Safeguard** (`src/Mailcode/Parser/Safeguard.php`) — `restore()` method, used by `makeWhole()`, `makeWholePartial()`, `makeHighlighted()`.
- **Translator** (`src/Mailcode/Translator/BaseSyntax.php`) — `translateSafeguard()` replaces safeguard placeholders with translated command strings.
- **PreProcessor** (`src/Mailcode/PreProcessor.php`) — `render()` applies pre-processing formatting then returns the string.

## Approach / Architecture

Add a single `PreParser::unescapeBrackets()` call at the output boundary of each consumer pipeline. This is the minimal change that completes the escaping round-trip:

```
Input:  "Hello \{CHECKSUM\} {showvar: $NAME}"
Parse:  Detects only {showvar: $NAME} as a command
Safe:   "Hello \{CHECKSUM\} 9990000000001999"
Whole:  "Hello \{CHECKSUM\} {showvar: $NAME}"   ← current (broken)
Fixed:  "Hello {CHECKSUM} {showvar: $NAME}"     ← after this plan
```

The fix points are:
1. `Safeguard::restore()` — wrap the return value of `formatting->toString()` with `PreParser::unescapeBrackets()`
2. `BaseSyntax::translateSafeguard()` — wrap the final `str_replace` return with `PreParser::unescapeBrackets()`
3. `PreProcessor::render()` — wrap the return value with `PreParser::unescapeBrackets()`

## Rationale

- **Smallest possible change.** Three one-line additions using an already-existing utility method.
- **No new syntax keyword.** Unlike a `{noparse}` block command, this introduces zero new classes, interfaces, traits, or translator entries.
- **Already half-implemented.** The `unescapeBrackets()` method exists unused — it was clearly prepared for this purpose.
- **No interaction with nesting validation.** Escaped brackets are invisible to the parser by design (placeholders don't match the command regex).

## Considered Alternatives

| Decision | Chosen Shape | Alternatives Considered | Trade-Off Summary |
|----------|--------------|-------------------------|-------------------|
| Escape mechanism | Inline `\{...\}` escape | New `{noparse}...{end}` block command | Block command adds ~8 new files (command class, validator, 2 translator classes, tests), interacts with nesting validation, `finalize()`, and translator registries; `\{...\}` requires 3 one-line edits. |
| Fix location | Output boundary of each pipeline | Central in `Formatting::toString()` | Formatting doesn't cover `translateSafeguard()` (which bypasses the Formatting system), and `PreProcessor::render()` calls `getSubject()->getString()` not `toString()`; output-boundary approach is explicit and covers all paths. |
| Method location | Keep existing `PreParser::unescapeBrackets()` | New method on `Safeguard` or `SpecialChars` | Method already exists and is well-named; no reason to duplicate or move. |

## Pattern Alignment

- **Escape character convention** (`src/Mailcode/Parser/Statement/Tokenizer/SpecialChars.php`): The `\` escape prefix is already used for `\"`, `\{`, `\}` inside command parameters. Extending it to template text follows the same convention.
- **Placeholder-based protection** (`src/Mailcode/Parser/StringPreProcessor.php`, `src/Mailcode/Parser/PreParser.php`): Both layers already convert `\{` → `__BRACKET_OPEN__` to protect against regex matching. This plan adds the final "decode" step at the output boundary.
- **Output normalization at pipeline exit** (`src/Mailcode/Parser/Safeguard.php#restore()`): The `restore()` method already normalizes command text before returning. Adding unescape here follows the same pattern.

## Detailed Steps

1. **Modify `Safeguard::restore()`** in `src/Mailcode/Parser/Safeguard.php`:
   - After `return $formatting->toString();`, wrap with `PreParser::unescapeBrackets()`.
   - This covers `makeWhole()`, `makeWholePartial()`, `makeHighlighted()`, and the partial-highlighted variant.

2. **Modify `BaseSyntax::translateSafeguard()`** in `src/Mailcode/Translator/BaseSyntax.php`:
   - Wrap the final `str_replace(...)` return with `PreParser::unescapeBrackets()`.
   - Also wrap the early return for `!$safeguard->hasPlaceholders()` (that path also returns `$subject` which may contain escaped brackets).

3. **Modify `PreProcessor::render()`** in `src/Mailcode/PreProcessor.php`:
   - Wrap the return value `$formatting->getSubject()->getString()` with `PreParser::unescapeBrackets()`.

4. **Add test: Safeguard round-trip with escaped brackets** in `tests/testsuites/Parser/SafeguardTests.php`:
   - Template contains `\{CHECKSUM\}` alongside a real command.
   - Assert `makeSafe()` preserves `\{CHECKSUM\}` (not treated as a command).
   - Assert `makeWhole()` produces `{CHECKSUM}` (escape removed) with the command intact.

5. **Add test: Safeguard partial with escaped brackets** in `tests/testsuites/Parser/SafeguardTests.php`:
   - Template contains only `\{TOKEN\}` with no commands.
   - Assert `makeSafePartial()` + `makeWholePartial()` produces `{TOKEN}`.

6. **Add test: Translator with escaped brackets** in `tests/testsuites/Translator/Velocity/` (new file `EscapedBracketsTests.php`):
   - Template: `"\{TRACKING\} {showvar: $NAME}"`.
   - Translate via Apache Velocity.
   - Assert output contains `{TRACKING}` (unescaped) and the Velocity variable syntax.

7. **Add test: PreProcessor with escaped brackets** in `tests/testsuites/PreProcessor/` (or `tests/testsuites/Parser/`):
   - Template: `"\{SAFE\} {mono}text{mono}"`.
   - Assert `render()` produces `{SAFE}` with `<code>text</code>`.

8. **Run PHPStan** (`composer analyze`) to verify level 9 compliance.

9. **Run full test suite** (`composer test`) to verify no regressions.

## Dependencies

- None. All required infrastructure (`PreParser::unescapeBrackets()`, placeholder mechanism) already exists.

## Required Components

- `src/Mailcode/Parser/Safeguard.php` — modify `restore()` method (existing file)
- `src/Mailcode/Translator/BaseSyntax.php` — modify `translateSafeguard()` method (existing file)
- `src/Mailcode/PreProcessor.php` — modify `render()` method (existing file)
- `tests/testsuites/Parser/SafeguardTests.php` — add test methods (existing file)
- `tests/testsuites/Translator/Velocity/EscapedBracketsTests.php` — **new file**
- `tests/testsuites/PreProcessor/EscapedBracketsTests.php` — **new file** (or add to existing PreProcessor test if one exists)

## Assumptions

- `PreParser::unescapeBrackets()` is safe to call on strings that do not contain any `\{` or `\}` (no-op in that case) — confirmed: it's a simple `str_replace`.
- The unescape should happen AFTER commands have been restored/translated, so it cannot accidentally turn a restored command's brackets back into escaped form (correct: unescape converts `\{` → `{`, not `{` → anything).
- No external consumers of the library rely on receiving `\{` or `\}` in the output of `makeWhole()` / `translateSafeguard()`. If any did, they would already be getting broken output since `\{` in template text is undocumented behavior.

## Constraints

- Must not affect bracket escaping **inside** command parameters (handled separately by `SpecialChars` at the tokenizer level — those are already correctly decoded by the tokenizer before the output boundary).
- Must not break the PreParser's `{code}...{end}` protected content extraction, which also uses `safeguardBrackets()`/`restoreBrackets()` internally. The `unescapeBrackets()` call is downstream of content extraction.
- PHPStan level 9 must remain clean.

## Out of Scope

- Adding a `{noparse}...{end}` block command (may be considered separately for large verbatim blocks in the future).
- **Double-escape (`\\{`):** Producing a literal `\{` in output by writing `\\{` is explicitly not supported by this plan. Currently `\\{` will be treated as a literal backslash followed by the escape sequence `\{`, producing `{` in output. This is a known limitation to be addressed only if a user requests it.
- Changes to the HTML highlighting system beyond what `makeHighlighted()` already covers via `restore()`.

## Acceptance Criteria

- `\{CHECKSUM\}` in template text produces `{CHECKSUM}` in `makeWhole()` output.
- `\{CHECKSUM\}` in template text produces `{CHECKSUM}` in `translateSafeguard()` output.
- `\{CHECKSUM\}` in template text produces `{CHECKSUM}` in `PreProcessor::render()` output.
- Real commands alongside escaped brackets are correctly parsed, safeguarded, and restored.
- Escaped brackets inside command string literals (`{if contains: $FOO "\{bar\}"}`) continue working as before (no regression).
- The collection is valid when template text contains `\{UNKNOWN\}` (not treated as an unknown command).
- `\{if\}` (escaped real command name) is NOT treated as a command; output contains literal `{if}`.
- `\{TOKEN}` (only opening bracket escaped) does not produce a command match; output contains `{TOKEN}`.
- `{TOKEN\}` (only closing bracket escaped) does **not** produce a command match; collection is valid; output contains `{TOKEN}`. _(Rationale: `StringPreProcessor::encodeBrackets()` converts `\}` → `__BRACKET_CLOSE__`, yielding `{TOKEN__BRACKET_CLOSE__`, which the parser regex cannot match because no literal closing `}` remains.)_
- Multiple escaped sequences `\{A\} text \{B\}` in one string both produce `{A}` and `{B}` correctly.
- `\{\}` (empty escaped braces) produces `{}` in output.
- `\\{CHECKSUM}` (literal backslash before an unescaped bracket) is explicitly out of scope; document as known limitation.
- PHPStan level 9 passes.
- Full test suite passes.

## Testing Strategy

Unit tests covering all three output pipelines with escaped brackets. Each test asserts both that escaped text is unescaped correctly AND that adjacent real commands are unaffected. Existing tests must continue passing to verify no regressions.

## Test Plan

### Core pipeline tests

- `tests/testsuites/Parser/SafeguardTests.php::test_escapedBrackets_makeWhole()` — Escaped brackets in template text are unescaped after `makeWhole()`; real commands are restored. Covers AC: "makeWhole output".
- `tests/testsuites/Parser/SafeguardTests.php::test_escapedBrackets_makeWholePartial()` — Escaped brackets work in partial mode (no commands present). Covers AC: "collection is valid".
- `tests/testsuites/Parser/SafeguardTests.php::test_escapedBrackets_collectionValid()` — Template with `\{CHECKSUM\}` parses as valid (0 commands, no errors). Covers AC: "collection is valid when template text contains escaped brackets".
- `tests/testsuites/Translator/Velocity/EscapedBracketsTests.php::test_translateWithEscapedBrackets()` — Escaped brackets survive translation alongside real commands. Covers AC: "translateSafeguard output".
- `tests/testsuites/PreProcessor/EscapedBracketsTests.php::test_renderWithEscapedBrackets()` — Escaped brackets are unescaped in PreProcessor output. Covers AC: "PreProcessor::render output".
- Existing `tests/testsuites/Parser/ParserTests.php::test_parseString_preProcess()` — continues passing (escaped brackets in command params unaffected). Covers AC: "no regression".

### Edge case tests

- `tests/testsuites/Parser/SafeguardTests.php::test_escapedBrackets_halfEscapedOpening()` — Input `\{TOKEN}` (only opening bracket escaped): verify collection is valid with 0 commands, output contains literal `{TOKEN}`. Covers AC: "only opening bracket escaped".
- `tests/testsuites/Parser/SafeguardTests.php::test_escapedBrackets_halfEscapedClosing()` — Input `{TOKEN\}` (only closing bracket escaped): verify collection is valid with 0 commands; `makeWholePartial()` output contains `{TOKEN}`. Covers AC: "only closing bracket escaped".
- `tests/testsuites/Parser/SafeguardTests.php::test_escapedBrackets_realCommandName()` — Input `\{if\} {showvar: $X}`: verify only `showvar` is detected as a command; `makeWhole()` output contains literal `{if}` alongside the restored showvar. Covers AC: "escaped real command name".
- `tests/testsuites/Parser/SafeguardTests.php::test_escapedBrackets_multiple()` — Input `\{A\} text \{B\}`: verify collection is valid with 0 commands; output produces `{A} text {B}`. Covers AC: "multiple escaped sequences".
- `tests/testsuites/Parser/SafeguardTests.php::test_escapedBrackets_empty()` — Input `\{\}`: verify collection is valid; output produces `{}`. Covers AC: "empty escaped braces".

## Documentation Updates

- `docs/agents/project-manifest/constraints.md` — Update the "Special characters in strings" bullet to clarify that `\{` / `\}` escaping also works at the template level (not just inside command parameters). Add a new subsection "Template-Level Bracket Escaping" documenting the behavior.
- `docs/agents/project-manifest/data-flows.md` — Add a note to the "Safeguard Text During Processing" flow mentioning that escaped brackets (`\{...\}`) are unescaped in the final output.
- `docs/user-guide/mailcode-documentation.md` — Add a section documenting how to escape text that resembles commands using `\{` and `\}`.

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **Existing templates contain literal `\{` intended for output** | Unlikely — `\{` in template text was previously non-functional (passed through unchanged). Any template already using it was already broken. The new behavior makes it work correctly. |
| **Double-unescape in nested pipeline calls** | Each pipeline calls `unescapeBrackets()` at its own exit. They don't nest: Safeguard's `makeWhole` is not called inside `translateSafeguard`, and PreProcessor has its own safeguard instance. No double-processing possible. |
| **Performance impact of extra str_replace** | `unescapeBrackets()` is a simple `str_replace` of 2 short literal strings. Negligible performance impact even on large templates. |
| **SpecialChars (tokenizer) conflict** | SpecialChars handles escaping INSIDE command parameters, which are already replaced by safeguard placeholders before the output-boundary unescape runs. No interaction possible. |
