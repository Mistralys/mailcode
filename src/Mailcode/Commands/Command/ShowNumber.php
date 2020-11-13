<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowNumber} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowNumber
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: show a date variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowNumber extends Mailcode_Commands_ShowBase
{
    const VALIDATION_NOT_A_FORMAT_STRING = 72201;
    const VALIDATION_PADDING_SEPARATOR_OVERFLOW = 72202;
    const VALIDATION_INVALID_FORMAT_NUMBER = 72203;
    const VALIDATION_INVALID_THOUSANDS_SEPARATOR = 72204;
    const VALIDATION_INVALID_DECIMALS_NO_DECIMALS = 72205;
    const VALIDATION_INVALID_CHARACTERS = 72206;
    const VALIDATION_PADDING_INVALID_CHARS = 72207;
    const VALIDATION_INVALID_DECIMALS_CHARS = 72208;
    const VALIDATION_INVALID_DECIMAL_SEPARATOR = 72209;
    const VALIDATION_SEPARATORS_SAME_CHARACTER = 72210;
    
   /**
    * The default number format string.
    * @var string
    */
    private $formatString = Mailcode_Number_Info::DEFAULT_FORMAT;
    
    public function getName() : string
    {
        return 'shownumber';
    }

    public function getLabel() : string
    {
        return t('Show number variable');
    }

    protected function getValidations() : array
    {
        return array(
            'variable',
            'check_format',
            'urlencode'
        );
    }

    protected function validateSyntax_check_format() : void
    {
         $tokens = $this->params->getInfo()->getStringLiterals();
         
         // no format specified? Use the default one.
         if(empty($tokens))
         {
             return;
         }

         $token = array_pop($tokens);
         $this->parseFormatString($token->getText());
    }

    private function parseFormatString(string $format) : void
    {
        $result = new Mailcode_Number_Info($format);

        if($result->isValid())
        {
            $this->formatString = $format;
            return;
        }

        $this->validationResult->makeError(
            $result->getErrorMessage(),
            $result->getCode()
        );
    }
    
   /**
    * Retrieves the format string used to format the number.
    * 
    * @return string
    */
    public function getFormatString() : string
    {
        return $this->formatString;
    }

    /**
     * Retrieves information on how the number should be formatted.
     *
     * @return Mailcode_Number_Info
     */
    public function getFormatInfo() : Mailcode_Number_Info
    {
        return new Mailcode_Number_Info($this->getFormatString());
    }
}

