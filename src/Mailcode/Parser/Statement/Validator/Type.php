<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Validator_Type} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Parser_Statement_Validator_Type
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Abstract base class for individual command validations. 
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Parser_Statement_Validator_Type
{
   /**
    * @var Mailcode_Parser_Statement
    */
    protected $statement;
    
    /**
     * @var Mailcode_Parser_Statement_Info
     */
    protected $params;
    
   /**
    * @var boolean
    */
    private $valid = true;
    
   /**
    * @var boolean
    */
    private $validated = false;
    
    public function __construct(Mailcode_Parser_Statement $statement)
    {
        $this->statement = $statement;
        $this->params = $statement->getInfo();
        
        $this->init();
    }
    
    protected function init() : void
    {
        
    }
    
   /**
    * @return $this
    */
    public function validate() : Mailcode_Parser_Statement_Validator_Type
    {
        if(!$this->validated)
        {
            $this->valid = $this->_validate();
            $this->validated = true;
        }
        
        return $this;
    }
    
    abstract protected function _validate() : bool;
    
    public function isValid() : bool
    {
        $this->validate();
        
        return $this->valid;
    }
}
