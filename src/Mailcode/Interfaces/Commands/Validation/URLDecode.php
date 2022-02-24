<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_Validation_URLDecode}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Interfaces_Commands_Validation_URLDecode
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_URLDecode
 */
interface Mailcode_Interfaces_Commands_Validation_URLDecode
{
    public const VALIDATION_NAME_URLDECODE = 'urldecode';

    public function getURLDecodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

    /**
     * @param bool $decode
     * @return $this
     */
    public function setURLDecoding(bool $decode=true);
}
