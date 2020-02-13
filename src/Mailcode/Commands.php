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
    const ERROR_COMMAND_NAME_DOES_NOT_EXIST = 45901;
    
   /**
    * @var Mailcode_Commands_Command[]
    */
    private $commands = array();
    
   /**
    * Retrieves a list of all available command IDs.
    * 
    * @return string[]
    */
    public function getIDs() : array
    {
        static $ids = array();
        
        if(empty($ids)) {
            $ids = \AppUtils\FileHelper::createFileFinder(__DIR__.'/Commands/Command')
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
            $result[] = $this->getByID($id);
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
        $class = 'Mailcode_Commands_Command_'.$id;
        
        return new $class($type, $params, $matchedString);
    }
}
