<?php
/**
 * @package Mailcode
 * @subpackage Factory
 */

declare(strict_types=1);

namespace Mailcode;

use Exception;

/**
 * Factory-specific exception.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Factory_Exception extends Mailcode_Exception
{
    protected ?Mailcode_Commands_Command $command = null;
    
   /**
    * @param string $message
    * @param string|NULL $details
    * @param int|NULL $code
    * @param Exception|NULL $previous
    * @param Mailcode_Commands_Command|NULL $command
    */
    public function __construct(string $message, $details=null, $code=null, $previous=null, ?Mailcode_Commands_Command $command=null)
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
