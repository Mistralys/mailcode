@echo off

cls

set Level=9

echo -------------------------------------------------------
echo RUNNING PHPSTAN ANALYSIS @ LEVEL %Level%
echo -------------------------------------------------------

echo.

call ../../vendor/bin/phpstan analyse -c ./config.neon -l %Level% > ./result.txt

start "" "phpstan/result.txt"
