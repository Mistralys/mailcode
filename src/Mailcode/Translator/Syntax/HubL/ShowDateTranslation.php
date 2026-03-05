<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Interfaces\Commands\Validation\TimezoneInterface;
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

        // Inspect the command params directly to detect whether a timezone was
        // explicitly provided. We avoid calling getTimezoneToken() here because
        // that method creates a default timezone token on demand, which would
        // make every command appear to have an explicit timezone.
        $timezoneToken = null;
        $params = $command->getParams();

        if($params !== null)
        {
            $rawToken = $params->getInfo()->getTokenByParamName(TimezoneInterface::PARAMETER_NAME);

            if($rawToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
                || $rawToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
            {
                $timezoneToken = $rawToken;
            }
        }

        if($timezoneToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
        {
            $inner = sprintf(
                '%s|format_datetime("%s", "%s")',
                $varName,
                $ldmlFormat,
                $timezoneToken->getText()
            );
        }
        elseif($timezoneToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
        {
            $tzVarName = $this->formatVariableName($timezoneToken->getVariable()->getFullName());
            $inner = sprintf(
                '%s|format_datetime("%s", %s)',
                $varName,
                $ldmlFormat,
                $tzVarName
            );
        }
        else
        {
            $inner = sprintf('%s|format_datetime("%s")', $varName, $ldmlFormat);
        }

        return sprintf('{{ %s }}', $this->renderEncodings($command, $inner));
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
