# Key Data Flows

## 1. Parse a String for Commands

```
User calls Mailcode::create()->parseString($text)
  → Mailcode::getParser() → Mailcode_Parser
    → PreParser strips protected content ({code}...{code})
    → Regex matches all command patterns in the string
    → Each match → Mailcode_Parser_Match
      → Mailcode_Commands resolves match to a command class
      → Mailcode_Factory_Instantiator creates command instance
        → Mailcode_Parser_Statement tokenizes parameters
          → Mailcode_Parser_Statement_Tokenizer → typed tokens
        → Mailcode_Parser_Statement_Validator validates against command rules
        → Validation traits fire (Variable, SearchTerm, Operand, etc.)
    → Valid command → added to Mailcode_Collection
    → Invalid command → Mailcode_Collection_Error
  → Collection finalized → NestingValidator checks open/close pairing
  → Returns ParseResult (wraps Collection + PreParser)
  → Mailcode::parseString() returns Mailcode_Collection
```

## 2. Safeguard Text During Processing

```
User calls Mailcode::create()->createSafeguard($htmlContent)
  → Mailcode_Parser_Safeguard created
    → Internally parses $htmlContent via Mailcode_Parser
    → Builds PlaceholderCollection (command ↔ numeric placeholder mapping)

User calls $safeguard->makeSafe()
  → All commands replaced with numeric placeholders (e.g., 9990000000001999)
  → Returns safe string (no Mailcode syntax)

User processes the safe string freely (HTML filtering, encoding, etc.)

User calls $safeguard->makeWhole($safeString)
  → Placeholders replaced back with original command text
  → Returns restored string with commands intact
```

## 3. Translate Commands to Target Syntax

```
User calls Mailcode::create()->createTranslator()
  → Mailcode_Translator::create() (singleton)
    → ClassCache discovers syntax classes in Translator/Syntax/
    → Instantiates ApacheVelocitySyntax and HubLSyntax

User calls $translator->createApacheVelocity()
  → Returns ApacheVelocitySyntax instance

User calls $syntax->translateSafeguard($safeguard)
  → Iterates over all placeholders in the safeguard
  → For each command:
    → Looks up per-command translation class (e.g., ShowVariableTranslation)
    → Translation class renders the command in Velocity syntax
      (e.g., {showvar: $CUSTOMER.NAME} → ${CUSTOMER_NAME})
  → Returns translated string with all commands converted
```

## 4. Create Commands Programmatically

```
User calls Mailcode_Factory::show()->var("CUSTOMER.NAME")
  → Mailcode_Factory::getSets() → Mailcode_Factory_CommandSets (lazy)
  → CommandSets::show() → Mailcode_Factory_CommandSets_Set_Show
  → Set_Show::var($name)
    → Builds parameter string
    → Mailcode_Factory_Instantiator creates ShowVariable command
    → Command validates its parameters
  → Returns validated Mailcode_Commands_Command_ShowVariable
```

## 5. Pre-Process Commands (e.g., {mono})

```
User calls Mailcode::create()->createPreProcessor($subject)
  → Mailcode_PreProcessor created
    → Internally creates a safeguard (partial mode)

User calls $preProcessor->render()
  → Safeguard creates formatting instance
  → PreProcessing formatter applied
    → {mono}...{mono} blocks → <code>...</code> HTML
  → Returns processed string
```

## 6. Highlight Commands for HTML Display

```
User gets a safeguard → $safeguard->makeSafe()
  → Creates formatting: $safeguard->createFormatting($safeString)
  → Adds HTMLHighlighting replacer
  → Applies formatting
  → Each command placeholder replaced with syntax-highlighted HTML
  → Styled via CSS from Mailcode_Styler::getCSS()
```

## 7. Find Variables in Text

```
User calls Mailcode::create()->findVariables($text)
  → Mailcode_Variables::parseString($text)
    → Regex matches all $PATH.NAME patterns
    → Each match → Mailcode_Variables_Variable instance
    → All variables collected in Mailcode_Variables_Collection_Regular
  → Returns collection
```

## 8. Compound Conditions (Logic Keywords)

```
User writes: {if contains: $FOO "bar" and: contains: $BAZ "qux"}
  → Parser detects the {if} command
  → LogicKeywords parser splits on `and:` / `or:` connectors
  → Creates sub-conditions, each validated independently
  → Translator renders compound output per target syntax
```
