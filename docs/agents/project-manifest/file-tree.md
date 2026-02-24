# File Tree

```
mailcode/
├── composer.json                      # Package metadata, dependencies, autoloading
├── phpunit.xml                        # PHPUnit configuration
├── Makefile                           # Build automation
├── changelog.md                       # Version history (v3.5.3 current)
├── README.md                          # Project overview and quick start
├── LICENSE                            # MIT license
│
├── css/                               # Stylesheets for HTML syntax highlighting
│   ├── highlight.css                  # Command highlight styles
│   └── marked-variables.css           # Variable marking styles
│
├── docs/
│   ├── architecture.md                # Internal architecture reference
│   ├── phpdoc/                        # PHPDoc generation config
│   └── user-guide/
│       ├── usage-guide.md             # Full syntax reference and examples
│       ├── translate-apache-velocity.md
│       └── translate-hubl.md
│
├── localization/                      # i18n translation files
│   ├── de_DE-mailcode-client.ini
│   ├── de_DE-mailcode-server.ini
│   └── storage.json
│
├── src/
│   ├── functions.php                  # Global helper functions (t(), dollarize(), etc.)
│   ├── Mailcode.php                   # ★ Main entry point class
│   └── Mailcode/
│       ├── ClassCache.php             # Dynamic class discovery & caching
│       ├── Collection.php             # Command container with validation
│       ├── Commands.php               # Command registry / repository
│       ├── Exception.php              # Library-specific exception
│       ├── Factory.php                # Static factory for command creation
│       ├── Parser.php                 # Regex-based command detection
│       ├── PreProcessor.php           # Pre-process commands (e.g., {mono} → <code>)
│       ├── Printer.php                # Command output to stdout
│       ├── Renderer.php               # Command-to-string conversion
│       ├── StringContainer.php        # Observable string wrapper
│       ├── Styler.php                 # CSS access for highlighting
│       ├── Translator.php             # Syntax translation orchestrator
│       ├── Variables.php              # Variable parsing ($PATH.NAME)
│       │
│       ├── Collection/                # Collection utilities
│       │   ├── Error.php              # Base validation error
│       │   ├── Error/                 # Error subtypes (Command, Message)
│       │   ├── NestingValidator.php   # Open/close block pairing
│       │   └── TypeFilter.php         # Filter commands by type
│       │
│       ├── Commands/                  # Command definitions
│       │   ├── Command.php            # ★ Abstract base for all commands
│       │   ├── Command/               # Concrete command classes
│       │   │   ├── Break.php
│       │   │   ├── Code.php
│       │   │   ├── Comment.php
│       │   │   ├── Else.php
│       │   │   ├── ElseIf.php         # + ElseIf/ (16 subtypes)
│       │   │   ├── End.php
│       │   │   ├── For.php
│       │   │   ├── If.php             # + If/ (16 subtypes)
│       │   │   ├── Mono.php
│       │   │   ├── SetVariable.php
│       │   │   ├── ShowDate.php
│       │   │   ├── ShowEncoded.php
│       │   │   ├── ShowNumber.php
│       │   │   ├── ShowPhone.php      # + ShowPhone/ (phone utilities)
│       │   │   ├── ShowPrice.php
│       │   │   ├── ShowSnippet.php
│       │   │   ├── ShowURL.php        # + ShowURL/ (URL utilities)
│       │   │   └── ShowVariable.php
│       │   │
│       │   ├── Command/If/            # 16 If subtypes
│       │   │   ├── BeginsWith.php
│       │   │   ├── BiggerThan.php
│       │   │   ├── Command.php        # If subtype base
│       │   │   ├── Contains.php
│       │   │   ├── Empty.php
│       │   │   ├── EndsWith.php
│       │   │   ├── EqualsNumber.php
│       │   │   ├── ListBeginsWith.php
│       │   │   ├── ListContains.php
│       │   │   ├── ListEndsWith.php
│       │   │   ├── ListEquals.php
│       │   │   ├── ListNotContains.php
│       │   │   ├── NotContains.php
│       │   │   ├── NotEmpty.php
│       │   │   ├── SmallerThan.php
│       │   │   └── Variable.php
│       │   │
│       │   ├── CommandException.php
│       │   ├── CommonConstants.php    # Shared command constants
│       │   ├── Highlighter.php        # HTML highlight rendering
│       │   ├── IfBase.php             # ★ Base for if/elseif commands
│       │   ├── Keywords.php           # Toggleable keyword flags
│       │   ├── LogicKeywords.php      # AND/OR compound conditions
│       │   ├── LogicKeywords/         # Logic keyword support classes
│       │   ├── Normalizer.php         # Command text normalization
│       │   ├── Normalizer/            # Normalizer utilities
│       │   ├── ParamsException.php
│       │   ├── ShowBase.php           # ★ Base for show* commands
│       │   └── Type/                  # Command type classes
│       │       ├── Type.php           # Type base
│       │       ├── Standalone         # Self-contained commands
│       │       ├── Opening            # Block-opening commands
│       │       ├── Closing            # Block-closing commands
│       │       └── Sibling            # Same-level commands (else, elseif)
│       │
│       ├── Date/                      # Date format handling
│       │   ├── FormatInfo.php         # Date format validation
│       │   └── FormatInfo/            # Character definitions
│       │
│       ├── Decrypt/                   # Decryption key management
│       │   └── DecryptSettings.php
│       │
│       ├── Factory/                   # Programmatic command creation
│       │   ├── CommandSets.php        # Command set registry
│       │   ├── CommandSets/
│       │   │   ├── IfBase.php         # Shared if/elseif factory logic
│       │   │   └── Set/               # Individual command sets
│       │   │       ├── ElseIf.php
│       │   │       ├── If.php
│       │   │       ├── Misc.php
│       │   │       ├── Set.php
│       │   │       ├── Show.php       # + Show/ (show command helpers)
│       │   │       └── Variables.php
│       │   ├── Exception.php
│       │   └── Instantiator.php       # Command instantiation logic
│       │
│       ├── Interfaces/                # Capability interfaces
│       │   └── Commands/
│       │       ├── Command.php        # Base command interface
│       │       ├── EncodableInterface.php
│       │       ├── ListVariables.php
│       │       ├── PreProcessing.php
│       │       ├── ProtectedContent.php
│       │       ├── TrackableInterface.php
│       │       └── Validation/        # 30+ validation interfaces
│       │
│       ├── Number/                    # Number formatting
│       │   ├── Info.php
│       │   └── LocalCurrency.php
│       │
│       ├── Parser/                    # Parsing engine
│       │   ├── Exception.php
│       │   ├── Match.php              # Single regex match
│       │   ├── ParseResult.php        # Parse operation result wrapper
│       │   ├── PreParser.php          # Protected content extraction
│       │   ├── PreParser/             # PreParser support classes
│       │   ├── Safeguard.php          # ★ Placeholder-based protection
│       │   ├── Safeguard/
│       │   │   ├── DelimiterValidator.php
│       │   │   ├── Formatter.php      # Formatting entry interface
│       │   │   ├── Formatting.php     # Formatting orchestrator
│       │   │   ├── Placeholder.php    # Single placeholder ↔ command
│       │   │   ├── PlaceholderCollection.php
│       │   │   ├── URLAnalyzer.php
│       │   │   └── Formatter/
│       │   │       ├── FormatType.php
│       │   │       ├── Location.php
│       │   │       ├── ReplacerType.php
│       │   │       └── Type/          # Formatter implementations
│       │   │           ├── HTMLHighlighting.php   # Replacer
│       │   │           ├── MarkVariables.php      # Formatter
│       │   │           ├── Normalized.php         # Replacer
│       │   │           ├── PreProcessing.php      # Replacer
│       │   │           ├── Remove.php             # Replacer
│       │   │           └── SingleLines.php        # Formatter
│       │   │
│       │   ├── Statement.php          # Parameter parsing
│       │   ├── Statement/
│       │   │   ├── Info.php           # Token query methods
│       │   │   ├── Info/              # Info support classes
│       │   │   ├── Tokenizer.php      # Parameter tokenizer
│       │   │   ├── Tokenizer/         # Token types
│       │   │   ├── Validator.php      # Statement validation
│       │   │   └── Validator/         # Validator support classes
│       │   └── StringPreProcessor.php # Raw string pre-processing
│       │
│       ├── Traits/                    # Capability traits
│       │   └── Commands/
│       │       ├── EncodableTrait.php
│       │       ├── PreProcessing.php
│       │       ├── ProtectedContent.php
│       │       ├── ListVariables.php
│       │       └── Validation/        # 30+ validation traits
│       │
│       ├── Translator/                # Output syntax generation
│       │   ├── BaseCommandTranslation.php   # Base for per-command translators
│       │   ├── BaseSyntax.php               # Base syntax class
│       │   ├── SyntaxInterface.php          # Syntax contract
│       │   ├── Exception.php
│       │   ├── Command/                     # Per-command translation interfaces
│       │   └── Syntax/
│       │       ├── ApacheVelocitySyntax.php           # Full coverage
│       │       ├── BaseApacheVelocityCommandTranslation.php
│       │       ├── ApacheVelocity/                    # 19 translation classes
│       │       ├── HubLSyntax.php                     # Partial coverage
│       │       ├── BaseHubLCommandTranslation.php
│       │       └── HubL/                              # 17 translation classes
│       │
│       └── Variables/                 # Variable handling
│           ├── Collection.php         # Variable collection base
│           ├── Collection/            # Collection subtypes (Regular)
│           └── Variable.php           # Single variable representation
│
├── tests/
│   ├── bootstrap.php                  # Test bootstrapping
│   ├── assets/                        # Test helper classes and fixtures
│   ├── cache/                         # Class repository cache
│   ├── phpstan/                       # PHPStan config and results
│   └── testsuites/                    # PHPUnit test suites
│       ├── Collection/
│       ├── Commands/
│       ├── Factory/
│       ├── Formatting/
│       ├── Highlighting/
│       ├── Isolation/
│       ├── LogicKeywords/
│       ├── Mailcode/
│       ├── Numbers/
│       ├── Parser/
│       ├── PreProcessor/
│       ├── StringContainer/
│       ├── Translator/
│       ├── Validator/
│       └── Variables/
│
├── tools/                             # Browser-based utilities
│   ├── syntax-highlighter.php
│   ├── translator.php
│   ├── extractPhoneCountries.php
│   ├── countrycodes.csv
│   ├── prepend.php
│   └── main.css
│
└── vendor/                            # Composer dependencies (auto-generated)
```
