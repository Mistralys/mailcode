[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Mistralys/mailcode/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Mistralys/mailcode/?branch=master)

# PHP Mailcode Syntax parser

Mailcode is a preprocessor command syntax created for use in emailings.

It aims to be easy to use by authors, and usable in a number of popular web formats, 
from plain text to HTML and XML. The Mailcode syntax is verbose by design, without 
shorthand notations, for both better readability and performance. 

It has been developed to support interchangeable backend preprocessor syntaxes,
to unify these into a single language. 

## The syntax

All commands follow the same structure.

Parameterless:

```
{command}
```

With parameters:

```
{command subtype: parameters}
```

The subtype can switch between modes of the same command.

Togglable flags to enable/disable features:

```
{command subtype: parameters flagFoo: flagBar:}
```

Named parameters:

```
{command subtype: "value" name="param value"}
```

### Escaping special characters

#### Double quotes

String literals are expected to be quoted using double quotes ("). To use double quotes within a string literal, it can be escaped using a backslash (\):

```
{if contains: $PRODUCT.NAME "Search term with \"quotes\""}
```

> Note: When using the Factory to create commands, this is done automatically.

#### Curly braces

To use curly braces in a document, or in string literals, they can be escaped:

```
{if contains: $PRODUCT.NAME "With \{braces\}"}
   Literal braces: \{end\}
{end}
```

## Supported commands

### Display variable values

```
{showvar: $CUSTOMER.NAME}
```

### Display a date and/or time

Using the default date and time settings for the current locale:

```
{showdate: $ORDER.DATE}
```

With a custom date/time format:

```
{showdate: $ORDER.DATE "d/m/Y"}
```

> Note: Also see the [section on date formats](#date-formats) for details 
> on how to specify date and time.

With a specific time zone:

```
{showdate: $ORDER.DATE "d.m.Y" timezone: "Europe/Paris"}
{showdate: $ORDER.DATE "d.m.Y" timezone: $TIME_ZONE}
```

If no time zone is specified, the default PHP time zone is used
(this is typically `UTC` unless the server is configured differently). 

It is possible to set the default time zone globally for the command, 
separately from the native PHP time zone:

```php
use Mailcode\Mailcode_Commands_Command_ShowDate;

Mailcode_Commands_Command_ShowDate::setDefaultTimezone('Europe/Paris');
```

This will make all `showdate` commands use `Europe/Paris`, unless a
specific time zone is specified with `timezone:` keyword.

### Display a formatted number

To specify the format for the number, simply write the number `1000` the way you would like to have it formatted. This will be applied to the values accordingly.

```
{shownumber: $ORDER.PRICE "1,000.00"}
```

This will use commas as thousands separator, a dot for the decimals, and two decimal positions.

For example, `10` will be displayed as `10.00`, and `5120.4` as `5,120.40`.

#### Zero-Padding

Zero-padding is specified by appending the required number length like this:

```
{shownumber: $MONTH "1000:##"}
```

The amount of hashes determines the target length of the number. This example will add a zero-padding of `2`, meaning a `5` will be shown as `05`.

#### Absolute numbers

When working with negative numbers, you can use the `absolute:` 
keyword to ensure that the minus sign is not shown.

```
{shownumber: $ORDER.PRICE "1,000.00" absolute:}
```

### Display a text snippet

Display a raw text snippet. Newlines are converted to HTML `<br>` 
tags automatically.

```
{showsnippet: $snippet_name}
```

To disable the  `<br>` tags, use the `nohtml:` keyword:

```
{showsnippet: $snippet_name nohtml:}
```

### Display a URL with or without tracking

URLs may contain variables, or even logic commands. The `showurl` command
makes it possible to integrate these into tracking URLs, by rendering the
final URL on the target language level.

#### Adding tracking

Consider the following URL:

```
{if variable: $COUNTRY == "fr"}
https://mistralys.fr
{else}
https://mistralys.eu
{end}
```

To make this trackable, use the following command:

```
{showurl: "TrackingID"}
{if variable: $COUNTRY == "fr"}
https://mistralys.fr
{else}
https://mistralys.eu
{end}
{showurl}
```

> NOTE: The command must be closed with {showurl}, not {end}.

On the target language level (e.g. Apache Velocity), this will evaluate 
the result of the `if` command first, to resolve the final URL. This can 
then be easily used in any tracking implementation, which also needs to
be implemented on the target language level.

#### Tracking IDs

The tracking ID is used to identify the location of the link in the 
document, e.g. `header-image`. If it is omitted or empty, an automatic
ID will be generated.

The minimum version of the command looks like this:

```
{showurl: ""}
https://mistralys.eu
{showurl}
```

The default generated tracking ID follows this scheme: `link-001`, with
a link counter that is unique for the whole request. A custom ID generator
can be registered like this:

```php
use \Mailcode\Mailcode_Commands_Command_ShowURL;
use \Mailcode\Commands\Command\ShowURL\AutoTrackingID;

// The method expects a callable, which must return a string.
AutoTrackingID::setGenerator(static function(Mailcode_Commands_Command_ShowURL $command) : string 
{
    return 'trackingID';
});
```

#### Adding query parameters

The command allows specifying additional query parameters that should
be added to the target URL, like UTM parameters or the like.

Example command:

```
{showurl: "TrackingID" "foo=bar" "other=value"}
https://mistralys.eu
{showurl}
```

Resulting example tracking URL:

```
https://track.domain?id=TrackingID&target=https%3A%2F%2Fmistralys.eu%3Ffoo%3Dbar%26other%3Dvalue
```

#### Disabling the tracking

The tracking can be disabled with the `no-tracking:` keyword,
in which case only the evaluated URL is used. Additional query
parameters can still be added.

```
{showurl: no-tracking: "foo=bar"}
https://mistralys.eu
{showurl}
```

Resulting URL:

```
https://mistralys.eu?foo=bar
```

### Phone numbers in URLs

The `{showphone}` command can convert a phone number in a country-specific or international
formatted style to the E164 format required for `tel:` URLs.

Whenever you wish to add a phone link, use this:

```html
<a href="tel:{showphone: $PHONE "US" urlencode:}">{showvar: $PHONE}</a>
```

This will convert the phone number to the expected format.

### Set a variable

#### String value

```
{setvar: $CUSTOMER.NAME = "value"}
```

#### Arithmetic operation

Basic arithmetic operations can be used, provided that the target
language supports these. They are typically passed on directly 
through the translator, unless it has special logic to convert
them.

```
{setvar: $AMOUNT = 45 * 2}
```

#### Counting lists 

The `count:` keyword allows specifying a list variable
to count the records of, and store the amount in the
target variable.

```
{setvar: $AMOUNT count: $LIST_VAR}
```

#### Omitting the = sign

The equals sign is implied, so it can be omitted.

```
{setvar: $AMOUNT 45 * 2}
```

### IF conditionals

#### Variable-based conditions

```
{if variable: $CUSTOMER.NAME == "John"}
    Hi, John.
{elseif variable: $CUSTOMER.NAME == "Jack"}
    Howdy, Jack.
{end}
```

#### Checking for empty or non-empty variables

Checking if a variable does not exist, or is empty:

```
{if empty: $CUSTOMER.NAME}
    Customer name is empty.
{end}
```

Checking if a variable exists and is not empty:

```
{if not-empty: $CUSTOMER.NAME}
    {showvar: $CUSTOMER.NAME}
{end}
```

#### Searching for substrings

Checking if a variable value contains a string:

```
{if contains: $PRODUCT.NAME "Search term"}
```

Making the search case insensitive:

```
{if contains: $PRODUCT.NAME "Search term" insensitive:}
```

Searching for multiple terms (applied if any of the terms is found):

```
{if contains: $PRODUCT.NAME "Term 1" "Term 2" "Term 3"}
```

Matching a variable value if it does NOT contain any of the search terms:

```
{if not-contains: $PRODUCT.NAME "Term 1" "Term 2" "Term 3"}
```

#### Searching lists by partial matches

If a variable contains several records, it is possible to search through a property 
in all records, without having to use a loop:

```
{if list-contains: $PRODUCTS.NAME "Server"}
```

This will search in the `NAME` property of all products for the specified search term.

The command otherwise behaves just like  the `contains` command, with the same options.

Case insensitive search:

```
{if list-contains: $PRODUCTS.NAME "server" insensitive:}
```

Negating the search, applying it only if the search terms are not found:

```
{if list-not-contains: $PRODUCTS.NAME "Hosting" "WordPress"}
```

#### Searching lists by regular expressions

The `list-contains` can be switched to regex mode with the `regex:` keyword:

```
{if list-contains: $PRODUCTS.NAME regex: "\\ASuperName\\Z"}
```

  > NOTE: This can be combined with the `insensitive:` keyword to make the
    regular expression case-insensitive.

Regular expressions may use curly braces when defining quantifiers, e.g. `{1,5}`.
This is a special case where you do not have to escape the braces. The parser
will recognize these braces so the regex stays readable. 

These commands are both valid:

```
{if list-contains: $PRODUCTS.NAME regex: "[0-9]{1,3}"}

{if list-contains: $PRODUCTS.NAME regex: "[0-9]\{1,3\}"}
```

#### Searching lists by exact matches

Using regular expressions allows searching for exact matches by using the
beginning and end anchors `\A` and `\Z`, but this is unwieldy and not
exactly readable. The `list-equals` command does exactly this.

```
{if list-equals: $PRODUCT.NAME "Search"}
```

This will match only if an entry in the list is an exact match for "Search".

It can be combined with the `insensitive:` keyword to search for the full
search term, but in a case insensitive way.

#### Searching lists by beginning or end

Checking if a variable value starts with a specific string:

```
{if begins-with: $PRODUCT.NAME "Search"}
```

Or checking if it ends with a specific string:

```
{if ends-with: $PRODUCT.NAME "term"}
```

Both can be made case insensitive:

```
{if begins-with: $PRODUCT.NAME "Search" insensitive:}
```

#### Numeric comparisons

Checking if a variable value is bigger than a specific number:

```
{if bigger-than: $PRODUCT.PRICE "220"}
```

Or checking if it is smaller:

```
{if smaller-than: $PRODUCT.PRICE "220"}
```

Or checking for an exact match:

```
{if equals-number: $PRODUCT.PRICE "220"}
``` 

#### Freeform conditions:

Without subtype, the IF condition is not validated, and will be passed through as-is to the translation backend.

```
{if: 6 + 2 == 8}
    It means 8.
{end}
```

#### AND/OR combinations

Several conditions can be combined within the same command using the `and:` and `or:` keywords. Either can be used, but not both within the same command. Subtypes can be mixed at will.

Using AND:

```
{if variable: $ORDER.MONTH == 8 and contains: $ORDER.TYPE "new_customer"}
    New customer order in August.
{end}
```

Using OR:

```
{if not-empty: $CUSTOMER.POSTCODE or variable: $CUSTOMER.USE_INVOICE == "true"}
```

### Loops

```
{for: $NAME in $CUSTOMER.NAMES}
    {showvar: $NAME}
{end}
```

#### Breaking out of loops

```
{for: $NAME in $CUSTOMER.NAMES}
    {if variable: $NAME == "John Doe"}
        {break}
    {end}
    {showvar: $NAME}
{end}
```

#### Stopping at a specific iteration

The `break-at` parameter allows stopping the loop at a 
specific loop iteration count (`0`-based).

```
{for: $ENTRY in $CUSTOMERS break-at: 6}
    {showvar: $ENTRY.NAME}
{end}
```

### Comments

Comments may be added to document things. Whether they are used when translated
to an specific preprocessor language depends on the translator. In general, 
the comments are converted to the target language.

```
{comment: "This is a comment."}
```

  > NOTE: Comments can contain special characters, except other Mailcode commands
    or texts that can be mistaken for commands (which use the brackets {}).

## Integrated preprocessing

Mailcode is a preprocessor language meant to be interpreted by a preprocessor
service, but some commands are made to be preprocessed natively by Mailcode 
itself. One example is the `mono` command, which applies monospace formatting
to text.

The preprocessing is optional, and can be done with the specialized PreProcessor
class.

  > NOTE: When translating to an output syntax like Apache Velocity, the default
    behavior is to strip out leftover preprocessor commands, so there can be no 
    Mailcode commands in the translated text.

### Working with the PreProcessor

The PreProcessor is very easy to use: simply feed it a string with Mailcode 
commands, and all commands that support pre-processing will be rendered.
After this, the resulting string can be passed into a safeguard instance or
parsed to fetch the commands.

```php
$subject = '(Mailcode text)';

$processor = \Mailcode\Mailcode::create()->createPreProcessor($subject);
$result = $processor->render();
```

  > NOTE: While the preprocessing can be done after safeguarding a text,
    it is recommended to do it beforehand, to avoid the overhead of
    unnecessarily parsing the commands. Also, these commands may actually 
    generate new Mailcode syntax to parse.

### Format a text as code

```
This text is {mono}monospaced{end}.
```

The resulting pre-processed text will look like this:

```html
This text is <code>monospaced</code>.
```

To create a `<pre>` tag, simply add the multiline keyword:

```
{mono: multiline:}
This is a multiline code block.
{end}
```

This gives the following pre-processed text:

```html
<pre>
This is a multiline code block.
</pre>
```

## Working with commands

### Closing, opening and sibling commands

Commands like for loops, and if statements that have a closing command
and are closed using the `{end}` command support accessing their siblings,
and respective opening and closing commands.

For example, the closing command of an if statement has the `getOpeningCommand()`
method, which returns the if command that it closes, and vice versa. If command
structures with `elseif` and `else` commands allow traversing the whole list of
sibling commands.

This makes it easy to work with complex command structures.

## Date formats

### Supported formatting characters

The ShowDate command uses formatting characters that are compatibvle with PHP's date formatting functions, but only a subset of these are allowed.

  * `d` Day number, with leading zeros
  * `m` Month number, with leading zeros
  * `y` Year, with 2 digits
  * `Y` Year, with 4 digits
  * `H` Hour, 24-hour format, with leading zeros
  * `i` Minutes, with leading zeros
  * `s` Seconds, with leading zeros 
  
Additionally, the following punctuation characters may be used:

  * `.` Dot
  * `/` Slash
  * `-` Hyphen
  * `:` Colon
  * ` ` Space     

### Accessing format information

The Mailcode_Date_FormatInfo class can be used to access information on the available date formats when using the ShowDate command. It is available globally via a factory method:

```php
use Mailcode\Mailcode_Factory;

$dateInfo = Mailcode_Factory::createDateInfo();
```

### Setting defaults

The ShowDate command uses `Y/m/d` as default date format. The format info class can be used to overwrite this:

```php
use Mailcode\Mailcode_Factory;

$dateInfo = Mailcode_Factory::createDateInfo();
$dateInfo->setDefaultFormat('d.m.Y');
```

Once it has been set, whenever the ShowDate command is used without specifying
a custom format string, it will use this default format.  

### Accessing formatting characters programmatically

To make it possible to integrate mailcode in existing documentation, the format info class offers the `getFormatCharacters()` method to get a list of all characters that can be used. 

Displaying a simple text-based list of allowed characters:

```php
use Mailcode\Mailcode_Factory;

$dateInfo = Mailcode_Factory::createDateInfo();
$characters = $dateInfo->getCharactersList();

foreach($characters as $character)
{
    echo sprintf(
        '%s: "%s" %s',
        $character->getTypeLabel(),
        $character->getChar(),
        $character->getDescription()
    );
    
    echo PHP_EOL;
}
```

### Manually validating a date format

Use the `validateFormat()` method to validate a date format string, and retrieve a validation message manually. The same method is used by the ShowDate command, but can be used separately for specific needs.

```php
use Mailcode\Mailcode_Factory;

$formatString = "d.m.Y H:i";

$dateInfo = Mailcode_Factory::createDateInfo();
$result = $dateInfo->validateFormat($formatString);

if($result->isValid())
{
    echo 'Format is valid.';
}
else
{
    echo sprintf(
        'Format is invalid: Error #%s, %s',
        $result->getCode(),
        $result->getErrorMessage()
    );
}
```

## Format compatibility

Mailcode mixes well with HTML and XML. Its strict syntax makes it easy to distinguish it from most text formats. with the notable exception of CSS. In HTML, all style tags are ignored.

## Safeguarding commands when filtering texts

When texts containing commands need to be filtered, or otherwise parsed in a way that could break the command syntax, the safeguard mechanism allows for easy replacement of all commands with neutral placeholder strings.

Assuming the text to filter, possibly containing commands, is stored in `$text`:

```php
use Mailcode\Mailcode;

$text = '(commands here)';

// create the safeguard instance for the text
$safeguard = Mailcode::create()->createSafeguard($text);

if(!$safeguard->isValid()) 
{
    // there are invalid commands in the text
}

// replace all commands with placeholders
$filterText = $safeguard->makeSafe();

// do any required filtering and processing of the text

// restore the placeholders to the full command texts
$result = $safeguard->makeWhole($filterText);
```

**HINT:** Placeholders are case neutral, and thus cannot be broken by changing the text case.  

### Avoiding delimiter conflicts

By default, the placeholders use `999` as delimiters, for example: `9990000000001999`. 
Each delimiter gets a unique number within the same request, which is zero-padded right,
making each placeholder unique in all subject strings.

Having number-based placeholders means that they are impervious to usual text transformations,
like changing the case or applying url encoding.

Still, the delimiter string can be adjusted as needed:

```php
use \Mailcode\Mailcode;

$text = '(Text with mailcode commands)';
$safeguard = Mailcode::create()->createSafeguard($text);

$safeguard->setDelimiter('__');
```

This would for example make the delimiters look like `__0000000001__`.

### Placeholder consistency check

When calling `makeWhole()`, the safeguarder will make sure that all placeholders 
that were initially replaced in the target string are still there. If they are 
not, an exception will be thrown.

### Accessing placeholder information

The placeholders used in a string can be easily retrieved. Just be sure to call 
`getPlaceholders()` after the initial configuration (setting the delimiters for 
example).

```php
use \Mailcode\Mailcode;

$text = '(Mailcode commands here)';
$safeguard = Mailcode::create()->createSafeguard($text);

$placeholders = $safeguard->getPlaceholdersCollection()->getAll();

foreach($placeholders as $placeholder)
{
    $string = $placeholder->getReplacementText(); // the placeholder text
    $command = $placeholder->getCommand(); // the detected command instance
    $original = $placeholder->getOriginalText(); // the original command text
}
```

## Applying formatting

By default, when using the safeguard's `makeWhole` method, all command placeholders are replaced with the normalized syntax of the commands. A number of additional formatting options are available via the safeguard's formatting class. In this case, the formatted string is retrieved via the formatting class instead of the safeguard itself. 

Creating a formatting instance, using a safeguard:

```php
use \Mailcode\Mailcode;

$text = '(Mailcode commands here)';
$safeguard = Mailcode::create()->createSafeguard($text);

$formatting = $safeguard->createFormatting($safeguard->makeSafe());
```

**Note:** Formatting is entirely separate from the safeguard. The safeguard instance retains the original text.

### Replacers and Formatters

There are two types of formatters: 

  - **Replacers**: These will replace the command placeholders themselves (example: HTML syntax highlighting of commands). Only one replacer may be selected.
  - **Formatters**: These will only modify the text around the placeholder, leaving the placeholder intact. Formatters can be combined at will.

While it is not possible to select several replacers, they can be freely combined with formatters.

The methods to add formatters reflect their type:

```php
use \Mailcode\Mailcode;

$text = '(Mailcode commands here)';
$safeguard = Mailcode::create()->createSafeguard($text);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());

$formatting->replaceWithHTMLHighlighting();
$formatting->formatWithMarkedVariables();
```

### HTML Highlighting

The HTML syntax highlighter will add highlighting to all commands in an intelligent way. Commands will not be highlighted if they are used in HTML tag attributes or nested in tags where adding the highlighting markup would break the HTML structure.

```php
use \Mailcode\Mailcode;

$text = '(Mailcode commands here)';
$safeguard = Mailcode::create()->createSafeguard($text);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());

// choose to replace commands with syntax highlighted commands
$formatting->replaceWithHTMLHighlighting();

$highlighted = $formatting->toString();
```

This will add the highlighting markup, but the necessary CSS styles must also be available in the document where the Mailcode will be displayed. More on this in the "Loading the required styles" section.

#### Excluding tags from the highlighting

By default, commands will not be highlighted within the `<style>` and `<script>` tags. Additional tags can easily be added to this list to customize it for your needs:

```php
use \Mailcode\Mailcode;

$text = '(Mailcode commands here)';
$safeguard = Mailcode::create()->createSafeguard($text);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());

// Get the formatter instance
$formatter = $formatting->replaceWithHTMLHighlighting();

// add a single tag to the exclusion list
$formatter->excludeTag('footer');

// add several tags at once
$formatter->excludeTags(array('footer', 'main', 'div'));
```

In this example, commands nested in `<footer>` tags will not be highlighted. 

NOTE: The excluded tag check goes up the whole tag nesting chain, which means that the following command would not be highlighted either, since it is contained in a tag that is nested within the `<footer>` tag:

```html
<footer>
	<p>
		<b>{showvar: $FOO}</b>
	</p>
</footer>
```

**WARNING:** The parser assumes that the HTML is valid. The tag nesting check does not handle nesting errors.

#### Loading the required styles

For the highlighting to work, the according CSS styles need to be loaded in the target page. 

There are two way to do this:

##### Including the stylesheet

Simply ensure that the stylesheet file `css/highlight.css` of the package is loaded. This requires knowing the exact URL to the package's vendor folder.

```html
<link rel="stylesheet" media="all" src="/vendor/mistralys/mailcode/css/highlight.css">
```

##### Using the Styler utility

The Styler utility class has a number of methods all around the CSS.

Creating/getting the styler instance: 

```php
use Mailcode\Mailcode;

$styler = Mailcode::create()->createStyler();
```

Getting the raw CSS code without the `<style>` tag, for example to use in a compiled stylesheet file:

```php
use Mailcode\Mailcode;

$styler = Mailcode::create()->createStyler();

$css = $styler->getCSS();
```

Retrieving the CSS including the `<style>` tag, for example to add it inline in a page:

```php
use Mailcode\Mailcode;

$styler = Mailcode::create()->createStyler();

$styleTag = $styler->getStyleTag();
```

Retrieving the absolute path on disk to the stylesheet file:

```php
use Mailcode\Mailcode;

$styler = Mailcode::create()->createStyler();

$path = $styler->getStylesheetPath();
```

Retrieving the `<link>` tag programmatically, using the URL to access the `vendor` folder:

```php
use Mailcode\Mailcode;

$styler = Mailcode::create()->createStyler();

$linkTag = $styler->getStylesheetTag('/url/to/vendor/folder');
```

Retrieving the URL to the stylesheet file, using the URL to access the `vendor` folder:

```php
use Mailcode\Mailcode;

$styler = Mailcode::create()->createStyler();

$stylesheetURL = $styler->getStylesheetURL('/url/to/vendor/folder');
```

### Highlighting variables in the final document

The "MarkVariables" highlighter allows highlighting (not syntax highlighting) all variable type commands, even once they have been processed by the mail preprocessor. This is handy when testing, to quickly identify all places in an HTML document where variables are used.

```php
use Mailcode\Mailcode;

$htmlString = '(HTML with Mailcode commands here)';

$safeguard = Mailcode::create()->createSafeguard($htmlString);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());

// Get the formatter instance
$formatter = $formatting->formatWithMarkedVariables();
```

Like the syntax highlighter, this will only highlight variables in valid contexts.

NOTE: This can be combined with any of the other formatters, like the syntax highlighter.

#### Load styles via style tag

The necessary style tag can be retrieved using the `getStyleTag` method:

```php
use Mailcode\Mailcode;

$htmlString = '(HTML with Mailcode commands here)';

$safeguard = Mailcode::create()->createSafeguard($htmlString);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());
$formatter = $formatting->formatWithMarkedVariables();

$styles = $formatter->getStyleTag();
```

This then only has to be added to the target document.

#### Integrate styles inline

For HTML mailings, or cases where the styles cannot be easily injected, the inline mode will automatically add the necessary styles to every command instance.

Simply enable the inline mode:

```php
use Mailcode\Mailcode;

$htmlString = '(HTML with Mailcode commands here)';

$safeguard = Mailcode::create()->createSafeguard($htmlString);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());
$formatter = $formatting->formatWithMarkedVariables();

$formatter->makeInline();
```


## Translation to other syntaxes

The translator class makes it easy to convert documents with mailcode to other syntaxes, like the bundled Apache Velocity converter.

### Translating whole strings

```php
use Mailcode\Mailcode;

$string = '(Text with Mailcode commands here)';

// create the safeguarder instance for the subject string
$safeguard = Mailcode::create()->createSafeguard($string);

// create the translator
$apache = Mailcode::create()->createTranslator()->createSyntax('ApacheVelocity');

// convert all commands in the safeguarded string
$convertedString = $apache->translateSafeguard($safeguard);
```

### Translating single commands

```php
use Mailcode\Mailcode;
use Mailcode\Mailcode_Factory;

// create the translator
$apache = Mailcode::create()->createTranslator()->createSyntax('ApacheVelocity');

// create a command
$command = Mailcode_Factory::set()->var('VAR.NAME', '8');

// convert it to an apache velocity command string
$apacheString = $apache->translateCommand($command);
```

### Translate to: Apache Velocity

See the [Velocity documentation][].

## Browser-enabled tools

In the subfolder `tools` are a few utilities meant to be used in a browser. To use
these, simply run a `composer install` in the package's folder, and point your 
browser there.

- `translator.php` - Translate a text with Mailcode commands to a supported syntax.
- `extractPhoneCountries.php` - Extracts a countries list for the `showphone` command.



[DateTool]: https://velocity.apache.org/tools/devel/apidocs/org/apache/velocity/tools/generic/DateTool.html
[EscapeTool]: https://velocity.apache.org/tools/devel/apidocs/org/apache/velocity/tools/generic/EscapeTool.html
[StringUtils]: http://commons.apache.org/proper/commons-lang/apidocs/org/apache/commons/lang3/StringUtils.html
[LibPhoneNumber]: https://github.com/google/libphonenumber  
[Velocity documentation]: https://github.com/Mistralys/mailcode/tree/master/docs/user-guide/translate-apache-velocity.md

