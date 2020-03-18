<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowVariable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowVariable
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
class Mailcode_Commands_Command_ShowVariable extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Standalone
{
    const ERROR_NO_VARIABLE_AVAILABLE = 49301;
    
    const VALIDATION_VARIABLE_MISSING = 48401;
    
   /**
    * @var Mailcode_Variables_Variable|NULL
    */
    protected $variable;
    
    public function getName() : string
    {
        return 'showvar';
    }
    
    public function getLabel() : string
    {
        return t('Show variable');
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
        $this->validate();
        
        if(isset($this->variable))
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

    protected function validateSyntax_require_variable() : void
    {
         $vars = $this->getVariables()->getGroupedByName();
         $amount = count($vars);
         
         if($amount === 1)
         {
             $this->variable = array_pop($vars);
             return;
         }
         
         $this->validationResult->makeError(
            t('Command has %1$s variables, %2$s expected.', $amount, 1),
            self::VALIDATION_VARIABLE_COUNT_MISMATCH
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
