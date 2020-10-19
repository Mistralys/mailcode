[![Build Status](https://travis-ci.com/Mistralys/mailcode.svg?branch=master)](https://travis-ci.com/Mistralys/mailcode) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Mistralys/mailcode/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Mistralys/mailcode/?branch=master)

# PHP Mailcode Syntax parser

Mailcode is a preprocessor command syntax created for use in emailings.

Mailcode is verbose by design, without shorthand notations, for both better readability and performance. It has been developed to unify interchangeable backend preprocessor syntaxes into one language that's easy to use. 

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

### Escaping quotes

String literals are expected to be quoted using double quotes ("). To use double quotes within a string literal, it can be escaped using a backslash (\):

```
{if contains: $PRODUCT.NAME "Search term with \"quotes\""}
```

Note: When using the Factory to create commands, this is done automatically.

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

Also see the section on date formats for details on how to specify date and time.

### Display a text snippet

```
{showsnippet: $snippet_name}
```

### Set a variable

With a string value:

```
{setvar: $CUSTOMER.NAME = "value"}
```

With an arithmetic operation:

```
{setvar: $AMOUNT = 45 * 2}
```

The equals sign is implied, so it can be omitted:

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

#### Searching by position

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

### Comments

Comments do not have to be quoted, but can be.

```
{comment: This is a comment.}
{comment: "This is a quoted comment."}
```

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
$dateInfo = Mailcode_Factory::createDateInfo();
```

### Setting defaults

The ShowDate command uses `Y/m/d` as default date format. The format info class can be used to overwrite this:

```php
$dateInfo->setDefaultFormat('d.m.Y');
```

Once it has been set, whenever the ShowDate command is used without specifying
a custom format string, it will use this default format.  

### Accessing formatting characters programmatically

To make it possible to integrate mailcode in existing documentation, the format info class offers the `getFormatCharacters()` method to get a list of all characters that can be used. 

Displaying a simple text-based list of allowed characters:

```php
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
$formatString = "d.m.Y H:i";
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

By default, the placeholders use `__` (double undserscore) as delimiters, for example: `__0001__`. If your text processing can affect underscores, the delimiter characters can be adjusted:

```php
$safeguard = Mailcode::create()->createSafeguard($text);

$safeguard->setDelimiter('%%');
```

This would for example make the delimiters look like `%%0001%%`.

### Placeholder consistency check

When calling `makeWhole()`, the safeguarder will make sure that all placeholders that were initially replaced in the target string are still there. If they are not, an exception will be thrown.

### Accessing placeholder information

The placeholders used in a string can be easily retrieved. Just be sure to call `getPlaceholders()` after the initial configuration (setting the delimiters for example).

```php
$placeholders = $safeguard->getPlaceholders();

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
$formatting->replaceWithHTMLHighlighting();
$formatting->formatWithMarkedVariables();
```

### HTML Highlighting

The HTML syntax highlighter will add highlighting to all commands in an intelligent way. Commands will not be highlighted if they are used in HTML tag attributes or nested in tags where adding the highlighting markup would break the HTML structure.

```php
// choose to replace commands with syntax highlighted commands
$formatting->replaceWithHTMLHighlighting();

$highlighted = $formatting->toString();
```

This will add the highlighting markup, but the necessary CSS styles must also be available in the document where the Mailcode will be displayed. More on this in the "Loading the required styles" section.

#### Excluding tags from the highlighting

By default, commands will not be highlighted within the `<style>` and `<script>` tags. Additional tags can easily be added to this list to customize it for your needs:

```php
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

```php
<link rel="stylesheet" media="all" src="/vendor/mistralys/mailcode/css/highlight.css">
```

##### Using the Styler utility

The Styler utility class has a number of methods all around the CSS.

Creating/getting the styler instance: 

```php
$styler = $mailcode->createStyler();
```

Getting the raw CSS code without the `<style>` tag, for example to use in a compiled stylesheet file:

```php
$css = $styler->getCSS();
```

Retrieving the CSS including the `<style>` tag, for example to add it inline in a page:

```php
$styleTag = $styler->getStyleTag();
```

Retrieving the absolute path on disk to the stylesheet file:

```php
$path = $styler->getStylesheetPath();
```

Retrieving the `<link>` tag programmatically, using the URL to access the `vendor` folder:

```php
$linkTag = $styler->getStylesheetTag('/url/to/vendor/folder');
```

Retrieving the URL to the stylesheet file, using the URL to access the `vendor` folder:

```php
$stylesheetURL = $styler->getStylesheetURL('/url/to/vendor/folder');
```

### Highlighting variables in the final document

The "MarkVariables" highlighter allows highlighting (not syntax highlighting) all variable type commands, even once they have been processed by the mail preprocessor. This is handy when testing, to quickly identify all places in an HTML document where variables are used.

```php
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
$styles = $formatter->getStyleTag();
```

This then only has to be added to the target document.

#### Integrate styles inline

For HTML mailings, or cases where the styles cannot be easily injected, the inline mode will automatically add the necessary styles to every command instance.

Simply enable the inline mode:

```php
$formatter->makeInline();
```


## Translation to other syntaxes

The translator class makes it easy to convert documents with mailcode to other syntaxes, like the bundled Apache Velocity converter.

### Translating whole strings

```php
// create the safeguarder instance for the subject string
$safeguard = Mailcode::create()->createSafeguard($string);

// create the translator
$apache = Mailcode::create()->createTranslator()->createSyntax('ApacheVelocity');

// convert all commands in the safeguarded string
$convertedString = $apache->translateSafeguard($safeguard);
```

### Translating single commands

```php
// create the translator
$apache = Mailcode::create()->createTranslator()->createSyntax('ApacheVelocity');

// create a command
$command = Mailcode_Factory::setVariable('VAR.NAME', '8');

// convert it to an apache velocity command string
$apacheString = $apache->translateCommand($command);
```

### Translate to: Apache Velocity

The Apache Velocity translator uses the formal reference notation for all commands, to minimize the risks of running into parsing conflicts. In general, all generated commands should be compatible from Apache Velocity 2.0 and upwards.

**Requirements**

The following tools have to be enabled in the Velocity templates:

  * [DateTool](https://velocity.apache.org/tools/devel/apidocs/org/apache/velocity/tools/generic/DateTool.html)
  * [EscapeTool](https://velocity.apache.org/tools/devel/apidocs/org/apache/velocity/tools/generic/EscapeTool.html)
  * [StringUtils](http://commons.apache.org/proper/commons-lang/apidocs/org/apache/commons/lang3/StringUtils.html)

These tools can be added to the context of templates like this:

```javascript
context.add("date", new DateTool());
context.add("esc", new EscapeTool());
context.put("StringUtils", new StringUtils());
```

NOTE: The names are case sensitive. There is a mix of cases here - they have to stay this way to be backwards compatible.

Commands that require these tools:

  - ShowDate (DateTool)
  - ShowSnippet (EscapeTool)
  - If Empty / If Not Empty (StringUtils)
  - ElseIf Empty / ElseIf Not Empty (StringUtils)

If these tools are not available, these commands will throw errors if they are used in a template.

#### Configuring date formats

When working with dates, the generated velocity statement will assume the date to be provided in the default internal format:

```
yyyy-MM-dd'T'HH:mm:ss.SSSXXX
```  

If the variable source data does not match this format, the date commands will fail. 

To change this, the internal format can be specified on a per-command basis, using translation parameters:

```php
$var = Mailcode_Factory::showDate('ORDER.DATE');
$var->setTranslationParam('internal_format', 'yyyy-MM-dd');
```

The translator will automatically use the specified format instead.

#### Configuring date formats via Safeguard

To adjust the format of dates in a safeguarded string, the shortest way is to set the translation parameter for relevant date variables.

```php
// Create the translator and the safeguard
$mailcode = Mailcode::create();
$syntax = $mailcode->createTranslator()->createSyntax('ApacheVelocity');
$safeguard = Mailcode::create()->createSafeguard($sourceString);

// Configure all date commands, as needed
$dateCommands = $safeguard->getCollection()->getShowDateCommands();
foreach($dateCommands as $dateCommand)
{
    $dateCommand->setTranslationParam('internal_format', $internalFormat);
}

// Translate the string
$result = $syntax->translateSafeguard($safeguard);
```