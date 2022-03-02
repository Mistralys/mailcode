<?php
/**
 * File containing the trait {@see \Mailcode\Traits\Commands\Validation\IDNEncodeTrait}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\IDNEncodeTrait
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\IDNEncodeInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

/**
 * Command validation drop-in: checks for the presence
 * of the `idnencode:` keyword in the command statement,
 * and sets the IDN encode enabled flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see IDNEncodeInterface
 */
trait IDNEncodeTrait
{
    /**
     * @param bool $enabled
     * @return $this
     */
    public function setIDNEncoding(bool $enabled) : self
    {
        return $this->setEncodingEnabled(Mailcode_Commands_Keywords::TYPE_IDN_ENCODE, $enabled);
    }

    public function isIDNEncoded() : bool
    {
        return $this->getIDNEncodingToken() !== null;
    }

    public function getIDNEncodingToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->getEncodingToken(Mailcode_Commands_Keywords::TYPE_IDN_ENCODE);
    }
}
