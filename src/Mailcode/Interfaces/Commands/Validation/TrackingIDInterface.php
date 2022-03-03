<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\TrackingIDInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\TrackingIDInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Traits\Commands\Validation\TrackingIDTrait;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see TrackingIDTrait
 */
interface TrackingIDInterface extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_TRACKING_ID = 'tracking_id';

    /**
     * Retrieves the tracking ID for the URL. If no tracking
     * ID has been specifically set, returns an automatically
     * generated one.
     *
     * @return string
     */
    public function getTrackingID() : string;

    public function setTrackingID(string $trackingID) : self;

    public function getTrackingIDToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
}
