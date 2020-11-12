<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_Value} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_Value
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of a value type token (string literal, number...). 
 * Will accept the first value type token it finds.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_Validation_Value
{
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_ValueInterface|NULL
    */
    protected $valueToken;
    
    protected function validateSyntax_value() : void
    {
        $var = $this->validator->createValue();
        
        if($var->isValid())
        {
            $this->valueToken = $var->getToken();
        }
        else
        {
            $this->validationResult->makeError(
                t('No value has been specified.'),
                Mailcode_Commands_CommonConstants::VALIDATION_VALUE_MISSING
            );
        }
    }
    
   /**
    * Retrieves the variable being compared.
    *
    * @return string
    */
    public function getValue() : string
    {
        if($this->valueToken instanceof Mailcode_Parser_Statement_Tokenizer_ValueInterface)
        {
            return $this->valueToken->getValue();
        }
        
        throw new Mailcode_Exception(
            'No value available',
            null,
            Mailcode_Commands_CommonConstants::ERROR_NO_VALUE_AVAILABLE
        );
    }
}
