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
class Mailcode_Commands_Command_SetVariable extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Standalone
{
    const ERROR_NO_VARIABLE_AVAILABLE = 49401;
    const ERROR_NO_VARIABLE_IN_ASSIGNMENT = 49403;
    
    const VALIDATION_NOT_ASSIGNMENT_STATEMENT = 48501;
    
   /**
    * @var Mailcode_Parser_Statement_Tokenizer_Type_Value
    */
    protected $value;
    
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
    
    public function getDefaultType() : string
    {
        return '';
    }
    
    public function requiresParameters(): bool
    {
        return true;
    }
    
    /**
     * Retrieves the variable to show.
     *
     * NOTE: Only available once the command has been
     * validated. Always use isValid() first.
     *
     * @throws Mailcode_Exception
     * @return Mailcode_Variables_Variable
     */
    public function getVariable() : Mailcode_Variables_Variable
    {
        $variable = $this->params->getInfo()->getVariableByIndex(0);
        
        if($variable)
        {
            return $variable->getVariable();
        }
        
        throw new Mailcode_Exception(
            'No variable at position #0 in statement.',
            'This signifies an error in the statement handling: a variable assignment should have a variable in the first token.',
            self::ERROR_NO_VARIABLE_IN_ASSIGNMENT
        );
    }
    
    /**
     * Retrieves the full name of the variable to show.
     *
     * NOTE: Only available once the command has been
     * validated. Always use isValid() first.
     *
     * @throws Mailcode_Exception
     * @return string
     */
    public function getVariableName() : string
    {
        return $this->getVariable()->getFullName();
    }
    
    protected function validateSyntax_assignment() : void
    {
        if($this->params->getInfo()->isVariableAssignment())
        {
            return;
        }
        
        $this->validationResult->makeError(
            t('Not a variable assignment.').' '.t('Is the equality sign (=) present?'),
            self::VALIDATION_NOT_ASSIGNMENT_STATEMENT
        );
    }
    
    protected function validateSyntax_value() : void
    {
        $info = $this->params->getInfo();
        
        $value = $info->getTokenByIndex(2);
        
        if($value instanceof Mailcode_Parser_Statement_Tokenizer_Type_Value)
        {
            $this->value = $value;
            return;
        }
    }
    
    protected function getValidations() : array
    {
        return array(
            'assignment',
            'value'
        );
    }
    
    public function getValue() : Mailcode_Parser_Statement_Tokenizer_Type_Value
    {
        return $this->value;
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
}
