<?php

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_Validation_TrackingID
 */
interface Mailcode_Interfaces_Commands_Validation_TrackingID
{
    public const VALIDATION_NAME_TRACKING_ID = 'tracking_id';

    public function getTrackingID() : string;
    public function hasTrackingID() : bool;
}
