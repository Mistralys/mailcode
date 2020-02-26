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
class Mailcode_Commands_Command_ShowVariable extends Mailcode_Commands_Command
{
    // TODO needs real code
    const VALIDATION_VARIABLE_COUNT_MISMATCH = 112001;
    
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

    public function requiresParameters(): bool
    {
        return true;
    }

    protected function validateSyntax_require_variable()
    {
         $amount = $this->getVariables()->countVariables();
         
         if($amount === 1)
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
        return array('require_variable');
    }
    
    public function generatesContent() : bool
    {
        return true;
    }
}
