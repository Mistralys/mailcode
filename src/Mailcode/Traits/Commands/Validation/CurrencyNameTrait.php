<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\CurrencyNameTrait} trait.
 *
 * @see \Mailcode\Traits\Commands\Validation\CurrencyNameTrait
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Interfaces\Commands\Validation\NoTrackingInterface;

/**
 * Command validation drop-in: checks for the presence
 * of the `currency-name:` keyword in the command statement,
 * and sets the currency name enabled flag accordingly.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see NoTrackingInterface
 */
trait CurrencyNameTrait
{
    public function isCurrencyNameEnabled(): bool
    {
        return $this->requireParams()
            ->getInfo()
            ->hasKeyword(Mailcode_Commands_Keywords::TYPE_CURRENCY_NAME);
    }

    /**
     * @param bool $enabled
     * @return $this
     * @throws Mailcode_Exception
     */
    public function setCurrencyNameEnabled(bool $enabled): self
    {
        $this->requireParams()
            ->getInfo()
            ->setKeywordEnabled(Mailcode_Commands_Keywords::TYPE_CURRENCY_NAME, $enabled);

        return $this;
    }

    public function getCurrencyNameToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        return $this->requireParams()
            ->getInfo()
            ->getKeywordsCollection()
            ->getByName(Mailcode_Commands_Keywords::TYPE_CURRENCY_NAME);
    }
}
