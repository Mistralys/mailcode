<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowSnippet} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowSnippet
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: show a variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowSnippet extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Standalone
{
    const ERROR_NO_VARIABLE_AVAILABLE = 51901;
    
    const VALIDATION_VARIABLE_MISSING = 52001;
    
   /**
    * @var Mailcode_Variables_Variable|NULL
    */
    protected $variable;
    
    public function getName() : string
    {
        return 'showsnippet';
    }
    
    public function getLabel() : string
    {
        return t('Show text snippet');
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
    
   /**
    * Retrieves the variable to show, provided the command
    * is valid.
    * 
    * @throws Mailcode_Exception
    * @return Mailcode_Variables_Variable
    */
    public function getVariable() : Mailcode_Variables_Variable
    {
        $this->validate();
        
        if($this->variable instanceof Mailcode_Variables_Variable)
        {
            return $this->variable;
        }
        
        throw new Mailcode_Exception(
            'No variable available.',
            'No variable is present at this time, or the validation failed.',
            self::ERROR_NO_VARIABLE_AVAILABLE
        );
    }
    
   /**
    * Retrieves the full name of the variable to show,
    * provided the command is valid.
    * 
    * @throws Mailcode_Exception
    * @return string
    */
    public function getVariableName() : string
    {
        return $this->getVariable()->getFullName();
    }

    protected function validateSyntax_require_variable() : void
    {
        $info = $this->params->getInfo();
        
        $var = $info->getVariableByIndex(0);
        
        if($var)
        {
            $this->variable = $var->getVariable();
            return;
        }
        
        $this->validationResult->makeError(
            t('No variable specified in the command.'),
            self::VALIDATION_VARIABLE_MISSING
        );
    }
    
    protected function getValidations() : array
    {
        return array('require_variable');
    }
    
    public function generatesContent() : bool
    {
        return true;
    }
}
