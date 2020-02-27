<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ElseIf} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ElseIf
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: An ELSE statement in an IF condition.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ElseIf extends Mailcode_Commands_Command_Type_Sibling
{
    public function getName() : string
    {
        return 'elseif';
    }
    
    public function getLabel() : string
    {
        return t('ELSE IF condition');
    }
    
    public function supportsType(): bool
    {
        return true;
    }

    public function requiresParameters(): bool
    {
        return true;
    }

    public function generatesContent(): bool
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
    
    protected function getValidations(): array
    {
        return array();
    }
    
    public function getParentName() : string
    {
        return 'if';
    }
}
