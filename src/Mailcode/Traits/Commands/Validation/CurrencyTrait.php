<?php
/**
 * File containing the trait {@see \Mailcode\Traits\Commands\Validation\CurrencyTrait}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\CurrencyTrait
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see CurrencyInterface
 */
trait CurrencyTrait
{
    private ?Mailcode_Parser_Statement_Tokenizer_Token $currency = null;

    /**
     * @throws Mailcode_Exception
     */
    protected function validateSyntax_check_currency(): void
    {
        $token = $this
            ->requireParams()
            ->getInfo()
            ->getTokenByParamName(CurrencyInterface::CURRENCY_PARAMETER_NAME);

        if ($token === null) {
            return;
        }

        if (!$token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable &&
            !$token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            $this->validationResult->makeError(
                t('Invalid currency token:') . ' ' . t('Expected a variable or a string.'),
                CurrencyInterface::VALIDATION_CURRENCY_WRONG_TYPE
            );
            return;
        }

        $this->currency = $token;
    }

    public function isCurrencyPresent(): bool
    {
        return $this->getCurrencyToken() !== null;
    }

    public function getCurrencyToken(): ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        return $this->currency;
    }
}
