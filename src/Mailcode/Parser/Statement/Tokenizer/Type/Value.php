<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Type_Value} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Type_Value
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for value type tokens (string literals, numbers).
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Parser_Statement_Tokenizer_Type_Value
{
    public function getValue() : string;
}
