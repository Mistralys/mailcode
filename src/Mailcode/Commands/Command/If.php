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
class Mailcode_Commands_Command_If extends Mailcode_Commands_IfBase implements Mailcode_Commands_Command_Type_Opening
{
    public function getName() : string
    {
        return 'if';
    }
    
    public function getLabel() : string
    {
        return t('IF condition');
    }
}
