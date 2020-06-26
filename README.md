[![Build Status](https://travis-ci.com/Mistralys/mailcode.svg?branch=master)](https://travis-ci.com/Mistralys/mailcode)

# Mailcode Syntax parser

The mailcode syntax was created for preprocessor commands in emailings.

Mailcode is verbose by design, without shorthand notations, for both better readability and performance. It has been developed to unify interchangeable backend preprocessor syntaxes
into one language that's easy to use. 

## The syntax

All commands follow the same structure.

Parameterless:

```
{command}
```

With parameters:

```
{command [subtype]: parameters,keywords}
```

The subtype can switch between modes of the same command.

## Supported commands

### Display variable values
```
{showvar: $CUSTOMER.NAME}
```

### Set a variable

```
{setvar: $CUSTOMER.NAME = "value"}
```

### IF conditionals

Variable-based conditions:

```
{if variable: $CUSTOMER.NAME == "John"}
    Hi, John.
{elseif variable: $CUSTOMER.NAME == "Jack"}
    Howdy, Jack.
{end}
```

Non-variable based conditions:

```
{if: 6 + 2 == 8}
    It means 8.
{end}
```

Checking if a variable is empty or does not exist:

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

```
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
