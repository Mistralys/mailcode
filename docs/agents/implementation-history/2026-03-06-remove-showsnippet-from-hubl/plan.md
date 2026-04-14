# Plan

## Summary

Remove `{showsnippet}` from the HubL translation layer and update documentation to reflect that the command is not supported in HubL due to its dependency on a server-side dictionary infrastructure that does not exist in HubL. Additionally, add a prerequisite note to the general `showsnippet` command documentation to make this infrastructure dependency explicit for all readers.

---

## Architectural Context

### The `showsnippet` command

- **Command class:** `src/Mailcode/Commands/Command/ShowSnippet.php` (`Mailcode_Commands_Command_ShowSnippet`)
- **Extends:** `Mailcode_Commands_ShowBase` → `Mailcode_Commands_Command`
- **Capabilities:** `NoHTML`, `Namespace`, `Variable` validation traits/interfaces.
- The command refers to a named text snippet. At send time, a **server-side dictionary** resolves the snippet name to its text content.

### Apache Velocity vs. HubL

- **Apache Velocity** has a `DictionaryTool` custom tool that maps snippet names to their text values. The translation (`src/Mailcode/Translator/Syntax/ApacheVelocity/ShowSnippetTranslation.php`) generates calls like `$dictionary.global("name")` or `$dictionary.namespace("ns").name("name")`. This infrastructure exists and is documented in `docs/user-guide/translate-apache-velocity.md`.
- **HubL** has no equivalent dictionary infrastructure. The current `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php` translates `{showsnippet}` to `{{ var }}` — essentially treating it as a plain variable reference — which is semantically incorrect and produces wrong output in production.

### Unsupported command pattern in HubL

The `{break}` command sets the precedent for how HubL handles unsupported commands:  
`src/Mailcode/Translator/Syntax/HubL/BreakTranslation.php` returns a HubL comment:
```
{# !break is not supported in HubL! #}
```
`{showsnippet}` must follow the same pattern and be moved to the "Not supported" section in the HubL user guide.

### Affected files

| File | Role |
|------|------|
| `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php` | HubL translation class (to be deleted) |
| `docs/user-guide/translate-hubl.md` | HubL support reference (update supported/unsupported lists) |
| `docs/user-guide/mailcode-documentation.md` | General command documentation (add prerequisite note) |
| `docs/agents/project-manifest/constraints.md` | Translation coverage table (verify; `showsnippet` already absent from HubL list — confirm and leave accurate) |

### No HubL tests to remove

Searching `tests/testsuites/Translator/HubL/` confirms there is no `ShowSnippetTests.php`. No test file needs to be created or deleted.

---

## Approach / Architecture

The change consists of three coordinated modifications:

1. **Delete the incorrect HubL translation class.** Without a translation class, the `BaseSyntax` translator falls back to the not-implemented stub (a comment), exactly as for `{break}`.

2. **Update the HubL user guide** to move `{showsnippet}` from the "Fully supported" list to the "Not supported" list, with a brief explanation of the missing infrastructure.

3. **Document the server-side dictionary prerequisite** in the main `mailcode-documentation.md` showsnippet section, so developers using any backend are aware the command cannot function without this infrastructure.

No new PHP classes are introduced. No test suites are affected. The Apache Velocity translation remains unchanged.

---

## Rationale

- The current HubL translation for `{showsnippet}` emits `{{ var }}`, which silently produces wrong output (it reads a HubL contact/email property instead of a text snippet). This is worse than a visible error.
- The established pattern for unsupported HubL commands (see `{break}`) is to delete the translation class and document the limitation — not to emit a silent wrong translation.
- The prerequisite note in the general docs makes the infrastructure dependency discoverable without reading backend-specific guides.

---

## Detailed Steps

1. **Delete** `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php`.

2. **Edit** `docs/user-guide/translate-hubl.md`:
   - Remove `- \`{showsnippet}\`` from the "Fully supported" bullet list.
   - In the "Not supported" section, add a new bullet for `{showsnippet}` with the following note:
     > `{showsnippet}` — Requires a server-side dictionary infrastructure (DictionaryTool) to resolve snippet names to their text content at send time. This infrastructure does not exist in HubL. The command is replaced by a HubL comment in the output.

