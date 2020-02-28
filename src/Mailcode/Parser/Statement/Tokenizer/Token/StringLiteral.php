<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Token representing a quoted string literal.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral extends Mailcode_Parser_Statement_Tokenizer_Token
{
    public function getNormalized() : string
    {
        return $this->restoreQuotes($this->matchedText);
    }
}
