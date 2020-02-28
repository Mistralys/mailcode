<?php
/**
 * File containing the {@see Mailcode_Commands_Command_If} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_If
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening IF statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_If extends Mailcode_Commands_Command_Type_Opening
{
    const VALIDATION_VARIABLE_COUNT_MISMATCH = 49201;
    
   /**
    * @var Mailcode_Variables_Variable
    */
    protected $variable;
    
    public function getName() : string
    {
        return 'if';
    }
    
    public function getLabel() : string
    {
        return t('IF condition');
    }
    
    public function supportsType(): bool
    {
        return true;
    }
    
    public function requiresParameters(): bool
    {
        return true;
    }
    
    public function isCommand() : bool
    {
        return $this->type === 'command' || empty($this->type); 
    }
    
    public function isVariable() : bool
    {
        return $this->type === 'variable';
    }
    
   /**
    * Available only if the command is of type "variable".
    * 
    * @return Mailcode_Variables_Variable|NULL
    */
    public function getVariable() : ?Mailcode_Variables_Variable
    {
        if(isset($this->variable))
        {
            return $this->variable;
        }
        
        return null;
    }
    
    protected function validateSyntax_require_variable() : void
    {
        $amount = $this->getVariables()->countVariables();
        
        if($amount >= 1)
        {
            return;
        }
        
        $this->validationResult->makeError(
            t('Command has %1$s variables, %2$s expected.', $amount, 1),
            self::VALIDATION_VARIABLE_COUNT_MISMATCH
        );
    }
    
    protected function getValidations() : array
    {
        $validations = array();
        
        if($this->getType() === 'variable')
        {
            $validations[] = 'require_variable';
        }
        
        return $validations;
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
    
    public function getSupportedTypes() : array
    {
        return array(
            'variable',
            'command'
        );
    }
}
