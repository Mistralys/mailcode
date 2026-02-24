# Public API Surface

> Signatures only — no implementation logic.

---

## `Mailcode` (Entry Point)

```php
namespace Mailcode;

class Mailcode
{
    public const PACKAGE_NAME = 'Mailcode';

    public static function create(): Mailcode;
    public static function getName(): string;
    public static function setCacheFolder(FolderInfo $cacheFolder): void;
    public static function getCacheFolder(): FolderInfo;

    public function parseString(string $string): Mailcode_Collection;
    public function getParser(): Mailcode_Parser;
    public function getCommands(): Mailcode_Commands;
    public function createSafeguard(string $subject): Mailcode_Parser_Safeguard;
    public function createString(string $subject): Mailcode_StringContainer;
    public function findVariables(string $subject, ?Mailcode_Commands_Command $sourceCommand = null): Mailcode_Variables_Collection_Regular;
    public function createVariables(): Mailcode_Variables;
    public function createTranslator(): Mailcode_Translator;
    public function createStyler(): Mailcode_Styler;
    public function createPreProcessor(string $subject): Mailcode_PreProcessor;

    // Logging
    public static function setLogger(LoggerInterface $logger): void;
    public static function getLogger(): ?LoggerInterface;
    public static function setDebugging(bool $enabled = true): void;
    public static function isDebugEnabled(): bool;
    public static function debug(string $message, array $context = array()): void;
}
```

---

## `Mailcode_Factory` (Static Command Factory)

```php
class Mailcode_Factory
{
    public const URL_ENCODING_NONE = 'none';
    public const URL_ENCODING_ENCODE = 'encode';
    public const URL_ENCODING_DECODE = 'decode';

    public static function show(): Mailcode_Factory_CommandSets_Set_Show;
    public static function set(): Mailcode_Factory_CommandSets_Set_Set;
    public static function if(): Mailcode_Factory_CommandSets_Set_If;
    public static function elseIf(): Mailcode_Factory_CommandSets_Set_ElseIf;
    public static function misc(): Mailcode_Factory_CommandSets_Set_Misc;
    public static function var(): Mailcode_Factory_CommandSets_Set_Variables;
    public static function createRenderer(): Mailcode_Renderer;
    public static function createPrinter(): Mailcode_Printer;
    public static function createDateInfo(): Mailcode_Date_FormatInfo;
}
```

---

## `Mailcode_Parser` (String Parsing)

```php
class Mailcode_Parser
{
    public const COMMAND_REGEX_PARTS = array(/* 3 regex patterns */);

    public function __construct(Mailcode $mailcode);
    public function parseString(string $string): Parser\ParseResult;
    public function createSafeguard(string $subject): Mailcode_Parser_Safeguard;
}
```

### `Parser\ParseResult`

```php
namespace Mailcode\Parser;

class ParseResult
{
    public function getCollection(): Mailcode_Collection;
    public function getPreParser(): PreParser;
}
```

### `Parser\PreParser`

```php
class PreParser
{
    public function getString(): string;
}
```

---

## `Mailcode_Parser_Statement` (Parameter Parsing)

```php
class Mailcode_Parser_Statement
{
    // Wraps tokenizer and info for a single command's parameter string
}
```

### `Mailcode_Parser_Statement_Tokenizer`

Tokenizes parameter strings into typed tokens (strings, variables, keywords, operators, numbers).

### `Mailcode_Parser_Statement_Info`

Provides query methods to inspect and retrieve tokens from a parsed statement.

### `Mailcode_Parser_Statement_Validator`

Validates a parsed statement against command-specific rules.

---

## `Mailcode_Collection` (Command Container)

```php
class Mailcode_Collection
{
    public function addCommand(Mailcode_Commands_Command $command): Mailcode_Collection;
    public function addCommands(array $commands): Mailcode_Collection;
    public function removeCommand(Mailcode_Commands_Command $command): void;
    public function hasCommands(): bool;
    public function countCommands(): int;
    public function getCommands(): array; // Mailcode_Commands_Command[]
    public function getGroupedByHash(): array;
    public function getFirstCommand(): ?Mailcode_Commands_Command;
    public function mergeWith(Mailcode_Collection $collection): Mailcode_Collection;
    public function getVariables(): Mailcode_Variables_Collection;

    // Validation
    public function isValid(): bool;
    public function hasBeenValidated(): bool;
    public function getValidationResult(): OperationResult;
    public function getErrors(): array; // Mailcode_Collection_Error[]
    public function getFirstError(): Mailcode_Collection_Error;
    public function hasErrorCode(int $code): bool;
    public function getErrorCodes(): array;
    public function addErrorMessage(string $matchedText, string $message, int $code): void;
    public function addInvalidCommand(Mailcode_Commands_Command $command): void;

    // Filtered access
    public function getShowCommands(): array; // Mailcode_Commands_ShowBase[]
    public function getShowVariableCommands(): array; // Mailcode_Commands_Command_ShowVariable[]
    public function getShowDateCommands(): array; // Mailcode_Commands_Command_ShowDate[]
    public function getIfCommands(): array; // Mailcode_Commands_Command_If[]
    public function getForCommands(): array; // Mailcode_Commands_Command_For[]
    public function getListVariableCommands(): array;

    // Lifecycle
    public function finalize(): void;
    public function isFinalized(): bool;
}
```

