<?php
/**
 * File containing the {@see Mailcode_Factory} class.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see Mailcode_Factory
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory utility used to create commands.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Factory_CommandSets_Set
{
   /**
    * @var Mailcode_Factory_Instantiator
    */
    protected $instantiator;

    /**
     * @var Mailcode_Commands
     */
    protected $commands;

    public function __construct()
    {
        $this->instantiator = new Mailcode_Factory_Instantiator();
        $this->commands = Mailcode::create()->getCommands();
    }
    
    public function end() : Mailcode_Commands_Command_End
    {
        $cmd = $this->commands->createCommand(
            'End',
            '',
            '',
            '{end}'
        );
        
        $this->instantiator->checkCommand($cmd);
        
        if($cmd instanceof Mailcode_Commands_Command_End)
        {
            return $cmd;
        }
        
        throw $this->instantiator->exceptionUnexpectedType('End', $cmd);
    }
}
