<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_NoTracking} trait.
 *
 * @see Mailcode_Traits_Commands_Validation_NoTracking
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use phpDocumentor\Reflection\Utils;

/**
 * Command validation drop-in: checks for the presence
 * of the `no-tracking:` keyword in the command statement,
 * and sets the tracking enabled flag accordingly.
 *
 * @see Mailcode_Interfaces_Commands_Validation_NoTracking
 *@subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 *
 * @package Mailcode
 */
trait Mailcode_Traits_Commands_Validation_NoTracking
{
    /**
     * @var boolean
     */
    protected bool $trackingEnabled = true;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    protected ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword $noTrackingToken = null;

    protected function validateSyntax_no_tracking() : void
    {
        $keywords = $this->requireParams()
            ->getInfo()
            ->getKeywords();

        // Reset the tracking values in case we are calling
        // this after the initial validation.
        $this->noTrackingToken = null;
        $this->trackingEnabled = true;

        foreach($keywords as $keyword)
        {
            if($keyword->getKeyword() === Mailcode_Commands_Keywords::TYPE_NO_TRACKING)
            {
                $this->noTrackingToken = $keyword;
                $this->trackingEnabled = false;
                break;
            }
        }
    }

    public function isTrackingEnabled() : bool
    {
        return $this->trackingEnabled;
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

        $this->validateSyntax_no_tracking();

        return $this;
    }

    public function getNoTrackingToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        if($this->noTrackingToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $this->noTrackingToken;
        }

        return null;
    }
}
