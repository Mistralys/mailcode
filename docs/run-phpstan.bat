@echo off

cls

set Level=9

echo -------------------------------------------------------
echo RUNNING PHPSTAN ANALYSIS @ LEVEL %Level%
echo -------------------------------------------------------

echo.

call ../vendor/bin/phpstan analyse -c ./config/phpstan.neon -l %Level% > phpstan/output.txt

start "" "phpstan/output.txt"
