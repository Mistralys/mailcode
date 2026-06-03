# Constraints & Conventions

## Mandatory Setup

- **Cache folder required.** Before using the library, the cache folder must be set via `Mailcode::setCacheFolder(FolderInfo::factory('/path'))`. Without this, a `Mailcode_Exception` is thrown. The cache is used by `ClassCache` (via `ClassRepositoryManager`) for dynamic class discovery of commands and translator syntaxes.
- Falls back to `ClassHelper::getCacheFolder()` if available from the parent `application-utils` library.

## Naming Conventions

### Classes

- **Underscore-delimited class names** following the pattern `Mailcode_Component_SubComponent` (legacy convention, pre-PSR-4). Examples: `Mailcode_Parser_Safeguard`, `Mailcode_Commands_Command_ShowVariable`.
- Newer classes use proper namespaces (e.g., `Mailcode\Parser\ParseResult`, `Mailcode\Translator\SyntaxInterface`), but the bulk of the codebase uses the underscore convention.
- All classes live under the `Mailcode` namespace in `src/`.

### Commands

- Command class names match their Mailcode syntax name: `{showvar}` → `ShowVariable`, `{if}` → `If`, `{setvar}` → `SetVariable`.
- If/ElseIf subtypes use descriptive names: `Contains`, `Empty`, `BiggerThan`, `ListContains`, etc.
- Command types (`Standalone`, `Opening`, `Closing`, `Sibling`) determine block behavior.

### Variables

- Variables follow the pattern `$UPPERCASE_PATH.UPPERCASE_NAME` or `$UPPERCASE_NAME`.
- Path and name segments must be `[A-Z0-9_]+`.
- The `$` prefix is mandatory in Mailcode syntax. Helper functions `dollarize()` / `undollarize()` handle normalization.

### Validation Traits & Interfaces

- Every capability has a paired trait + interface:
  - Interface: `Mailcode/Interfaces/Commands/Validation/{CapabilityName}.php`
  - Trait: `Mailcode/Traits/Commands/Validation/{CapabilityName}.php`
- Commands declare capabilities by implementing the interface and using the trait.

## Strict Typing

- All source files use `declare(strict_types=1)`.
- PHPStan analysis is clean at **level 9** (the strictest practical level).
- **PHP 8.4 type system:** The project requires PHP >= 8.4. Native union types (`string|FolderInfo`), intersection types, and `never` are available and preferred over `@param`-only docblock annotations for all new and modified code. Use `@phpstan-param` annotations only when PHPStan requires a refinement that the native type cannot express (e.g., `class-string` narrowing within a `string` type).

## String Functions

- Use **`mb_strtolower()`** (not `strtolower()`) and **`mb_strtoupper()`** (not `strtoupper()`) whenever operating on strings that may contain non-ASCII characters (e.g., search terms, variable values, user content). The translator layer handles multilingual content and must be Unicode-safe throughout.

## Error Handling

- The library throws `Mailcode_Exception` for internal errors. Each exception has a unique integer error code defined as class constants (e.g., `ERROR_CACHE_FOLDER_NOT_SET`, `ERROR_INVALID_SYNTAX_NAME`).
- Validation errors are collected via `Mailcode_Collection_Error` objects, not exceptions. Call `$collection->isValid()` and `$collection->getErrors()` to inspect parse-time issues.
- `OperationResult` (from `application-utils`) is used for structured validation results.

## Safeguard Placeholder Format

- Placeholders are numeric strings matching the pattern `999XXXXXXXXX999` (e.g., `9990000000001999`). This format is chosen to survive HTML encoding, URL encoding, and most text transformations without corruption.
- Placeholders must not be manually modified; use `makeSafe()` / `makeWhole()`.

## Formatting System Rules

- **Replacers are mutually exclusive** — only one replacer can be active (HTMLHighlighting, Normalized, PreProcessing, or Remove).
- **Formatters are combinable** — multiple formatters (MarkVariables, SingleLines) can be stacked.

## Translation Coverage

- **Apache Velocity**: Full coverage — all commands have translation classes.
- **Hubspot HubL**: Partial coverage — see the table below. `Break` and `ShowSnippet` are declared unsupported via the registry and emit a "not supported" comment at runtime. `ShowPrice` has no translation class and is not declared in the unsupported registry; attempting to translate it to HubL will throw a `Mailcode_Translator_Exception` (unresolved defect).

#### Unsupported-Commands Registry Pattern

`BaseSyntax` provides an overridable `getUnsupportedCommands() : string[]` method that returns an empty list by default. `translateCommand()` checks this list **before** attempting class resolution: if the command's ID is in the list, a canonical `{# !command is not supported in SyntaxName! #}` comment is returned immediately, avoiding `Mailcode_Translator_Exception`. `HubLSyntax` overrides the method to declare `['Break', 'ShowSnippet']` as unsupported. Stub translation classes for these commands **must not be deleted** — they satisfy the `Mailcode_Translator_Command_*` interface contracts and serve as documentation of the unsupported status.
- Each syntax lives in `Translator/Syntax/{SyntaxName}/` with one translation class per command.
- Syntax classes are discovered dynamically via `ClassCache::findClassesInFolder()`.

