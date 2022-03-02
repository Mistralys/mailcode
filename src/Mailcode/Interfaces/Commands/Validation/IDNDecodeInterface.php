<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\IDNDecodeInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\IDNDecodeInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Interfaces\Commands\EncodableInterface;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
use Mailcode\Traits\Commands\Validation\IDNDecodeTrait;

/**
 * Interface for commands that support IDN decoding.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see IDNDecodeTrait
 */
interface IDNDecodeInterface extends EncodableInterface
{
    /**
     * @param bool $enabled
     * @return $this
     */
    public function setIDNDecoding(bool $enabled) : self;

    public function isIDNDecoded() : bool;

    /**
     * Retrieves the IDN decode token - but only if it
     * is enabled. Throws an exception otherwise.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     * @throws Mailcode_Exception
     */
    public function getIDNDecodingToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
