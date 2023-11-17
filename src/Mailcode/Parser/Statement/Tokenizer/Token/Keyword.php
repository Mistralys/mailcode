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

    public function hasSpacing(): bool
    {
        return true;
    }

    /**
     * Retrieves the keyword, with : appended.
     *
     * @return string
     */
    public function getKeyword() : string
    {
        return $this->getMatchedText();
    }
    
    public function isForIn() : bool
    {
        return $this->getKeyword() === Mailcode_Commands_Keywords::TYPE_IN;
    }
    
    public function isInsensitive() : bool
    {
        return $this->getKeyword() === Mailcode_Commands_Keywords::TYPE_INSENSITIVE;
    }

    public function isURLEncoded() : bool
    {
        return $this->getKeyword() === Mailcode_Commands_Keywords::TYPE_URLENCODE;
    }

    public function isURLDecode() : bool
    {
        return $this->getKeyword() === Mailcode_Commands_Keywords::TYPE_URLDECODE;
    }

    public function isNoHTML() : bool
    {
        return $this->getKeyword() === Mailcode_Commands_Keywords::TYPE_NOHTML;
    }
}
