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

    private static ?Mailcode_Number_LocalCurrency $instance = null;

    /**
     * @param string $country
     * @param string $currencyName
     * @param string $currencySymbol
     * @param string $unitSeparator
     * @param string $formatString
     */
    public function __construct(string $country, string $currencyName, string $currencySymbol, string $unitSeparator, string $formatString)
    {
        $this->country = $country;
        $this->currencyName = $currencyName;
        $this->currencySymbol = $currencySymbol;
        $this->unitSeparator = $unitSeparator;
        $this->formatString = $formatString;
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