<?php
/**
 * File containing the {@see Mailcode_Commands_Command_For} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_For
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: opening FOR statement.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_For extends Mailcode_Commands_Command_Type_Opening
{
    public function getName() : string
    {
        return 'for';
    }
    
    public function getLabel() : string
    {
        return t('FOR loop');
    }
    
    public function supportsType(): bool
    {
        return false;
    }
    
    public function requiresParameters(): bool
    {
        return true;
    }
    
    protected function getValidations() : array
    {
        return array();
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
    
    public function getSiblings() : array
    {
        return array();
    }
}
