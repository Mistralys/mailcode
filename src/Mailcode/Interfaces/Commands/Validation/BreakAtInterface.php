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
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Traits\Commands\Validation\BreakAtTrait;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see BreakAtTrait
 */
interface BreakAtInterface extends Mailcode_Interfaces_Commands_Command
{
    public const PARAMETER_NAME = 'break-at';
    public const VALIDATION_BREAK_AT_NAME = 'check_break_at';
    public const VALIDATION_BREAK_AT_CODE_WRONG_TYPE = 135601;

    public function isBreakAtEnabled(): bool;

    public function getBreakAtToken(): ?Mailcode_Parser_Statement_Tokenizer_Token;

}
