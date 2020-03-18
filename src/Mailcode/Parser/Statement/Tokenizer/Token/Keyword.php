<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token_Keyword} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token_Keyword
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Token representing a special keyword.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Token_Keyword extends Mailcode_Parser_Statement_Tokenizer_Token
{
    public function getNormalized(): string
    {
        return $this->getMatchedText();
    }
    
    public function getKeyword() : string
    {
        return $this->getMatchedText();
    }
    
    public function isForIn() : bool
    {
        return $this->getKeyword() === 'in:';
    }
    
    public function isInsensitive() : bool
    {
        return $this->getKeyword() === 'insensitive:';
    }
}
