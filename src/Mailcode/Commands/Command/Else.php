<?php
/**
 * File containing the {@see Mailcode_Commands_Command_Else} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_Else
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
class Mailcode_Commands_Command_Else extends Mailcode_Commands_Command implements Mailcode_Commands_Command_Type_Sibling
{
    public function getName() : string
    {
        return 'else';
    }
    
    public function getLabel() : string
    {
        return t('ELSE condition');
    }
    
    public function supportsType(): bool
    {
        return false;
    }

    public function supportsURLEncoding(): bool
    {
        return false;
    }
    
    public function getDefaultType() : string
    {
        return '';
    }

    public function requiresParameters(): bool
    {
        return false;
    }
    
    public function supportsLogicKeywords() : bool
    {
        return false;
    }

    public function generatesContent(): bool
    {
        return false;
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
