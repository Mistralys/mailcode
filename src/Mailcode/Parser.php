<?php
/**
 * File containing the {@see Mailcode_Parser} class.
 * 
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode parser, capable of detecting commands in strings.
 * 
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser
{
    const COMMAND_REGEX_PARTS = array( 
        '{\s*([a-z]+)\s*}',
        '{\s*([a-z]+)\s*:([^}]+)}',
        '{\s*([a-z]+)\s+([a-z]+)\s*:([^}]+)}'
    );
    
   /**
    * @var Mailcode
    */
    protected $mailcode;
    
   /**
    * @var Mailcode_Commands
    */
    protected $commands;
    
    public function __construct(Mailcode $mailcode)
    {
        $this->mailcode = $mailcode;
        $this->commands = $this->mailcode->getCommands();
    }
    
   /**
    * Gets the regex format string used to detect commands.
    * 
    * @return string
    */
    protected static function getRegex() : string
    {
        return '/'.implode('|', self::COMMAND_REGEX_PARTS).'/sixU';
    }
    
   /**
    * Parses a string to detect all commands within. Returns a
    * collection instance that contains information on all the 
    * commands.
    * 
    * @param string $string
    * @return Mailcode_Collection A collection with all unique commands found.
    */
    public function parseString(string $string) : Mailcode_Collection
    {
        $collection = new Mailcode_Collection();
        
        $matches = array();
        preg_match_all(self::getRegex(), $string, $matches, PREG_PATTERN_ORDER);
        
        $total = count($matches[0]);
        
        for($i=0; $i < $total; $i++)
        {
            $match = $this->parseMatch($matches, $i);
            
            $this->processMatch($match, $collection);
        }
        
        return $collection;
    }
    
   /**
    * Processes a single match found in the string: creates the command,
    * and adds it to the collection if it's a valid command, or to the list
    * of invalid commands otherwise.
    * 
    * @param Mailcode_Parser_Match $match
    * @param Mailcode_Collection $collection
    */
    protected function processMatch(Mailcode_Parser_Match $match, Mailcode_Collection $collection) : void
    {
        $name = $match->getName();
        
        if(!$this->commands->nameExists($name))
        {
            $collection->addErrorMessage(
                $match->getMatchedString(),
                t('No command found with the name %1$s.', $name),
                Mailcode_Commands_Command::VALIDATION_UNKNOWN_COMMAND_NAME
            );
            return;
        }
        
        $cmd = $this->commands->createCommand(
            $this->commands->getIDByName($name),
            $match->getType(),
            $match->getParams(),
            $match->getMatchedString()
        );
        
        if($cmd->isValid())
        {
            $collection->addCommand($cmd);
            return;
        }
        
        $collection->addInvalidCommand($cmd);
    }
    
   /**
    * Parses a single regex match: determines which named group
    * matches, and retrieves the according information.
    * 
    * @param array $matches The regex results array.
    * @param int $index The matched index.
    * @return Mailcode_Parser_Match
    */
    protected function parseMatch(array $matches, int $index) : Mailcode_Parser_Match
    {
        $name = ''; // the command name, e.g. "showvar"
        $type = '';
        $params = '';
        
        // 1 = single command
        // 2 = parameter command, name
        // 3 = parameter command, params
        // 4 = parameter type command, name
        // 5 = parameter type command, type
        // 6 = parameter type command, params
        
        if(!empty($matches[1][$index]))
        {
            $name = $matches[1][$index];
        }
        else if(!empty($matches[2][$index]))
        {
            $name = $matches[2][$index];
            $params = $matches[3][$index];
        }
        else if(!empty($matches[4][$index]))
        {
            $name = $matches[4][$index];
            $type = $matches[5][$index];
            $params = $matches[6][$index];
        }
        
        return new Mailcode_Parser_Match(
            $name, 
            $type, 
            $params, 
            $matches[0][$index]
        );
    }
    
   /**
    * Creates an instance of the safeguard tool, which
    * is used to safeguard commands in a string with placeholders.
    * 
    * @param string $subject The string to use to safeguard commands in.
    * @return Mailcode_Parser_Safeguard
    * @see Mailcode_Parser_Safeguard
    */
    public function createSafeguard(string $subject) : Mailcode_Parser_Safeguard
    {
        return new Mailcode_Parser_Safeguard($this, $subject);
    }
}
