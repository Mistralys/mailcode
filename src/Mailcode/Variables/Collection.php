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
    * @var Mailcode_Variables_Variable[]
    */
    protected array $variables = array();
    
    public function __construct()
    {
        $this->init();
    }
    
    protected function init() : void
    {
        
    }
    
    public function add(Mailcode_Variables_Variable $variable) : Mailcode_Variables_Collection
    {
        $this->variables[] = $variable;
        
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
        return dollarize($fullName);
    }
    
   /**
    * Retrieves all variables, grouped by their hash - meaning
    * unique matched strings.
    * 
    * @return Mailcode_Variables_Variable[]
    */
    public function getGroupedByHash() : array
    {
        $entries = array();
        
        foreach($this->variables as $variable)
        {
            $entries[$variable->getHash()] = $variable;
        }
        
        return $this->sortVariables($entries);
    }
    
   /**
    * Retrieves all variables, grouped by their name. 
    * 
    * @return Mailcode_Variables_Variable[]
    */
    public function getGroupedByName() : array
    {
        $entries = array();
        
        foreach($this->variables as $variable)
        {
            $entries[$variable->getFullName()] = $variable;
        }
        
        return $this->sortVariables($entries);
    }

    /**
     * Retrieves all variables, grouped by the unique commands
     * they are tied to.
     *
     * @return Mailcode_Variables_Variable[]
     * @throws Mailcode_Exception
     */
    public function getGroupedByUniqueName() : array
    {
        $entries = array();

        foreach($this->variables as $variable)
        {
            $entries[$variable->getUniqueName()] = $variable;
        }

        return $this->sortVariables($entries);
    }
    
   /**
    * Retrieves all variables, in the order they were addded.
    * @return Mailcode_Variables_Variable[]
    */
    public function getAll()
    {
        return $this->variables;
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
    * @param Mailcode_Variables_Variable[] $entries
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

   /**
    *  Merges the variables collection with the target collection
    *  by inheriting all that collection's variables.
    *  
    * @param Mailcode_Variables_Collection $collection
    * @return Mailcode_Variables_Collection
    */
    public function mergeWith(Mailcode_Variables_Collection $collection) : Mailcode_Variables_Collection
    {
        $variables = $collection->getGroupedByHash();
        
        foreach($variables as $variable)
        {
            $this->add($variable);
        }
        
        return $this;
    }

    public function getFirst() : ?Mailcode_Variables_Variable
    {
        $variables = $this->getAll();
        
        if(!empty($variables))
        {
            return array_shift($variables);
        }
        
        return null;
    }
}