### HubL Translation Coverage Table

Translation classes in `src/Mailcode/Translator/Syntax/HubL/`:

| Command | Translation Class | Tier |
|---------|-------------------|------|
| `break` | `BreakTranslation` | Stub / Not Supported |
| `code` | `CodeTranslation` | Fully Translated |
| `comment` | `CommentTranslation` | Fully Translated |
| `elseif` | `ElseIfTranslation` | Fully Translated |
| `else` | `ElseTranslation` | Fully Translated |
| `end` | `EndTranslation` | Fully Translated |
| `for` | `ForTranslation` | Fully Translated |
| `if` | `IfTranslation` | Fully Translated |
| `mono` | `MonoTranslation` | Fully Translated |
| `setvar` | `SetVariableTranslation` | Fully Translated |
| `showdate` | `ShowDateTranslation` | Fully Translated |
| `showencoded` | `ShowEncodedTranslation` | Fully Translated |
| `shownumber` | `ShowNumberTranslation` | Fully Translated |
| `showphone` | `ShowPhoneTranslation` | Fully Translated |
| `showprice` | *(none)* | Missing — No Translation Class |
| `showsnippet` | `ShowSnippetTranslation` | Stub / Not Supported |
| `showurl` | `ShowURLTranslation` | Fully Translated |
| `showvar` | `ShowVariableTranslation` | Fully Translated |

**Tier definitions:**

| Tier | Definition |
|------|------------|
| **Fully Translated** | Class emits valid HubL output for the command. |
| **Stub / Not Supported** | Class emits `{# !command is not supported in HubL! #}` — no HubL equivalent exists. |
| **Missing — No Translation Class** | No translation class exists and the command is not in the unsupported registry. Translating it will throw a `Mailcode_Translator_Exception`. |

## Command Parameter Syntax

- Parameters are parsed left-to-right by the tokenizer.
- String literals must use **double quotes** (`"`). Single quotes are not supported.
- Special characters in strings: escape double quotes with `\"`, escape curly braces with `\{` and `\}`. Note: `\{`/`\}` inside command parameters are handled by the `SpecialChars` tokenizer; the same escape syntax also works at the **template level** (outside commands) — see "Template-Level Bracket Escaping" below.
- Keywords (flags) are appended with a colon suffix: `insensitive:`, `regex:`, `urlencode:`, etc.

## Template-Level Bracket Escaping

A user may need to include `{...}` sequences in template text that are **not** Mailcode commands (e.g., a checksum placeholder `{CHECKSUM}` used by a downstream system). Prefix both brackets with a backslash to mark them as escaped:

```
\{CHECKSUM\} {showvar: $NAME}
```

The final output of any consumer pipeline (`makeWhole()`, `translateSafeguard()`, `PreProcessor::render()`) will strip the backslashes, producing:

```
{CHECKSUM} <restored-or-translated-command>
```

**How it works (pipeline):**
1. `StringPreProcessor::encodeBrackets()` converts `\{` → `__BRACKET_OPEN__` and `\}` → `__BRACKET_CLOSE__` before the parser regex runs, so escaped sequences are never mistaken for commands.
2. The safe string (after `makeSafe()` / `makeSafePartial()`) still contains the raw `\{`/`\}` sequences in the template-level text (all commands have already been replaced by numeric placeholders at this point).
3. At the output boundary of each consumer (`restore()` in `Safeguard`, `translateSafeguard()` in `BaseSyntax`, `render()` in `PreProcessor`), `PreParser::unescapeBrackets()` is called on the safe string **before** placeholder restoration. Because all commands are still placeholders at that point, only template-level escape sequences are unescaped — bracket escapes inside command parameters are untouched.

**Key rules:**
- Only template-level `\{`/`\}` sequences (outside of detected/parsed commands) are unescaped.
- `\{` and `\}` inside a **parsed** command's parameters are decoded by the tokenizer (`SpecialChars`) and are not affected by the output-boundary unescape step.
- A `\{` or `\}` that appears in text that **looks like but fails to parse as** a command (e.g., `{comment: data \}` where the `\}` eats the closing brace) will be unescaped in the output. This is intentional: from the library's perspective, the text is template-level content.
- **Double-escape** (`\\{`) to produce a literal `\{` in output is **not supported** in this version.

## Nested Command Escaping (v3.7.2)

Content-type commands (`{code}`, `{mono}`) cannot contain un-escaped Mailcode commands in their body. If a nested command is found unescaped inside the content block, `PreParser` adds a `VALIDATION_UNESCAPED_NESTED_COMMAND` error (code `49215`) to the collection with a message directing the author to escape the nested command's brackets.

**Fix:** Prefix both brackets of the nested command with a backslash — `\{showvar: $NAME\}`. `StringPreProcessor.encodeBrackets()` converts `\{`/`\}` to internal placeholders before the parser runs, so the escaped syntax is never matched as a live command.

**Key rule:** This applies only inside content-type command bodies. Template-level escaped brackets (`\{CHECKSUM\}`) outside commands follow the same mechanism but serve a different purpose (see "Template-Level Bracket Escaping" above).

