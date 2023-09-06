<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Abstract base class for a single token in a statement.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Parser_Statement_Tokenizer_Token implements Mailcode_Parser_Statement_Tokenizer_TypeInterface
{
    protected string $tokenID;
    protected ?Mailcode_Parser_Statement_Tokenizer_Token_ParamName $nameToken = null;
    protected string $matchedText;
    private ?Mailcode_Commands_Command $sourceCommand;

   /**
    * @var mixed
    */
    protected $subject;

    /**
     * @param string $tokenID
     * @param string $matchedText
     * @param mixed $subject
     * @param Mailcode_Commands_Command|null $sourceCommand
     */
    public function __construct(string $tokenID, string $matchedText, $subject=null, ?Mailcode_Commands_Command $sourceCommand=null)
    {
        $this->tokenID = $tokenID;
        $this->matchedText = $matchedText;
        $this->subject = $subject;
        $this->sourceCommand = $sourceCommand;

        $this->init();
    }

    protected function init() : void
    {

    }

    /**
     * Gets the name of the parameter, if specified.
     * Returns an empty string otherwise.
     *
     * NOTE: To set the name, use the command's statement
     * instead, and call the {@see Mailcode_Parser_Statement_Info::setParamName()}
     * method.
     *
     * @return string
     */
    public function getName() : string
    {
        if(isset($this->nameToken)) {
            return $this->nameToken->getParamName();
        }

        return '';
    }

    /**
     * @param Mailcode_Parser_Statement_Tokenizer_Token_ParamName $token
     * @return $this
     */
    public function registerNameToken(Mailcode_Parser_Statement_Tokenizer_Token_ParamName $token) : self
    {
        $this->nameToken = $token;
        return $this;
    }

    abstract public function hasSpacing() : bool;

    /**
     * @return Mailcode_Commands_Command|null
     */
    public function getSourceCommand(): ?Mailcode_Commands_Command
    {
        return $this->sourceCommand;
    }
    
   /**
    * The ID of the type. i.e. the class name ("Keyword", "StringLiteral").
    * @return string
    */
    public function getTypeID() : string
    {
        $parts = explode('_', get_class($this));
        return array_pop($parts);
    }
    
   /**
    * Retrieves a unique ID of the token.
    * @return string  
    */
    public function getID() : string
    {
        return $this->tokenID;
    }
    
    public function getMatchedText() : string
    {
        return $this->matchedText;
    }

    abstract public function getNormalized() : string;
    
    final public function isValue() : bool
    {
        return $this instanceof Mailcode_Parser_Statement_Tokenizer_ValueInterface;
    }
}
