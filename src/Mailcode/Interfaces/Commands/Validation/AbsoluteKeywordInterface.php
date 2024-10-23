<?php
/**
 * @package Mailcode
 * @subpackage Validation
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Traits\Commands\Validation\AbsoluteKeywordTrait;

/**
 * Interface for the trait {@see AbsoluteKeywordTrait}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see AbsoluteKeywordTrait
 */
interface AbsoluteKeywordInterface extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME = 'absolute';

    /**
     * Whether the target number is set to be displayed as an absolute value.
     * @return bool
     */
    public function isAbsolute(): bool;

    /**
     * Sets whether the target number should be displayed as an absolute value.
     * @param bool $absolute
     * @return $this
     */
    public function setAbsolute(bool $absolute): self;
}
