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
abstract class Mailcode_Parser_Statement_Tokenizer_Token
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
        return $this instanceof Mailcode_Parser_Statement_Tokenizer_Type_Value;
    }
}
