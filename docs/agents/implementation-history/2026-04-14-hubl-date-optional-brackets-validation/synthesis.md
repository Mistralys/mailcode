## Synthesis

### Completion Status
- Date: 2026-04-14
- Status: COMPLETE
- Completed by: Standalone Developer Agent

### Implementation Summary
- Added `Mailcode_Date_FormatInfo::validateJavaFormat()` static method that rejects Java `DateTimeFormatter` optional-section brackets (`[` and `]`) from format strings. Returns an `OperationResult` with error code `55803` when brackets are found.
- Added error code constant `ERROR_INTERNAL_FORMAT_CONTAINS_OPTIONAL_BRACKETS = 55503` to `ApacheVelocity\ShowDateTranslation`.
- Modified `ShowDateTranslation::getInternalFormat()` to call the new validation after resolving the internal format value. Throws `Mailcode_Translator_Exception` if brackets are detected.
- Added 6 new test methods covering: bracket rejection during translation, clean format acceptance, and direct `validateJavaFormat()` unit tests (open bracket only, close bracket only, both brackets, clean string).

### Documentation Updates
- `docs/user-guide/translate-apache-velocity.md` — Added note after the `internal_format` usage example warning that optional-section brackets are not supported and will throw an exception.
- `docs/agents/project-manifest/constraints.md` — Added new "Date Translation Constraints" section documenting the three validation layers (PHP format whitelist, Java internal format bracket check, output format character mapping).
- `docs/agents/project-manifest/api-surface.md` — Added full `Mailcode_Date_FormatInfo` class section with all public method signatures and constants, including the new `validateJavaFormat()` method.

### Verification Summary
- Tests run: `composer test-filter -- ShowDateTests` (25 tests, 139 assertions — PASS), `composer test` (532 tests, 2349 assertions — PASS)
- Static analysis run: `composer analyze` (PHPStan level 9, 530 files)
- Result: All tests pass, no PHPStan errors.

### Code Insights
- [low] (improvement) `src/Mailcode/Date/FormatInfo.php`: The `validateJavaFormat()` method creates a `new self()` instance solely to satisfy `OperationResult`'s constructor requirement for a source object. This is a minor allocation; an alternative would be to use `self::getInstance()` to reuse the singleton, but the current approach avoids the side effect of initializing the character table when only Java format validation is needed.
- [low] (convention) `src/Mailcode/Translator/Syntax/ApacheVelocity/ShowDateTranslation.php`: The `getInternalFormat()` method is public but is only called internally by `translate()`. The `Mailcode_Translator_Command_ShowDate` interface may require it to be public; if not, it could be made private for better encapsulation.
- [low] (debt) `tests/testsuites/Translator/Velocity/ShowDateTests.php`: The PHPUnit deprecation warning (1 deprecation across the full suite) is pre-existing and unrelated to this change.

### Additional Comments
- If HubL ever introduces `internal_format` support, it should call `Mailcode_Date_FormatInfo::validateJavaFormat()` at the same point — the validation is already centralized for reuse.
- The `DEFAULT_INTERNAL_FORMAT` constant was confirmed to contain no brackets and passes validation successfully.
