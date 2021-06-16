<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_Operand} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_Operand
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Command validation drop-in: checks for the presence
 * of an operand token (any operand sign). Will accept 
 * the first operand token it finds.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Interfaces_Commands_Validation_Operand
 *
 * @property OperationResult $validationResult
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait Mailcode_Traits_Commands_Validation_Operand
{
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Token_Operand|NULL
    */
    protected $operandToken;
    
   /**
    * @return string[]
    */
    abstract protected function getAllowedOperands() : array;
    
    protected function validateSyntax_operand() : void
    {
        $var = $this->validator->createOperand();
        
        if($var->isValid())
        {
            if(in_array($var->getSign(), $this->getAllowedOperands()))
            {
                $this->operandToken = $var->getToken();
            }
            else
            {
                $this->validationResult->makeError(
                    t('Invalid operand %1$s.', $var->getSign()).' '.
                    t('The following operands may be used in this command:').' '.
                    '<code>'.implode('</code>, <code>', $this->getAllowedOperands()).'</code>',
                    Mailcode_Commands_CommonConstants::VALIDATION_INVALID_OPERAND
                );
            }
        }
        else
        {
            $this->validationResult->makeError(
                t('No operand has been specified.'),
                Mailcode_Commands_CommonConstants::VALIDATION_OPERAND_MISSING
            );
        }
    }
    
    public function getOperand() : Mailcode_Parser_Statement_Tokenizer_Token_Operand
    {
        if($this->operandToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Operand)
        {
            return $this->operandToken;
        }
        
        throw new Mailcode_Exception(
            'No operand available',
            null,
            Mailcode_Commands_CommonConstants::ERROR_NO_OPERAND_AVAILABLE
        );
    }
    
   /**
    * Retrieves the operand sign.
    *
    * @return string
    */
    public function getSign() : string
    {
        return $this->getOperand()->getSign();
    }
}
