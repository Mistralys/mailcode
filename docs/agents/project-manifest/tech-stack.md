# Tech Stack & Patterns

## Runtime & Language

| Item | Value |
|------|-------|
| Language | PHP |
| Minimum Version | `>= 7.4` (also supports PHP 8.x including 8.4) |
| Strict Typing | `declare(strict_types=1)` in all source files |
| Namespace | `Mailcode` (root); sub-namespaces for `Parser`, `Translator`, `Commands`, etc. |

## Package Identity

| Item | Value |
|------|-------|
| Composer Name | `mistralys/mailcode` |
| Type | Library |
| License | MIT |
| Author | Sebastian Mordziol (`s.mordziol@mistralys.eu`) |
| Current Version | 3.5.3 |

## Dependencies (Runtime)

| Package | Constraint | Purpose |
|---------|-----------|---------|
| `mistralys/application-utils` | `>= 2.1.2` | General utility classes (FileHelper, ConvertHelper, OperationResult, etc.) |
| `mistralys/application-utils-core` | `>= 2.3.7` | Core utilities (ClassHelper, ClassRepositoryManager) |
| `mistralys/application-localization` | `>= 1.5` | Localization / translation support (`t()` function) |
| `giggsey/libphonenumber-for-php` | `^8.12` | Phone number parsing and formatting for `{showphone}` |
| `monolog/monolog` | `>= 2.7` | PSR-3 compatible logging (optional debug logger) |
| `ext-json` | `*` | JSON extension (PHP built-in) |

## Dependencies (Dev)

| Package | Constraint | Purpose |
|---------|-----------|---------|
| `phpunit/phpunit` | `>= 9.6` | Unit testing |
| `phpstan/phpstan` | `>= 1.10` | Static analysis (clean at level 9) |

## Autoloading

| Strategy | Scope | Path |
|----------|-------|------|
| Classmap | Production | `src/` |
| Files | Production | `src/functions.php` (global helper functions) |
| Classmap | Dev | `tests/assets/` |
| Files | Dev | `tests/bootstrap.php` |

## Architectural Patterns

| Pattern | Where |
|---------|-------|
| **Registry / Repository** | `Mailcode_Commands` — discovers and caches all command classes from the filesystem. |
| **Factory (static)** | `Mailcode_Factory` — programmatic command creation via typed command sets. |
| **Strategy** | `Mailcode_Translator` + `SyntaxInterface` — each target syntax is a strategy. |
| **Trait + Interface** | Commands declare capabilities (URL encoding, case sensitivity, regex, etc.) via paired traits and interfaces in `Traits/Commands/Validation/` and `Interfaces/Commands/Validation/`. |
| **Observer / Listener** | `Mailcode_StringContainer` — notifies listeners on string mutation. |
| **Pipeline** | Parse → Collection → Safeguard → Formatting → Translation is a sequential pipeline. |
| **Placeholder / Safeguard** | Commands are replaced with numeric placeholders during text processing, then restored. |
| **Template Method** | `Mailcode_Commands_Command` (abstract) defines validation hooks; subclasses implement specifics. |
| **Singleton (lazy)** | `Mailcode_Translator::create()` uses a cached static instance. |
| **Command** | Each Mailcode command (`{showvar}`, `{if}`, etc.) is an object with type, parameters, and validation. |

## Build & Quality Tools

| Tool | Config File | Purpose |
|------|-------------|---------|
| Composer | `composer.json` | Dependency management and autoloading |
| PHPUnit | `phpunit.xml` | Test runner — all suites under `tests/testsuites/` |
| PHPStan | `tests/phpstan/config.neon` | Static analysis at level 9 |
| Makefile | `Makefile` | Build automation |
| PHPDoc | `docs/phpdoc/phpdoc.dist.xml` | API documentation generation |

## Localization

| Locale | Files |
|--------|-------|
| German (`de_DE`) | `localization/de_DE-mailcode-client.ini`, `localization/de_DE-mailcode-server.ini` |
| Storage index | `localization/storage.json` |
