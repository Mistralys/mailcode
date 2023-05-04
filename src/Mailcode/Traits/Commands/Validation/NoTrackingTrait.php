<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\NoTrackingTrait} trait.
 *
 * @see \Mailcode\Traits\Commands\Validation\NoTrackingTrait
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\NoTrackingInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use phpDocumentor\Reflection\Utils;

/**
 * Command validation drop-in: checks for the presence
 * of the `no-tracking:` keyword in the command statement,
 * and sets the tracking enabled flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see NoTrackingInterface
 */
trait NoTrackingTrait
{
    public function isTrackingEnabled() : bool
    {
        return !$this->requireParams()
            ->getInfo()
            ->hasKeyword(Mailcode_Commands_Keywords::TYPE_NO_TRACKING);
    }

    /**
     * @param bool $enabled
     * @return $this
     * @throws Mailcode_Exception
     */
    public function setTrackingEnabled(bool $enabled) : self
    {
        $this->requireParams()
            ->getInfo()
            ->setKeywordEnabled(Mailcode_Commands_Keywords::TYPE_NO_TRACKING, !$enabled);

        return $this;
    }

    public function getNoTrackingToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->requireParams()
            ->getInfo()
            ->getKeywordsCollection()
            ->getByName(Mailcode_Commands_Keywords::TYPE_NO_TRACKING);
    }
}