---

## `Mailcode_Commands` (Command Registry)

```php
class Mailcode_Commands
{
    public function getIDs(): array;        // string[] — all command class names
    public function getAll(): array;        // Mailcode_Commands_Command[] — dummy instances
    public function getByID(string $id): Mailcode_Commands_Command;
    public function nameExists(string $name): bool;
    public function getByName(string $name): Mailcode_Commands_Command;
    public function getDummyCommand(string $id): Mailcode_Commands_Command;
}
```

---

## `Mailcode_Commands_Command` (Abstract Base)

```php
abstract class Mailcode_Commands_Command
    implements Mailcode_Interfaces_Commands_Command
{
    // Identity
    public function getName(): string;
    public function getLabel(): string;
    public function getID(): string;
    public function getHash(): string;
    public function getMatchedText(): string;

    // Type
    public function getCommandType(): string;
    public function supportsType(): bool;
    public function getType(): string;
    public function requiresParameters(): bool;
    public function supportsLogicKeywords(): bool;
    public function supportsURLEncoding(): bool;

    // Parameters
    public function getParams(): ?string;
    public function hasParameters(): bool;
    public function getStatement(): Mailcode_Parser_Statement;
    public function getStatementInfo(): Mailcode_Parser_Statement_Info;

    // Validation
    public function isValid(): bool;
    public function getValidationResult(): OperationResult;
    public function validateNesting(): void;

    // Variables
    public function getVariables(): Mailcode_Variables_Collection;

    // Logic keywords
    public function getLogicKeywords(): Mailcode_Commands_LogicKeywords;

    // Nesting
    public function hasParent(): bool;
    public function getParent(): ?Mailcode_Commands_Command;
    public function hasContentParent(): bool;

    // Highlighting
    public function getHighlighted(): string;
    public function getNormalized(): string;
}
```

### Command Type Hierarchy

| Type Class | Role | Examples |
|-----------|------|---------|
| `Mailcode_Commands_Command_Type_Standalone` | Self-contained commands | `showvar`, `setvar`, `comment`, `break` |
| `Mailcode_Commands_Command_Type_Opening` | Opens a block | `if`, `for`, `mono`, `code`, `showurl` |
| `Mailcode_Commands_Command_Type_Closing` | Closes a block | `end` |
| `Mailcode_Commands_Command_Type_Sibling` | Same-level within a block | `else`, `elseif` |

### `Mailcode_Commands_ShowBase` (Show Command Base)

```php
abstract class Mailcode_Commands_ShowBase extends Mailcode_Commands_Command
{
    // Inherits all from Command, adds show-specific validation
}
```

### `Mailcode_Commands_IfBase` (If/ElseIf Base)

```php
abstract class Mailcode_Commands_IfBase extends Mailcode_Commands_Command
{
    // Inherits all from Command, adds conditional-specific validation
    // Supports 16 subtypes (Variable, Contains, Empty, NotContains, etc.)
}
```

---

## `Mailcode_Parser_Safeguard` (Placeholder Protection)

```php
class Mailcode_Parser_Safeguard
{
    public function makeSafe(): string;
    public function makeSafePartial(): string;
    public function makeWhole(string $string): string;
    public function makeWholePartial(string $string): string;
    public function isValid(): bool;
    public function getCollection(): Mailcode_Collection;
    public function getPlaceholders(): Mailcode_Parser_Safeguard_PlaceholderCollection;
    public function createFormatting(string $subject): Mailcode_Parser_Safeguard_Formatting;
    public function getPlaceholderStrings(): array;
    public function isStringValid(string $subject): bool;
}
```

### `Mailcode_Parser_Safeguard_Formatting`

```php
class Mailcode_Parser_Safeguard_Formatting
{
    public function makePartial(): self;
    public function addFormatter($formatter): void;
    public function applyFormatting(): void;
    public function getSubject(): Mailcode_StringContainer;

    // Formatter factory methods
    public function createHTMLHighlighting(): HTMLHighlighting;
    public function createNormalized(): Normalized;
    public function createPreProcessing(): PreProcessing;
    public function createRemove(): Remove;
    public function createMarkVariables(): MarkVariables;
    public function createSingleLines(): SingleLines;
}
```

