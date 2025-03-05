@echo off

cls

set /p Level=<level.txt

echo -------------------------------------------------------
echo RUNNING PHPSTAN ANALYSIS @ LEVEL %Level%
echo -------------------------------------------------------

echo.

call ../../vendor/bin/phpstan analyse -c ./config.neon -l %Level% > ./result.txt

start "" "phpstan/result.txt"
