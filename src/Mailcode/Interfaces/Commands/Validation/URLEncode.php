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

use Mailcode\Interfaces\Commands\EncodableInterface;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_URLEncode
 */
interface Mailcode_Interfaces_Commands_Validation_URLEncode extends EncodableInterface
{
    public const VALIDATION_NAME_URLENCODE = 'urlencode';

    public function getURLEncodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

    /**
     * @param bool $encoding
     * @return $this
     */
    public function setURLEncoding(bool $encoding = true) : self;

    public function isURLEncoded(): bool;
}
