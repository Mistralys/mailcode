<?php
/**
 * File containing the {@see Mailcode_Variables_Variable} class.
 *
 * @package Mailcode
 * @subpackage Variables
 * @see Mailcode_Variables_Variable
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Simple container for a single variable name occurrence: used
 * to store information on variable names used in commands, with
 * the possibility to retrieve the actual matched text among other
 * things.
 *
 * @package Mailcode
 * @subpackage Variables
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Variables_Variable
{
    const VALIDATION_ERROR_PATH_NUMERIC = 48201;
    
    const VALIDATION_ERROR_NAME_NUMERIC = 48202;
    
   /**
    * @var string
    */
    protected $path;
    
   /**
    * @var string
    */
    protected $name;
    
   /**
    * @var string
    */
    protected $matchedText;
    
   /**
    * @var string
    */
    protected $hash = '';
    
   /**
    * @var OperationResult
    */
    protected $validationResult = null;
    
    public function __construct(string $path, string $name, string $matchedText)
    {
        $this->path = $path;
        $this->name = $name;
        $this->matchedText = $matchedText;
    }
    
    public function getFullName() : string
    {
        return '$'.$this->path.'.'.$this->name;
    }
    
    public function getName() : string
    {
        return $this->name;
    }
    
    public function getPath() : string
    {
        return $this->path;
    }
    
    public function getMatchedText() : string
    {
        return $this->matchedText;
    }
    
    public function getHash() : string
    {
        if(empty($this->hash))
        {
            $this->hash = md5($this->matchedText);
        }
        
        return $this->hash;
    }
    
    public function isValid() : bool
    {
        $this->validate();
        
        return $this->validationResult->isValid();
    }
    
    public function getValidationResult() : OperationResult
    {
        $this->validate();
        
        return $this->validationResult;
    }
    
    protected function validate() : void
    {
        if(isset($this->validationResult))
        {
            return;
        }
        
        $this->validationResult = new OperationResult($this);
        
        if(is_numeric(substr($this->path, 0, 1))) 
        {
            $this->validationResult->makeError(
                t(
                    'The path %1$s of variable %2$s must begin with a letter.', 
                    $this->path, 
                    $this->getFullName()
                ),
                self::VALIDATION_ERROR_PATH_NUMERIC
            );
        }
        
        if(is_numeric(substr($this->name, 0, 1)))
        {
            $this->validationResult->makeError(
                t(
                    'The name %1$s of variable %2$s must begin with a letter.',
                    $this->name,
                    $this->getFullName()
                ),
                self::VALIDATION_ERROR_NAME_NUMERIC
            );
        }
    }
}
