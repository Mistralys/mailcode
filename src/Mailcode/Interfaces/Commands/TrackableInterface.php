<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\TrackableInterface}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Interfaces\Commands\TrackableInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands;

use Mailcode\Interfaces\Commands\Validation\NoTrackingInterface;
use Mailcode\Interfaces\Commands\Validation\TrackingIDInterface;

/**
 * Interface for commands that support the tracking ID
 * and the `no-tracking:` keyword.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface TrackableInterface
    extends
    NoTrackingInterface,
    TrackingIDInterface
{

}
