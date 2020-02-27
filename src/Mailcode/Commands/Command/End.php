<?php
/**
 * File containing the {@see Mailcode_Commands_Command_End} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_End
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: closing statement for any open command.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_End extends Mailcode_Commands_Command_Type_Closing
{
    public function getName() : string
    {
        return 'end';
    }
    
    public function getLabel() : string
    {
        return t('Close open command');
    }
    
    public function supportsType(): bool
    {
        return false;
    }
    
    public function requiresParameters(): bool
    {
        return false;
    }
    
    public function generatesContent() : bool
    {
        return false;
    }
    
    protected function getValidations(): array
    {
        return array();
    }
}
