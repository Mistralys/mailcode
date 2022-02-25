<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_Multiline}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_Multiline
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_Multiline
 */
interface Mailcode_Interfaces_Commands_Validation_Multiline extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_MULTILINE = 'multiline';

    public function isMultiline() : bool;
    public function getMultilineToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
