<?php
/**
 * File containing the {@see Mailcode_Variables} class.
 *
 * @package Mailcode
 * @subpackage Variables
 * @see Mailcode_Variables
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Handler for all variable-related tasks.
 *
 * @package Mailcode
 * @subpackage Variables
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Variables
{
    const REGEX_VARIABLE_NAME = '/\$\s*([A-Z0-9_]+)\s*\.\s*([A-Z0-9_]+)|\$\s*([A-Z0-9_]+)/six';
    
   /**
    * @var Mailcode_Variables_Collection_Regular
    */
    protected $collection;

    /**
     * Parses the specified string to find all variable names contained within, if any.
     *
     * @param string $subject
     * @param Mailcode_Commands_Command|null $sourceCommand
     * @return Mailcode_Variables_Collection_Regular
     */
    public function parseString(string $subject, ?Mailcode_Commands_Command $sourceCommand=null) : Mailcode_Variables_Collection_Regular
    {
        $this->collection = new Mailcode_Variables_Collection_Regular();
        
        $matches = array();
        preg_match_all(self::REGEX_VARIABLE_NAME, $subject, $matches, PREG_PATTERN_ORDER);
        
        if(!isset($matches[0]) || empty($matches[0]))
        {
            return $this->collection;
        }
        
        foreach($matches[0] as $idx => $matchedText)
        {
            if(!empty($matches[3][$idx]))
            {
                $this->addSingle($matches[3][$idx], $matchedText, $sourceCommand);
            }
            else 
            {
                $this->addPathed($matches[1][$idx], $matches[2][$idx], $matchedText, $sourceCommand);
            }
        }
        
        return $this->collection;
    }
    
    protected function addSingle(string $name, string $matchedText, ?Mailcode_Commands_Command $sourceCommand=null) : void
    {
        // ignore US style numbers like $451
        if(is_numeric($name))
        {
            return;
        }
        
        $this->collection->add(new Mailcode_Variables_Variable('', $name, $matchedText, $sourceCommand));
    }
    
    protected function addPathed(string $path, string $name, string $matchedText, ?Mailcode_Commands_Command $sourceCommand=null) : void
    {
        // ignore US style numbers like $45.12
        if(is_numeric($path.'.'.$name))
        {
            return;
        }
        
        $this->collection->add(new Mailcode_Variables_Variable($path, $name, $matchedText, $sourceCommand));
    }
}
