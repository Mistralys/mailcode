<?php

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

class Mailcode_Number_LocalCurrency extends OperationResult
{
    public const DEFAULT_COUNTRY = "US";
    public const DEFAULT_CURRENCY_NAME = "USD";
    public const DEFAULT_CURRENCY_SYMBOL = "$";
    public const DEFAULT_UNIT_SEPARATOR = " ";
    public const DEFAULT_FORMAT = "1,000.00";

    private string $country;
    private string $currencyName;
    private string $currencySymbol;
    private string $unitSeparator;
    private string $formatString;

    private ?Mailcode_Parser_Statement_Tokenizer_Token_Variable $currency = null;
    private ?Mailcode_Parser_Statement_Tokenizer_Token_Variable $region = null;

    private static ?Mailcode_Number_LocalCurrency $instance = null;

    /**
     * @param string $country
     * @param string $currencyName
     * @param string $currencySymbol
     * @param string $unitSeparator
     * @param string $formatString
     * @param ?Mailcode_Parser_Statement_Tokenizer_Token $currency
     * @param ?Mailcode_Parser_Statement_Tokenizer_Token $region
     */
    public function __construct(string                                     $country,
                                string                                     $currencyName,
                                string                                     $currencySymbol,
                                string                                     $unitSeparator,
                                string                                     $formatString,
                                ?Mailcode_Parser_Statement_Tokenizer_Token $currency = null,
                                ?Mailcode_Parser_Statement_Tokenizer_Token $region = null)
    {
        $this->country = $country;
        $this->currencyName = $currencyName;
        $this->currencySymbol = $currencySymbol;
        $this->unitSeparator = $unitSeparator;
        $this->formatString = $formatString;

        $this->currency = $currency;
        $this->region = $region;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCurrencyName(): string
    {
        return $this->currencyName;
    }

    public function getCurrencySymbol(): string
    {
        return $this->currencySymbol;
    }

    public function getUnitSeparator(): string
    {
        return $this->unitSeparator;
    }

    public function getFormatString(): string
    {
        return $this->formatString;
    }

    public function getRegion(): ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        return $this->region;
    }

    public function getCurrency(): ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        return $this->currency;
    }

    public function withRegion(?Mailcode_Parser_Statement_Tokenizer_Token $region = null): Mailcode_Number_LocalCurrency
    {
        return new Mailcode_Number_LocalCurrency(
            $this->country,
            $this->currencyName,
            $this->currencySymbol,
            $this->unitSeparator,
            $this->formatString,
            $this->currency,
            $region
        );
    }

    public function withCurrency(?Mailcode_Parser_Statement_Tokenizer_Token $currency = null): Mailcode_Number_LocalCurrency
    {
        return new Mailcode_Number_LocalCurrency(
            $this->country,
            $this->currencyName,
            $this->currencySymbol,
            $this->unitSeparator,
            $this->formatString,
            $currency,
            $this->region
        );
    }

    public function getFormatInfo(): Mailcode_Number_Info
    {
        return new Mailcode_Number_Info($this->formatString);
    }

    public static function defaultInstance(): Mailcode_Number_LocalCurrency
    {
        if (!isset(self::$instance)) {
            self::$instance = new self(
                self::DEFAULT_COUNTRY,
                self::DEFAULT_CURRENCY_NAME,
                self::DEFAULT_CURRENCY_SYMBOL,
                self::DEFAULT_UNIT_SEPARATOR,
                self::DEFAULT_FORMAT
            );
        }
        return self::$instance;
    }
}