<?php

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands;

use Mailcode\Interfaces\Commands\Validation\NoTrackingInterface;
use Mailcode\Interfaces\Commands\Validation\TrackingIDInterface;

interface TrackableInterface
    extends
    NoTrackingInterface,
    TrackingIDInterface
{

}
