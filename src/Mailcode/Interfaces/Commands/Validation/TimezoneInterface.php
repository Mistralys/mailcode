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

use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Mailcode_Variables_Variable;
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
    public const PARAMETER_NAME = 'timezone';
    public const VALIDATION_TIMEZONE_NAME = 'check_timezone';
    public const VALIDATION_TIMEZONE_CODE_WRONG_TYPE = 135501;

    /**
     * Retrieves the token containing the timezone information.
     * @return Mailcode_Parser_Statement_Tokenizer_Token
     */
    public function getTimezoneToken(): Mailcode_Parser_Statement_Tokenizer_Token;

    /**
     * @param Mailcode_Variables_Variable|string|NULL $timezone Set to null to remove any existing timezone, and use the default instead.
     * @return self
     */
    public function setTimezone($timezone) : self;
}
