<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token_Unknown} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token_Unknown
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Token representing unknown/unrecognized content.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Token_Unknown extends Mailcode_Parser_Statement_Tokenizer_Token
{
    public function getNormalized() : string
    {
        return '';
    }
}
