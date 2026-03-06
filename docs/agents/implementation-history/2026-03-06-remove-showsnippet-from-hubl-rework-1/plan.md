# Plan

## Summary

Address all five strategic recommendations surfaced in the synthesis for the "Remove `{showsnippet}` from HubL Translation" plan cycle. The work spans four discrete concerns: (1) a HIGH-priority HubL manifest audit that corrects an incomplete coverage table in `constraints.md`, (2) a MEDIUM-priority regression test for the `ShowSnippet` stub translation, (3) a MEDIUM-priority architectural improvement adding an unsupported-commands registry to `BaseSyntax` to eliminate the need for content-free stub files, and (4) a LOW-priority housekeeping batch that back-fills the `BreakTranslation.php` docblock and removes residual `★` annotation markers from `file-tree.md`.

---

## Architectural Context

### Relevant modules and patterns

- **`src/Mailcode/Translator/BaseSyntax.php`** — abstract base for all syntax translators. `createTranslator()` (lines 39–73) dynamically resolves a per-command translation class by class name and throws `Mailcode_Translator_Exception` if it does not exist. This is the root cause forcing stub files.
- **`src/Mailcode/Translator/Syntax/HubLSyntax.php`** — HubL syntax entry point; extends `BaseSyntax`. Currently carries no unsupported-command logic.
- **`src/Mailcode/Translator/Syntax/HubL/`** — 17 translation class files (full list: `BreakTranslation`, `CodeTranslation`, `CommentTranslation`, `ElseIfTranslation`, `ElseTranslation`, `EndTranslation`, `ForTranslation`, `IfTranslation`, `MonoTranslation`, `SetVariableTranslation`, `ShowDateTranslation`, `ShowEncodedTranslation`, `ShowNumberTranslation`, `ShowPhoneTranslation`, `ShowSnippetTranslation`, `ShowURLTranslation`, `ShowVariableTranslation`).
- **`docs/agents/project-manifest/constraints.md`** — HubL coverage table (Translation Coverage section) currently lists only **6 commands** (`showvar`, `showencoded`, `showurl`, `setvar`, `showdate`, `for`) plus a generic note about stubs. It is missing `shownumber`, `showphone`, `mono`, `code`, `comment`, `else`, `end`, `break`, and the If/ElseIf family.
- **`docs/agents/project-manifest/file-tree.md`** — contains five residual `★` annotation markers (lines 33, 56, 98, 105, 154) on non-added entries (`Mailcode.php`, `Command.php`, `IfBase.php`, `ShowBase.php`, `Safeguard.php`).
- **`src/Mailcode/Translator/Syntax/HubL/BreakTranslation.php`** — existing stub with a minimal docblock; lacks a rationale paragraph explaining why `{break}` cannot be translated to HubL.
- **`tests/testsuites/Translator/HubL/`** — 24 existing HubL test files; no file for `ShowSnippet` or `Break` stub behavior.
- **`tests/assets/classes/HubLTestCase.php`** — abstract test case base; used by all HubL test files via `runCommands()`.

---

## Approach / Architecture

### WP-A — HubL Manifest Audit (HIGH)

Rewrite the "Translation Coverage" section of `constraints.md` with a complete, structured table. Divide coverage into three tiers:

| Tier | Definition |
|------|------------|
| **Fully Translated** | Command has a real translation class that emits valid HubL |
| **Stub / Not Supported** | Command has a stub class that emits `{# !cmd is not supported in HubL! #}` |
| **No class / Not applicable** | (ApacheVelocity only; HubL has no such case currently) |

List all 17 HubL translation file names against their assigned tier. No source code changes are needed — this is documentation only.

### WP-B — `ShowSnippetTests.php` Regression Guard (MEDIUM)

Create `tests/testsuites/Translator/HubL/ShowSnippetTests.php` following the exact pattern of `ShowVariableTests.php`. Assert that `{# !showsnippet is not supported in HubL! #}` is returned for all 6 known `ShowSnippet` command variants:

