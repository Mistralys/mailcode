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
        return false;
    }
    
    public function getSupportedTypes() : array
    {
        return array(
            'variable',
            'command'
        );
    }
    
    public function getRole() : string
    {
        return self::ROLE_OPENING;
    }

    public function getSiblings() : array
    {
        return array(
            'elseif'
        );
    }
}
