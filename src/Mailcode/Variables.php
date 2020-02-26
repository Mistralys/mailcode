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
    const REGEX_VARIABLE_NAME = '/\$\s*([A-Z0-9]+)\s*\.\s*([A-Z0-9]+)/sx';
    
   /**
    * Parses the specified string to find all variable names contained within, if any.
    * 
    * @param string $subject
    * @return Mailcode_Variables_Collection_Regular
    */
    public function parseString(string $subject) : Mailcode_Variables_Collection_Regular
    {
        $collection = new Mailcode_Variables_Collection_Regular();
        
        $matches = array();
        preg_match_all(self::REGEX_VARIABLE_NAME, $subject, $matches, PREG_PATTERN_ORDER);
        
        if(!isset($matches[0]) || empty($matches[0]))
        {
            return $collection;
        }
        
        foreach($matches[0] as $idx => $matchedText)
        {
            $path = $matches[1][$idx];
            $name = $matches[2][$idx];
            
            $collection->add(new Mailcode_Variables_Variable($path, $name, $matchedText));
        }
        
        return $collection;
    }
}
