<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_URLEncode} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see Mailcode_Traits_Commands_Validation_URLEncode
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Commands\ParamsException;

/**
 * Command validation drop-in: checks for the presence
 * of a "urlencode:" keyword, to automatically set the
 * command's URL encoding flag.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Interfaces_Commands_Validation_URLEncode
 */
trait Mailcode_Traits_Commands_Validation_URLEncode
{
    public function getURLEncodeToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->getEncodingToken(Mailcode_Commands_Keywords::TYPE_URLENCODE);
    }

    public function isURLEncoded() : bool
    {
        return $this->getURLEncodeToken() !== null;
    }

    /**
     * @param bool $encoding
     * @return $this
     * @throws Mailcode_Exception
     */
    public function setURLEncoding(bool $encoding=true) : self
    {
        return $this->setEncodingEnabled(Mailcode_Commands_Keywords::TYPE_URLENCODE, $encoding);
    }
}
