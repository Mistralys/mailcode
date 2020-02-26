<?php
/**
 * File containing the {@see Mailcode_Variables_Collection} class.
 *
 * @package Mailcode
 * @subpackage Variables
 * @see Mailcode_Variables_Collection
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
abstract class Mailcode_Variables_Collection
{
   /**
    * Stores variables by their hash.
    * 
    * @var array[string]Mailcode_Variables_Variable
    */
    protected $variables = array();
    
    public function __construct()
    {
        $this->init();
    }
    
    protected function init() : void
    {
        
    }
    
    public function add(Mailcode_Variables_Variable $variable) : Mailcode_Variables_Collection
    {
        $hash = $variable->getHash();
        
        $this->variables[$hash] = $variable;
        
        return $this;
    }
    
    public function hasVariables() : bool
    {
        return !empty($this->variables);
    }
    
    public function countVariables() : int
    {
        return count($this->variables);
    }
    
   /**
    * Checks whether the collection contains a variable with the specified name.
    * 
    * @param string $fullName The variable name, with or without $ sign.
    * @return bool
    */
    public function hasVariableName(string $fullName) : bool
    {
        $fullName = $this->fixName($fullName);
        
        foreach($this->variables as $variable)
        {
            if($variable->getFullName() === $fullName)
            {
                return true;
            }
        }
        
        return false;
    }
    
   /**
    * Retrieves a collection of all variable instances for
    * the specified name (there can be several with differing
    * matched texts because of spacing).
    * 
    * @param string $fullName
    * @return Mailcode_Variables_Collection
    */
    public function getByFullName(string $fullName) : Mailcode_Variables_Collection
    {
        $fullName = $this->fixName($fullName);
        
        $collection = new Mailcode_Variables_Collection_Regular();
        
        foreach($this->variables as $variable)
        {
            if($variable->getFullName() === $fullName)
            {
                $collection->add($variable);
            }
        }
        
        return $collection;
    }
    
   /**
    * Prepends the $ sign to a variable name if it does not have it.
    * 
    * @param string $fullName
    * @return string
    */
    protected function fixName(string $fullName) : string
    {
        if(substr($fullName, 0, 1) === '$')
        {
            return $fullName;
        }
        
        return '$'.$fullName;
    }
    
   /**
    * Retrieves all variables, grouped by their hash - meaning
    * unique matched strings.
    * 
    * @return Mailcode_Variables_Variable[]
    */
    public function getGroupedByHash()
    {
        return $this->sortVariables($this->variables);
    }
    
   /**
    * Retrieves all variables, grouped by their name. 
    * 
    * @return Mailcode_Variables_Variable[]
    */
    public function getGroupedByName()
    {
        $entries = array();
        
        foreach($this->variables as $variable)
        {
            $entries[$variable->getFullName()] = $variable;
        }
        
        return $this->sortVariables($entries);
    }
    
   /**
    * Retrieves the full names of the variables that are present in the collection.
    * @return string[]
    */
    public function getNames() : array
    {
        $result = array();
        
        foreach($this->variables as $variable)
        {
            $name = $variable->getFullName();
            
            if(!in_array($name, $result))
            {
                $result[] = $name;
            }
        }
        
        return $result;
    }
    
   /**
    * Takes a list of variables and sorts them, throwing away
    * the source array's keys.
    * 
    * @param array $entries
    * @return Mailcode_Variables_Variable[]
    */
    protected function sortVariables(array $entries)
    {
        $result = array_values($entries);
        
        usort($entries, function(Mailcode_Variables_Variable $a, Mailcode_Variables_Variable $b)
        {
            return strnatcasecmp($a->getFullName(), $b->getFullName());
        });
        
        return $result;
    }
}
