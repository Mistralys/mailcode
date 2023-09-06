<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\CountInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\CountInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Mailcode_Variables_Variable;
use Mailcode\Traits\Commands\Validation\CountTrait;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see CountTrait
 */
interface CountInterface extends Mailcode_Interfaces_Commands_Command
{
    public const PARAMETER_NAME = 'count';
    public const VALIDATION_COUNT_NAME = 'check_count';
    public const VALIDATION_COUNT_CODE_WRONG_TYPE = 135701;

    public function isCountEnabled(): bool;

    public function getCountVariable(): ?Mailcode_Variables_Variable;

    public function setCount(?Mailcode_Variables_Variable $variable) : self;
}
