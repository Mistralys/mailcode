<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_URLEncode}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_URLEncode
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_URLEncode
 */
interface Mailcode_Interfaces_Commands_Validation_URLEncode
{
    public const VALIDATION_NAME_URLENCODE = 'urlencode';

    public function getURLEncodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
