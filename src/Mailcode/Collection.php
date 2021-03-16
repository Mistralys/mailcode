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
    const ERROR_CANNOT_MODIFY_FINALIZED = 52302;
    
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
     * @var bool
     */
    private $finalized = false;

    /**
     * @var bool
     */
    private $validating = false;

    /**
     * Adds a command to the collection.
     *
     * @param Mailcode_Commands_Command $command
     * @return Mailcode_Collection
     * @throws Mailcode_Exception
     */
    public function addCommand(Mailcode_Commands_Command $command) : Mailcode_Collection
    {
        if($this->finalized)
        {
            throw new Mailcode_Exception(
                'Cannot add commands to a finalized collection',
                'When a collection has been finalized, it may not be modified anymore.',
                self::ERROR_CANNOT_MODIFY_FINALIZED
            );
        }

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
        // Remove the command in case it was already added
        $this->removeCommand($command);

        $this->errors[] = new Mailcode_Collection_Error_Command($command);
    }

    public function removeCommand(Mailcode_Commands_Command $command) : void
    {
        $keep = array();

        foreach($this->commands as $existing)
        {
            if($existing !== $command)
            {
                $keep[] = $existing;
            }
        }

        $this->commands = $keep;
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
    * @return Mailcode_Commands_Command[]
    */
    public function getCommands()
    {
        $this->validate();

        return $this->commands;
    }

    /**
     * Retrieves all unique commands by their matched
     * string hash: this ensures only commands that were
     * written the exact same way (including spacing)
     * are returned.
     *
     * @return Mailcode_Commands_Command[]
     * @throws Mailcode_Exception
     */
    public function getGroupedByHash() : array
    {
        $this->validate();

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
     * @throws Mailcode_Exception
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
        $merged = new Mailcode_Collection();
        $merged->addCommands($this->getCommands());
        $merged->addCommands($collection->getCommands());

        return $merged;
    }
    
    public function getVariables() : Mailcode_Variables_Collection
    {
        $this->validate();

        $collection = new Mailcode_Variables_Collection_Regular();
        
        foreach($this->commands as $command)
        {
            $collection->mergeWith($command->getVariables());
        }
        
        return $collection;
    }

    /**
     * Whether the collection has been validated yet. This is used
     * primarily in the test suites.
     *
     * @return bool
     */
    public function hasBeenValidated() : bool
    {
        return isset($this->validationResult);
    }

    public function getValidationResult() : OperationResult
    {
        $this->validate();

        return $this->validationResult;
    }

    private function validate() : void
    {
        if(isset($this->validationResult) || $this->validating)
        {
            return;
        }

        // The nesting validator calls the getCommands() method, which
        // creates a circular call, since that calls validate(). To
        // avoid this issue, we use the validating flag.
        $this->validating = true;
        
        $nesting = new Mailcode_Collection_NestingValidator($this);
        $this->validationResult = $nesting->validate();

        $this->validating = false;
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
     * Retrieves only ShowXXX commands in the collection, if any.
     * Includes ShowVariable, ShowDate, ShowNumber, ShowSnippet.
     *
     * @return Mailcode_Commands_ShowBase[]
     */
    public function getShowCommands(): array
    {
        return $this->getCommandsByClass(Mailcode_Commands_ShowBase::class);
    }

    /**
     * Retrieves all commands that implement the ListVariables interface,
     * meaning that they use list variables.
     *
     * @return Mailcode_Interfaces_Commands_ListVariables[]
     * @see Mailcode_Interfaces_Commands_ListVariables
     */
    public function getListVariableCommands() : array
    {
        return $this->getCommandsByClass(Mailcode_Interfaces_Commands_ListVariables::class);
    }

    /**
    * Retrieves only show variable commands in the collection, if any.
    * 
    * @return Mailcode_Commands_Command_ShowVariable[]
    */
    public function getShowVariableCommands(): array
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

    public function finalize() : void
    {
        $this->finalized = true;

        $this->validateNesting();
    }

    public function isFinalized() : bool
    {
        return $this->finalized;
    }

    private function validateNesting() : void
    {
        foreach($this->commands as $command)
        {
            $command->validateNesting();

            if(!$command->isValid()) {
                $this->addInvalidCommand($command);
            }
        }
    }
}
