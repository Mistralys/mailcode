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
- **Hubspot HubL**: Partial coverage — limited to `showvar`, `showencoded`, `showurl`, `setvar`, and a subset of `if`/`elseif` subtypes (`variable`, `empty`, `equals-number`, `smaller-than`, `bigger-than`).
- Each syntax lives in `Translator/Syntax/{SyntaxName}/` with one translation class per command.
- Syntax classes are discovered dynamically via `ClassCache::findClassesInFolder()`.

## Command Parameter Syntax

- Parameters are parsed left-to-right by the tokenizer.
- String literals must use **double quotes** (`"`). Single quotes are not supported.
- Special characters in strings: escape double quotes with `\"`, escape curly braces with `\{` and `\}`.
- Keywords (flags) are appended with a colon suffix: `insensitive:`, `regex:`, `urlencode:`, etc.

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

- PHPUnit `>= 9.6` with test suites organized by subsystem under `tests/testsuites/`.
- Test bootstrap in `tests/bootstrap.php`.
- Test assets (helper classes, fixture files) in `tests/assets/`.
- Class cache for tests stored in `tests/cache/`.