3. **Edit** `docs/user-guide/mailcode-documentation.md` — In the "Inserting text snippets" section (around line 434), add a prerequisite callout immediately after the introductory paragraph (before the first code block), e.g.:
   > **Prerequisite:** The `showsnippet` command depends on a server-side snippet dictionary that must be configured for the target mail platform. In Apache Velocity, this is provided by the `DictionaryTool`. HubL does not support this infrastructure and cannot use this command.

4. **Verify** `docs/agents/project-manifest/constraints.md` — Confirm that `showsnippet` is already absent from the HubL coverage list in the Translation Coverage section. If found, remove it. No other change to this file is expected.

5. **Run** `composer test` to confirm 519 tests pass (baseline) and no regressions were introduced.

6. **Run** `composer analyze` to confirm PHPStan level 9 compliance is maintained.

---

## Dependencies

- No external dependencies. The deletion of the translation class is sufficient for the translator to fall back to its not-implemented stub behavior.

---

## Required Components

**Modified (existing files):**
- `docs/user-guide/translate-hubl.md`
- `docs/user-guide/mailcode-documentation.md`

**Deleted:**
- `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php`

**Verified only (no change expected):**
- `docs/agents/project-manifest/constraints.md`

---

## Assumptions

- The `BaseSyntax` translator emits a "not implemented" comment stub for commands with no translation class, matching the behavior observed for `{break}`. This should be verified by the Engineer before completing Step 1.
- The Apache Velocity `ShowSnippetTranslation` and its documentation are intentionally left unchanged — the dictionary infrastructure exists there.

---

## Constraints

- PHPStan level 9 must remain clean after deletion.
- The 519-test baseline must not regress.
- No new PHP source files are to be created.
- The HubL "not supported" comment format must match the existing `{break}` pattern: `{# !<command> is not supported in HubL! #}`.

---

## Out of Scope

- Implementing a HubL equivalent for `{showsnippet}` (no such infrastructure exists).
- Modifying Apache Velocity translation or tests.
- Adding a new HubL test suite for showsnippet.
- Changes to the `ShowSnippet` command class itself.

---

## Acceptance Criteria

- `src/Mailcode/Translator/Syntax/HubL/ShowSnippetTranslation.php` no longer exists.
- `{showsnippet}` does **not** appear under "Fully supported" in `translate-hubl.md`.
- `{showsnippet}` **does** appear under "Not supported" in `translate-hubl.md` with an explanation of the missing infrastructure.
- `docs/user-guide/mailcode-documentation.md` contains a prerequisite note in the showsnippet section that explains the server-side dictionary dependency and the HubL limitation.
- `composer test` exits with code 0, 519 tests passing.
- `composer analyze` exits with code 0 (PHPStan level 9 clean).

---

## Testing Strategy

This is a deletion-and-documentation change with no new code paths. The existing test suite is used as a regression guard:

- **`composer test`**: Confirms no existing tests break after removing the HubL translation class.
- **`composer analyze`**: Confirms no orphaned type references or interface violations remain after the class deletion.

If the Engineer discovers that removing the translation class causes any test to fail (e.g., a test asserting HubL can translate `{showsnippet}`), that test must be removed or updated to assert the "not supported" comment output instead.

---

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **`BaseSyntax` throws an exception instead of emitting a stub comment when no translation class is found** | Engineer must verify the fallback behavior before deleting the file. If it throws, the `BreakTranslation` pattern (stub class returning a comment) must be kept but its `translate()` body changed to emit the correct "not supported" comment. |
| **A test in the suite asserts HubL showsnippet output** | No such test file was found in `tests/testsuites/Translator/HubL/`, but the Engineer should run `composer test-filter -- showsnippet` to be certain before deleting the translation class. |
| **`constraints.md` HubL coverage list diverges further from reality** | The plan scopes a read-only verification step. The Engineer should note any other discrepancies found and flag them for a separate manifest cleanup. |
