# Mailcode

**A backend-agnostic preprocessor command language for email templates.**

## What Is Mailcode?

Email template systems often lock you into a specific backend preprocessor — Velocity, HubL, or whatever your mailing platform speaks. Mailcode frees you from that. Authors write in one readable, verbose syntax, and the library translates it into whatever the backend requires. Your email editor becomes portable across platforms without rewriting a single template.

## Features

- **One syntax, multiple backends** — Write once, translate to Apache Velocity or Hubspot HubL automatically.
- **Self-documenting templates** — Commands read like sentences (`{if variable: $STATUS == "premium"}`), making reviews easy for non-developers.
- **Rich command set** — Variables, conditionals (16 subtypes), loops, date/number/price formatting, phone formatting, URL encoding, snippets, and more.
- **Safeguard system** — Protect Mailcode commands while you process surrounding HTML/text freely, then restore them intact.
- **Programmatic command creation** — Build commands in PHP via a fluent factory API, not just by parsing strings.
- **Syntax highlighting** — Render commands with CSS-styled HTML for visual editors.
- **Validation built in** — Parse results include detailed error reporting; no silent failures.

## Requirements

- PHP **>= 8.4**
- Composer
- `ext-json`

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

## Syntax at a Glance

```
Hello {showvar: $CUSTOMER.FIRSTNAME}, your order is ready.

Ordered on: {showdate: $ORDER.DATE "d.m.Y"}
Total: {showprice: $ORDER.TOTAL}

{if variable: $CUSTOMER.STATUS == "premium"}
    You have free shipping on this order.
{end}

{for: $PRODUCT in: $USER_LIST_PRODUCTS}
- {showvar: $PRODUCT.NAME}: {showprice: $PRODUCT.PRICE}
{end}
```

## Supported Commands

| Category | Commands |
|----------|----------|
| **Display** | `{showvar}`, `{showdate}`, `{shownumber}`, `{showprice}`, `{showsnippet}`, `{showencoded}`, `{showphone}`, `{showurl}` |
| **Variables** | `{setvar}` (string, arithmetic, list counting) |
| **Conditionals** | `{if}`, `{elseif}`, `{else}`, `{end}` — 16 subtypes (variable, contains, empty, list-contains, begins-with, bigger-than, etc.) |
| **Loops** | `{for}`, `{break}` |
| **Formatting** | `{mono}`, `{code}` |
| **Meta** | `{comment}` |

## Translation Targets

| Syntax | Coverage |
|--------|----------|
| **Apache Velocity** | Full — all commands translated |
| **Hubspot HubL** | Partial — `{break}` and `{showsnippet}` not supported |

## Learn More

| Resource | Description |
|----------|-------------|
| [Usage Guide](docs/user-guide/mailcode-documentation.md) | Full syntax reference, all commands, encoding, safeguarding, and translation examples |
| [Apache Velocity Translation](docs/user-guide/translate-apache-velocity.md) | Velocity-specific translation details and configuration |
| [HubL Translation](docs/user-guide/translate-hubl.md) | Hubspot HubL translation support and limitations |
| [Changelog](changelog.md) | Version history and breaking changes |

## License

[MIT](LICENSE) — Sebastian Mordziol / [Mistralys](https://github.com/Mistralys)
