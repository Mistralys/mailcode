<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If_Variable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If_Variable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * IF for variable comparisons.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Statement $params
 * @property \AppUtils\OperationResult $validationResult
 */
trait Mailcode_Traits_Commands_IfVariable
{
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
    */
    protected $variableToken;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Operand|NULL
    */
    protected $comparisonToken;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Type_Value|NULL
    */
    protected $valueToken;
    
    protected function getValidations() : array
    {
        return array(
            'variable',
            'operand',
            'value'
        );
    }
    
    protected function validateSyntax_variable() : void
    {
        $info = $this->params->getInfo();
        
        $var = $info->getVariableByIndex(0);
        
        if($var !== null)
        {
            $this->variableToken = $var;
            return;
        }
        
        $this->validationResult->makeError(
            t('No variable specified in the command.'),
            Mailcode_Commands_IfBase::VALIDATION_VARIABLE_MISSING
        );
    }
    
    protected function validateSyntax_operand() : void
    {
        $info = $this->params->getInfo();
        
        $operand = $info->getOperandByIndex(1);
        
        if($operand === null)
        {
            $this->validationResult->makeError(
                t('No operand sign after the variable name.'),
                Mailcode_Commands_IfBase::VALIDATION_OPERAND_MISSING
            );
            
            return;
        }
        
        if(!$operand->isComparator())
        {
            $this->validationResult->makeError(
                t('The operand sign is not a comparison operand.'),
                Mailcode_Commands_IfBase::VALIDATION_OPERAND_NOT_COMPARISON
            );
            
            return;
        }
        
        $this->comparisonToken = $operand;
    }
    
    protected function validateSyntax_value() : void
    {
        $info = $this->params->getInfo();
        
        $token = $info->getTokenByIndex(2);
        
        if($token === null)
        {
            $this->validationResult->makeError(
                t('Nothing found after the comparison operand.'),
                Mailcode_Commands_IfBase::VALIDATION_NOTHING_AFTER_OPERAND
            );
            
            return;
        }
        
        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Type_Value)
        {
            $this->valueToken = $token;
            
            return;
        }
        
        $this->validationResult->makeError(
            t('Not a valid value to compare to.'),
            Mailcode_Commands_IfBase::VALIDATION_INVALID_COMPARISON_TOKEN
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
    
   /**
    * Retrieves the comparison operator sign.
    * 
    * @throws Mailcode_Exception
    * @return string The comparison string, e.g. "==", "!=", etc.
    */
    public function getComparator() : string
    {
        if($this->comparisonToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Operand)
        {
            return $this->comparisonToken->getOperand();
        }
        
        throw new Mailcode_Exception(
            'No comparator available',
            null,
            Mailcode_Commands_IfBase::ERROR_NO_COMPARATOR_AVAILABLE
        );
    }
    
   /**
    * Retrieves the unquoted value 
    * @return string
    */
    public function getValue() : string
    {
        if($this->valueToken instanceof Mailcode_Parser_Statement_Tokenizer_Type_Value)
        {
            return $this->valueToken->getValue();
        }
        
        throw new Mailcode_Exception(
            'No value available',
            null,
            Mailcode_Commands_IfBase::ERROR_NO_VALUE_AVAILABLE
        );
    }
}
