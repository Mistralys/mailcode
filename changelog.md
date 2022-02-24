### v3.0.0 - PHP 7.4 and new commands 

- Added this changelog file.
- Safeguard: Added the placeholder collection class to consolidate placeholders access.
- Safeguard: The `getPlaceholderXXX` methods are now deprecated.
- Parser: Added a pre-parser for protected content commands.
- Parser: Protected content commands may now contain any text, including Mailcode commands.
- Formatting: Added a replacer to remove all Mailcode commands from a string.

**Breaking changes**

- PHP requirement increased to PHP v7.4.
- Parser: `parseString()` now returns an instance of `Mailcode_Parser_ParseResult` instead of a collection.
- Commands: Removed `Mailcode_Commands_Command_Code::getSyntax()`.
- Commands: The `{code}` command must now be closed with `{code}` instead of `{end}`.

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


### v2.1.7 - Bugfix release

- Fixed erroneous detection of commands caused by the literal brackets detection.
- Parser: Only regex brackets are now supported in string literals.
- Parser: Fixed string literal keywords' `getText()` not stripping out escaped quotes.
- Parser: Moved string preprocessing to a dedicated class.


### v2.1.6 - Maintenance release

- Updated the `shownumber` command to use the price tool implementation.


### v2.1.5 - Minor feature release

- Added the `absolute:` keyword to the `shownumber` command, to remove the minus signs of negative numbers.
- It is now possible to use brackets (`{` and `}`) in string literals, for example for regex expressions in `list-contains`.


### v2.1.4 - Minor improvement release

- Added the `nohtml:` keyword to the `showsnippet` command, to turn off newlines to `<br>` tag conversion.


### v2.1.3 - Bugfix & minor feature release

- Fixed some methods not using the price tool instead of the number tool for number conversions.
- Added constants in the validation interfaces for the validation names.
- Added interfaces for the internal validation traits.
- Moved the validation interfaces to their own subfolder, like the traits.
- Added support for the `insensitive:` keyword in the `if variable` command.


### v2.1.2 - Bugfix release
- Fixed commands with optional parameters losing their parameters when normalized.


### v2.1.1 - Phone number feature update

- Improved the handling of countries in the `{showphone}` command.
- Fixed US and other countries missing from the supported countries list.
- Supported countries are now objects with utility methods.
- Added example phone numbers for all countries.


### v2.1.0 - Phone formatting feature release

- Commands: Added the `{showphone}` command to convert phone numbers to E164 format.
- Translation: Added the translator web tool under `/tools/translator.php`.
- Velocity: Moved the URL encoding handling to a central method.
- Marked Variables: Fixed layout issues with long texts.
- Commands: Fixed URL decoding not being done in some of the `showxxx` methods.
- Safeguard: The collection instance is now accessible in the invalid collection exception.
- Exception: Added the `getCollection()` method to get the related collection instance if available.


### v2.0.0 - Nesting and preprocessing feature release

- Added preprocessing capability with the new PreProcessor class.
- All opening, closing and sibling commands now have methods to retrieve the related commands.
- Added the `{list-equals}` command.
- Added the `{mono}` command.
- ShowNumber command: Updated the translation to velocity.
- Highlighting: Fixed unneeded HTML tags being generated when parameters are empty.
- Internals: Parameters may now be used even if not required.

Read more about the [preprocessing](https://github.com/Mistralys/mailcode#integrated-preprocessing) and the [nesting](https://github.com/Mistralys/mailcode#closing-opening-and-sibling-commands) upgrade in the documentation.

