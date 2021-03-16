@echo off

cls 

echo ------------------------------------------------------------------
echo MAILCODE TESTSUITES
echo ------------------------------------------------------------------
echo.
echo Available suites:
echo.
echo - Collection (Commands collection)
echo - Commands (Validation of all commands)
echo - Factory (Instantiating all commands)
echo - Formatting (Safeguard formatting options)
echo - Highlighting (Highlighting commands)
echo - Isolation (Temporary tests)
echo - LogicKeywords (Logic connections via and: and or:)
echo - Mailcode (Global Mailcode class methods)
echo - Numbers (Number formatting information)
echo - Parser (Parsing strings and HTML)
echo - PreProcessor (Converting commands to markup)
echo - StringContainer (The StringContainer utility class)
echo - Translator (Translating to other languages)
echo - Validator (Parameters statement validator)
echo - Variables (Parsing variable names)
echo.
echo Run a specific suite with the command line parameter:
echo.
echo --testsuite Name
echo.
echo ------------------------------------------------------------------
echo.
echo.

call "vendor/bin/phpunit" %*

echo.
echo.
echo ALL DONE.

pause
