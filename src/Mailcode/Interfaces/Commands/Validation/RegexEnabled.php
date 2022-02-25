<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_RegexEnabled}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_RegexEnabled
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_RegexEnabled
 */
interface Mailcode_Interfaces_Commands_Validation_RegexEnabled extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_REGEX_ENABLED = 'regex_enabled';

    public function isRegexEnabled() : bool;
    public function getRegexToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
