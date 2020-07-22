<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_Variable} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command validation drop-in: checks for the presence
 * of a variable name. Will accept the first variable 
 * it finds.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_Validation_Variable
{
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
    */
    protected $variableToken;
    
    protected function validateSyntax_variable() : void
    {
        $var = $this->validator->createVariable();
        
        if($var->isValid())
        {
            $this->variableToken = $var->getToken();
        }
        else
        {
            $this->validationResult->makeError(
                t('No variable has been specified.'),
                Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            );
        }
    }
    
   /**
    * Retrieves the variable being compared.
    *
    * @return Mailcode_Variables_Variable
    */
    public function getVariable() : Mailcode_Variables_Variable
    {
        if($this->variableToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
        {
            return $this->variableToken->getVariable();
        }
        
        throw new Mailcode_Exception(
            'No variable available',
            null,
            Mailcode_Commands_CommonConstants::ERROR_NO_VARIABLE_AVAILABLE
        );
    }
}
