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

## Format compatibility

Mailcode mixes well with HTML and XML. Its strict syntax makes it easy to distinguish it from most text formats. with the notable exception of CSS. In HTML, all style tags are
ignored.

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

