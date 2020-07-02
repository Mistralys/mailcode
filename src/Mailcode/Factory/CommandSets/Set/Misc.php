<?php
/**
 * File containing the {@see Mailcode_Factory_CommandSets_Set_Misc} class.
 *
 * @package Mailcode
 * @subpackage Utilities
 * @see Mailcode_Factory_CommandSets_Set_Misc
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
class Mailcode_Factory_CommandSets_Set_Misc extends Mailcode_Factory_CommandSets_Set
{
    public function comment(string $comments) : Mailcode_Commands_Command_Comment
    {
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'Comment',
            '', // type
            $comments, // params
            sprintf(
                '{comment: %s}',
                $comments
            )
        );
    
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_Comment)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('Comment', $cmd);
    }
}