<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\CurrencyInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\CurrencyInterface
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see CurrencyTrait
 */
interface CurrencyInterface extends Mailcode_Interfaces_Commands_Command
{
    public const CURRENCY_PARAMETER_NAME = 'currency';

    public const VALIDATION_CURRENCY_WRONG_TYPE = 166201;
    public const VALIDATION_CURRENCY_EXCLUSIVE = 166202;

    public function isCurrencyPresent(): bool;

    public function getCurrencyToken(): ?Mailcode_Parser_Statement_Tokenizer_Token;
}
