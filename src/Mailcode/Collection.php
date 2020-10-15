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

use AppUtils\OperationResult;

/**
 * Commands collection: container for commands.
 *
 * @package Mailcode
 * @subpackage Collection
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Collection
{
    const ERROR_CANNOT_RETRIEVE_FIRST_ERROR = 52301;
    
   /**
    * @var Mailcode_Commands_Command[]
    */
    protected $commands = array();
    
    /**
     * @var Mailcode_Collection_Error[]
     */
    protected $errors = array();
    
   /**
    * @var OperationResult|NULL
    */
    protected $validationResult;
    
   /**
    * Adds a command to the collection.
    * 
    * @param Mailcode_Commands_Command $command
    * @return Mailcode_Collection
    */
    public function addCommand(Mailcode_Commands_Command $command) : Mailcode_Collection
    {
        $this->commands[] = $command;
        
        // reset the collection's validation result, since it 
        // depends on the commands.
        $this->validationResult = null;
        
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
        $result = $this->getValidationResult();
        
        $errors = $this->errors;
        
        if(!$result->isValid())
        {
            $errors[] = new Mailcode_Collection_Error_Message(
                '',
                $result->getCode(),
                $result->getErrorMessage()
            );
        }
        
        return $errors;
    }
    
    public function getFirstError() : Mailcode_Collection_Error
    {
        $errors = $this->getErrors();
        
        if(!empty($errors))
        {
            return array_shift($errors);
        }
        
        throw new Mailcode_Exception(
            'Cannot retrieve first error: no errors detected',
            null,
            self::ERROR_CANNOT_RETRIEVE_FIRST_ERROR
        );
    }
    
    public function isValid() : bool
    {
        $errors = $this->getErrors();
        
        return empty($errors);
    }
    
   /**
    * Retrieves all commands that were detected, in the exact order
    * they were found.
    * 
    * @return \Mailcode\Mailcode_Commands_Command[]
    */
    public function getCommands()
    {
       return $this->commands;
    }
    
   /**
    * Retrieves all unique commands by their matched
    * string hash: this ensures only commands that were
    * written the exact same way (including spacing)
    * are returned.
    * 
    * @return \Mailcode\Mailcode_Commands_Command[]
    */
    public function getGroupedByHash()
    {
        $hashes = array();
        
        foreach($this->commands as $command)
        {
            $hash = $command->getHash();
            
            if(!isset($hashes[$hash]))
            {
                $hashes[$hash] = $command;
            }
        }
            
        return array_values($hashes);
    }

   /**
    * Adds several commands at once.
    * 
    * @param Mailcode_Commands_Command[] $commands
    * @return Mailcode_Collection
    */
    public function addCommands(array $commands) : Mailcode_Collection
    {
        foreach($commands as $command)
        {
            $this->addCommand($command);
        }
        
        return $this;
    }
    
    public function mergeWith(Mailcode_Collection $collection) : Mailcode_Collection
    {
        $this->addCommands($collection->getCommands());
        
        return $this;
    }
    
    public function getVariables() : Mailcode_Variables_Collection
    {
        $collection = new Mailcode_Variables_Collection_Regular();
        
        foreach($this->commands as $command)
        {
            $collection->mergeWith($command->getVariables());
        }
        
        return $collection;
    }
    
    public function getValidationResult() : OperationResult
    {
        if($this->validationResult instanceof OperationResult)
        {
            return $this->validationResult;
        }
        
        $nesting = new Mailcode_Collection_NestingValidator($this);
        
        $this->validationResult = $nesting->validate(); 
        
        return $this->validationResult;
    }
    
    public function hasErrorCode(int $code) : bool
    {
        $errors = $this->getErrors();
        
        foreach($errors as $error)
        {
            if($error->getCode() === $code)
            {
                return true;
            }
        }
        
        return false;
    }
    
   /**
    * Retrieves only show variable commands in the collection, if any.
    * 
    * @return Mailcode_Commands_Command_ShowVariable[]
    */
    public function getShowVariableCommands()
    {
        return $this->getCommandsByClass(Mailcode_Commands_Command_ShowVariable::class);
    }

    /**
     * @return Mailcode_Commands_Command_For[]
     */
    public function getForCommands()
    {
        return $this->getCommandsByClass(Mailcode_Commands_Command_For::class);
    }

   /**
    * Retrieves only show date commands in the collection, if any.
    *
    * @return Mailcode_Commands_Command_ShowDate[]
    */
    public function getShowDateCommands() : array
    {
        return $this->getCommandsByClass(Mailcode_Commands_Command_ShowDate::class);
    }

    /**
     * @param string $className
     * @return Mailcode_Commands_Command[]
     */
    public function getCommandsByClass(string $className) : array
    {
        $result = array();

        foreach($this->commands as $command)
        {
            if($command instanceof $className)
            {
                $result[] = $command;
            }
        }

        return $result;
    }
    
    public function getFirstCommand() : ?Mailcode_Commands_Command
    {
        $commands = $this->getCommands();
        
        if(!empty($commands))
        {
            return array_shift($commands);
        }
        
        return null;
    }
}
