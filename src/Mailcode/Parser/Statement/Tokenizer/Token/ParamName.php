<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token_ParamName} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token_ParamName
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Token representing an operand sign.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Token_ParamName extends Mailcode_Parser_Statement_Tokenizer_Token
{
    public function getName() : string
    {
        return trim($this->matchedText, ' =');
    }

    public function getNormalized() : string
    {
        return $this->getName().'=';
    }
}
