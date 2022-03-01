<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\IDNEncodeInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\IDNEncodeInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Interfaces\Commands\EncodableInterface;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
use Mailcode\Traits\Commands\Validation\IDNEncodeTrait;

/**
 * Interface for commands that support IDN encoding.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see IDNEncodeTrait
 */
interface IDNEncodeInterface extends EncodableInterface
{
    /**
     * @param bool $enabled
     * @return $this
     */
    public function setIDNEncodingEnabled(bool $enabled) : self;

    public function isIDNEncoded() : bool;

    /**
     * Retrieves the IDN encode token - but only if it
     * is enabled. Throws an exception otherwise.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     * @throws Mailcode_Exception
     */
    public function getIDNEncodingToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
