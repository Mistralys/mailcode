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
   /**
    * @var string
    */
    protected $tokenID;
    
   /**
    * @var string
    */
    protected $matchedText;
    
   /**
    * @var mixed
    */
    protected $subject;
    
   /**
    * @param string $tokenID
    * @param string $matchedText
    * @param mixed $subject
    */
    public function __construct(string $tokenID, string $matchedText, $subject=null)
    {
        $this->tokenID = $tokenID;
        $this->matchedText = $matchedText;
        $this->subject = $subject;
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
    
    public function getID() : string
    {
        return $this->tokenID;
    }
    
    public function getMatchedText() : string
    {
        return $this->matchedText;
    }
    
    protected function restoreQuotes(string $subject) : string
    {
        return str_replace('__QUOTE__', '\"', $subject);
    }

    abstract public function getNormalized() : string;
    
    final public function isValue() : bool
    {
        return $this instanceof Mailcode_Parser_Statement_Tokenizer_ValueInterface;
    }
}