---

## `Mailcode_Translator` (Syntax Translation)

```php
class Mailcode_Translator
{
    public static function create(): Mailcode_Translator;
    public function createSyntax(string $name): SyntaxInterface;
    public function createApacheVelocity(): ApacheVelocitySyntax;
    public function createHubL(): HubLSyntax;
    public function getSyntaxes(): array; // SyntaxInterface[]
    public function getSyntaxNames(): array; // string[]
    public function syntaxExists(string $name): bool;
}
```

### `Translator\SyntaxInterface`

```php
namespace Mailcode\Translator;

interface SyntaxInterface
{
    public function getTypeID(): string;
    public function translateCommand(Mailcode_Commands_Command $command): string;
    public function translateSafeguard(Mailcode_Parser_Safeguard $safeguard): string;
}
```

---

## `Mailcode_Variables` (Variable Parsing)

```php
class Mailcode_Variables
{
    public const REGEX_VARIABLE_NAME = '/...pattern.../six';

    public function parseString(string $subject, ?Mailcode_Commands_Command $sourceCommand = null): Mailcode_Variables_Collection_Regular;
}
```

### `Mailcode_Variables_Variable`

```php
class Mailcode_Variables_Variable
{
    public function getFullName(): string;   // e.g. "$PATH.NAME"
    public function getPath(): string;       // e.g. "PATH"
    public function getName(): string;       // e.g. "NAME"
    public function getHash(): string;
    public function getMatchedText(): string;
}
```

---

## `Mailcode_Renderer` (Command → String)

```php
class Mailcode_Renderer
{
    public function setOutputHighlighted(bool $highlighted = true): Mailcode_Renderer;
    public function showVar(string $variableName): string;
    public function showSnippet(string $snippetName): string;
    public function setVar(string $variableName, string $value, bool $quoteValue = false): string;
    public function setVarString(string $variableName, string $value): string;
    public function if(string $condition, string $type = ''): string;
    public function ifVar(string $variable, string $operand, string $value, bool $quoteValue = false): string;
    public function ifVarString(string $variable, string $operand, string $value): string;
    public function ifVarEquals(string $variable, string $value, bool $quoteValue = false): string;
    public function ifVarEqualsString(string $variable, string $value): string;
    public function ifVarNotEquals(string $variable, string $value, bool $quoteValue = false): string;
    public function ifVarNotEqualsString(string $variable, string $value): string;
    // ... additional convenience methods for other commands
}
```

---

## `Mailcode_PreProcessor`

```php
class Mailcode_PreProcessor
{
    public function __construct(string $subject);
    public function getSafeguard(): Mailcode_Parser_Safeguard;
    public function isValid(): bool;
    public function getValidationResult(): OperationResult;
    public function render(): string;
}
```

---

## `Mailcode_Styler` (CSS Access)

```php
class Mailcode_Styler
{
    public function getCSS(): string;
    public function getStyleTag(): string;
    public function getStylesheetPath(): string;
    public function getStylesheetURL(string $vendorURL): string;
}
```

---

## `Mailcode_StringContainer` (Observable String)

```php
class Mailcode_StringContainer
{
    public function __construct(string $subject);
    public function getID(): int;
    public function getString(): string;
    public function getLength(): int;
    public function updateString(string $subject): bool;
    public function addListener(callable $listener): int;
    public function removeListener(int $listenerId): void;
}
```

---

## `ClassCache` (Dynamic Class Discovery)

```php
class ClassCache
{
    public static function findClassesInFolder(
        string|FolderInfo $folder,
        bool $recursive = false,
        ?string $instanceOf = null
    ): array; // class-string[]
}
```

---

## `Mailcode_Commands_Keywords` (Toggleable Flags)

Keywords that can be appended to commands: `insensitive:`, `regex:`, `urlencode:`, `urldecode:`, `multiline:`, `idnencode:`, `idndecode:`, `nohtml:`, `absolute:`, `no-tracking:`, `currency-name:`, `shorten:`.

---

## `Mailcode_Commands_LogicKeywords` (Compound Conditions)

Enables `and:` / `or:` connectors within `if`/`elseif` commands for compound conditions.

---

## Global Functions (`src/functions.php`)

```php
namespace Mailcode;

function t(string $subject, ...$args): string;            // i18n translation
function dollarize(string $variableName): string;          // Prepend $ if missing
function undollarize(string $variableName): string;        // Remove $ prefix
```
