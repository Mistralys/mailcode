<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowDate} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowDate
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
class Mailcode_Commands_Command_ShowDate extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Standalone
{
    use Mailcode_Traits_Commands_Validation_Variable;
    use Mailcode_Traits_Commands_Validation_URLEncode;

    const VALIDATION_NOT_A_FORMAT_STRING = 55401;
    
   /**
    * The date format string.
    * @var string
    */
    private $formatString;
    
   /**
    * @var Mailcode_Date_FormatInfo
    */
    private $formatInfo;

    public function getName() : string
    {
        return 'showdate';
    }

    public function getLabel() : string
    {
        return t('Show date variable');
    }

    public function supportsType(): bool
    {
        return false;
    }

    public function getDefaultType() : string
    {
        return '';
    }

    public function requiresParameters(): bool
    {
        return true;
    }

    public function supportsLogicKeywords() : bool
    {
        return false;
    }

    public function supportsURLEncoding() : bool
    {
        return true;
    }

    public function generatesContent() : bool
    {
        return true;
    }

    protected function getValidations() : array
    {
        return array(
            'variable',
            'check_format',
            'urlencode'
        );
    }

    protected function init() : void
    {
        $this->formatInfo = Mailcode_Factory::createDateInfo();
        $this->formatString = $this->formatInfo->getDefaultFormat();
        
        parent::init();
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
        $result = $this->formatInfo->validateFormat($format);

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
    * Retrieves the format string used to format the date.
    * 
    * @return string A PHP compatible date format string.
    */
    public function getFormatString() : string
    {
        return $this->formatString;
    }
}

