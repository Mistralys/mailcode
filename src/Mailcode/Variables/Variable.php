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
    public const ERROR_MISSING_VALIDATION_METHOD = 48601;
    
    public const VALIDATION_ERROR_PATH_NUMERIC = 48201;
    public const VALIDATION_ERROR_NAME_NUMERIC = 48202;
    public const VALIDATION_ERROR_PATH_UNDERSCORE = 48203;
    public const VALIDATION_ERROR_NAME_UNDERSCORE = 48204;
    
   /**
    * @var string
    */
    protected string $path;
    
   /**
    * @var string
    */
    protected string $name;
    
   /**
    * @var string
    */
    protected string $matchedText;
    
   /**
    * @var string
    */
    protected string $hash = '';
    
   /**
    * @var OperationResult|NULL
    */
    protected ?OperationResult $validationResult = null;
    
   /**
    * @var array<string>
    */
    protected $validations = array(
        'number_path',
        'number_name',
        'underscore_path',
        'underscore_name'
    );

    /**
     * @var Mailcode_Commands_Command|null
     */
    private ?Mailcode_Commands_Command $command;

    public function __construct(string $path, string $name, string $matchedText, ?Mailcode_Commands_Command $sourceCommand=null)
    {
        $this->path = $path;
        $this->name = $name;
        $this->matchedText = $matchedText;
        $this->command = $sourceCommand;
    }

    /**
     * Retrieves the source command of the variable, if any.
     * The string parser automatically sets the variable's
     * command, but if it was created via other means it will
     * not have one.
     *
     * @return Mailcode_Commands_Command|null
     */
    public function getSourceCommand() : ?Mailcode_Commands_Command
    {
        return $this->command;
    }
    
    public function getFullName() : string
    {
        if(empty($this->path))
        {
            return '$'.$this->name;
        }
        
        return '$'.$this->path.'.'.$this->name;
    }

    /**
     * Retrieves the name of the variable with a suffix that
     * uniquely identifies it together with the command it
     * came from. Used for grouping identical variables.
     *
     * @return string
     * @throws Mailcode_Exception
     */
    public function getUniqueName() : string
    {
        if(isset($this->command))
        {
            return $this->getFullName().'/'.$this->command->getHash();
        }

        return $this->getFullName();
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

    /**
     * @return OperationResult
     * @throws Mailcode_Exception
     */
    public function getValidationResult() : OperationResult
    {
        if(isset($this->validationResult))
        {
            return $this->validationResult;
        }

        $result = new OperationResult($this);
        $this->validationResult = $result;
        $this->validate($result);

        return $result;
    }

    protected function validate(OperationResult $result) : void
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

            /*
            if(!$result->isValid())
            {
                return;
            }*/
        }
    }
    
    protected function validate_number_path() : void
    {
        $this->validateNumber($this->path, self::VALIDATION_ERROR_PATH_NUMERIC);
    }
    
    protected function validate_number_name() : void
    {
        $this->validateNumber($this->name, self::VALIDATION_ERROR_NAME_NUMERIC);
    }
    
    protected function validate_underscore_path() : void
    {
        $this->validateUnderscore($this->path, self::VALIDATION_ERROR_PATH_UNDERSCORE);
    }
    
    protected function validate_underscore_name() : void
    {
        $this->validateUnderscore($this->name, self::VALIDATION_ERROR_NAME_UNDERSCORE);
    }
    
    protected function validateNumber(string $value, int $errorCode) : void
    {
        if(!is_numeric(substr($value, 0, 1)))
        {
            return;
        }
        
        $this->getValidationResult()->makeError(
            t(
                'The %1$s in variable %2$s must begin with a letter.',
                $value,
                $this->getFullName()
            ),
            $errorCode
        );
    }
    
    protected function validateUnderscore(string $value, int $errorCode) : void
    {
        // allow empty paths
        if(empty($value))
        {
            return;
        }
        
        $length = strlen($value);
        
        // trimming underscores does not change the length: no underscores at start or end of string.
        if(strlen(trim($value, '_')) === $length)
        {
            return;
        }
        
        $this->getValidationResult()->makeError(
            t(
                'The %1$s in variable %2$s may not start or end with an underscore.',
                $value,
                $this->getFullName()
            ),
            $errorCode
        );
    }
}
