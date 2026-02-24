# Mailcode — PHP Syntax Parser for Email Preprocessing

**A backend-agnostic preprocessor command language for email templates.** Mailcode provides a unified, verbose syntax for variables, conditionals, loops, and formatting commands that can be translated into target preprocessor languages like **Apache Velocity** or **Hubspot HubL**.

[![PHP](https://img.shields.io/badge/PHP-%3E%3D7.4-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

---

## Why Mailcode Exists

Email template systems often depend on a specific backend preprocessor (Velocity, HubL, etc.). Mailcode **decouples the authoring experience** from the backend implementation: authors write in one readable syntax, and the library translates it to whatever the backend requires. This makes email editor interfaces portable across different mailing platforms.

## Syntax at a Glance

Mailcode is intentionally verbose — every command reads like a sentence, making templates self-documenting and easy to review without knowing the backend.

**Display a variable value**

```
Hello {showvar: $CUSTOMER.FIRSTNAME}, your order is ready.
```

**Format a date, a number, or a price**

```
Ordered on: {showdate: $ORDER.DATE "d.m.Y"}
Items in cart: {shownumber: $CART.ITEMS "1,000"}
Total: {showprice: $ORDER.TOTAL}
```

**Conditional logic with readable subtypes**

```
{if variable: $CUSTOMER.STATUS == "premium"}
    You have free shipping on this order.
{elseif list-contains: $ORDER.TAGS "fragile"}
    Please handle with care.
{else}
    Standard shipping rates apply.
{end}
```

**Loop through a list variable**

```
Your ordered products:

{for: $PRODUCT in: $USER_LIST_PRODUCTS}
- {showvar: $PRODUCT.NAME}: {showprice: $PRODUCT.PRICE}
{end}
```

**Build tracking URLs with automatic encoding**

```
{showurl: "header-cta"}
    https://example.com/offers?ref={showvar: $CUSTOMER.ID urlencode:}&domain={showvar: $CUSTOMER.DOMAIN idnencode: urlencode:}
{showurl}
```

All of the above translates automatically to Apache Velocity or HubL — no template changes required.

---

## Quick Start

```bash
composer require mistralys/mailcode
```

```php
use Mailcode\Mailcode;
use AppUtils\FileHelper\FolderInfo;

// Required: set a cache folder for class discovery
Mailcode::setCacheFolder(FolderInfo::factory('/path/to/cache'));

// Parse a string containing Mailcode commands
$collection = Mailcode::create()->parseString('{showvar: $CUSTOMER.NAME}');

// Safeguard commands during text processing
$safeguard = Mailcode::create()->createSafeguard($htmlContent);
$safe = $safeguard->makeSafe();
// ... process the text freely ...
$result = $safeguard->makeWhole($safe);

// Translate to Apache Velocity
$velocity = Mailcode::create()->createTranslator()->createApacheVelocity();
$output = $velocity->translateSafeguard($safeguard);
```

## Folder Overview

| Folder | Purpose |
|--------|---------|
| **`src/`** | All library source code. Entry point: `Mailcode.php`. |
| **`src/Mailcode/Commands/`** | Command definitions — the 20+ command types (show, if, for, set, etc.) and their type hierarchy. |
| **`src/Mailcode/Parser/`** | String parsing engine: regex matching, statement tokenization, safeguard system, and formatting pipeline. |
| **`src/Mailcode/Translator/`** | Output syntax translators (Apache Velocity, HubL) with per-command translation classes. |
| **`src/Mailcode/Factory/`** | Programmatic command creation organized into command sets (show, set, if, elseif, misc, var). |
| **`src/Mailcode/Variables/`** | Variable parsing and representation (`$PATH.NAME` pattern). |
| **`src/Mailcode/Traits/`** | Reusable validation and capability traits for commands (encoding, keywords, search terms, etc.). |
| **`src/Mailcode/Interfaces/`** | Corresponding interfaces for the traits system. |
| **`src/Mailcode/Date/`** | Date format validation and character definitions. |
| **`src/Mailcode/Number/`** | Number and currency formatting configuration. |
| **`src/Mailcode/Decrypt/`** | Decryption key name management for encrypted variable values. |
| **`src/Mailcode/Collection/`** | Command collection utilities: nesting validation, type filtering, error tracking. |
| **`css/`** | Stylesheets for HTML syntax highlighting of commands. |
| **`localization/`** | Translation files (German locale included). |
| **`tests/`** | PHPUnit test suites — extensive coverage across all subsystems. |
| **`tools/`** | Browser-based utilities (syntax translator, highlighter, phone country extractor). |
| **`docs/`** | Documentation: user guides, architecture reference, PHPDoc config. |

## Public API / Entry Points

All interactions start through the **`Mailcode`** class or the **`Mailcode_Factory`** static API:

| Entry Point | Method | Returns |
|-------------|--------|---------|
| **Parse commands** | `Mailcode::create()->parseString($text)` | `Mailcode_Collection` |
| **Safeguard text** | `Mailcode::create()->createSafeguard($text)` | `Mailcode_Parser_Safeguard` |
| **Find variables** | `Mailcode::create()->findVariables($text)` | `Mailcode_Variables_Collection_Regular` |
| **Translate syntax** | `Mailcode::create()->createTranslator()` | `Mailcode_Translator` |
| **Preprocess** | `Mailcode::create()->createPreProcessor($text)` | `Mailcode_PreProcessor` |
| **CSS styling** | `Mailcode::create()->createStyler()` | `Mailcode_Styler` |
| **Create commands** | `Mailcode_Factory::show()`, `::set()`, `::if()`, `::elseIf()`, `::misc()`, `::var()` | Command set instances |
| **Render commands** | `Mailcode_Factory::createRenderer()` | `Mailcode_Renderer` |
| **Date format info** | `Mailcode_Factory::createDateInfo()` | `Mailcode_Date_FormatInfo` |

## Documentation Index

| Document | Description |
|----------|-------------|
| [Usage Guide](docs/user-guide/usage-guide.md) | Full syntax reference, all commands, encoding, safeguarding, formatting, and translation examples. |
| [Architecture](docs/architecture.md) | Internal class hierarchy, pipeline diagram, subsystem descriptions, and dependency graph. |
| [Apache Velocity Translation](docs/user-guide/translate-apache-velocity.md) | Velocity-specific translation details, required tools, and configuration. |
| [HubL Translation](docs/user-guide/translate-hubl.md) | Hubspot HubL translation support and limitations. |
| [Changelog](changelog.md) | Version history and breaking changes. |

## Supported Commands (Summary)

| Category | Commands |
|----------|----------|
| **Display** | `{showvar}`, `{showdate}`, `{shownumber}`, `{showprice}`, `{showsnippet}`, `{showencoded}`, `{showphone}`, `{showurl}` |
| **Variables** | `{setvar}` (string, arithmetic, list counting) |
| **Conditionals** | `{if}`, `{elseif}`, `{else}`, `{end}` — with 16 subtypes (variable, contains, empty, list-contains, begins-with, bigger-than, etc.) |
| **Loops** | `{for}`, `{break}` |
| **Formatting** | `{mono}`, `{code}` (preprocessed) |
| **Meta** | `{comment}` |

## Translation Targets

| Syntax | Coverage |
|--------|----------|
| **Apache Velocity** | Full — all commands translated |
| **Hubspot HubL** | Partial — `showvar`, `showencoded`, `showurl`, `setvar`, subset of `if`/`elseif` |

## License

[MIT](LICENSE) — Sebastian Mordziol / [Mistralys](https://github.com/Mistralys)
