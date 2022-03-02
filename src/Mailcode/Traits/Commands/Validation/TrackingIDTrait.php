<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\TrackingIDTrait} trait.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\TrackingIDTrait
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Commands\Command\ShowURL\AutoTrackingID;
use Mailcode\Interfaces\Commands\Validation\TrackingIDInterface;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Validator;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;

/**
 * Command validation drop-in: checks for the presence
 * of a tracking ID, which must be the first string
 * literal in the command's parameters list. If not
 * present or not a match for a tracking ID name, an
 * empty string is used as default.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see TrackingIDInterface
 * @property Mailcode_Parser_Statement_Validator $validator
 *
 */
trait TrackingIDTrait
{
    private ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $trackingIDToken = null;

    /**
     * @return string
     */
    public function getTrackingID() : string
    {
        return $this->getTrackingIDToken()->getText();
    }

    public function getTrackingIDToken() : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        return $this->initTrackingToken();
    }

    private function initTrackingToken() : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        if(isset($this->trackingIDToken))
        {
            return $this->trackingIDToken;
        }

        $token = $this->detectToken();
        if($token === null)
        {
            $token = $this->requireParams()
                ->getInfo()
                ->addStringLiteral(AutoTrackingID::generate($this));
        }

        $this->trackingIDToken = $token;

        return $token;
    }

    public function setTrackingID(string $trackingID) : self
    {
        $this->getTrackingIDToken()->setText($trackingID);
        return $this;
    }

    public function hasTrackingID() : bool
    {
        return !empty($this->getTrackingIDToken()->getText());
    }

    private function detectToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        $literals = $this->requireParams()
            ->getInfo()
            ->getStringLiterals();

        if(empty($literals))
        {
            return null;
        }

        $trackingID = array_shift($literals);

        $id = $trackingID->getText();

        if(strpos($id, '=') === false)
        {
            return $trackingID;
        }

        return null;
    }

    /**
     * Checks if any of the parameters contain a trackingID.
     * This must be the first string literal in the parameters,
     * allowing any keywords to be placed before it, but not
     * after the optional query parameters.
     */
    protected function validateSyntax_tracking_id() : void
    {
        $this->initTrackingToken();
    }
}
