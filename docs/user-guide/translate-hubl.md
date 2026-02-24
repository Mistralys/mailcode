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
- `{showsnippet}`
- `{comment}`
- `{code}`
- `{mono}`
- `{else}`
- `{end}`

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
| Contains / Not contains | No |
| List contains / List not contains | No |
| List equals | No |
| List begins with / List ends with | No |
| Begins with / Ends with | No |

> Unsupported IF sub-types are replaced by a HubL comment indicating
> that the command is not fully implemented.

### Not supported

The following commands are replaced by a HubL comment explaining
that they are not supported:

- `{for}`
- `{break}`
- `{showdate}`

## Variable name handling

In HubL, the typical naming convention is to use lowercase variable
names. The translation layer will convert all variable names to lowercase.
