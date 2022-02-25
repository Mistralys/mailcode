<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_CaseSensitive}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_CaseSensitive
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_CaseSensitive
 */
interface Mailcode_Interfaces_Commands_Validation_CaseSensitive extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_CASE_SENSITIVE = 'case_sensitive';

    public function isCaseInsensitive() : bool;
    public function getCaseToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
