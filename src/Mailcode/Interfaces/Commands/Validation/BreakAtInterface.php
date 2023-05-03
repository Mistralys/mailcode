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
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
use Mailcode\Traits\Commands\Validation\BreakAtTrait;

/**
 * @package Mailcode
 * @subpackage Validation
 *
 * @see BreakAtTrait
 */
interface BreakAtInterface extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_BREAK_AT = 'break_at';

    public function getBreakAtToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
}
