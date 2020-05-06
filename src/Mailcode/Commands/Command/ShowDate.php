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
class Mailcode_Commands_Command_ShowDate extends Mailcode_Commands_Command_ShowVariable
{
    const VALIDATION_NOT_A_FORMAT_STRING = 55401;
    
   /**
    * The date format string.
    * @var string
    */
    private $formatString = "Y/m/d";
    
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
    
    protected function init() : void
    {
        $this->formatInfo = Mailcode_Factory::createDateInfo();
        $this->formatString = $this->formatInfo->getDefaultFormat();
        
        parent::init();
    }
    
    protected function validateSyntax_check_format() : void
    {
         $token = $this->params->getInfo()->getTokenByIndex(1);
         
         // no format specified? Use the default one.
         if($token === null)
         {
             return;
         }
         
         if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
         {
             $format = $token->getText();
             
             $result = $this->formatInfo->validateFormat($format);
             
             if($result->isValid())
             {
                $this->formatString = $format;
             }
             else
             {
                 $this->validationResult->makeError(
                     $result->getErrorMessage(), 
                     $result->getCode()
                 );
             }
             
             return;
         }
         
         $this->validationResult->makeError(
            t('The second parameter must be a date format string.'),
            self::VALIDATION_NOT_A_FORMAT_STRING
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
    
    protected function getValidations() : array
    {
        $validations = parent::getValidations();
        $validations[] = 'check_format';
        
        return $validations;
    }
}
