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
    
    public function __construct(Mailcode $mailcode)
    {
        $this->mailcode = $mailcode;
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
    * Parses a string to detect all commands within.
    * 
    * @param string $string
    * @return Mailcode_Collection A collection with all unique commands found.
    */
    public function parseString(string $string) : Mailcode_Collection
    {
        $collection = new Mailcode_Collection();
        
        $matches = array();
        preg_match_all(self::getRegex(), $string, $matches, PREG_PATTERN_ORDER);
        
        $commands = $this->mailcode->getCommands();
        
        $total = count($matches[0]);
        
        // 1 = single command
        // 2 = parameter command, name
        // 3 = parameter command, params
        // 4 = parameter type command, name
        // 5 = parameter type command, type
        // 6 = parameter type command, params
        
        for($i=0; $i < $total; $i++)
        {
            $name = ''; // the command name, e.g. "showvar"
            $type = '';
            $params = '';
            $matched = $matches[0][$i]; // the exact matched string including spacing
            
            if(!empty($matches[1][$i])) 
            {
                $name = $matches[1][$i];
            }
            else if(!empty($matches[2][$i])) 
            {
                $name = $matches[2][$i];
                $params = $matches[3][$i];
            }
            else if(!empty($matches[4][$i]))
            {
                $name = $matches[4][$i];
                $type = $matches[5][$i];
                $params = $matches[6][$i];
            }
            
            $name = strtolower($name);
            $type = strtolower($type);
            $params = trim($params);
            
            if(!$commands->nameExists($name)) 
            {
                $collection->addErrorMessage(
                    $matched,
                    t('No command found with the name %1$s.', $name),
                    Mailcode_Commands_Command::VALIDATION_UNKNOWN_COMMAND_NAME
                );
                continue;
            }
            
            $cmd = $commands->createCommand(
                $commands->getIDByName($name),
                $type,
                $params,
                $matched
            );
            
            if(!$cmd->isValid()) 
            {
                $collection->addInvalidCommand($cmd);
                continue;
            }
            
            $collection->addCommand($cmd);
        }
        
        return $collection;
    }
}
