## v3.5.3 - PHP8.4 compatibility
- Core: Fixed deprecation notices for PHP 8.4.

## v3.5.2 - Add Shorten command
- Command: Added the `shorten:` keyword to shorten URLs.

## v3.5.1 - Cache folder update
- Core: The cache folder is now inherited from the AppUtil's `ClassHelper` if present.

## v3.5.0 - Translator changes and class cache (Breaking-L)
- Translators: Added actual syntax classes that extend `BaseSyntax`.
- Translators: Added  `SyntaxInterface`.
- Translators: Syntaxes are now loaded using the class cache.
- Translators: Added the `SYNTAX_NAME` constant to the syntax classes.
- Translators: Narrowed the type of the `createApacheVelocity()` method.
- Translators: Narrowed the type of the `createHubL()` method.
- Translators: Added the `Mailcode_Translator::create()` factory method.
- ShowPhone: Updated some example phone numbers from PhoneLib.
- Core: Added `Mailcode::setCacheFolder()` to set the cache folder.
- Core: Added the `ClassCache` class to handle dynamic class loading and caching.
- Code: Minor code tweaks for PHPStan recommendations.
- Code: PHPStan clean @ level 9.

### Breaking changes
- It is now mandatory to set a cache folder with `Mailcode::setCacheFolder()`.
- The Apache velocity and HubL translator syntax classes have been renamed.

