# Translate to: Hubspot HubL

## Requirements

No special requirements needed for the current implementation.

## Supported commands

The HubL syntax is not fully implemented in the translation layer,
but a number of commands are available.

### Fully supported

- `{showvar}`
- `{showencoded}`
- `{showurl}`
- `{setvar}`
- `{shownumber}`
- `{showphone}`
- `{comment}`
- `{code}`
- `{mono}`
- `{else}`
- `{end}`
- `{for}` / `{end}` _(see notes below)_
- `{showdate}` _(see notes below)_

**{showdate} notes:**
- `{showdate: $VAR "d/m/Y"}` → `{{ var|format_datetime("dd/MM/yyyy") }}`
- PHP date format characters are converted to LDML equivalents (e.g. `Y`→`yyyy`, `m`→`MM`, `d`→`dd`, `H`→`HH`, `i`→`mm`, `s`→`ss`).
- Timezone string literal: `{showdate: $VAR "format" "Europe/Berlin"}` → `format_datetime("ldml", "Europe/Berlin")`
- Timezone variable: `{showdate: $VAR "format" $TZ}` → `format_datetime("ldml", "", tz)`
- No-variable case (`{showdate}`): uses HubL’s built-in `local_dt` variable as the source.
- URL encoding is applied correctly to the inner Jinja expression before the `{{ }}` wrapper.- **`internal_format` translation parameter (date string support):** When a Java/SimpleDateFormat pattern is provided via `setTranslationParam('internal_format', 'dd/MM/yyyy')`, the output wraps the expression in a Jinja2 `is string` condition to handle both HubL date objects and raw date strings:
  ```
  {% if var is string %}{{ var|strtotime("dd/MM/yyyy")|format_datetime("dd/MM/yyyy") }}{% else %}{{ var|format_datetime("dd/MM/yyyy") }}{% endif %}
  ```
  Without `internal_format`, only the object path is emitted (no conditional wrapper).
**{for} notes:**
- `{for: $RECORD in: $SOURCE}` → `{% for record in source %}`
- `break_at: N` (numeric) → `{% for record in source[:N] %}` (Jinja2 slice)
- `break_at: $LIMIT` (variable) → `{% for record in source[:limit] %}`
- All variable names are lowercased in the output.
- Note: the `{break}` command inside a `{for}` block is **not** supported (see below).

### IF / ElseIf commands (partial support)

The `{if}` and `{elseif}` commands support the following sub-types:

| Sub-type | Supported |
|---|---|
| Variable comparison | Yes |
| Empty / Not empty | Yes |
| Bigger than | Yes |
| Smaller than | Yes |
| Equals number | Yes |
| Generic (freeform) | Yes |
| Contains / Not contains | Yes |
| List contains / List not contains | No |
| List equals | No |
| List begins with / List ends with | No |
| Begins with / Ends with | Yes |

**Begins with / Ends with notes:**
- `{if begins-with: $VAR "pre"}` → `{% if var is string_startingwith "pre" %}`
- `{if ends-with: $VAR "suffix"}` → `{% if var[-N:] == "suffix" %}` where N = `mb_strlen` of the term.
- The `insensitive:` flag appends `|lower` to the variable and lowercases the search term.

**Contains / Not contains notes:**
- `{if contains: $VAR "term"}` → `{% if "term" in var %}`
- `{if not-contains: $VAR "term"}` → `{% if "term" not in var %}`
- Multiple search terms are joined with `or` (contains) or `and` (not-contains).
- The `insensitive:` flag appends `|lower` to the variable and lowercases the search term.
- List-contains sub-types (`list-contains`, `list-not-contains`) are not supported and produce the not-implemented stub.

> Unsupported IF sub-types are replaced by a HubL comment indicating
> that the command is not fully implemented.

### Not supported

The following commands are replaced by a HubL comment explaining
that they are not supported:

- `{break}`
- `{showsnippet}` — Requires a server-side dictionary infrastructure (DictionaryTool) to resolve snippet names to their text content at send time. This infrastructure does not exist in HubL. The command is replaced by a HubL comment in the output.

Each unsupported command produces a comment in this format:

```
{# !<command> is not supported in HubL! #}
```

**Examples:**

| Mailcode command | HubL output |
|---|---|
| `{break}` | `{# !break is not supported in HubL! #}` |
| `{showsnippet: "name"}` | `{# !showsnippet is not supported in HubL! #}` |

> **Implementation note:** The unsupported-commands list is declared via `HubLSyntax::getUnsupportedCommands()`
> (an override of `BaseSyntax::getUnsupportedCommands()`). The translation guard fires before any class
> resolution, so no exception is thrown and the stub translation classes for these commands are
> intentionally retained but bypassed at runtime.

## Variable name handling

In HubL, the typical naming convention is to use lowercase variable
names. The translation layer will convert all variable names to lowercase.
