<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token_Number} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token_Number
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Token representing a number.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Token_Number extends Mailcode_Parser_Statement_Tokenizer_Token implements Mailcode_Parser_Statement_Tokenizer_ValueInterface
{
    public function getNormalized() : string
    {
        return $this->matchedText;
    }
    
    public function getValue() : string
    {
        return $this->matchedText;
    }
}
