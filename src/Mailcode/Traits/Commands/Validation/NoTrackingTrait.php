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
use Mailcode\Mailcode_Parser_Statement_Validator;
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
 *
 * @property Mailcode_Parser_Statement_Validator $validator
 */
trait NoTrackingTrait
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
