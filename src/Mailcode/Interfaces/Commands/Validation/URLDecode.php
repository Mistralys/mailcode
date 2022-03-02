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

use Mailcode\Commands\ParamsException;
use Mailcode\Interfaces\Commands\EncodableInterface;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_URLDecode
 */
interface Mailcode_Interfaces_Commands_Validation_URLDecode extends EncodableInterface
{
    public const VALIDATION_NAME_URLDECODE = 'urldecode';

    public function getURLDecodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

    /**
     * @param bool $enabled
     * @return $this
     * @throws ParamsException
     */
    public function setURLDecoding(bool $enabled=true) : self;

    public function isURLDecoded(): bool;
}
