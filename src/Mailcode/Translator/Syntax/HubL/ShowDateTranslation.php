<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_ShowDate;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Translator_Command_ShowDate;
use Mailcode\Mailcode_Translator_Exception;
use Mailcode\Translator\Syntax\ApacheVelocity\ShowDateTranslation as ApacheVelocityShowDateTranslation;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowDate} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ShowDateTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_ShowDate
{
    public const ERROR_UNKNOWN_DATE_FORMAT_CHARACTER = 119001;

    public function translate(Mailcode_Commands_Command_ShowDate $command): string
    {
        $ldmlFormat = $this->convertToLDML($command->getFormatString());

        $varName = $command->hasVariable()
            ? $this->formatVariableName($command->getVariableName())
            : 'local_dt';

        $formatFilter = $this->buildFormatFilter($ldmlFormat, $command);
        $internalFormat = $this->resolveInternalFormat($command);

        if($internalFormat !== null)
        {
            $stringInner = sprintf(
                '%s|strtotime("%s")%s',
                $varName,
                $internalFormat,
                $formatFilter
            );
            $objectInner = $varName . $formatFilter;

            return sprintf(
                '{%% if %1$s is string %%}{{ %2$s }}{%% else %%}{{ %3$s }}{%% endif %%}',
                $varName,
                $this->renderEncodings($command, $stringInner),
                $this->renderEncodings($command, $objectInner)
            );
        }

        $inner = $varName . $formatFilter;

        return sprintf('{{ %s }}', $this->renderEncodings($command, $inner));
    }

    /**
     * Resolves the internal format from the command's translation parameters.
     * Returns `null` if no internal format is configured.
     *
     * @param Mailcode_Commands_Command_ShowDate $command
     * @return string|null The internal format string, or null if not set.
     */
    private function resolveInternalFormat(Mailcode_Commands_Command_ShowDate $command): ?string
    {
        $internalFormat = $command->getTranslationParam('internal_format');

        if(is_string($internalFormat) && $internalFormat !== '')
        {
            return $internalFormat;
        }

        return null;
    }

    /**
     * Builds the `|format_datetime(...)` filter portion of the HubL expression,
     * including timezone if applicable.
     *
     * @param string $ldmlFormat The LDML output format string.
     * @param Mailcode_Commands_Command_ShowDate $command
     * @return string The filter string, e.g. `|format_datetime("dd/MM/yyyy", "Europe/Paris")`.
     */
    private function buildFormatFilter(string $ldmlFormat, Mailcode_Commands_Command_ShowDate $command): string
    {
        $timezoneToken = $command->hasExplicitTimezone() ? $command->getTimezoneToken() : null;

        if($timezoneToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
        {
            return sprintf('|format_datetime("%s", "%s")', $ldmlFormat, $timezoneToken->getText());
        }

        if($timezoneToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
        {
            $tzVarName = $this->formatVariableName($timezoneToken->getVariable()->getFullName());
            return sprintf('|format_datetime("%s", %s)', $ldmlFormat, $tzVarName);
        }

        return sprintf('|format_datetime("%s")', $ldmlFormat);
    }

    /**
     * Converts a PHP date format string to LDML format using the Apache
     * Velocity character conversion table (shared between both syntaxes).
     *
     * @param string $formatString A PHP-compatible date format string.
     * @return string LDML format string suitable for HubL's format_datetime filter.
     * @throws Mailcode_Translator_Exception {@see self::ERROR_UNKNOWN_DATE_FORMAT_CHARACTER}
     */
    private function convertToLDML(string $formatString): string
    {
        $charTable = ApacheVelocityShowDateTranslation::$charTable;
        $chars = str_split($formatString);
        $result = array();

        foreach($chars as $char)
        {
            if(!isset($charTable[$char]))
            {
                throw new Mailcode_Translator_Exception(
                    'Unknown date format string character',
                    sprintf(
                        'No LDML translation available for PHP format character [%s].',
                        $char
                    ),
                    self::ERROR_UNKNOWN_DATE_FORMAT_CHARACTER
                );
            }

            $result[] = $charTable[$char];
        }

        return implode('', $result);
    }
}
