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

use AppUtils\OperationResult;

abstract class Mailcode_Commands_Command
    implements
    Mailcode_Interfaces_Commands_Command,
    Mailcode_Interfaces_Commands_Validation_EmptyParams,
    Mailcode_Interfaces_Commands_Validation_ParamKeywords,
    Mailcode_Interfaces_Commands_Validation_ParseParams,
    Mailcode_Interfaces_Commands_Validation_TypeSupported,
    Mailcode_Interfaces_Commands_Validation_TypeUnsupported
{
    use Mailcode_Traits_Commands_Validation_EmptyParams;
    use Mailcode_Traits_Commands_Validation_ParamKeywords;
    use Mailcode_Traits_Commands_Validation_ParseParams;
    use Mailcode_Traits_Commands_Validation_TypeSupported;
    use Mailcode_Traits_Commands_Validation_TypeUnsupported;

    const ERROR_NON_DUMMY_OPERATION = 46001;
    const ERROR_NO_VALIDATION_RESULT_AVAILABLE = 46002;
    const ERROR_MISSING_VALIDATION_METHOD = 46003;
    const ERROR_MISSING_TYPE_INTERFACE = 46004;
    const ERROR_LOGIC_COMMANDS_NOT_SUPPORTED = 46005;
    const ERROR_URL_ENCODING_NOT_SUPPORTED = 46006;
    
    const VALIDATION_MISSING_PARAMETERS = 48301;
    const VALIDATION_ADDONS_NOT_SUPPORTED = 48302;
    const VALIDATION_ADDON_NOT_SUPPORTED = 48303;
    const VALIDATION_UNKNOWN_COMMAND_NAME = 48304;
    const VALIDATION_INVALID_PARAMS_STATEMENT = 48305;

    const META_URL_ENCODING = 'url_encoding';

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
    * @var OperationResult
    */
    protected $validationResult = null;
    
   /**
    * @var Mailcode
    */
    protected $mailcode;
    
   /**
    * @var Mailcode_Parser_Statement
    */
    protected $params;

   /**
    * @var string[] 
    */
    protected $validations = array(
        Mailcode_Interfaces_Commands_Validation_EmptyParams::VALIDATION_NAME,
        Mailcode_Interfaces_Commands_Validation_ParamKeywords::VALIDATION_NAME,
        Mailcode_Interfaces_Commands_Validation_ParseParams::VALIDATION_NAME,
        Mailcode_Interfaces_Commands_Validation_TypeSupported::VALIDATION_NAME,
        Mailcode_Interfaces_Commands_Validation_TypeUnsupported::VALIDATION_NAME
    );
    
   /**
    * @var string
    */
    protected $comment = '';
    
   /**
    * @var Mailcode_Commands_LogicKeywords|NULL
    */
    protected $logicKeywords;
    
   /**
    * @var Mailcode_Parser_Statement_Validator
    */
    protected $validator;
    
   /**
    * @var boolean
    */
    private $validated = false;

    /**
     * Collection of parameters for the translation backend.
     * @var array<string,mixed>
     */
    protected $translationParams = array();

    /**
     * @var Mailcode_Commands_Command|NULL
     */
    protected $parent = null;

    /**
     * @var bool
     */
    private $nestingValidated = false;

    public function __construct(string $type='', string $paramsString='', string $matchedText='')
    {
        $this->type = $type;
        $this->paramsString = html_entity_decode($paramsString);
        $this->matchedText = $matchedText;
        $this->mailcode = Mailcode::create();
        $this->validationResult = new OperationResult($this);
        
        $this->init();
    }
    
    protected function init() : void
    {
        
    }

   /**
    * Sets the command's parent opening command, if any.
    * NOTE: This is set automatically by the parser, and
    * should not be called manually.
    *
    * @param Mailcode_Commands_Command $command
    */
    public function setParent(Mailcode_Commands_Command $command) : void
    {
        $this->parent = $command;
    }

    public function hasParent() : bool
    {
        return isset($this->parent);
    }

    public function getParent() : ?Mailcode_Commands_Command
    {
        return $this->parent;
    }

    public function getID() : string
    {
        // account for commands with types: If_Variable should still return If.
        $base = str_replace(Mailcode_Commands_Command::class.'_', '', get_class($this));
        $tokens = explode('_', $base);
        return array_shift($tokens);
    }

    public function setComment(string $comment) : Mailcode_Commands_Command
    {
        $this->comment = $comment;
        
        return $this;
    }

    public function getComment() : string
    {
        return $this->comment;
    }

    public function isDummy() : bool
    {
        return $this->type === '__dummy';
    }

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
    
    protected function validate() : OperationResult
    {
        if(!$this->validated)
        {
            $this->requireNonDummy();
            $this->validateSyntax();

            $this->validated = true;
        }
        
        return $this->validationResult;
    }
    
    public function getValidationResult() :  OperationResult
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
        $validations = $this->resolveValidations();

        foreach($validations as $validation)
        {
            // break off at the first validation issue
            if(!$this->validateSyntaxMethod($validation))
            {
                return;
            }
        }
    }

    /**
     * @return string[]
     */
    protected function resolveValidations() : array
    {
        return array_merge($this->validations, $this->getValidations());
    }
    
    protected function validateSyntaxMethod(string $validation) : bool
    {
        $method = 'validateSyntax_'.$validation;
        
        if(!method_exists($this, $method))
        {
            throw new Mailcode_Exception(
                'Missing validation method ['.$validation.']',
                sprintf(
                    'The method [%s] is missing from class [%s].',
                    $method,
                    get_class($this)
                ),
                self::ERROR_MISSING_VALIDATION_METHOD
            );
        }
        
        $this->$method();
        
        return $this->validationResult->isValid();
    }
    
   /**
    * @return string[]
    */
    abstract protected function getValidations() : array;

    protected function _validateNesting() : void
    {

    }

    public function validateNesting() : OperationResult
    {
        if($this->nestingValidated)
        {
            return $this->validationResult;
        }

        $this->nestingValidated = true;

        $this->_validateNesting();

        return $this->validationResult;
    }
    
    public function hasFreeformParameters() : bool
    {
        return false;
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

    abstract public function supportsURLEncoding() : bool;

    abstract public function supportsLogicKeywords() : bool;
    
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
        $normalizer = new Mailcode_Commands_Normalizer($this);
        
        return $normalizer->normalize();
    }

    public function getSupportedTypes() : array
    {
        return array();
    }

    public function getVariables() : Mailcode_Variables_Collection_Regular
    {
        return Mailcode::create()->findVariables($this->paramsString, $this);
    }
    
    public function __toString()
    {
        return $this->getNormalized();
    }
    
    public function getLogicKeywords() : Mailcode_Commands_LogicKeywords
    {
        if($this->supportsLogicKeywords() && isset($this->logicKeywords))
        {
            return $this->logicKeywords;
        }
        
        throw new Mailcode_Exception(
            'Logic keywords are not supported',
            'Cannot retrieve the logic keywords instance: it is only available for commands supporting logic commands.',
            self::ERROR_LOGIC_COMMANDS_NOT_SUPPORTED
        );
    }

    public function setTranslationParam(string $name, $value)
    {
        $this->translationParams[$name] = $value;
        return $this;
    }

    public function getTranslationParam(string $name)
    {
        if(isset($this->translationParams[$name]))
        {
            return $this->translationParams[$name];
        }

        return null;
    }

    public function setURLEncoding(bool $encoding=true)
    {
        $this->requireURLEncoding();

        $this->params->getInfo()->setKeywordEnabled(Mailcode_Commands_Keywords::TYPE_URLENCODE, $encoding);

        return $this;
    }

    public function setURLDecoding(bool $decode=true)
    {
        $this->requireURLEncoding();

        $this->params->getInfo()->setKeywordEnabled(Mailcode_Commands_Keywords::TYPE_URLDECODE, $decode);

        return $this;
    }

    protected function requireURLEncoding() : void
    {
        if($this->supportsURLEncoding()) {
            return;
        }

        throw new Mailcode_Exception(
            'Command does not support URL encoding.',
            sprintf(
                'The command [%s] cannot use URL encoding.',
                get_class($this)
            ),
            self::ERROR_URL_ENCODING_NOT_SUPPORTED
        );
    }

    public function isURLEncoded() : bool
    {
        return $this->params->getInfo()->hasKeyword(Mailcode_Commands_Keywords::TYPE_URLENCODE);
    }

    public function isURLDecoded() : bool
    {
        return $this->params->getInfo()->hasKeyword(Mailcode_Commands_Keywords::TYPE_URLDECODE);
    }
}