1. Basic (`{showsnippet: $SNIPPET}`)
2. URL-encoding (`{showsnippet: $SNIPPET urlencode:}`)
3. URL-decoding (`{showsnippet: $SNIPPET urldecode:}`)
4. No-HTML (`{showsnippet: $SNIPPET nohtml:}`)
5. With-namespace (`{showsnippet: $SNIPPET namespace="ns"}`)
6. No-HTML + URL-encoded (`{showsnippet: $SNIPPET nohtml: urlencode:}`)

Use `Mailcode_Factory::show()->snippet(...)` to create commands with the appropriate method chaining. Expected result for all cases: `{# !showsnippet is not supported in HubL! #}`.

Update `file-tree.md` to add `ShowSnippetTests.php` to the HubL test listing.

### WP-C — `BaseSyntax` Unsupported-Commands Registry (MEDIUM)

Add an overridable method to `BaseSyntax`:

```php
/**
 * Returns a list of command IDs that this syntax does not support.
 * If a command matches one entry, translateCommand() will return
 * "{# !<commandName> is not supported in <syntaxID>! #}" instead of
 * throwing an exception.
 *
 * @return string[]
 */
protected function getUnsupportedCommands() : array
{
    return array();
}
```

Modify `createTranslator()` (or `translateCommand()`) to check `getUnsupportedCommands()` before attempting class resolution. If the command ID is in the list, return the canonical not-supported comment immediately.

Override `getUnsupportedCommands()` in `HubLSyntax.php` to return `['Break', 'ShowSnippet']`.

