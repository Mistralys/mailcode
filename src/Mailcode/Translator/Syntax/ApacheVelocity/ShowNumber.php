<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowNumber} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator_Syntax_ApacheVelocity_ShowNumber
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;

/**
 * Translates the "ShowNumber" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ShowNumber extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowNumber
{
    public function translate(Mailcode_Commands_Command_ShowNumber $command): string
    {
        $template = <<<'EOD'
number.format('%s', $number.toNumber('#.####', $%s.replace(',', '.'), 'en_US'), '%s')
EOD;

        $varName = ltrim($command->getVariableName(), '$');
        $formatInfo = $command->getFormatInfo();
        $javaFormat = $this->translateFormat($formatInfo);
        $locale = $this->getLocale($formatInfo);

        $statement = sprintf(
            $template,
            $javaFormat,
            $varName,
            $locale
        );

        if($command->isURLEncoded())
        {
            return sprintf(
                '${esc.url($%s)}',
                $statement
            );
        }

        return sprintf(
            '${%s}',
            $statement
        );
    }

    /**
     * en_US: 100,000.00
     * de_DE: 100.000,00
     * fr_FR: 100 000,00
     *
     * @var array<string,string>
     */
    protected $typeLocales = array(
        // No separators at all
        '__' => 'en_US',

        // International variants
        ',.' => 'en_US',
        '.,' => 'de_DE',
        ' ,' => 'fr_FR',

        // No thousands separator
        '_.' => 'en_US',
        '_,' => 'de_DE',

        // Do decimals separator
        ',_' => 'en_US',
        '._' => 'de_DE',
        ' _' => 'fr_FR'
    );

    private function getLocale(Mailcode_Number_Info $format) : string
    {
        $th = $format->getThousandsSeparator();
        $dc = $format->getDecimalsSeparator();

        if ($th === '') {
            $th = '_';
        }
        if ($dc === '') {
            $dc = '_';
        }

        $type = $th . $dc;

        if (isset($this->typeLocales[$type])) {
            return $this->typeLocales[$type];
        }

        return 'en_US';
    }

    private function translateFormat(Mailcode_Number_Info $format) : string
    {
        $result = '#';

        if($format->hasThousandsSeparator())
        {
            $result = '#,###';
        }

        if($format->hasDecimals())
        {
            $result .= '.'.str_repeat('#', $format->getDecimals());
        }

        return $result;
    }
}
