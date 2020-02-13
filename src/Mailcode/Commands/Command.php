<?php
/**
 * File containing the {@see Mailcode_Commands_Command} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Base command class with the common functionality for all commands.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Commands_Command
{
    const ERROR_NON_DUMMY_OPERATION = 46001;
    
    const ERROR_NO_VALIDATION_RESULT_AVAILABLE = 46002;
    
    const VALIDATION_MISSING_PARAMETERS = 1;
    
    const VALIDATION_ADDONS_NOT_SUPPORTED = 2;
    
    const VALIDATION_ADDON_NOT_SUPPORTED = 3;
    
    const VALIDATION_UNKNOWN_COMMAND_NAME = 4;
    
   /**
    * @var string
    */
    protected $type = '';

   /**
    * @var string
    */
    protected $paramsString = '';
    
   /**
    * @var string
    */
    protected $matchedText = '';

   /**
    * @var string
    */
    protected $hash = '';
    
   /**
    * @var \AppUtils\OperationResult
    */
    protected $validationResult = null;
    
    public function __construct(string $type='', string $paramsString='', string $matchedText='')
    {
        $this->type = $type;
        $this->paramsString = $paramsString;
        $this->matchedText = $matchedText;
    }
    
   /**
    * @return string The ID of the command = the name of the command class file.
    */
    public function getID() : string
    {
        return str_replace('Mailcode_Commands_Command_', '', get_class($this));
    }
    
   /**
    * Checks whether this is a dummy command, which is only
    * used to access information on the command type. It cannot
    * be used as an actual live command.
    * 
    * @return bool
    */
    public function isDummy() : bool
    {
        return $this->type === '__dummy';
    }
    
   /**
    * Retrieves a hash of the actual matched command string,
    * which is used in collections to detect duplicate commands.
    * 
    * @return string
    */
    public function getHash() : string
    {
        $this->requireNonDummy();
        
        if($this->hash === '') {
            $this->hash = md5($this->matchedText);
        }
        
        return $this->hash;
    }
    
    protected function requireNonDummy() : void
    {
        if(!$this->isDummy())
        {
            return;
        }
        
        throw new Mailcode_Exception(
            'Operation not allowed with dummy commands',
            null,
            self::ERROR_NON_DUMMY_OPERATION
        );
    }
    
    public function isValid() : bool
    {
        return $this->validate()->isValid();
    }
    
    protected function validate() : \AppUtils\OperationResult
    {
        $this->requireNonDummy();
        
        if(isset($this->validationResult)) 
        {
            return $this->validationResult;
        }
        
        $this->validationResult = new \AppUtils\OperationResult($this);

        $this->validateSyntax();
        
        $this->_validate();
        
        return $this->validationResult;
    }
    
    public function getValidationResult() :  \AppUtils\OperationResult
    {
        if(isset($this->validationResult)) 
        {
            return $this->validationResult;
        }
        
        throw new Mailcode_Exception(
            'No validation result available',
            'The command has no validation error, the validation result cannot be accessed.',
            self::ERROR_NO_VALIDATION_RESULT_AVAILABLE
        );
    }
    
    protected function validateSyntax()
    {
        if($this->requiresParameters() && empty($this->paramsString))
        {
            $this->validationResult->makeError(
                t('Parameters have to be specified.'),
                self::VALIDATION_MISSING_PARAMETERS
            );
            return;
        }
        
        if($this->supportsType() && !empty($this->type))
        {
            $types = $this->getSupportedTypes();

            if(!in_array($this->type, $types))
            {
                $this->validationResult->makeError(
                    t('The command addon %1$s is not supported.', $this->type).' '.
                    t('Valid addons are %1$s.', implode(', ', $types)),
                    self::VALIDATION_ADDON_NOT_SUPPORTED
                );
                
                return;
            }
        }
        
        if(!$this->supportsType() && !empty($this->type))
        {
            $this->validationResult->makeError(
                t('Command addons are not supported (the %1$s part).', $this->type),
                self::VALIDATION_ADDONS_NOT_SUPPORTED
            );
            
            return;
        }
    }
    
    public function getMatchedText() : string
    {
        return $this->matchedText;
    }
    
    abstract protected function _validate() : void;
    
    abstract public function getName() : string;
    
    abstract public function getLabel() : string;
    
    abstract public function requiresParameters() : bool;
    
    abstract public function supportsType() : bool;
    
    public function getSupportedTypes() : array
    {
        return array();
    }
}
