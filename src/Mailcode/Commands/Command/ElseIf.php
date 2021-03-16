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
class Mailcode_Commands_Command_ElseIf extends Mailcode_Commands_IfBase implements Mailcode_Commands_Command_Type_Sibling
{
    use Mailcode_Traits_Commands_Type_Sibling;

    public function getName() : string
    {
        return 'elseif';
    }
    
    public function getLabel() : string
    {
        return t('ELSE IF condition');
    }
    
    public function getParentName() : string
    {
        return 'if';
    }
}
