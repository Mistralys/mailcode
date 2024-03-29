<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Variables} class.
 *
 * @package Mailcode
 * @subpackage Variables
 * @see \Mailcode\Mailcode_Variables
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
    public const REGEX_VARIABLE_NAME = '/\$\s*([A-Z0-9_]+)\s*\.\s*([A-Z0-9_]+)|\$\s*([A-Z0-9_]+)/six';
    
   /**
    * @var Mailcode_Variables_Collection_Regular
    */
    protected Mailcode_Variables_Collection_Regular $collection;

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
        
        if(empty($matches[0]))
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

    public static function dollarizeName(string $name) : string
    {
        return '$'.self::undollarizeName($name);
    }

    public static function undollarizeName(string $name) : string
    {
        return ltrim($name, '$');
    }

    public function createVariable(string $path, ?string $name='') : Mailcode_Variables_Variable
    {
        if(empty($name)) {
            $fullName = dollarize($path);
            $name = $path;
            $path = '';
        } else {
            $fullName = dollarize($path.'.'.$name);
        }

        return new Mailcode_Variables_Variable($path, $name, $fullName);
    }

    public function createVariableByName(string $name) : Mailcode_Variables_Variable
    {
        $parts = explode('.', undollarize($name));

        if(count($parts) === 1) {
            return $this->createVariable($parts[0]);
        }

        return $this->createVariable($parts[0], $parts[1]);
    }
}
