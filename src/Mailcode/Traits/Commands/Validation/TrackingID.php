<?php
/**
 * File containing the {@see Mailcode_Traits_Commands_Validation_TrackingID} trait.
 *
 * @see Mailcode_Traits_Commands_Validation_TrackingID
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

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
 * @property Mailcode_Parser_Statement_Validator $validator
 *
 * @see Mailcode_Interfaces_Commands_Validation_TrackingID
 */
trait Mailcode_Traits_Commands_Validation_TrackingID
{
    protected string $trackingID = '';
    private ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $trackingIDToken = null;

    public function getTrackingID() : string
    {
        return $this->trackingID;
    }

    public function hasTrackingID() : bool
    {
        return !empty($this->trackingID);
    }

    /**
     * Checks if any of the parameters contain a trackingID.
     * This must be the first string literal in the parameters,
     * allowing any keywords to be placed before it, but not
     * after the optional query parameters.
     */
    protected function validateSyntax_tracking_id() : void
    {
        $trackingID = $this->requireParams()
            ->getInfo()
            ->getStringLiteralByIndex(0);

        if($trackingID instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral)
        {
            $id = $trackingID->getText();

            if(strpos($id, '=') === false)
            {
                $this->trackingID = $id;
                $this->trackingIDToken = $trackingID;
            }
        }
    }
}
