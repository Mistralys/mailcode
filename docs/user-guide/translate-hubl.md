# Translate to: Hubspot HubL

## Requirements

No special requirements needed for the current implementation.

## Supported commands

The HubL syntax is currently not fully implemented in the translation layer.

These are the commands that can be used:

- `{showvar}`
- `{showencoded}`
- `{showurl}`
- `{setvar}`

> All other commands are replaced by a comment explaining that the command 
> is not supported.

## Variable name handling

In HubL, the typical naming convention is to use lowercase variable
names. The translation layer will convert all variable names to lowercase.
