<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\BreakAtInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\BreakAtInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Mailcode_Variables_Variable;
use Mailcode\Traits\Commands\Validation\CountTrait;

/**
 * @package Mailcode
 * @subpackage Validation
 *
 * @see CountTrait
 */
interface CountInterface extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_COUNT = 'count';

    public function isCountEnabled(): bool;

    public function getCountVariable(): ?Mailcode_Variables_Variable;

}
