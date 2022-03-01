<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Traits_Commands_Validation_URLDecode} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Mailcode_Traits_Commands_Validation_URLDecode
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Commands\ParamsException;

/**
 * Command validation drop-in: checks for the presence
 * of a "urldecode:" keyword.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Interfaces_Commands_Validation_URLDecode
 */
trait Mailcode_Traits_Commands_Validation_URLDecode
{
    public function getURLDecodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->getEncodingToken(Mailcode_Commands_Keywords::TYPE_URLDECODE);
    }

    /**
     * @param bool $decode
     * @return $this
     * @throws Mailcode_Exception
     */
    public function setURLDecoding(bool $decode=true) : self
    {
        return $this->setEncodingEnabled(Mailcode_Commands_Keywords::TYPE_URLDECODE, $decode);
    }

    public function isURLDecoded() : bool
    {
        return $this->getURLDecodeToken() !== null;
    }
}
