<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\TimezoneInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\TimezoneInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Traits\Commands\Validation\TimezoneTrait;

/**
 * Interface for commands that support timezone.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see TimezoneTrait
 */
interface TimezoneInterface extends Mailcode_Interfaces_Commands_Command
{

    /**
     * Retrieves the timezone - but only if it is enabled. Throws an exception otherwise.
     *
     * @return string|NULL
     * @throws Mailcode_Exception
     */
    public function getTimezone() : ?string;
}
