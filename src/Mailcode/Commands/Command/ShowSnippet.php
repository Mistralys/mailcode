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
class Mailcode_Commands_Command_ShowSnippet extends Mailcode_Commands_ShowBase
{
    public function getName() : string
    {
        return 'showsnippet';
    }
    
    public function getLabel() : string
    {
        return t('Show text snippet');
    }
    
    protected function getValidations() : array
    {
        return array(
            Mailcode_Interfaces_Commands_Variable::VALIDATION_NAME,
            Mailcode_Interfaces_Commands_URLEncode::VALIDATION_NAME,
            Mailcode_Interfaces_Commands_URLDecode::VALIDATION_NAME
        );
    }
    
    public function generatesContent() : bool
    {
        return true;
    }
}
