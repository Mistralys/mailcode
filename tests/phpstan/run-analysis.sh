#!/usr/bin/env sh

MemoryLimit=900M
AnalysisLevel=9
OutputFile=result.txt
ConfigFile=config.neon
BinFolder=../../vendor/bin

echo "-------------------------------------------------------"
echo "RUNNING PHPSTAN AT LEVEL $AnalysisLevel"
echo "-------------------------------------------------------"
echo ""

$BinFolder/phpstan analyse -l $AnalysisLevel -c $ConfigFile --memory-limit=$MemoryLimit > $OutputFile

echo ""
echo "Output saved to file:"
echo "$OutputFile"
