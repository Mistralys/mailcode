<?php
/**
 * File containing the {@see Mailcode} class.
 *
 * @package Mailcode
 * @subpackage Core
 * @see Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Main hub for the "Mailcode" syntax handling, which is used
 * to abstract the actual commands syntax used by the selected
 * mailing format.
 * 
 * Users only work with the mailcode commands to ensure that
 * the mail editor interface stays independent of the actual
 * format implementation used by the backend systems.
 *
 * @package Mailcode
 * @subpackage Core
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode
{
   /**
    * @var Mailcode_Parser|NULL
    */
    protected $parser = null;
    
   /**
    * @var Mailcode_Commands|NULL
    */
    protected $commands = null;
    
   /**
    * @var Mailcode_Variables|NULL
    */
    protected $variables = null;
    
   /**
    * Creates a new mailcode instance.
    * @return Mailcode
    */
    public static function create() : Mailcode
    {
        return new Mailcode();
    }
    
   /**
    * Parses the string to detect all commands contained within.
    * 
    * @param string $string
    * @return Mailcode_Collection
    */
    public function parseString(string $string) : Mailcode_Collection
    {
        return $this->getParser()->parseString($string);
    }
    
   /**
    * Retrieves the string parser instance used to detect commands.
    * 
    * @return Mailcode_Parser
    */
    public function getParser() : Mailcode_Parser
    {
        if(!isset($this->parser)) 
        {
            $this->parser = new Mailcode_Parser($this);
        }
        
        return $this->parser;
    }
    
   /**
    * Retrieves the commands collection, which is used to
    * access information on the available commands.
    * 
    * @return Mailcode_Commands
    */
    public function getCommands() : Mailcode_Commands
    {
        if(!isset($this->commands)) 
        {
            $this->commands = new Mailcode_Commands();
        }
        
        return $this->commands;
    }
    
    public function createSafeguard(string $subject) : Mailcode_Parser_Safeguard
    {
        return $this->getParser()->createSafeguard($subject);
    }
    
   /**
    * Attempts to find all variables in the target string.
    * 
    * @param string $subject
    * @return Mailcode_Variables_Collection_Regular
    */
    public function findVariables(string $subject) : Mailcode_Variables_Collection_Regular
    {
        return $this->createVariables()->parseString($subject);
    }
    
    public function createVariables() : Mailcode_Variables
    {
        if(!isset($this->variables))
        {
            $this->variables = new Mailcode_Variables();
        }
        
        return $this->variables;
    }
}
