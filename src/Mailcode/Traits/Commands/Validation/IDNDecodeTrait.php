<?php
/**
 * File containing the trait {@see \Mailcode\Traits\Commands\Validation\IDNDecodeTrait}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\IDNDecodeTrait
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\IDNDecodeInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

/**
 * Command validation drop-in: checks for the presence
 * of the `idndecode:` keyword in the command statement,
 * and sets the IDN decode enabled flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see IDNDecodeInterface
 */
trait IDNDecodeTrait
{
    /**
     * @param bool $enabled
     * @return $this
     */
    public function setIDNDecoding(bool $enabled) : self
    {
        return $this->setEncodingEnabled(Mailcode_Commands_Keywords::TYPE_IDN_DECODE, $enabled);
    }

    public function isIDNDecoded() : bool
    {
        return $this->getIDNDecodingToken() !== null;
    }

    public function getIDNDecodingToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->getEncodingToken(Mailcode_Commands_Keywords::TYPE_IDN_DECODE);
    }
}
