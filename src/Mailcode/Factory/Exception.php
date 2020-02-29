<?php
/**
 * File containing the {@see Mailcode_Factory_Exception} class.
 *
 * @package Mailcode
 * @subpackage Core
 * @see Mailcode_Factory_Exception
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Factory-specific exception.
 *
 * @package Mailcode
 * @subpackage Core
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_Exception extends Mailcode_Exception
{
   /**
    * @var Mailcode_Commands_Command|NULL
    */
    protected $command;
    
   /**
    * @param string $message
    * @param string|NULL $details
    * @param int|NULL $code
    * @param \Exception|NULL $previous
    * @param Mailcode_Commands_Command|NULL $command
    */
    public function __construct(string $message, $details=null, $code=null, $previous=null, Mailcode_Commands_Command $command=null)
    {
        parent::__construct($message, $details, $code, $previous);
        
        $this->command = $command;
    }
    
   /**
    * Retrieves the erroneous command, if any.
    * 
    * @return Mailcode_Commands_Command|NULL
    */
    public function getCommand() : ?Mailcode_Commands_Command
    {
        return $this->command;
    }
}
