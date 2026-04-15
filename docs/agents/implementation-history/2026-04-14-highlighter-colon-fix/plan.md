# Plan

## Summary

Fix a bug in `Mailcode_Commands_Highlighter` where the colon separator between the command name and parameters is missing when a command has optional parameters. The `{mono multiline:}` command is highlighted without the colon (`{mono multiline:}` instead of `{mono: multiline:}`). The root cause is that the colon rendering is gated on `requiresParameters()` rather than on the actual presence of parameters.

## Architectural Context

The command string reconstruction system has two parallel paths:

- **Normalizer** (`src/Mailcode/Commands/Normalizer.php`): Produces the canonical text form of a command. Adds `: ` in `addParams()` whenever the params string is non-empty — no dependency on `requiresParameters()`. This works correctly.
- **Highlighter** (`src/Mailcode/Commands/Highlighter.php`): Produces an HTML-highlighted form of a command. Currently renders the colon in `appendCommand()`, gated on `requiresParameters()`. This is the buggy path.

The `mono` command (`src/Mailcode/Commands/Command/Mono.php`) returns `false` from `requiresParameters()` because its parameters (e.g., `multiline:`, CSS classes) are optional. When these optional parameters are present, the Highlighter omits the colon separator.

## Approach / Architecture

Mirror the Normalizer's strategy in the Highlighter: move the colon rendering from `appendCommand()` into `appendParams()`, so it is emitted based on the actual presence of parameter tokens rather than on whether parameters are required.

### Before (buggy)

In `appendCommand()`:
```php
if($this->command->requiresParameters())
{
    $this->parts[] = $this->renderTag(array('hyphen'), ':');
    $this->parts[] = '<wbr>';
}
```

In `appendParams()`:
```php
if(!empty($tokens))
{
    $this->parts[] = ' ';
    // ... token rendering ...
}
```

### After (fixed)

In `appendCommand()`:
```php
// Colon logic removed from here entirely
```

In `appendParams()`:
```php
if(!empty($tokens))
{
    $this->parts[] = $this->renderTag(array('hyphen'), ':');
    $this->parts[] = '<wbr>';
    $this->parts[] = ' ';
    // ... token rendering ...
}
```

This ensures the colon is rendered if and only if tokens are present, regardless of whether the command considers parameters mandatory.

## Rationale

- The Normalizer already implements the correct logic. Aligning the Highlighter with the same strategy ensures consistency and eliminates this class of bugs.
- Moving the colon into `appendParams()` is the minimal, targeted fix. It does not affect commands that require parameters (they always have tokens), and it correctly handles commands with optional parameters.
- No other commands are affected negatively: commands that require parameters will still get the colon (they always have tokens), and parameterless commands (`{else}`, `{end}`, `{break}`) will not get a colon (no tokens).

## Detailed Steps

1. **Edit `src/Mailcode/Commands/Highlighter.php` — `appendCommand()` method:**
   Remove the `requiresParameters()` block that renders the colon and `<wbr>`.

2. **Edit `src/Mailcode/Commands/Highlighter.php` — `appendParams()` method:**
   Inside the `if(!empty($tokens))` block, insert the colon rendering (`renderTag(array('hyphen'), ':')` and `<wbr>`) before the space and params `<span>`.

3. **Add a highlighting test to `tests/testsuites/Commands/Types/MonoTests.php`:**
   Add a `test_highlight()` method that verifies:
   - `{mono}` (no params) → no colon in highlighted output
   - `{mono: multiline:}` → colon present in highlighted output
   - `{mono: multiline: "class1"}` → colon present in highlighted output

4. **Run the full test suite** (`composer test`) to confirm no regressions.

5. **Run PHPStan** (`composer analyze`) to confirm level 9 compliance.

## Dependencies

- None. This is a self-contained bug fix in the Highlighter class.

## Required Components

- `src/Mailcode/Commands/Highlighter.php` — Bug fix (edit existing)
- `tests/testsuites/Commands/Types/MonoTests.php` — New test method (edit existing)

## Assumptions

- The Normalizer's approach (colon when params are present) is the intended behavior for all rendering paths.
- No command exists that should show parameters *without* a colon separator in the highlighted output.

## Constraints

- Must maintain PHPStan level 9 compliance.
- Must not break existing highlighting tests for other commands.

## Out of Scope

- Refactoring the Highlighter or Normalizer beyond this fix.
- Reviewing other commands for similar optional-parameter edge cases (can be a follow-up audit).

## Acceptance Criteria

- `Mailcode_Factory::misc()->mono(true)->getHighlighted()` contains the colon separator between `mono` and `multiline:`.
- `Mailcode_Factory::misc()->mono()->getHighlighted()` does NOT contain a colon (no params).
- All existing tests pass.
- PHPStan level 9 passes.

## Testing Strategy

- Add a dedicated `test_highlight()` method in `MonoTests.php` covering all three mono variants (no params, multiline, multiline with class).
- Run the full PHPUnit suite to detect regressions in other commands' highlighting.
- Run PHPStan to validate type safety.

## Risks & Mitigations

| Risk | Mitigation |
|------|------------|
| **Other commands with `requiresParameters()=true` could get a double colon** | Moving the colon out of `appendCommand()` and into `appendParams()` means it's only rendered once, in one place. Commands requiring params always have tokens, so the colon still appears exactly once. |
| **Commands with no params could unexpectedly gain a colon** | The colon in `appendParams()` is inside `if(!empty($tokens))`, so parameterless commands are unaffected. |
