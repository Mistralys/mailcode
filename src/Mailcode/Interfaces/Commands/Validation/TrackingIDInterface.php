<?php

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Traits\Commands\Validation\TrackingIDTrait;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see TrackingIDTrait
 */
interface TrackingIDInterface
{
    public const VALIDATION_NAME_TRACKING_ID = 'tracking_id';

    public function getTrackingID() : string;
    public function hasTrackingID() : bool;
}
