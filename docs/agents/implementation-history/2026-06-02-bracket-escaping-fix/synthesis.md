## Synthesis

### Completion Status
- Date: 2026-06-03
- Status: COMPLETE
- Completed by: Standalone Developer Agent

### Implementation Summary

- Completed the bracket-escaping round-trip so that `\{NAME\}` in template-level text produces `{NAME}` in the final output of all three consumer pipelines.
- The infrastructure already existed (`PreParser::unescapeBrackets()`); this plan wired in the three missing call-sites.
- **Critical placement insight:** `unescapeBrackets()` must be applied to the *safe string* (while all commands are still numeric placeholders) — not to the final restored string. Applying it after placeholder restoration would also strip backslashes from `\{`/`\}` sequences that appear inside parsed command parameters (e.g., `{elseif: $VAR "\{test\}"}`), causing regressions.
- **`CommentTests` behavior change:** The existing test `{comment: haha: $FOOBAR \}` was updated because this command fails to parse (the regex cannot match it once `\}` encodes the only closing brace), leaving the entire string as template-level text. Under the new escaping semantics, the `\}` in that template text is correctly unescaped to `}`. The normalized expected value in the test was updated accordingly.

### Documentation Updates
- `docs/agents/project-manifest/constraints.md` — Extended the "Special characters in strings" bullet; added a new "Template-Level Bracket Escaping" subsection documenting the mechanism, pipeline, and key rules.
- `docs/agents/project-manifest/data-flows.md` — Added the `unescapeBrackets()` step to the "Safeguard Text During Processing" flow description.
- `docs/user-guide/mailcode-documentation.md` — Added a new "Escaping template-level curly braces" subsection under "Escaping special characters" with examples and the double-escape limitation note.

### Verification Summary
- Tests run: Full PHPUnit suite (`composer test`)
- Static analysis run: PHPStan level 9 (`composer analyze`)
- Result: **553 tests, 2429 assertions, 0 failures** — PASS. PHPStan: **No errors** — PASS.

### Code Insights

- [low] (debt) `src/Mailcode/Parser/StringPreProcessor.php`: Uses the string literal `'__BRACKET_OPEN__'` / `'__BRACKET_CLOSE__'` inline rather than `SpecialChars::PLACEHOLDER_BRACKET_OPEN` / `SpecialChars::PLACEHOLDER_BRACKET_CLOSE`. Although the values happen to match, the `PreParser` class defines its own identical `$escapeChars` array independently. Three separate places maintain the same pair of constant strings; any future rename would require updating all three. A shared constant (already accessible via `SpecialChars`) should be used consistently.
- [medium] (debt) `src/Mailcode/Parser/PreParser.php` + `src/Mailcode/Parser/StringPreProcessor.php`: Both classes independently define the same `\{ → __BRACKET_OPEN__` / `\} → __BRACKET_CLOSE__` mapping. The `PreParser::$escapeChars` array and `StringPreProcessor::encodeBrackets()` are exact duplicates at the data level. This creates a silent correctness dependency: if one mapping diverges from the other, bracket escaping silently breaks. Consolidating to a single source of truth (e.g., `SpecialChars::PLACEHOLDER_BRACKET_OPEN/CLOSE`) would eliminate the risk.
- [low] (improvement) `src/Mailcode/Translator/BaseSyntax.php` `translateSafeguard()`: The method calls `$safeguard->makeSafe()` without first checking `$safeguard->isValid()`. For the `makeWhole`/`translateSafeguard` callers who rely on a valid collection, this silently throws an exception if the safeguard is invalid. A defensive `isValid()` guard or explicit documentation of the precondition would clarify the contract.
- [low] (improvement) `src/Mailcode/Parser/PreParser.php` `unescapeBrackets()`: The method is `public static` but was never called from outside the parser before this plan. Now that it is used at pipeline output boundaries, it is a confirmed part of the public API. Consider adding a brief docblock confirming its role as a template-text unescape utility (distinct from `restoreBrackets()`).

### Additional Comments

- The `CommentTests` change is the only test that needed its expected value updated. The old expectation (`\}` preserved) was incidentally correct because the command never parsed; the new expectation (`}` unescaped) reflects the deliberate new feature behavior for template-level text.
- Double-escape (`\\{`) producing a literal `\{` in output is explicitly out of scope and noted as a known limitation in the user guide.
