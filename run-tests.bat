@echo off

cls 

echo ------------------------------------------------------------------
echo MAILCODE TESTSUITES
echo ------------------------------------------------------------------
echo.
echo Available suites:
echo.
echo - Commands
echo - Factory
echo - Highlighting
echo - LogicKeywords
echo - Mailcode
echo - Parser
echo - Translator
echo - Validator
echo - Variables
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
