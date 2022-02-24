<?php
/**
 * File containing the {@see Mailcode_Commands} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\FileHelper;

/**
 * Mailcode commands repository: factory for command instances,
 * and for fetching command information.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands
{
    public const ERROR_COMMAND_NAME_DOES_NOT_EXIST = 45901;
    public const ERROR_COMMAND_DOES_NOT_EXIST = 45902;
    public const ERROR_INVALID_DUMMY_COMMAND_TYPE = 45903;
    public const ERROR_INVALID_COMMAND_CLASS = 45904;
    
   /**
    * @var Mailcode_Commands_Command[]
    */
    private $commands = array();
    
   /**
    * @var array<string,Mailcode_Commands_Command>
    */
    private static $dummyCommands = array();
    
   /**
    * Retrieves a list of all available command IDs.
    * 
    * @return string[]
    */
    public function getIDs() : array
    {
        static $ids = array();
        
        if(empty($ids)) {
            $ids = FileHelper::createFileFinder(__DIR__.'/Commands/Command')
            ->getPHPClassNames();
        }
        
        return $ids;
    }

    /**
     * Retrieves a list of all available commands, sorted by label.
     *
     * NOTE: These instances are only used for information purposes.
     *
     * @return Mailcode_Commands_Command[]
     * @throws Mailcode_Exception
     */
    public function getAll()
    {
        if(!empty($this->commands)) {
            return $this->commands;
        }
        
        $ids = $this->getIDs();
        
        $result = array();
        
        foreach($ids as $id) 
        {
            $result[] = $this->getDummyCommand($id);
        }
        
        usort($result, function(Mailcode_Commands_Command $a, Mailcode_Commands_Command $b) 
        {
            return strnatcasecmp($a->getLabel(), $b->getLabel());
        });
        
        $this->commands = $result; 
        
        return $result;
    }
    
   /**
    * Gets an available command by its ID.
    * 
    * @param string $id
    * @return Mailcode_Commands_Command
    */
    public function getByID(string $id) : Mailcode_Commands_Command
    {
        static $instances = array();
        
        if(!isset($instances[$id])) 
        {
            $instances[$id] = $this->createCommand($id, '__dummy', '', '');
        }
        
        return $instances[$id];
    }
    
   /**
    * Retrieves the ID of a command by its name.
    * 
    * @param string $name
    * @throws Mailcode_Exception
    * @return string
    * 
    * @see Mailcode_Commands::ERROR_COMMAND_NAME_DOES_NOT_EXIST
    */
    public function getIDByName(string $name) : string
    {
        $items = $this->getAll();
        
        foreach($items as $item) 
        {
            if($item->getName() === $name) {
                return $item->getID();
            }
        }
        
        throw new Mailcode_Exception(
            'No such command name',
            sprintf(
                'The command name [%1$s] does not exist.',
                $name
            ),
            self::ERROR_COMMAND_NAME_DOES_NOT_EXIST
        );
    }
    
    public function idExists(string $id) : bool
    {
        $ids = $this->getIDs();
        
        return in_array($id, $ids);
    }
    
   /**
    * Checks wether the specified name exists.
    * 
    * @param string $name For example: "showvar".
    * @return bool
    */
    public function nameExists(string $name) : bool
    {
        $items = $this->getAll();
        
        foreach($items as $item)
        {
            if($item->getName() === $name) {
                return true;
            }
        }
        
        return false;
    }
    
    public function createCommand(string $id, string $type, string $params, string $matchedString) : Mailcode_Commands_Command
    {
        $class = $this->resolveClassName($id, $type);
        
        if(!class_exists($class))
        {
            throw new Mailcode_Exception(
                'No such command',
                sprintf(
                    'The command ID [%1$s] does not exist, class [%2$s] not found.',
                    $id,
                    $class
                ),
                self::ERROR_COMMAND_DOES_NOT_EXIST
            );
        }
        
        $command = new $class($type, $params, $matchedString);

        if($command instanceof Mailcode_Commands_Command)
        {
            return $command;
        }

        throw new Mailcode_Exception(
            'Invalid command class',
            sprintf(
                'The class [%s] does not extend the base command class.',
                $class
            ),
            self::ERROR_INVALID_COMMAND_CLASS
        );
    }
    
    protected function resolveClassName(string $id, string $type) : string
    {
        $class = 'Mailcode\Mailcode_Commands_Command_'.$id;
        
        $dummy = $this->getDummyCommand($id);
        
        if($dummy->supportsType())
        {
            if(empty($type))
            {
                $type = $dummy->getDefaultType();
            }
            
            $class .= '_'.$this->adjustTypeName($type);
        }
        
        return $class;
    }
    
   /**
    * Translates the command type to the expected class naming scheme.
    * 
    * Example: not-empty => NotEmpty
    * 
    * @param string $type
    * @return string
    */
    private function adjustTypeName(string $type) : string
    {
        $type = str_replace('-', ' ', $type);
        $type = ucwords($type);
        $type = str_replace(' ', '', $type);
        
        return $type;
    }

    /**
     * Retrieves the dummy command of the specified type, which
     * is used to retrieve information on the command's capabilities.
     *
     * @param string $id
     * @return Mailcode_Commands_Command
     * @throws Mailcode_Exception
     */
    private function getDummyCommand(string $id) : Mailcode_Commands_Command
    {
        if(isset(self::$dummyCommands[$id])) {
            return self::$dummyCommands[$id];
        }

        $class = 'Mailcode\Mailcode_Commands_Command_'.$id;
        $cmd = new $class('__dummy');

        if($cmd instanceof Mailcode_Commands_Command)
        {
            self::$dummyCommands[$id] = $cmd;
            return $cmd;
        }
        
        throw new Mailcode_Exception(
            'Invalid dummy command type',
            sprintf('The stored variable type is %1$s.', gettype(self::$dummyCommands[$id])),
            self::ERROR_INVALID_DUMMY_COMMAND_TYPE
        );
    }

    /**
     * Retrieves all commands that can contain content
     * that is not parsed by the main parsing process.
     *
     * @return Mailcode_Interfaces_Commands_ProtectedContent[]
     * @throws Mailcode_Exception
     */
    public function getContentCommands() : array
    {
        $result = array();
        $commands = $this->getAll();

        foreach($commands as $command)
        {
            if($command instanceof Mailcode_Interfaces_Commands_ProtectedContent)
            {
                $result[] = $command;
            }
        }

        return $result;
    }
}
