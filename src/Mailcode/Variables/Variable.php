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
    const ERROR_MISSING_VALIDATION_METHOD = 48601;
    
    const VALIDATION_ERROR_PATH_NUMERIC = 48201;
    const VALIDATION_ERROR_NAME_NUMERIC = 48202;
    const VALIDATION_ERROR_PATH_UNDERSCORE = 48203;
    const VALIDATION_ERROR_NAME_UNDERSCORE = 48204;
    
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
        return $this->getValidationResult()->isValid();
    }
    
    public function getValidationResult() : OperationResult
    {
        if(isset($this->validationResult))
        {
            return $this->validationResult;
        }
        
        $this->validationResult = new OperationResult($this);
        
        $this->validate();
        
        return $this->validationResult;
    }

    protected $validations = array(
        'number_path',
        'number_name',
        'underscore_path',
        'underscore_name'
    );
    
    protected function validate() : void
    {
        foreach($this->validations as $validation)
        {
            $method = 'validate_'.$validation;
            
            if(!method_exists($this, $method))
            {
                throw new Mailcode_Exception(
                    'Unknown validation method',
                    sprintf(
                        'The method [%s] is missing in class [%s].',
                        $method,
                        get_class($this)
                    ),
                    self::ERROR_MISSING_VALIDATION_METHOD
                );
            }
            
            $this->$method();
            
            if(!$this->validationResult->isValid())
            {
                return;
            }
        }
    }
    
    protected function validate_number_path()
    {
        $this->validateNumber($this->path, self::VALIDATION_ERROR_PATH_NUMERIC);
    }
    
    protected function validate_number_name()
    {
        $this->validateNumber($this->name, self::VALIDATION_ERROR_NAME_NUMERIC);
    }
    
    protected function validate_underscore_path()
    {
        $this->validateUnderscore($this->path, self::VALIDATION_ERROR_PATH_UNDERSCORE);
    }
    
    protected function validate_underscore_name()
    {
        $this->validateUnderscore($this->name, self::VALIDATION_ERROR_NAME_UNDERSCORE);
    }
    
    protected function validateNumber(string $value, int $errorCode)
    {
        if(!is_numeric(substr($value, 0, 1)))
        {
            return;
        }
        
        $this->validationResult->makeError(
            t(
                'The %1$s in variable %2$s must begin with a letter.',
                $value,
                $this->getFullName()
            ),
            $errorCode
        );
    }
    
    protected function validateUnderscore(string $value, int $errorCode)
    {
        $length = strlen($value);
        
        // trimming underscores does not change the length: no underscores at start or end of string.
        if(strlen(trim($value, '_')) == $length)
        {
            return;
        }
        
        $this->validationResult->makeError(
            t(
                'The %1$s in variable %2$s may not start or end with an underscore.',
                $value,
                $this->getFullName()
            ),
            $errorCode
        );
    }
}
