<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowPrice} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowPrice
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Mailcode command: show a date variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 */
class Mailcode_Commands_Command_ShowPrice extends Mailcode_Commands_ShowBase
    implements
    RegionInterface, CurrencyInterface, CurrencyNameInterface
{
    use RegionTrait;
    use CurrencyTrait;
    use CurrencyNameTrait;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    private ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword $absoluteKeyword = null;

    /**
     * @var Mailcode_Number_LocalCurrency
     */
    private Mailcode_Number_LocalCurrency $localCurrency;

    public function getName(): string
    {
        return 'showprice';
    }

    public function getLabel(): string
    {
        return t('Show price variable');
    }

    protected function getValidations(): array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE,
            'absolute',
            'check_currency_exclusive',
            'check_currency',
            'check_region'
        );
    }

    protected function validateSyntax_absolute(): void
    {
        $keywords = $this->requireParams()
            ->getInfo()
            ->getKeywords();

        foreach ($keywords as $keyword) {
            if ($keyword->getKeyword() === Mailcode_Commands_Keywords::TYPE_ABSOLUTE) {
                $this->absoluteKeyword = $keyword;
                break;
            }
        }
    }

    protected function validateSyntax_check_currency_exclusive(): void
    {
        $hasCurrencyNameKeyword = $this
            ->requireParams()
            ->getInfo()
            ->hasKeyword(Mailcode_Commands_Keywords::TYPE_CURRENCY_NAME);

        $hasCurrencyParameter = $this
            ->requireParams()
            ->getInfo()
            ->getTokenByParamName(CurrencyInterface::CURRENCY_PARAMETER_NAME);

        if ($hasCurrencyParameter && $hasCurrencyNameKeyword) {
            $this->validationResult->makeError(
                t("Can not use both 'currency-name' and 'currency'"),
                CurrencyInterface::VALIDATION_CURRENCY_EXCLUSIVE
            );
        }
    }

    public function isAbsolute(): bool
    {
        return isset($this->absoluteKeyword);
    }

    public function setAbsolute(bool $absolute): Mailcode_Commands_Command_ShowPrice
    {
        if ($absolute === false && isset($this->absoluteKeyword)) {
            $this->requireParams()->getInfo()->removeKeyword($this->absoluteKeyword->getKeyword());
            $this->absoluteKeyword = null;
        }

        if ($absolute === true && !isset($this->absoluteKeyword)) {
            $this->requireParams()
                ->getInfo()
                ->addKeyword(Mailcode_Commands_Keywords::TYPE_ABSOLUTE);

            $this->validateSyntax_absolute();
        }

        return $this;
    }

    public function getLocalCurrency(): Mailcode_Number_LocalCurrency
    {
        if (!isset($this->localCurrency)) {
            $this->localCurrency = Mailcode_Number_LocalCurrency::defaultInstance();
        }
        return $this->localCurrency;
    }

    public function setLocalCurrency(Mailcode_Number_LocalCurrency $localCurrency): Mailcode_Commands_Command_ShowPrice
    {
        $this->localCurrency = $localCurrency;
        return $this;
    }
}

