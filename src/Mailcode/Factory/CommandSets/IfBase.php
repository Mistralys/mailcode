<?php
/**
 * File containing the {@see Mailcode_Factory} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Factory_CommandSets_IfBase extends Mailcode_Factory_CommandSets_Set
{
    public function else() : Mailcode_Commands_Command_Else
    {
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'Else',
            '',
            '',
            '{else}'
        );
        
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_Else)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('Else', $cmd);
    }
}