## v3.4.0 - Added ShowPrice command
- Commands: Added the `{showprice}` command to format numbers as prices.
- Commands: Added the `namespace` parameter in the `{showsnippet}` command.
- Commands: The date variable is now optional in the `{showdate}` command.
- Traits: Added the `AbsoluteKeywordTrait`.
- Traits: Added the `NamespaceTrait`.
- Traits: Added the `CurrencyNameTrait`.
- Traits: Added the `RegionTrait`.
- Dependencies: Updated AppLocalization to [v1.5.0](https://github.com/Mistralys/application-localization/releases/tag/1.5.0).

## v3.3.2 - Minor update
- HubL: Added type casting to `float` for number comparisons.

## v3.3.1 - Added HubL IF and ELSEIF support
- HubL: Added translation for a subset of `if` and `elseif` commands.
- HubL: Added `if variable`.
- HubL: Added `if empty`.
- HubL: Added `if equals-number`.
- HubL: Added `if smaller-than`.
- HubL: Added `if bigger-than`.
- ShowPhone: Updated the phone number examples from the library.
- Dependencies: Updated LibPhoneNumber to [v8.13.36](https://github.com/giggsey/libphonenumber-for-php/releases/tag/8.13.36).

## v3.3.0 - Hubspot HubL syntax support
- Translator: Added a first iteration of HubL syntax support for a selection of commands.
- Translator: Namespaced the command translation classes.
- Tests: Moved PHPStan support files to `tests/phpstan`.
- Tests: Added the test suites to the PHPStan analysis.
- Tests: Partially added strict typing and namespaces.
- Tests: Implemented PHPStan recommendations in tests.

## v3.2.4 - Minor update
- Factory: Improved an error message when an invalid command is created.

## v3.2.3 - SetVar and Decryption
- SetVar Command: Fixed the Velocity translation for counting single variables.
- SetVar Command: Added the `decrypt` parameter.
- Commands: Added the `DecryptInterface` for commands with decryption capability.
- Translator: Added the `createApacheVelocity()` helper method.
- Parser: Added `addParamString()` to the statement info class.
- Docs: Added encoding and decoding examples.

## v3.2.2 - Fixed a test
- Tests: Fixed a minor highlighting test not updated after v3.2.1.

## v3.2.1 - Minor enhancements
- Highlighting: Added support for the named parameters.
- Highlighting: Fixed spacing in tokens, now using the token's `hasSpacing()`.
- Browser Tools: Added a syntax highlighter to see the highlighting in action.
- Core: Added `Mailcode::getName()` to get a human-readable package name.

## v3.2.0 - Named parameters support (breaking)
- Syntax: Added support for named parameters via `name=value` syntax.
- ShowDate Command: `timezone:` keyword converted to a named parameter.
- SetVar Command: `count:` keyword converted to a named parameter.
- SetVar Command: Added `setCount()`.
- For Command: `break-at:` keyword converted to a named parameter.
- For Command: Added `setBreakAt()`.
- ShowDate Command: Fixed incorrect conversion of some PHP date format signs.
- ShowDate Command: Added `a`, `G` and `g` to the formats for AM/PM times.
- ShowDate Command: Added tests for all format conversions.
- ShowDate Command: Added constants for all PHP format signs.
- ShowDate Command: Added `setTimezone()`.
- Error Messages: Added `getCommand()` to fetch the originating command, if any.
- Factory: Added the `var()` set to access variable related methods.

### Breaking changes

Some keyword constants were removed because they have been replaced
with named parameters:

- `TYPE_TIMEZONE`
- `TYPE_COUNT`
- `TYPE_BREAK_AT`

## v3.1.4 - Date formats update
- ShowDate Command: Added milliseconds and time zone format support with `e` and `v`.

## v3.1.3 - Velocity translation fix
- ListContains Command: Fixed the Velocity regex.

## v3.1.2 - Bugfix release
- Foreach: Fixed the `break-at:` keyword translation to Velocity not using the extended command syntax.

## v3.1.1 - TimeZone update
- ShowDate Command: Fixed the command being considered invalid without a timezone.
- ShowDate Command: Now using the PHP timezone as default if none is set.
- ShowDate Command: Added the `timezone:` keyword to specify the timezone.
- ShowDate Command: Added `setDefaultTimezone()` to set it globally for the command.
- ShowDate Command: `getTimezoneToken()` now always returns a value.
- Variables: Added `createVariable()` and `createVariableByName()`.
- Translation: Added missing german translations.

## v3.1.0 - New parameters update
- Commands: Added `break-at:` keyword in the `for` command.
- Commands: Added the `count:` keyword in the `setvar` command.
- Velocity: Updated the time tool commands.
- Global functions: Added `dollarize()` and `undollarize()`.
- Variables: Added static `dollarizeName()` and `undollarizeName()`.
- Factory: Added `set()->varCount()` as shortcut to create a count variable.
- Dependencies: Increased minimum `mistralys/application-utils` to v2.1.2.
- Commands: Updated example phone numbers for some countries.
- Parser: Fixed the tokenizer breaking same name variable mixes ($FOO + $FOO.BAR).

## v3.0.4 - Dependencies update
- Loosened the `mistralys/application-tools` version constraint. 

## v3.0.3 - ShowURL command syntax fix
- Fixed the syntax of the `$tracking.url()` command.

## v3.0.2 - Variable name bugfix
- Fixed the name of the `$envelope.hash` variable.

## v3.0.1 - IDN encoding update
- Renamed the IDN encoding methods to `$text.idn` and `$text.unidn`.
- Added some missing german translations.

## v3.0.0 - PHP 7.4 and new commands 

- Added this changelog file.
- Safeguard: Added the placeholder collection class to consolidate placeholders access.
- Safeguard: The `getPlaceholderXXX` methods are now deprecated.
- Parser: Added a pre-parser for protected content commands.
- Parser: Protected content commands may now contain any text, including Mailcode commands.
- Parser: Added `Mailcode_Parser_Statement_Info_Keywords::getByName()`.
- Parser: Params: Added `copy()` to the parameter statement class.
- Parser: Params: Added `prependStringLiteral()` to the statement info.
- Parser: Params: Fixed sync issues with the Tokenizer's tokens collection.
- Parser: Params: Added `getEventHandler()`.
- Parser: Tokenizer: Added possibility to prepend tokens to the list.
- Parser: Tokenizer: Added more detailed event handling via the `EventHandler` class.
- Formatting: Added a replacer to remove all Mailcode commands from a string.
- Commands: Added `Mailcode_Commands::getContentCommands()`.
- Commands: Added `Mailcode_Commands_Command_Code::getSupportedSyntaxes()`.
- Commands: Added the new `showurl` command.
- Commands: Added the new `showencoded` command.
- Commands: Added IDN encoding to select show commands via `IDNEncodingInterface`.
- Commands: Added the `URLEncodingInterface` interface (encoding and decoding).
- Factory: Added the `$content` parameter for code commands.
- Factory: Added `filterKeyword()` to the instantiator.
- Debugging: Added support for debugging with a monolog logger.
- Collection: Added `getErrorCodes()`.
- Code quality: Increased PHPStan analysis level to `9`.
- Code quality: Newly added classes now use namespaces in preparation for future changes.

**Breaking changes**

- PHP requirement increased to PHP v7.4.
- Parser: `parseString()` now returns an instance of `ParseResult` instead of a collection.
- Commands: Removed `Mailcode_Commands_Command_Code::getSyntax()`.
- Commands: The `{code}` command must now be closed with `{code}` instead of `{end}`.
- Commands: `setURLEncoding()` and `setURLDecoding()` are now only available 
  for commands that implement the `URLEncodingInterface` interface.
- Factory: The `misc::code()` method now requires the content to be specified.

**Deprecated methods** 

The following methods have been marked as deprecated,
and will be removed in a future update:

```
Mailcode_Parser_Safeguard::getPlaceholderByID()
Mailcode_Parser_Safeguard::getPlaceholderByString()
Mailcode_Parser_Safeguard::getPlaceholders()
Mailcode_Parser_Safeguard::getPlaceholderStrings()
```

Use the new method `getPlaceholdersCollection()` to access the safeguard's collection
of placeholders. This class offers handy utility methods for most use cases.


## v2.1.7 - Bugfix release

- Fixed erroneous detection of commands caused by the literal brackets detection.
- Parser: Only regex brackets are now supported in string literals.
- Parser: Fixed string literal keywords' `getText()` not stripping out escaped quotes.
- Parser: Moved string preprocessing to a dedicated class.


## v2.1.6 - Maintenance release

- Updated the `shownumber` command to use the price tool implementation.


## v2.1.5 - Minor feature release

- Added the `absolute:` keyword to the `shownumber` command, to remove the minus signs of negative numbers.
- It is now possible to use brackets (`{` and `}`) in string literals, for example for regex expressions in `list-contains`.


## v2.1.4 - Minor improvement release

- Added the `nohtml:` keyword to the `showsnippet` command, to turn off newlines to `<br>` tag conversion.


## v2.1.3 - Bugfix & minor feature release

- Fixed some methods not using the price tool instead of the number tool for number conversions.
- Added constants in the validation interfaces for the validation names.
- Added interfaces for the internal validation traits.
- Moved the validation interfaces to their own subfolder, like the traits.
- Added support for the `insensitive:` keyword in the `if variable` command.


## v2.1.2 - Bugfix release
- Fixed commands with optional parameters losing their parameters when normalized.


## v2.1.1 - Phone number feature update

- Improved the handling of countries in the `{showphone}` command.
- Fixed US and other countries missing from the supported countries list.
- Supported countries are now objects with utility methods.
- Added example phone numbers for all countries.


## v2.1.0 - Phone formatting feature release

- Commands: Added the `{showphone}` command to convert phone numbers to E164 format.
- Translation: Added the translator web tool under `/tools/translator.php`.
- Velocity: Moved the URL encoding handling to a central method.
- Marked Variables: Fixed layout issues with long texts.
- Commands: Fixed URL decoding not being done in some of the `showxxx` methods.
- Safeguard: The collection instance is now accessible in the invalid collection exception.
- Exception: Added the `getCollection()` method to get the related collection instance if available.


## v2.0.0 - Nesting and preprocessing feature release

- Added preprocessing capability with the new PreProcessor class.
- All opening, closing and sibling commands now have methods to retrieve the related commands.
- Added the `{list-equals}` command.
- Added the `{mono}` command.
- ShowNumber command: Updated the translation to velocity.
- Highlighting: Fixed unneeded HTML tags being generated when parameters are empty.
- Internals: Parameters may now be used even if not required.

Read more about the [preprocessing](https://github.com/Mistralys/mailcode#integrated-preprocessing) and the [nesting](https://github.com/Mistralys/mailcode#closing-opening-and-sibling-commands) upgrade in the documentation.


