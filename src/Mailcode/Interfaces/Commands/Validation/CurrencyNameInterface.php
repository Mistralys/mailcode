<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\CurrencyNameInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\CurrencyNameInterface
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see CurrencyNameTrait
 */
interface CurrencyNameInterface extends Mailcode_Interfaces_Commands_Command
{
    public function isCurrencyNameEnabled(): bool;

    public function getCurrencyNameToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

    public function setCurrencyNameEnabled(bool $enabled): self;
}
