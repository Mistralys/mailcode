<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_IfEmpty} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Traits_Commands_IfEmpty
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF NOTEMPTY statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Statement $params
 * @property \AppUtils\OperationResult $validationResult
 */
trait Mailcode_Traits_Commands_IfNotEmpty
{
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
    */
    protected $variableToken;

    protected function getValidations() : array
    {
        return array(
            'variable'
        );
    }
    
    protected function validateSyntax_variable() : void
    {
        $info = $this->params->getInfo();
        
        $variable = $info->getVariableByIndex(0);
        
        if($variable !== null)
        {
            $this->variableToken = $variable;
            return;
        }

        $this->validationResult->makeError(
            t('No variable specified in the command.'),
            Mailcode_Commands_IfBase::VALIDATION_VARIABLE_MISSING
        );
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
            Mailcode_Commands_IfBase::ERROR_NO_VARIABLE_AVAILABLE
        );
    }
}
