<?php
/**
 * File containing the {@see Mailcode_Commands_Command_SetVariable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_SetVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: set a variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_SetVariable extends Mailcode_Commands_Command_Type_Standalone
{
    const VALIDATION_MISSING_EQUALS_SIGN = 48501;
    const VALIDATION_NOT_SINGLE_EQUALS_SIGN = 48502;
    const VALIDATION_EMPTY_ASSIGNMENT = 48503;
    const VALIDATION_VARIABLE_LEFT_UNRECOGNIZED = 48504;
    const VALIDATION_ASSIGNMENT_STATEMENT_INVALID = 48505;
    
   /**
    * @var string[]
    */
    protected $parts = array();
    
   /**
    * @var Mailcode_Variables_Variable
    */
    protected $leftVar;
    
   /**
    * @var Mailcode_Parser_Statement
    */
    protected $statement;
    
    public function getName() : string
    {
        return 'setvar';
    }
    
    public function getLabel() : string
    {
        return t('Set variable value');
    }
    
    public function supportsType(): bool
    {
        return false;
    }
    
    public function requiresParameters(): bool
    {
        return true;
    }
    
    protected function validateSyntax_equals_sign()
    {
        $amount = substr_count($this->paramsString, '=');
        
        if($amount === 1)
        {
            return;
        }
        
        if($amount < 1)
        {
            $this->validationResult->makeError(
                t('The quality operator (=) is missing.'),
                self::VALIDATION_MISSING_EQUALS_SIGN
            );
        }
        else
        {
            $this->validationResult->makeError(
                t('Only a single equality operator (=) should be used for variable assignment.'),
                self::VALIDATION_NOT_SINGLE_EQUALS_SIGN
            );
        }
    }
    
    protected function validateSyntax_split_parts()
    {
        $this->parts = \AppUtils\ConvertHelper::explodeTrim('=', $this->paramsString);
        
        if(count($this->parts) === 2) 
        {
            return;
        }
        
        $this->validationResult->makeError(
            t('Command has an empty assignment on either part of the equals sign.'),
            self::VALIDATION_EMPTY_ASSIGNMENT
        );
    }
    
    protected function validateSyntax_left()
    {
        // any variables we may find have already been validated.
        $vars = $this->mailcode->findVariables($this->parts[0])->getGroupedByName();
        
        if(count($vars) === 1)
        {
            $this->leftVar = array_shift($vars);
            return;
        }
        
        $this->validationResult->makeError(
            t('The name of the variable being set, %1$s, could not be recognized.', '"'.$this->parts[0].'"'),
            self::VALIDATION_VARIABLE_LEFT_UNRECOGNIZED
        );
    }
    
    protected function validateSyntax_right()
    {
        $this->statement = $this->mailcode->getParser()->createStatement($this->parts[1]);
        
        if($this->statement->isValid())
        {
            return;
        }
        
        $result = $this->statement->getValidationResult();
        
        $this->validationResult->makeError(
            t('The assignment statement is invalid:').' '.$result->getErrorMessage(),
            self::VALIDATION_ASSIGNMENT_STATEMENT_INVALID
        );
    }
    
    protected function getValidations() : array
    {
        return array(
            'equals_sign', 
            'split_parts',
            'left',
            'right'
        );
    }
    
    public function generatesContent() : bool
    {
        return true;
    }
}