The existing `BreakTranslation.php` and `ShowSnippetTranslation.php` stub files **must not be deleted** in this WP — they are still required by `ClassCache` discovery. A follow-up plan (outside this rework's scope) can deprecate or remove them once the registry proves stable. However, the registry acts as a safety net: if the stub files are ever accidentally removed, the architecture handles it gracefully.

Update `constraints.md` to document the new pattern and update `api-surface.md` with the new method signature.

### WP-D — Housekeeping (LOW)

Two sub-tasks batched into one work package:

**D1 — `BreakTranslation.php` docblock back-fill:**  
Add a rationale paragraph mirroring the quality of `ShowSnippetTranslation.php`:  
*"HubL for-loops do not support early-exit (`{break}`). There is no equivalent HubL statement."*  
Cross-reference `ShowSnippetTranslation` as the canonical stub pattern.

**D2 — `file-tree.md` `★` marker removal:**  
Strip the `★` character prefix from the annotation comments on lines 33, 56, 98, 105, and 154. Replace with plain descriptive text (e.g., `# Main entry point class`, `# Abstract base for all commands`, etc.).

---

## Rationale

- **WP-A first** because a correct constraints manifest is a prerequisite for every future manifest-first agent session. An agent that reads the old table and concludes HubL does not support `{shownumber}` or `{mono}` will produce incorrect plans.
- **WP-B immediately after WP-A** to lock in the stub behavior as an automated regression guard, preventing silent rollbacks.
- **WP-C sequenced after WP-B** so the regression test is already in place before the `BaseSyntax` internals change. The test verifies behavior regardless of the underlying mechanism.
- **WP-D batched last** as it is purely cosmetic/documentation and has no blockers.

---

## Detailed Steps

1. **WP-A** — Audit all 17 files in `src/Mailcode/Translator/Syntax/HubL/`, classify each as Fully Translated or Stub. Rewrite the Translation Coverage section of `constraints.md` with a two-column table.
2. **WP-B** — Create `tests/testsuites/Translator/HubL/ShowSnippetTests.php` with the 6 stub-assertion test cases. Run `composer test` to verify 525 passing tests (519 baseline + 6 new). Update `file-tree.md` HubL test listing.
3. **WP-C** — Add `getUnsupportedCommands(): array` to `BaseSyntax`. Update `translateCommand()` to check command ID against the list and return the not-supported comment. Override the method in `HubLSyntax`. Run PHPStan (level 9) and `composer test`. Update `constraints.md` and `api-surface.md`.
4. **WP-D** — Back-fill `BreakTranslation.php` docblock. Remove five `★` markers from `file-tree.md`.

---

## Dependencies

- WP-B depends on WP-A completing first (manifest must be correct before the test is considered "validated by manifest").
- WP-C depends on WP-B (test guards must exist before changing `BaseSyntax` internals).
- WP-D has no dependencies; it can be done at any point but is most efficiently run last.

---

## Required Components

### Modified files
- `docs/agents/project-manifest/constraints.md` — WP-A, WP-C
- `docs/agents/project-manifest/api-surface.md` — WP-C (new `getUnsupportedCommands()` signature)
- `docs/agents/project-manifest/file-tree.md` — WP-B (new test file entry), WP-D2 (★ removal)
- `src/Mailcode/Translator/BaseSyntax.php` — WP-C
- `src/Mailcode/Translator/Syntax/HubLSyntax.php` — WP-C
- `src/Mailcode/Translator/Syntax/HubL/BreakTranslation.php` — WP-D1

### New files
- `tests/testsuites/Translator/HubL/ShowSnippetTests.php` — WP-B

---

## Assumptions

- All 17 HubL translation files have been inspected and correctly categorized as Fully Translated or Stub (to be confirmed during WP-A).
- `ClassCache` continues to require stub files to exist on disk; the registry approach does not remove the stub files.
- `getUnsupportedCommands()` returns command ID strings (e.g., `'Break'`, `'ShowSnippet'`) matching the `$command->getID()` return value used in `createTranslator()`.
- Test baseline is 519 passing tests (last confirmed in the previous synthesis).

---

## Constraints

- PHPStan must remain clean at level 9 after every WP.
- `declare(strict_types=1)` in all modified/created PHP files.
- New test file must use namespace `MailcodeTests\Translator\HubL` per `constraints.md`.
- The Registry implementation in WP-C must not break existing Apache Velocity translation (Apache Velocity's `getUnsupportedCommands()` returns `[]` by default).
- Stub files (`BreakTranslation.php`, `ShowSnippetTranslation.php`) must not be deleted in this rework cycle.

---

## Out of Scope

- Deleting or deprecating existing stub translation files.
- Adding HubL support for any currently-unsupported command.
- Updating `changelog.md` or bumping the version number.
- Any changes to the Apache Velocity syntax.

---

## Acceptance Criteria

- `constraints.md` Translation Coverage section lists all 17 HubL translation classes with correct tier assignments.
- `tests/testsuites/Translator/HubL/ShowSnippetTests.php` exists and passes for all 6 variants.
- `composer test` reports ≥ 525 passing tests, 0 failures.
- `composer analyze` reports 0 errors at PHPStan level 9.
- `BaseSyntax::getUnsupportedCommands()` is present and documented; `HubLSyntax` overrides it with `['Break', 'ShowSnippet']`.
- `BreakTranslation.php` has a rationale docblock paragraph.
- `file-tree.md` contains no `★` annotation characters on lines 33, 56, 98, 105, or 154.
- `api-surface.md` documents `getUnsupportedCommands()`.

---

## Testing Strategy

- **WP-A, WP-D:** Documentation-only changes; validated by human review and confirmed by running PHPStan (no PHP code changes).
- **WP-B:** New PHPUnit test file. Run `composer test` to confirm the new tests pass and baseline is not regressed.
- **WP-C:** Run `composer analyze` (PHPStan level 9) and `composer test` after modifying `BaseSyntax` and `HubLSyntax`. The tests added in WP-B serve as the regression guard for `translateCommand()` behavior.

---

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **`getUnsupportedCommands()` short-circuits the exception path, breaking visibility of truly unknown commands** | The method only fires when the command ID is in the explicit list; unknown commands still throw `Mailcode_Translator_Exception` as before |
| **HubL classification in WP-A is incorrect for edge-case commands (e.g., `Mono`, `Code`)** | Inspect each translation file's `translate()` method body during WP-A; mark as stub only when it returns a literal comment string |
| **ShowSnippet test variants are incomplete** | Cross-reference `ShowSnippet.php` command class and `Snippet.php` factory to enumerate all parameter combinations before writing the test |
| **`ClassCache` auto-discovery picks up both the stub files and the new registry mechanism, causing double-handling** | Registry check happens *before* class resolution in `createTranslator()`; class files are still loaded but their `translate()` output is bypassed — OR — registry check happens in `translateCommand()` before calling `createTranslator()`. The latter avoids the class entirely and is the safer approach |
