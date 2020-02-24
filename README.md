[![Build Status](https://travis-ci.com/Mistralys/mailcode.svg?branch=master)](https://travis-ci.com/Mistralys/mailcode)

# Mailcode Syntax parser

First draft of the type of commands planned: 

```
{showvar: $CUSTOMER.NAME}

{comment: This is a comment.}

{setvar: $CUSTOMER.NAME = "value"}

{if variable: $CUSTOMER.NAME == "value"}
{elseif variable: $CUSTOMER.NAME == "blabla"}
{endif}

{if command:velocity statement}
{endif}

{command:raw command statement}

{for: $NAME in $CUSTOMER.NAMES}
    {showvar: $NAME}
{endfor}
```

This follows the same structure everywhere:

```
{command [type]}

{command [type]: params}
```

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