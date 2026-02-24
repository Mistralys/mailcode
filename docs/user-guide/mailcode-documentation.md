# Mailcode documentation

## The syntax

### Base structure

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

Toggleable flags to enable/disable features:

```
{command subtype: parameters flagFoo: flagBar:}
```

Named parameters:

```
{command subtype: "value" name="param value"}
```

### Escaping special characters

#### Double quotes

String literals are expected to be quoted using double quotes (`"`). To use double quotes within a string literal, they can be escaped using a backslash (`\`):

```
{if contains: $PRODUCT.NAME "Search term with \"quotes\""}
```

#### Curly braces

To use curly braces in a document, or in string literals, they can be escaped:

```
{if contains: $PRODUCT.NAME "With \{braces\}"}
   Literal braces: \{end\}
{end}
```

## Inserting variable values

Variables are inserted using the `showvar` command:

```
The brown fox jumped over the {showvar: $FENCE.COLOR} fence.
```

The value of the variable will be inserted as-is, without any changes or formatting.

> As a global convention, variable names are always uppercase.

## Encoding texts

Depending on the context in which variables are used, their values may need to be encoded. The following encodings are available:

- `urlencode:`
  Apply URL encoding to the variable value, to escape special URL characters.
  Example: `Text to encode` → `Text%20to%20encode`

- `urldecode:`
  Remove any existing URL encoding in a variable value.
  Example: `Text%20to%20encode` → `Text to encode`

- `idnencode:`
  Apply IDN encoding to the variable value, to escape special characters in internationalized domain names (aka umlaut domains).
  Example: `ümläüt-domain.de` → `xn--mlt-domain-r5a9zd.de`

- `idndecode:`
  Convert an IDN encoded text to the special characters version.
  Example: `xn--mlt-domain-r5a9zd.de` → `ümläüt-domain.de`

### Variable values

Applying encoding for a variable value can be done simply by adding the keyword to the command. Examples:

```
{showvar: $PRODUCT.LABEL urlencode:}
{showdate: $ORDER.DATE "Y-m-d H:i:s" urlencode:}
{showvar: $DOMAIN.NAME idnencode:}
```

### Bits of text

Encoding may also be applied manually to bits of text, with the `showencoded` command.

```
{showencoded: "ümläüt-domain.de" idnencode:}
Result: xn--mlt-domain-r5a9zd.de

{showencoded: "Text to encode" urlencode:}
Result: Text%20to%20encode
```

This is typically used in links, when they contain values that must be encoded. Instead of converting it yourself manually (with an online tool for example), consider using the command instead. Examples:

```
https://mistralys.eu?domain={showencoded: "iönös.com" idnencode: urlencode:}

https://mistralys.eu?message={showencoded: "Please review the e-mail we have sent you" urlencode:}
```

> **Note:** The `urlencode:` keyword is added automatically when Mailcode detects that a variable is used in an URL. It does not conflict with the IDN encoding in the first command in the example. This is because once the IDN encoding has been applied, there are no special characters left to URL encode.

### Combining encodings

Encodings can be combined to achieve advanced effects. In the following example, the text is an URL encoded internationalized domain name. The name is first url decoded to restore the special characters, then IDN encoded.

```
{showencoded: "i%C3%B6n%C3%B6s.com" urldecode: idndecode:}

Result: xn--ins-snab.com
```

> **Note:** Encodings are applied in the exact order they are specified.

## Decrypting secrets

Sensitive data like passwords are encrypted in the rendered templates, to guarantee that they are never stored in plain text. Systems that need access to the encrypted data (like the login system for auth tokens) can decrypt them using server side encryption keys. These keys are known only between these systems, but are given names that can be used in the `showvar` command.

```
{showvar: $USER_FIELD.ENCRYPTED_PASSWORD decrypt="key_name"}
```

This will guarantee that systems that have the `key_name` encryption key can decrypt the password. All others (as well as invalid key names) will only see the encrypted password.

## Formatting dates

### Formatting examples

Variables that contain dates can be easily formatted using the `showdate` command:

```
Date of your order: {showdate: $ORDER.DATE}
```

By default, the date will automatically be formatted according to the preferences of the mailing's country.

However, you can easily specify a custom format as necessary, or even add the time:

```
Time of your order: {showdate: $ORDER.DATE "Y-m-d H:i:s"}
```

> For more details on how to format dates, look at the [date formats](#date-formats) section.

If the date variable is omitted, the date and time at the time of sending will be used. This is useful to display the current year, for example:

```
Current year: {showdate: "Y"}
```

### Handling time zones

By default, dates will use the default time zone of the document's country. For example, german mailings will use `Europe/Berlin`. Also see [default time zones](#default-time-zones).

This can be overridden by specifying an explicit time zone in a command:

```
{showdate: $ORDER.DATE "Y-m-d H:i:s" timezone="Europe/Paris"}
```

The time zone can also be specified using a variable:

```
{showdate: $ORDER.DATE "Y-m-d H:i:s" timezone=$ORDER.TIME_ZONE}
```

### Dates in links

As a general rule, **avoid using human readable dates in links**. The reason is simple: Dates inserted in links are usually not meant to be read by humans, but by automated systems. These will prefer dates in the original, non formatted form, unless specifically requested by the technicians responsible for the target page.

To insert dates in links, simply use the `showvar` command instead. Here's an example URL:

```
https://mistralys.eu/?date={showvar: $USER_FIELD.DATE urlencode:}
```

> Note the `urlencode:`, which ensures that even the raw date is transmitted correctly.

If the target page requires a specific date format, make sure to also use the `urlencode:` parameter for it to be transmitted correctly.

Example: The original date includes the time (e.g. 2026-02-20 08:37:58), but the target page needs a german date without time (i.e. 20.02.2026). This can be done with the following syntax:

```
https://mistralys.eu/?formattedDate={showdate: $USER_FIELD.DATE "d.m.Y" urlencode:}
```

## Formatting numbers

Variables that contain numbers can be easily formatted using the `shownumber` command:

```
Total number of items: {shownumber: $ITEMS.TOTAL "1,000.00"}
```

The target format can be specified simply by formatting the number `1000` the way you would display that number. On the decimal side, add as many zeros as you want decimal places to show. This formatting will then be applied to any value in the variable.

Some examples:

| Description | Format | Number | Result |
|---|---|---|---|
| Without decimals | Without thousands separator | `1000` | 6841.58 | 6842 |
| With 4 decimals | Without thousands separator | `1000.0000` | 3013.24 | 3013.2400 |
| Without decimals | Without thousands separator | `1000` | 4062503.99 | 4062504 |
| Without decimals | With thousands separator | `1,000` | 955163.43 | 955,163 |
| US style number | `1,000.00` | 1626.46 | 1,626.46 |
| DE style number | `1.000,00` | 2345.11 | 8.565,44 |
| FR style number | `1 000,00` | 7754.96 | 7 754,96 |

### Handling negative numbers

By default, negative numbers are shown with the minus sign, e.g. `-89.34`. To show only absolute numbers (without the minus sign), use the `absolute:` keyword. The format is applied as usual.

```
{shownumber: $ITEMS.TOTAL "1 000,00" absolute:}
```

Examples for this command:

| Number | Result |
|---|---|
| -73.22 | 73,22 |
| 3.75 | 3,75 |
| -7201.19 | 7 201,19 |
| 6585.79 | 6 585,79 |

### Zero-padding

Zero-padding is specified by appending the required number length like this:

```
{shownumber: $MONTH "1000:##"}
```

The number of hashes determines the target length of the number. This example will add a zero-padding of `2`, meaning a `5` will be shown as `05`.

## Formatting prices

Variables that contain prices can be formatted using the `showprice` command:

```
Total cost: {showprice: $ORDER.TOTAL}
```

> The country's defaults are automatically applied to the formatting. This includes the currency symbol, the decimal separator, and the thousands separator.

### Localized formatting overview

| Country | Name | Symbol | Preferred symbol | Formatted price | Formatted price (Negative) |
|---|---|---|---|---|---|
| AT - Austria | EUR | € | € | `10.222,99 €` | `-10.222,99 €` |
| CA - Canada | CAD | $ | $ | `$ 10,222.99` | `$ -10,222.99` |
| DE - Germany | EUR | € | € | `10.222,99 €` | `-10.222,99 €` |
| ES - Spain | EUR | € | € | `10.222,99 €` | `-10.222,99 €` |
| FI - Finland | EUR | € | € | `10 222,99 €` | `-10 222,99 €` |
| FR - France | EUR | € | € | `10 222,99 €` | `- 10 222,99 €` |
| GB - Great Britain | GBP | £ | £ | `£10,222.99` | `-£10,222.99` |
| IE - Ireland | EUR | € | € | `10.222,99 €` | `-10.222,99 €` |
| IT - Italy | EUR | € | € | `10.222,99 €` | `-10.222,99 €` |
| MX - Mexico | MXN | $ | MXN | `MXN 10,222.99` | `MXN -10,222.99` |
| NL - Netherlands | EUR | € | € | `10.222,99 €` | `-10.222,99 €` |
| PL - Poland | PLN | zł | zł | `10.222,99 zł` | `-10.222,99 zł` |
| RO - Romania | RON | | | `10.222,99` | `-10.222,99` |
| SE - Sweden | SEK | kr | kr | `10 222,99 kr` | `-10 222,99 kr` |
| US - United States | USD | $ | $ | `$10,222.99` | `-$10,222.99` |
| ZZ - Country-independent | USD | $ | $ | `$10.222,99` | `-$10.222,99` |

### Handling negative numbers

By default, negative numbers are shown with the minus sign, e.g. `-18.81`. To show only absolute numbers (without the minus sign), use the `absolute:` keyword. The format is applied as usual.

```
{showprice: $ORDER.TOTAL absolute:}
```

### Overriding the defaults

The default currency and/or region can be specifically overridden.

```
{showprice: $ORDER.TOTAL currency="MXN" region="es_MX"}
```

The same can be done with variables:

```
{showprice: $ORDER.TOTAL currency=$ORDER.CURRENCY region=$ORDER.REGION}
```

### Choice of symbol

By default, whether the currency symbol or name is used is determined automatically according to the country's preferences. The use of the currency name can be forced using the `currency-name:` keyword.

```
{showprice: $ORDER.TOTAL currency-name:}
```

## Inserting URLs

The `showurl` command accepts anything that resolves into a valid URL, including nested variables. Before doing anything, it renders its body (between the opening and closing `{showurl}`) to generate the target URL. It then applies any transformations, like appending URL parameters and generating the tracking link.

### Regular URLs

Regular URLs with or without query parameters, including variable parameters.

```
{showurl: "tracking-id"}
    https://mistralys.eu?param=value&variable={showvar: $VAR.NAME urlencode:}
{showurl}
```

### Dynamic URLs

URLs whose domain part and/or path part are generated dynamically.

```
{showurl: "tracking-id"}
    https://{showvar: $DOMAIN.NAME idnencode:}.{showvar: $DOMAIN.EXTENSION}
{showurl}
```

### Variable URLs

URLs that come entirely from a variable.

```
{showurl: "tracking-id"}
    {showvar: $CUSTOMER.WEBSITE_URL}
{showurl}
```

### Tracking IDs

The tracking ID is used to identify the location of the link in the document, e.g. `header-image`.

The minimum version of the command looks like this:

```
{showurl: ""}
https://mistralys.eu
{showurl}
```

### Adding query parameters

The command allows specifying additional query parameters that should be added to the target URL, like UTM parameters or the like.

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

Additional query parameters can also be used when the tracking is disabled:

```
{showurl: no-tracking: "foo=bar"}
https://mistralys.eu
{showurl}
```

Resulting URL:

```
https://mistralys.eu?foo=bar
```

### Tracking exceptions

Some URL schemes are natively excluded from the tracking, and will never be tracked:

- `ftp://`
- `mailto://`
- `ssh://`
- `tel://`

## Phone number links

Phone numbers must be formatted in a specific way for phone links to work. The `{showphone}` command ensures that a phone number stored in a variable is converted automatically to the E164 standard for `tel` URLs.

When adding phone links as a global link or in a mailing, make sure to always use this command. You can specify the URL like this:

```
tel://{showphone: $PHONE.NUMBER "DE" urlencode:}
```

This will create the following URL:

```
tel://%2B49721913740
```

Without URL encoding, it looks like this: `tel://+49721913740`

The second parameter, `"DE"`, is used to tell the command in which format the phone numbers are stored in the target variable. This should typically be the mailing's country.

> **Note:** You must specify the country even if the phone number is already in E164 format, or an international format. It future proofs the mailing for any format changes.

Do not forget to add the `urlencode:` so it gets properly encoded when used in an URL.

### Supported countries

The `{showphone}` command can convert from the country-specific phone number formats of a large number of countries. The following list shows all countries that are supported by Mailcode and the Pigeon backend.

**Note:** There are many more countries listed here than can be reasonably expected to be needed. That's normal: The list is automated and simply shows all known countries in the system.

> **Hint:** The country code for UK is `GB`.

## Inserting text snippets

Text snippets are inserted using the `showsnippet` command:

```
Your selected product: {showsnippet: $product_cloudserver}
```

You can also verify the required Mailcode command in the snippet's properties in the text snippets management.

> Snippet names are always lowercase, to distinguish them from variables.

Snippet texts are inserted as-is: In practice this is like copying and pasting the snippet text wherever you add the `showsnippet` command.

### Disabling HTML conversion

By default, newlines in snippets are converted to HTML `<br>` tags. To disable this conversion, use the `nohtml:` keyword:

```
{showsnippet: $snippet_name nohtml:}
```

### Handling line breaks

In the case of multiline snippets, any line breaks in the text are preserved.

Hint: As a general rule, we recommend to remove any newlines from the end of a snippet. This way, you remain free choose whether a snippet needs newlines after it, wherever you insert it.

### Working with namespaces

Namespaces are used to group snippets together thematically. By default, all snippets are loaded from the global namespace.

To load snippets from a specific namespace, use the `namespace` parameter:

```
{showsnippet: $snippet_name namespace="other_namespace"}
```

> **Note:** Snippets with the same name can exist in different namespaces.

## Monospaced text

The `{mono}` command can be used to format text with a monospaced font.

The command has two modes:

**Inline**

Format a bit of text within a sentence.

```
Text with {mono}monospaced{end} font.
```

This generates the following output:

Text with `monospaced` font.

**Multiline**

Format a block of text, respecting newlines. Ideal for displaying log file contents for example, where newlines have to be made compatible with HTML. The text would otherwise be displayed in a single line.

```
{mono multiline:}
----------
First line.
Second line.
----------
{end}
```

This generates the following output:

```
----------
First line
Second line
----------
```

> Other Mailcode commands can be used inside of `{mono}` commands as usual.

## Styling commands

Variables, snippets and the like do not contain any styling, since they contain only plain text. If you wish to make a variable or snippet text bold for example, you have to make the whole command bold in the WYSIWYG text editor.

### Bold, italic, etc

Take special care when styling Mailcode commands. Styling one only partially will break it. For example, this command will not work because only part of it is bold:

```
Product: **{showsn**ippet: $product_name}
```

The reason for this is the (invisible) HTML code that is generated by the WYSIWYG text editor. Looking at the broken command, you can see that the closing `</strong>` tag is placed right in the middle of the command. This causes the command to not be recognized anymore:

```html
Product: <strong>{showsn</strong>ippet: $product_name}
```

The correct way is to make the whole command bold:

```
Product: **{showsnippet: $product_name}**
```

Now the HTML code does not break up the command anymore:

```html
Product: <strong>{showsnippet: $product_name}</strong>
```

### Removing formatting

When in doubt, use the WYSIWYG text editor's "Remove formatting" feature to remove all styling from a command:

- Select the whole text of the target command
- Click on "Remove formatting"

### Logic commands

Logic commands like `if`, `elseif` or `for` should never be styled. They are used for logic only, and do not leave any content in their place, like a `showvar` command does, for example.

If a logic command is styled, the command itself will be removed when the mailing is sent, but the styling will remain. This can have detrimental effects on spacing and the mailing's layout in general.

The only commands that may be styled are the following:

- `showvar`
- `showsnippet`
- `showdate`

## Setting variable values

### Basic usage

Variable values can be set with the `setvar` command. This can be done anywhere in the mailing, but must be done before the variable is used.
We recommend adding all required variables together at the beginning of the mailing, to make them easier to maintain.

> Variables created this way are temporary, and only exist within the mailing. They must not be added as custom variables to communication types.

Set a text value:

```
{setvar: $TEMP_VARIABLE = "Variable value"}
```

Set a numeric value:

```
{setvar: $TEMP_VARIABLE = 42}
```

Set the result of an arithmetic operation as value:

```
{setvar: $TEMP_VARIABLE = $AMOUNT_PRODUCTS * 5 / 100}
```

> Make sure to use a single `=` sign. The double `==` is used for comparisons in IF commands.

Tip: For convenience, you may omit the `=` sign, as it is implied.

```
{setvar: $TEMP_VARIABLE "Variable value"}
```

### Counting records

The `count` parameter can be used to count the items in a list variable, and use the result as value.

```
{setvar: $TEMP_VARIABLE count=$USER_LIST_PRODUCTS}
```

The `$TEMP_VARIABLE` variable will contain the number of records in `$TEMP_VARIABLE`.

This can be used, for example, to hide content when a list is empty:

```
{setvar: $TEMP_VARIABLE count=$USER_LIST_PRODUCTS}

{if variable: $TEMP_VARIABLE > 0}
    (Shown when the list is not empty)
{end}
```

## Displaying content depending on variable values

The most common use of IF commands is to display or hide content according to the value of a variable.

### Show a text if a value matches

This will show the text between the `if` and `end` commands if the variable matches the expected value.

```
{if variable: $VAR.NAME == "Variable value"}
   Text to show if the value matches.
{end}
```

By default, this will expect an exact match, which is case sensitive. To make the check case insensitive, add the `insensitive:` keyword:

```
{if variable: $VAR.NAME == "Variable value" insensitive:}
   Shown if the value matches any case variation of "Variable value" ("variable value", "VARIABLE VALUE", ...).
{end}
```

### Checking for empty values

This will show a text if the variable value is empty.

```
{if empty: $VAR.NAME}
    The variable value is empty.
{end}
```

This on the other hand will make sure the variable is not empty.

```
{if not-empty: $VAR.NAME}
    The variable value is NOT empty.
{end}
```

### Searching in variable values

The `if contains` command allows searching for one or more terms in a variable value.

```
{if contains: $PRODUCT.NAME "Cloud"}
    Shown if the product name contains the search term "Cloud".
{end}
```

With several search terms:

```
{if contains: $PRODUCT.NAME "Cloud" "Backup" "Dedicated"}
    Shown if the product name contains any of the specified search terms.
{end}
```

By default the search is case sensitive. It can be made case insensitive with the `insensitive:` keyword:

```
{if contains: $PRODUCT.NAME "Cloud" insensitive:}
    Shown if the product name contains the search term "Cloud" or "cloud".
{end}
```

You can use the order you prefer for the command's parameters, it is entirely free.

```
{if contains: insensitive: "Cloud" $PRODUCT.NAME}
    Shown if the product name contains the search term "Cloud" or "cloud".
{end}
```

### Excluding terms in variable values

The `if not-contains` command allows ensuring none of the specified search terms occur in a variable value.

```
{if not-contains: $PRODUCT.NAME "Cloud"}
    Shown if the product name does not contain the search term "Cloud".
{end}
```

With several search terms:

```
{if not-contains: $PRODUCT.NAME "Cloud" "Server" "Backup"}
    Shown if none of the specified search terms are found in the product name.
{end}
```

### Searching in the beginning or end of a variable value

The commands `if begins-with` and `if ends-with` allow searching in the beginning or the end of a variable value respectively.

```
{if begins-with: $PRODUCT.NAME "Cloud"}
    Shown if the product name begins with "Cloud"
{end}
```

```
{if ends-with: $PRODUCT.NAME "Server"}
    Shown if the product name ends with "Server"
{end}
```

By default the search is case sensitive. It can be made case insensitive with the `insensitive:` keyword:

```
{if begins-with: $PRODUCT.NAME "Cloud" insensitive:}
    Shown if the product name begins with "Cloud" or "cloud".
{end}
```

You can use the order you prefer for the command's parameters, it is entirely free.

```
{if begins-with: insensitive: "Cloud" $PRODUCT.NAME}
    Shown if the product name begins with "Cloud" or "cloud".
{end}
```

### Comparing numeric values

Clients can provide numbers with different decimal separator characters (comma or dot). To ensure that number comparisons work as expected (computers work with dots), there are several number-specific commands. These do the necessary conversions in the background.

This will show a text if the variable value is bigger than 347.

```
{if bigger-than: $PRODUCT.PRICE "347"}
    The price exceeds 347.
{end}
```

In same way, it is possible to check if the value is smaller:

```
{if smaller-than: $PRODUCT.PRICE "347"}
    The price is below 347.
{end}
```

Or to check if the value matches a specific number:

```
{if equals-number: $PRODUCT.PRICE "347"}
    The product costs exactly 347.
{end}
```

Hint: The target amount can be specified with either a dot or a comma as decimal separator.

### Show a text, or an alternative text otherwise

This will either show the text if the value matches, or the other text in all other cases.

```
{if variable: $VAR.NAME == "Variable value"}
    Text to show if the value matches.
{else}
    Text to show in all other cases.
{end}
```

### Switching between several possibilities

To switch between several possible values, add an `elseif` command for each additional value.

```
{if variable: $PRODUCT.NAME == "Cloud Server"}
    Shown if the product name equals "Cloud Server".
{elseif variable: $PRODUCT.NAME == "Dedicated Server"}
    Shown if the product name equals "Dedicated Server".
{elseif empty: $PRODUCT.NAME}
    Text to show if the variable is empty.
{else}
    Catch-all text to show in all other cases.
{end}
```

### Combining criteria with AND / OR

In some cases, the usual if/else if combination will not be sufficient. Mailcode supports using the keywords `and:` and `or:` to combine several criteria in a single command.

An OR condition is applied when any of the conditions are true:

```
{if variable: $OFFER.PRODUCT_GROUP == "Shared Hosting" or variable: $OFFER.PRODUCT_GROUP == "Managed Server"}
    Shown if the product group matches one or the other.
{end}
```

An AND condition is applied when all of the conditions are true:

```
{if variable: $OFFER.PRODUCT_GROUP == "Shared Hosting" and variable: $OFFER.PRODUCT_NAME == "MyWebsite"}
    Shown if the product group and name match.
{end}
```

It is also possible to combine different if command types (here the `variable` and `contains` types):

```
{if variable: $PRODUCT.ID == 4785 and contains: $PRODUCT.NAME "ListLocal"}
    Shown if the product ID matches, and the name contains the search term.
{end}
```

You may combine as many criteria like this as needed. There is no hard limit, but for readability purposes the general rule is to not combine more than 3 conditions.

Note: Use either the `and:` keyword or the `or:` keyword, but using both at the same time in the same command is not allowed.

### Freeform conditions

Without subtype, the `IF` condition is not validated, and will be passed through as-is to the translation backend.

```
{if: 6 + 2 == 8}
    It means 8.
{end}
```

### Comparison operators

- `==` Equals
- `!=` Not equals

Numerical comparisons:

- `<` Smaller than
- `>` Bigger than
- `<=` Smaller or equals
- `>=` Bigger or equals

## Searching in list variables

These commands are specialized in searching in list variable records: They go through all records available in a list variable, and search in the value of a list property.

### Searching for partial texts

The `if list-contains` command allows searching through all available values of a single property in a list variable. It is applied if any of the values contain any of the search terms.

```
{if list-contains: $PRODUCTS.NAME "Cloud"}
    Shown if any product name in the list contains the search term "Cloud".
{end}
```

With several search terms:

```
{if list-contains: $PRODUCT.NAME "Cloud" "Server" "Backup"}
    Shown if any of the search terms are found in any of the product names of the list.
{end}
```

### Searching for exact matches

The `list-equals` command works just like the `list-contains` command, but runs an exact match search. It is only applied if a property value in the list matches the search term(s) exactly.

```
{if list-equals: $PRODUCTS.NAME "Cloud Server Pro"}
    Shown if any product name in the list equals "Cloud Server Pro".
{end}
```

With several search terms:

```
{if list-equals: $PRODUCT.NAME "Cloud Server Pro" "Server Unlimited"}
    Shown if any of the search terms equal any of the product names of the list.
{end}
```

The `insensitive:` keyword can make the search case insensitive.

```
{if list-equals: $PRODUCTS.NAME insensitive: "Cloud Server Pro"}
    Shown if any product name in the list equals "Cloud Server Pro", disregarding the case of letters.
    It will match "CLOUD SERVER PRO" as well as "cloud server pro", or any other variations.
{end}
```

### Searching in the beginning only

The `if list-begins-with` command works like `if list-contains`, but finds only values that start with the search term(s). For example, searching for "MyWeb" will find the value "MyWebsite Professional", but not "Cloud enabled MyWebsite".

```
{if list-begins-with: $PRODUCT.NAME "MyWeb"}
    Shown if the value starts with the search term.
{end}
```

### Searching in the end only

The `if list-ends-with` command works like `if list-contains`, but finds only values that end with the search term(s). For example, searching for "Server" will match the value "Cloud Server", but not "Cloud Server Professional".

```
{if list-ends-with: $PRODUCT.NAME "Server"}
    Shown if the value ends with the search term.
{end}
```

### Excluding values in list variables

The `if list-not-contains` command allows searching through all available values of a single property in a list variable, to exclude specific values.

```
{if list-not-contains: $PRODUCTS.NAME "Cloud"}
    Shown if none of the product names in the list contain the search term "Cloud".
{end}
```

With several search terms:

```
{if list-not-contains: $PRODUCT.NAME "Cloud" "Server" "Backup"}
    Shown if none of the search terms are found in the product names of the list.
{end}
```

### Using regular expressions

The `list-contains` command can be switched to regex mode, to allow using regular expressions in search terms. The expressions must use JAVA regular expression syntax, as they are interpreted directly on the Pigeon side. Use the `regex:` keyword to enable regex mode.

Example:

```
{if list-contains: $PRODUCTS.PRODUCT_ID regex: "2[0-9]+"}
    Shown if any product ID in the list is a number starting with a 2, followed by one or more digits.
{end}
```

**Note:** The `regex:` keyword can be combined with the `insensitive:` keyword to make the expression case insensitive.

#### Curly braces in regular expressions

Regular expressions may use curly braces when defining quantifiers, e.g. `{1,5}`. This is a special case where you do not have to escape the braces. The parser will recognize these braces so the regex stays readable.

These commands are both valid:

```
{if list-contains: $PRODUCTS.NAME regex: "[0-9]{1,3}"}

{if list-contains: $PRODUCTS.NAME regex: "[0-9]\{1,3\}"}
```

## Looping through variables containing several records

When a variable contains several records, loops allow going through these and displaying properties of the individual records. This is most commonly used to create lists: Whatever is shown inside the command will be duplicated for each record.

### Text-based list

Assuming a variable called `$USER_FIELD.DOMAINS` contains a list of domain names, the name being stored in the record property `NAME`:

```
{for: $DOMAIN_ENTRY in: $USER_LIST_DOMAINS}
- {showvar: $DOMAIN_ENTRY.NAME}
{end}
```

This will display a list like this:

```
- google.com
- mistralys.eu
- aeonoftime.com
```

### Content-based list

In the mail builder, surrounding one or more contents with a loop will duplicate these contents for each record. The record property variables can be used within the contents, to make them record-specific.

**Use loops with contents sparingly.** The size of the HTML document can increase substantially, since a lot of HTML code is created for each record. It is essential to know the maximum possible amount of records beforehand.

### Stop by loop number

The `break-at` parameter allows specifying a loop number to stop at.

For example, consider a list of products that should display a maximum of 15 products. This can be done simply by adding the parameter to the command like this:

```
{for: $PRODUCT in: $USER_LIST_PRODUCTS break-at=15}
    {showvar: $PRODUCT.NAME}
{end}
```

> **Note:** If there are less than 15 products in the list, the parameter will have no effect.

### Stop by condition

In some cases, a loop needs to be stopped before it has gone through the whole list of records. Typically, this happens when a specific condition is met.

For example, consider a list of products ordered by price, in ascending order: The requirement is to show products up to a maximum price of 100 EUR. This could be done with a break as follows:

```
{for: $PRODUCT in: $USER_LIST_PRODUCTS}
    {if bigger-than: $PRODUCT.PRICE 100}
        {break}
    {end}
    
    {showvar: $PRODUCT.NAME}: {shownumber: $PRODUCT.PRICE "1,000.00"}
{end}
```

The if command will check each product's price, and if it is bigger than 100 EUR, it will stop the loop.

## URL encoding variable values

In cases where a variable needs to be inserted into an URL, it is possible to turn on URL encoding with the `urlencode:` keyword.

```
{showvar: $CUSTOMER.FIRSTNAME urlencode:}
```

All commands that display variable values support this keyword, namely:

- `showvar`
- `showdate`
- `shownumber`
- `showsnippet`

## Adding comments

Comments can be used to document commands, and will not be shown anywhere in the mailings. Unlike other commands, the comment text does not have to be quoted (but can be, if you prefer to do so).

```
{comment: "This text is used to document things."}
```

## Date formats

A date format string is made up of the following parts:

- Date and/or time signs
- Punctuation

The signs each represent a part of a date (like the year, month number or day), and the punctuation can be used to separate these parts.

The standard german date format (day.month.year) for example, is written like this:

`d.m.Y`

The signs and punctuation can be combined in any order.

For example, a typical US date (month/day/year):

`m/d/Y H:i`

### Date signs

Use these signs to add the date information you need.

- `d` Day of the month, with leading zeros
- `j` Day of the month, without leading zeros
- `m` Month number, with leading zeros
- `n` Month number, without leading zeros
- `Y` Year, 4 digits
- `y` Year, 2 digits

### Time signs

These signs are used to add time information.

- `H` Hour, 24-hour format with leading zeros
- `G` Hour, 24-hour format without leading zeros
- `h` Hour, 12-hour format with leading zeros
- `g` Hour, 12-hour format without leading zeros
- `a` AM/PM marker
- `i` Minutes with leading zeros
- `s` Seconds with leading zeros
- `v` Milliseconds
- `e` Timezone

### Punctuation

You may use any of these punctuation characters in the format string:

- `.` Dot
- `/` Slash
- `-` Hyphen
- `:` Colon
- ` ` Space

## Default time zones

These are the default time zones used by country:

| Country | Time zone |
|---|---|
| Austria | `Europe/Vienna` |
| Canada | `America/Vancouver` |
| Germany | `Europe/Berlin` |
| Spain | `Europe/Madrid` |
| Finland | `Europe/Helsinki` |
| France | `Europe/Paris` |
| Great Britain | `Europe/London` |
| Ireland | `Europe/Dublin` |
| Italy | `Europe/Rome` |
| Mexico | `America/Mexico_City` |
| Netherlands | `Europe/Amsterdam` |
| Poland | `Europe/Warsaw` |
| Romania | `Europe/Bucharest` |
| Sweden | `Europe/Stockholm` |
| United States | `US/Eastern` |

---

## PHP API reference

The following sections document the PHP library API for developers integrating Mailcode programmatically.

### Setting the default time zone

It is possible to set the default time zone globally for the `showdate` command, separately from the native PHP time zone:

```php
use Mailcode\Mailcode_Commands_Command_ShowDate;

Mailcode_Commands_Command_ShowDate::setDefaultTimezone('Europe/Paris');
```

This will make all `showdate` commands use `Europe/Paris`, unless a specific time zone is specified explicitly in a command.

### Default encryption key names

It is possible to set a default key name that will be automatically used for all commands with an empty `decrypt` parameter:

```php
use Mailcode\Decrypt\DecryptSettings;

DecryptSettings::getDefaultKeyName('default-key');
```

After this method is called, the following commands are functionally equivalent:

```
{showvar: $CUSTOMER.NAME decrypt=""}
{showvar: $CUSTOMER.NAME decrypt="default-key"}
```

### Custom tracking ID generator

The default generated tracking ID follows the scheme `link-001`, with a link counter that is unique for the whole request. A custom ID generator can be registered like this:

```php
use \Mailcode\Mailcode_Commands_Command_ShowURL;
use \Mailcode\Commands\Command\ShowURL\AutoTrackingID;

// The method expects a callable, which must return a string.
AutoTrackingID::setGenerator(static function(Mailcode_Commands_Command_ShowURL $command) : string 
{
    return 'trackingID';
});
```

### Integrated preprocessing

Mailcode is a preprocessor language meant to be interpreted by a preprocessor service, but some commands are made to be preprocessed natively by Mailcode itself. One example is the `mono` command, which applies monospace formatting to text.

The preprocessing is optional and can be done with the specialized PreProcessor class.

> **Note:** When translating to an output syntax like Apache Velocity, the default behavior is to strip out leftover preprocessor commands, so there can be no Mailcode commands in the translated text.

#### Working with the PreProcessor

The PreProcessor is very easy to use: feed it a string with Mailcode commands, and all commands that support pre-processing will be rendered. After this, the resulting string can be passed into a safeguard instance or parsed to fetch the commands.

```php
$subject = '(Mailcode text)';

$processor = \Mailcode\Mailcode::create()->createPreProcessor($subject);
$result = $processor->render();
```

> **Note:** While the preprocessing can be done after safeguarding a text, it is recommended to do it beforehand, to avoid the overhead of unnecessarily parsing the commands. Also, these commands may actually generate new Mailcode syntax to parse.

#### Preprocessing the mono command

The `{mono}` command generates HTML code when preprocessed:

```
This text is {mono}monospaced{end}.
```

The resulting pre-processed text:

```html
This text is <code>monospaced</code>.
```

To create a `<pre>` tag, add the `multiline:` keyword:

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

### Working with commands

Commands like for loops, and if statements that have a closing command and are closed using the `{end}` command support accessing their siblings, and respective opening and closing commands.

For example, the closing command of an `IF` statement has the `getOpeningCommand()` method, which returns the `IF` command that it closes, and vice versa. If command structures with `elseif` and `else` commands allow traversing the whole list of sibling commands.

This makes it easy to work with complex command structures.

### Date format API

#### Accessing format information

The `Mailcode_Date_FormatInfo` class can be used to access information on the available date formats when using the `showdate` command. It is available globally via a factory method:

```php
use Mailcode\Mailcode_Factory;

$dateInfo = Mailcode_Factory::createDateInfo();
```

#### Setting defaults

The `showdate` command uses `Y/m/d` as default date format. The format info class can be used to overwrite this:

```php
use Mailcode\Mailcode_Factory;

$dateInfo = Mailcode_Factory::createDateInfo();
$dateInfo->setDefaultFormat('d.m.Y');
```

Once it has been set, whenever the `showdate` command is used without specifying a custom format string, it will use this default format.

#### Accessing formatting characters programmatically

The format info class offers the `getFormatCharacters()` method to get a list of all characters that can be used.

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

#### Manually validating a date format

Use the `validateFormat()` method to validate a date format string, and retrieve a validation message manually. The same method is used by the `showdate` command, but can be used separately for specific needs.

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

### Format compatibility

Mailcode mixes well with HTML and XML. Its strict syntax makes it easy to distinguish it from most text formats, with the notable exception of CSS. In HTML, all style tags are ignored.

### Safeguarding commands

When texts containing commands need to be filtered, or otherwise parsed in a way that could break the command syntax, the safeguard mechanism allows for easy replacement of all commands with neutral placeholder strings.

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

> **Hint:** Placeholders are case neutral, and thus cannot be broken by changing the text case.

#### Avoiding delimiter conflicts

By default, the placeholders use `999` as delimiters, for example: `9990000000001999`. Each delimiter gets a unique number within the same request, which is zero-padded right, making each placeholder unique in all subject strings.

Having number-based placeholders means that they are impervious to usual text transformations, like changing the case or applying url encoding.

The delimiter string can be adjusted as needed:

```php
use \Mailcode\Mailcode;

$text = '(Text with mailcode commands)';
$safeguard = Mailcode::create()->createSafeguard($text);

$safeguard->setDelimiter('__');
```

This would for example make the delimiters look like `__0000000001__`.

#### Placeholder consistency check

When calling `makeWhole()`, the Safeguard will make sure that all placeholders initially replaced in the target string are still there. If they are not, an exception will be thrown.

#### Accessing placeholder information

The placeholders used in a string can be easily retrieved. Be sure to call `getPlaceholders()` after the initial configuration (setting the delimiters, for example).

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

### Applying formatting

By default, when using the safeguard's `makeWhole` method, all command placeholders are replaced with the normalized syntax of the commands. A number of additional formatting options are available via the safeguard's formatting class.

Creating a formatting instance, using a safeguard:

```php
use \Mailcode\Mailcode;

$text = '(Mailcode commands here)';
$safeguard = Mailcode::create()->createSafeguard($text);

$formatting = $safeguard->createFormatting($safeguard->makeSafe());
```

> **Note:** Formatting is entirely separate from the safeguard. The safeguard instance retains the original text.

#### Replacers and Formatters

There are two types of formatters:

- **Replacers**: These will replace the command placeholders themselves (example: HTML syntax highlighting of commands). Only one replacer may be selected.
- **Formatters**: These will only modify the text around the placeholder, leaving the placeholder intact. Formatters can be combined at will.

While it is not possible to select several replacers, they can be freely combined with formatters.

```php
use \Mailcode\Mailcode;

$text = '(Mailcode commands here)';
$safeguard = Mailcode::create()->createSafeguard($text);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());

$formatting->replaceWithHTMLHighlighting();
$formatting->formatWithMarkedVariables();
```

#### HTML highlighting

The HTML syntax highlighter will add highlighting to all commands in an intelligent way. Commands will not be highlighted if they are used in HTML tag attributes or nested in tags where adding the highlighting markup would break the HTML structure.

```php
use \Mailcode\Mailcode;

$text = '(Mailcode commands here)';
$safeguard = Mailcode::create()->createSafeguard($text);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());

// Select to replace commands with syntax-highlighted commands
$formatting->replaceWithHTMLHighlighting();

$highlighted = $formatting->toString();
```

##### Excluding tags from the highlighting

By default, commands will not be highlighted within the `<style>` and `<script>` tags. Additional tags can be added to this list:

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

> **Note:** The excluded tag check goes up the whole tag nesting chain, which means that commands nested in excluded tags (even when inside other tags within the excluded tag) will not be highlighted either.

> **Warning:** The parser assumes that the HTML is valid. The tag nesting check does not handle nesting errors.

##### Loading the required styles

For the highlighting to work, the according CSS styles need to be loaded in the target page.

**Including the stylesheet:**

Ensure that the stylesheet file `css/highlight.css` of the package is loaded:

```html
<link rel="stylesheet" media="all" src="/vendor/mistralys/mailcode/css/highlight.css">
```

**Using the Styler utility:**

The Styler utility class has a number of methods all around the CSS:

```php
use Mailcode\Mailcode;

$styler = Mailcode::create()->createStyler();

// Get the raw CSS code (for compiled stylesheets)
$css = $styler->getCSS();

// Get the CSS in a <style> tag (for inline use)
$styleTag = $styler->getStyleTag();

// Get the absolute path to the stylesheet file
$path = $styler->getStylesheetPath();

// Get the <link> tag (provide the vendor folder URL)
$linkTag = $styler->getStylesheetTag('/url/to/vendor/folder');

// Get the stylesheet URL (provide the vendor folder URL)
$stylesheetURL = $styler->getStylesheetURL('/url/to/vendor/folder');
```

#### Highlighting variables in the final document

The "MarkVariables" highlighter allows highlighting all variable type commands, even once they have been processed by the mail preprocessor. This is handy when testing to quickly identify all places in an HTML document where variables are used.

```php
use Mailcode\Mailcode;

$htmlString = '(HTML with Mailcode commands here)';

$safeguard = Mailcode::create()->createSafeguard($htmlString);
$formatting = $safeguard->createFormatting($safeguard->makeSafe());

// Get the formatter instance
$formatter = $formatting->formatWithMarkedVariables();
```

Like the syntax highlighter, this will only highlight variables in valid contexts.

> **Note:** This can be combined with any of the other formatters, like the syntax highlighter.

**Load styles via style tag:**

```php
$styles = $formatter->getStyleTag();
```

**Integrate styles inline** (for HTML mailings where styles cannot be easily injected):

```php
$formatter->makeInline();
```

### Translation to other syntaxes

The translator class makes it easy to convert documents with mailcode to other syntaxes, like the bundled Apache Velocity converter.

#### Translating whole strings

```php
use Mailcode\Mailcode;

$string = '(Text with Mailcode commands here)';

// create the safeguarder instance for the subject string
$safeguard = Mailcode::create()->createSafeguard($string);

// create the translator
$apache = Mailcode::create()->createTranslator()->createApacheVelocity();

// convert all commands in the safeguarded string
$convertedString = $apache->translateSafeguard($safeguard);
```

#### Translating single commands

```php
use Mailcode\Mailcode;
use Mailcode\Mailcode_Factory;

// create the translator
$apache = Mailcode::create()->createTranslator()->createApacheVelocity();

// create a command
$command = Mailcode_Factory::set()->var('VAR.NAME', '8');

// convert it to an apache velocity command string
$apacheString = $apache->translateCommand($command);
```

#### Translate to: Apache Velocity

See [translate-apache-velocity.md](translate-apache-velocity.md).

#### Translate to: Hubspot HubL

See [translate-hubl.md](translate-hubl.md).

### Browser-enabled tools

In the subfolder `tools` are a few utilities meant to be used in a browser. To use these, run a `composer install` in the package's folder, and point your browser there.

- **Syntax translator:** Translate a document with Mailcode commands to a supported syntax.
- **Syntax highlighter:** Syntax highlighting of a document with Mailcode commands.
- **Phone countries extractor:** Extracts a country list for the `showphone` command.
