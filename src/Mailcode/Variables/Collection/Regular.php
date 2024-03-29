<?php
/**
 * File containing the {@see Mailcode_Variables_Collection_Regular} class.
 *
 * @package Mailcode
 * @subpackage Variables
 * @see Mailcode_Variables_Collection_Regular
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
class Mailcode_Variables_Collection_Regular extends Mailcode_Variables_Collection
{
   /**
    * @var Mailcode_Variables_Collection_Invalid
    */
    protected Mailcode_Variables_Collection_Invalid $invalid;

    protected function init() : void
    {
        $this->invalid = new Mailcode_Variables_Collection_Invalid();
    }
    
    public function add(Mailcode_Variables_Variable $variable) : Mailcode_Variables_Collection
    {
        if(!$variable->isValid())
        {
            return $this->addInvalid($variable);
        }
        
        return parent::add($variable);
    }

    protected function addInvalid(Mailcode_Variables_Variable $variable) : Mailcode_Variables_Collection_Regular
    {
        $this->invalid->add($variable);
        
        return $this;
    }

   /**
    * Whether any of the variables in the collection are invalid.
    * 
    * @return bool
    */
    public function hasInvalid() : bool
    {
        return $this->invalid->hasVariables();
    }
    
   /**
    * Retrieves the collection of invalid variables, if any.
    * Behaves like a variables collection.
    * 
    * @return Mailcode_Variables_Collection_Invalid
    */
    public function getInvalid() : Mailcode_Variables_Collection_Invalid
    {
        return $this->invalid;
    }
    
   /**
    * @return Mailcode_Variables_Collection_Regular
    */
    public function mergeWith(Mailcode_Variables_Collection $collection) : Mailcode_Variables_Collection
    {
        parent::mergeWith($collection);
        
        // also inherit any invalid variables
        if($collection instanceof Mailcode_Variables_Collection_Regular)
        {
            $invalid = $collection->getInvalid();
            
            $this->invalid->mergeWith($invalid);
        }
        
        return $this;
    }
}
