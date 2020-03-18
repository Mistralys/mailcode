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
    const ERROR_MISSING_VALIDATION_METHOD = 46003;
    const ERROR_MISSING_TYPE_INTERFACE = 46004;
    
    const VALIDATION_MISSING_PARAMETERS = 48301;
    const VALIDATION_ADDONS_NOT_SUPPORTED = 48302;
    const VALIDATION_ADDON_NOT_SUPPORTED = 48303;
    const VALIDATION_UNKNOWN_COMMAND_NAME = 48304;
    const VALIDATION_INVALID_PARAMS_STATEMENT = 48305;

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
    
   /**
    * @var \Mailcode\Mailcode
    */
    protected $mailcode;
    
   /**
    * @var \Mailcode\Mailcode_Parser_Statement
    */
    protected $params;

   /**
    * @var string[] 
    */
    protected $validations = array(
        'params',
        'type_supported',
        'type_unsupported'
    );
    
   /**
    * @var string
    */
    protected $comment = '';
    
    public function __construct(string $type='', string $paramsString='', string $matchedText='')
    {
        $this->type = $type;
        $this->paramsString = html_entity_decode($paramsString);
        $this->matchedText = $matchedText;
        $this->mailcode = Mailcode::create();
        
        $this->init();
    }
    
    protected function init() : void
    {
        
    }
    
   /**
    * @return string The ID of the command = the name of the command class file.
    */
    public function getID() : string
    {
        // account for commands with types: If_Variable should still return If.
        $base = str_replace(Mailcode_Commands_Command::class.'_', '', get_class($this));
        $tokens = explode('_', $base);
        return array_shift($tokens);
    }
    
   /**
    * Sets an optional comment that is not used anywhere, but
    * can be used by the application to track why a command is
    * used somewhere. 
    * 
    * @param string $comment
    * @return Mailcode_Commands_Command
    */
    public function setComment(string $comment) : Mailcode_Commands_Command
    {
        $this->comment = $comment;
        
        return $this;
    }
    
   /**
    * Retrieves the previously set comment, if any.
    * 
    * @return string
    */
    public function getComment() : string
    {
        return $this->comment;
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
    
    protected function validateSyntax() : void
    {
        $validations = array_merge($this->validations, $this->getValidations());
        
        foreach($validations as $validation)
        {
            $method = 'validateSyntax_'.$validation;
            
            if(!method_exists($this, $method))
            {
                throw new Mailcode_Exception(
                    'Missing validation method',
                    sprintf(
                        'The method [%s] is missing from class [%s].',
                        $method,
                        get_class($this)
                    ),
                    self::ERROR_MISSING_VALIDATION_METHOD
                );
            }
            
            $this->$method();
            
            // break off at the first validation issue
            if(!$this->validationResult->isValid())
            {
                return;
            }
        }
    }
    
   /**
    * @return string[]
    */
    abstract protected function getValidations() : array;
    
    protected function validateSyntax_params() : void
    {
        if(!$this->requiresParameters())
        {
            return;
        }
        
        if(empty($this->paramsString))
        {
            $this->validationResult->makeError(
                t('Parameters have to be specified.'),
                self::VALIDATION_MISSING_PARAMETERS
            );
            return;
        }
        
        $this->params = $this->mailcode->getParser()->createStatement($this->paramsString);
        
        if(!$this->params->isValid())
        {
            $error = $this->params->getValidationResult();
            
            $this->validationResult->makeError(
                t('Invalid parameters:').' '.$error->getErrorMessage(), 
                self::VALIDATION_INVALID_PARAMS_STATEMENT
            );
        }
    }
    
    protected function validateSyntax_type_supported() : void
    {
        if(!$this->supportsType() || empty($this->type))
        {
            return;
        }
        
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
    
    protected function validateSyntax_type_unsupported() : void
    {
        if($this->supportsType() || empty($this->type))
        {
            return;
        }
        
        $this->validationResult->makeError(
            t('Command addons are not supported (the %1$s part).', $this->type),
            self::VALIDATION_ADDONS_NOT_SUPPORTED
        );
    }
    
    public function hasType() : bool
    {
        return $this->supportsType() && !empty($this->type);
    }
    
    public function getType() : string
    {
        if($this->supportsType())
        {
            return $this->type;
        }
        
        return '';
    }
    
    public function hasParameters() : bool
    {
        return $this->requiresParameters() && !empty($this->paramsString);
    }
    
    public function getMatchedText() : string
    {
        return $this->matchedText;
    }
    
    public function getHighlighted() : string
    {
        if(!$this->isValid())
        {
            return '';
        }
        
        $highlighter = new Mailcode_Commands_Highlighter($this);
        return $highlighter->highlight();
    }
    
    public function getParamsString() : string
    {
        if($this->requiresParameters())
        {
            return $this->paramsString;
        }
        
        return '';
    }
    
    public function getParams() : ?Mailcode_Parser_Statement
    {
        return $this->params;
    }
    
    abstract public function getName() : string;
    
    abstract public function getLabel() : string;
    
    abstract public function requiresParameters() : bool;
    
    abstract public function supportsType() : bool;
    
    abstract public function generatesContent() : bool;

    abstract public function getDefaultType() : string;
    
    public final function getCommandType() : string
    {
        if($this instanceof Mailcode_Commands_Command_Type_Closing)
        {
            return 'Closing';
        }
        
        if($this instanceof Mailcode_Commands_Command_Type_Opening)
        {
            return 'Opening';
        }
        
        if($this instanceof Mailcode_Commands_Command_Type_Sibling)
        {
            return 'Sibling';
        }
        
        if($this instanceof Mailcode_Commands_Command_Type_Standalone)
        {
            return 'Standalone';
        }
        
        throw new Mailcode_Exception(
            'Invalid command type',
            sprintf(
                'The command [%s] does not implement any of the type interfaces.',
                get_class($this)
            ),
            self::ERROR_MISSING_TYPE_INTERFACE
        );
    }
    
    public function getNormalized() : string
    {
        if(!$this->isValid())
        {
            return '';
        }
        
        $parts = array();
        $parts[] = '{'.$this->getName();
        
        if($this->supportsType() && $this->hasType())
        {
            $parts[] = ' '.$this->getType();
        }
        
        if($this->requiresParameters() && isset($this->params))
        {
            $parts[] = ': ';
            $parts[] = $this->params->getNormalized();
        }
        
        $parts[] = '}';
        
        return implode('', $parts);
    }
    
   /**
    * Retrieves the names of all the command's supported types: the part
    * between the command name and the colon. Example: {command type: params}.
    * 
    * @return string[]
    */
    public function getSupportedTypes() : array
    {
        return array();
    }
    
   /**
    * Retrieves all variable names used in the command.
    * 
    * @return Mailcode_Variables_Collection_Regular
    */
    public function getVariables() : Mailcode_Variables_Collection_Regular
    {
        return Mailcode::create()->findVariables($this->paramsString);
    }
    
    public function __toString()
    {
        return $this->getNormalized();
    }
}
