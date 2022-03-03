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

use AppUtils\NamedClosure;
use Closure;
use Mailcode\Commands\Command\ShowURL\AutoTrackingID;
use Mailcode\Interfaces\Commands\Validation\TrackingIDInterface;
use Mailcode\Mailcode_Parser_Statement_Tokenizer;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;

/**
 * Command validation drop-in: checks for the presence
 * of a tracking ID, which must be the first string
 * literal in the command's parameters list. If not
 * present or not a match for a tracking ID name, an
 * empty string is used as default.
 *
 * When the `no-tracking:` keyword is enabled, the
 * tracking ID will always be empty.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see TrackingIDInterface
 */
trait TrackingIDTrait
{
    private ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $trackingIDToken = null;

    /**
     * @return string
     */
    public function getTrackingID() : string
    {
        $token = $this->getTrackingIDToken();

        if($token === null)
        {
            return '';
        }

        // In case of an empty tracking ID
        $token->setText($this->filterTrackingID($token->getText()));

        return $token->getText();
    }

    private function filterTrackingID(string $trackingID) : string
    {
        if(empty($trackingID))
        {
            return AutoTrackingID::generate($this);
        }

        return $trackingID;
    }

    public function getTrackingIDToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        $this->initTrackingToken();

        return $this->trackingIDToken;
    }

    private function initTrackingToken() : void
    {
        if(!$this->isTrackingEnabled())
        {
            $this->clearTrackingIDToken();
            return;
        }

        if(isset($this->trackingIDToken))
        {
            return;
        }

        $token = $this->detectToken();
        if($token === null)
        {
            $token = $this->requireParams()
                ->getInfo()
                ->addStringLiteral(AutoTrackingID::generate($this));
        }

        $this->trackingIDToken = $token;
    }

    private function clearTrackingIDToken() : void
    {
        $this->trackingIDToken = null;

        $token = $this->detectToken();
        if($token !== null)
        {
            $this->requireParams()
                ->getInfo()
                ->removeToken($token);
        }
    }

    public function setTrackingID(string $trackingID) : self
    {
        $token = $this->getTrackingIDToken();

        if($token !== null)
        {
            $token->setText($this->filterTrackingID($trackingID));
        }

        return $this;
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
        // Add a listener to automatically update the
        // tracking ID if the tracking is disabled
        // programmatically via `setTrackingEnabled()`.
        $this->requireParams()->getEventHandler()->onKeywordsChanged(
            NamedClosure::fromClosure(
                Closure::fromCallable(array($this, 'handleKeywordsChanged')),
                array($this, 'handleKeywordsChanged')
            )
        );

        $this->initTrackingToken();
    }

    private function handleKeywordsChanged(Mailcode_Parser_Statement_Tokenizer $tokenizer) : void
    {
        $this->initTrackingToken();
    }
}
