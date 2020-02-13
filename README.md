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
