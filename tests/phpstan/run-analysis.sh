#!/usr/bin/env sh

MemoryLimit=900M
AnalysisLevel=$(cat ./level.txt)
OutputFile=result.txt
ConfigFile=config.neon
BinFolder=../../vendor/bin

echo "-------------------------------------------------------"
echo "RUNNING PHPSTAN AT LEVEL $AnalysisLevel"
echo "-------------------------------------------------------"
echo ""

$BinFolder/phpstan analyse -l "$AnalysisLevel" -c $ConfigFile --memory-limit=$MemoryLimit > $OutputFile

echo ""
echo "Output saved to file:"
echo "$OutputFile"

code "$OutputFile"
