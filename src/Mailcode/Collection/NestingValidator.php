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
class Mailcode_Collection_NestingValidator
{
    public const ERROR_MISSING_COMMAND_TYPE_METHOD = 49001;
    
    public const VALIDATION_SIBLING_WITHOUT_PARENT = 49101;
    public const VALIDATION_SIBLING_WRONG_PARENT = 49102; 
    public const VALIDATION_COMMANDS_ALREADY_CLOSED = 49103;
    public const VALIDATION_UNCLOSED_COMMAND = 49104;
    public const VALIDATION_CLOSING_NON_OPENING_COMMAND = 49105;
    
   /**
    * @var Mailcode_Collection
    */
    protected $collection;
    
   /**
    * @var OperationResult
    */
    protected $validationResult;
    
   /**
    * @var Mailcode_Commands_Command_Type[]
    */
    protected $stack = array();
    
    public function __construct(Mailcode_Collection $collection)
    {
        $this->collection = $collection;
    }
    
    public function getCollection() : Mailcode_Collection
    {
        return $this->collection;
    }

    /**
     * @return OperationResult
     * @throws Mailcode_Exception
     *
     * @see Mailcode_Collection_NestingValidator::validate_Closing()
     * @see Mailcode_Collection_NestingValidator::validate_Opening()
     * @see Mailcode_Collection_NestingValidator::validate_Sibling()
     * @see Mailcode_Collection_NestingValidator::validate_Standalone()
     * @see Mailcode_Collection_NestingValidator::validate_Unclosed()
     */
    public function validate() : OperationResult
    {
        $this->validationResult = new OperationResult($this);
        
        $commands = $this->collection->getCommands();
        
        foreach($commands as $command)
        {
            $method = 'validate_'.$command->getCommandType();
            
            if(!method_exists($this, $method))
            {
                throw new Mailcode_Exception(
                    'Unknown command type validation method.',
                    sprintf(
                        'The method [%s] does not exist in class [%s].',
                        $method,
                        get_class($this)
                    ),
                    self::ERROR_MISSING_COMMAND_TYPE_METHOD
                );
            }
            
            $this->$method($command);
            
            if(!$this->validationResult->isValid())
            {
                break;
            }
        }
        
        if($this->validationResult->isValid())
        {
            $this->validate_Unclosed();
        }
        
        return $this->validationResult;
    }

    protected function validate_Unclosed() : void
    {
        $leftover = $this->getOpenCommand();
        
        if($leftover === null)
        {
            return;
        }
        
        $this->validationResult->makeError(
            t(
                'The command %1$s was never ended.',
                $leftover->getName()
            ).' ('.$leftover->getMatchedText().')',
            self::VALIDATION_UNCLOSED_COMMAND
        );
    }
    
    protected function getOpenCommand() : ?Mailcode_Commands_Command_Type_Opening
    {
        if(empty($this->stack))
        {
            return null;
        }
        
        end($this->stack);
        $idx = key($this->stack);
        reset($this->stack);
        
        $cmd = $this->stack[$idx];
        
        if($cmd instanceof Mailcode_Commands_Command_Type_Opening)
        {
            return $cmd;
        }
        
        return null;
    }
    
    protected function validate_Standalone(Mailcode_Commands_Command_Type_Standalone $command) : void
    {
        // standalone commands have no nesting issues
    }
    
    protected function validate_Opening(Mailcode_Commands_Command_Type_Opening $command) : void
    {
        $this->log(sprintf('Opening %s', $command->getName()));
        
        $this->stack[] = $command;
    }
    
    protected function validate_Sibling(Mailcode_Commands_Command_Type_Sibling $command) : void
    {
        $parent = $this->getOpenCommand();
        
        if($parent === null)
        {
            $this->validationResult->makeError(
                t(
                    '%1$s command has no parent %2$s command.',
                    $command->getName(),
                    $command->getParentName()
                ),
                self::VALIDATION_SIBLING_WITHOUT_PARENT
            );
            
            return;
        }
        
        if($parent->getName() !== $command->getParentName())
        {
            $this->validationResult->makeError(
                t(
                    '%1$s command cannot be used as child of a %2$s command.',
                    $command->getName(),
                    $parent->getName()
                ),
                self::VALIDATION_SIBLING_WRONG_PARENT
            );
            
            return;
        }

        $command->registerOpening($parent);
        $parent->registerSibling($command);

        $this->log(sprintf('Sibling command %s in %s', $command->getName(), $parent->getName()));
    }
    
    protected function validate_Closing(Mailcode_Commands_Command_Type_Closing $command) : void
    {
        if(empty($this->stack))
        {
            $this->validationResult->makeError(
                t('All open commands have already been ended.'),
                self::VALIDATION_COMMANDS_ALREADY_CLOSED
            );
            
            return;
        }
        
        $openingCommand = array_pop($this->stack);

        if($openingCommand instanceof Mailcode_Commands_Command_Type_Opening)
        {
            $this->log(sprintf('Closing command %s', $openingCommand->getName()));

            $command->registerOpening($openingCommand);
            $openingCommand->registerClosing($command);
            return;
        }

        $this->validationResult->makeError(
            t('Trying to close a non-opening command.'),
            self::VALIDATION_CLOSING_NON_OPENING_COMMAND
        );
    }
    
    protected function log(string $message) : void
    {
        //echo $message.PHP_EOL;
    }
}