## Date Translation Constraints

- **PHP format strings** are validated character-by-character against a closed whitelist (`Mailcode_Date_FormatInfo::validateFormat()`). Unrecognized characters are rejected.
- **Java internal format strings** (used by the `internal_format` translation parameter) are validated by `Mailcode_Date_FormatInfo::validateJavaFormat()`. Optional-section brackets (`[` and `]`) from `DateTimeFormatter` are rejected because the target platforms (Apache Velocity and HubL) use `SimpleDateFormat`, which does not support them.
- The **output LDML/Java format** is generated from a 1:1 character mapping table and cannot produce invalid characters.
- **HubL `internal_format` conditional pattern:** When `internal_format` is set on a `{showdate}` command, `ShowDateTranslation` emits a Jinja2 `is string` condition to support both HubL date objects and raw date strings:
  - String path: `var|strtotime("<internalFormat>")|format_datetime("<ldml>")`
  - Object path: `var|format_datetime("<ldml>")`
  - Full output: `{% if var is string %}{{ <string-path> }}{% else %}{{ <object-path> }}{% endif %}`
  Without `internal_format`, no conditional is emitted and only the object path is used.

## Collection Finalization

- After parsing, `Mailcode_Collection::finalize()` is called, which:
  1. Runs nesting validation (open/close block pairing).
  2. Prunes commands nested inside protected content blocks (e.g., `{code}`).
- A finalized collection cannot have commands added to it.

## Pre-Processing

- Pre-processable commands (e.g., `{mono}`) are rendered before the main document processing pipeline. They produce HTML output (e.g., `<code>` tags) and are then removed from the command stream.

## No Side Effects

- The library is purely functional in nature — it does not perform I/O beyond reading its own CSS files and the class cache. It does not send emails, connect to APIs, or modify the filesystem (except the class cache folder).

## Logging

- Optional PSR-3 logger support via `Mailcode::setLogger()`. Debug messages are only emitted when `Mailcode::setDebugging(true)` is called.

## Testing

- PHPUnit `>= 12.0` with test suites organized by subsystem under `tests/testsuites/`.
- Test bootstrap in `tests/bootstrap.php`.
- Test assets (helper classes, fixture files) in `tests/assets/`.
- Class cache for tests stored in `tests/cache/`.
- **Universal test namespace pattern:** Every test file under `tests/testsuites/` must use the namespace `MailcodeTests\{Suite}[\{SubDir}]`, where `{Suite}` matches the top-level directory and `{SubDir}` matches any intermediate directory. Examples:
  - `tests/testsuites/Commands/Types/` → `namespace MailcodeTests\Commands\Types;`
  - `tests/testsuites/Translator/HubL/` → `namespace MailcodeTests\Translator\HubL;`
  - `tests/testsuites/Variables/` → `namespace MailcodeTests\Variables;`
  Do **not** use the bare `Mailcode` namespace, the legacy `testsuites\...` prefix, or mixed-case variants such as `MailCodeTests` (uppercase 'C'). All test files must end in `*Tests.php` and include `declare(strict_types=1)`.
- **HubL-specific note (special case of the general pattern):** All HubL test files under `tests/testsuites/Translator/HubL/` use `namespace MailcodeTests\Translator\HubL;`. This is the canonical form — do **not** use `testsuites\Translator\HubL` or `MailCodeTests\Translator\HubL`.
- **HubL not-supported test helper:** When testing a command that emits a "not supported" comment, use `self::buildNotSupportedComment(string $commandId): string` defined on `HubLTestCase` (`tests/assets/classes/HubLTestCase.php`). This method returns the canonical `{# !<commandId> is not supported in HubL! #}` string and stays in sync with `BaseSyntax::translateCommand()`. Do **not** duplicate the format string as a private constant in individual test files.

### HubL Stub Test Convention

HubL translator test files fall into two categories with distinct patterns:

**Stub/unsupported commands** (e.g., `Break`, `ShowSnippet`):
- Use **separate `test_<variant>()` methods**, one per command variant.
- Each method contains a single `runCommands()` call.
- Do NOT use a single `test_translateCommand()` method with an array.
- This pattern makes test failures immediately locatable and keeps fixes granular.
- Reference file: `tests/testsuites/Translator/HubL/ShowSnippetTests.php`

**Fully-translated commands** (e.g., `ShowVariable`, `For`):
- Use a single `test_translateCommand()` method with an array of cases.
- Each array entry covers one output variant.

**Expected-string source:**
- Use `self::buildNotSupportedComment(string $commandId)` (defined on `HubLTestCase`) to derive the expected "not supported" comment. Do NOT hardcode private constants.

## Manifest Maintenance — Annotation Policy

- Temporary `★ Added` markers may be placed in `file-tree.md` during a plan cycle to highlight new files.
- After the plan's code-review cycle completes, all `★ Added` markers must be stripped from `file-tree.md`. The tree should always reflect the current state without historical markers.
- Agents executing post-plan cleanup work packages must remove all `★ Added` annotations as part of the housekeeping pass.
