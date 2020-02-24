<?php
/**
 * File containing the {@see Mailcode_Collection} class.
 *
 * @package Mailcode
 * @subpackage Collection
 * @see Mailcode_Collection
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Commands collection: container for commands.
 *
 * @package Mailcode
 * @subpackage Collection
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Collection
{
   /**
    * @var Mailcode_Commands_Command[]
    */
    protected $commands = array();
    
    /**
     * @var Mailcode_Collection_Error[]
     */
    protected $errors = array();
    
   /**
    * Adds a command to the collection.
    * 
    * @param Mailcode_Commands_Command $command
    * @return Mailcode_Collection
    */
    public function addCommand(Mailcode_Commands_Command $command) : Mailcode_Collection
    {
        $hash = $command->getHash();
        
        if(!isset($this->commands[$hash])) {
            $this->commands[$hash] = $command;
        }
        
        return $this;
    }
    
   /**
    * Whether there are any commands in the collection.
    * 
    * @return bool
    */
    public function hasCommands() : bool
    {
        return !empty($this->commands);
    }
    
   /**
    * Counts the amount of commands in the collection.
    * 
    * @return int
    */
    public function countCommands() : int
    {
        return count($this->commands);
    }

    public function addErrorMessage(string $matchedText, string $message, int $code) : void
    {
        $this->errors[] = new Mailcode_Collection_Error_Message(
            $matchedText,
            $code,
            $message
        );
    }
    
    public function addInvalidCommand(Mailcode_Commands_Command $command) : void
    {
        $this->errors[] = new Mailcode_Collection_Error_Command($command);
    }
    
   /**
    * @return Mailcode_Collection_Error[]
    */
    public function getErrors()
    {
        return $this->errors;
    }
    
    public function isValid() : bool
    {
        return empty($this->errors);
    }
    
   /**
    * Retrieves all commands that were detected.
    * 
    * @return \Mailcode\Mailcode_Commands_Command[]
    */
    public function getCommands()
    {
       return $this->commands;
    }
}
