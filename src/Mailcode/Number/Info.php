<?php

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;
use AppUtils\OperationResult;

class Mailcode_Number_Info extends OperationResult
{
    const ERROR_VALIDATION_METHOD_MISSING = 72301;

    const DEFAULT_FORMAT = "1000.00";

    /**
     * @var string
     */
    private $format;

    /**
     * @var int
     */
    private $padding = 0;

    /**
     * @var string
     */
    private $thousandsSeparator = '';

    /**
     * @var int
     */
    private $decimals = 0;

    /**
     * @var string
     */
    private $decimalsSeparator = '';

    public function __construct(string $format)
    {
        $format = trim($format);

        if(empty($format))
        {
            $format = self::DEFAULT_FORMAT;
        }

        $this->format = $format;

        $this->parse();
    }

    public function getDecimalsSeparator() : string
    {
        return $this->decimalsSeparator;
    }

    public function getThousandsSeparator() : string
    {
        return $this->thousandsSeparator;
    }

    public function getDecimals() : int
    {
        return $this->decimals;
    }

    public function getPadding() : int
    {
        return $this->padding;
    }

    public function hasDecimals() : bool
    {
        return $this->decimals > 0;
    }

    public function hasPadding() : bool
    {
        return $this->padding > 0;
    }

    public function hasThousandsSeparator() : bool
    {
        return !empty($this->thousandsSeparator);
    }

    /**
     * @var string[]
     */
    private $validations = array(
        'padding',
        'number',
        'thousands_separator',
        'decimal_separator',
        'separators',
        'decimals',
        'regex'
    );

    /**
     *
     * @see Mailcode_Commands_Command_ShowNumber::VALIDATION_PADDING_SEPARATOR_OVERFLOW
     */
    private function parse() : void
    {
        $format = $this->format;

        foreach($this->validations as $validation)
        {
            $method = 'parse_'.$validation;

            if(method_exists($this, $method))
            {
                $format = $this->$method($format);

                if(!$this->isValid())
                {
                    return;
                }

                continue;
            }

            throw new Mailcode_Exception(
                'Missing format validation method.',
                sprintf(
                    'The validation method [%s] is missing in the class [%s].',
                    $method,
                    get_class($this)
                ),
                self::ERROR_VALIDATION_METHOD_MISSING
            );
        }
    }

    private function parse_padding(string $format) : string
    {
        if(strstr($format, ':') === false) {
            return $format;
        }

        $parts = ConvertHelper::explodeTrim(':', $this->format);

        if(count($parts) !== 2)
        {
            $this->makeError(
                t(
                    'The padding sign %1$s may only be used once in the format string.',
                    '<code>:</code>'
                ),
                Mailcode_Commands_Command_ShowNumber::VALIDATION_PADDING_SEPARATOR_OVERFLOW
            );

            return '';
        }

        $padding = $parts[1];

        if(!preg_match('/\A[#]+\z/x', $padding))
        {
            $this->makeError(
                t('The padding may only contain hashes (%1$s given).', '<code>'.$padding.'</code>'),
                Mailcode_Commands_Command_ShowNumber::VALIDATION_PADDING_INVALID_CHARS
            );

            return $format;
        }

        $this->padding = strlen($padding);

        return $parts[0];
    }

    private function parse_number(string $format) : string
    {
        if($format[0] !== '1')
        {
            $this->makeError(
                t('The first character of the format must be a %1$s.', '<code>1</code>'),
                Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_FORMAT_NUMBER
            );

            return $format;
        }

        // Get the actual number behind the format
        $base = str_replace(array('.', ',', ' '), '', $format);
        $number = intval(substr($base, 0, 4));

        if($number === 1000) {
            return $format;
        }

        $this->makeError(
            t(
                'The format must be specified using the number %1$s.',
                '<code>1000</code>'
            ),
            Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_FORMAT_NUMBER
        );

        return $format;
    }

    private function parse_thousands_separator(string $format) : string
    {
        $separator = $format[1];

        // No thousands separator
        if($separator === '0')
        {
            return $format;
        }

        // Valid thousands separator
        $validSeparators = array(' ', ',', '.');

        if(in_array($separator, $validSeparators))
        {
            $this->thousandsSeparator = $separator;
            $format = str_replace('1'.$separator, '1', $format);
            return $format;
        }

        $this->makeError(
            t(
                'The character %1$s is not a valid thousands separator.',
                '<code>'.$separator.'</code>'
            ),
            Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_THOUSANDS_SEPARATOR
        );

        return $format;
    }

    private function parse_decimal_separator(string $format) : string
    {
        // Number is 1000, so no decimals
        if (strlen($format) === 4)
        {
            return $format;
        }

        if ($this->validateDecimalSeparator($format[4]))
        {
            $this->decimalsSeparator = $format[4];
        }

        return $format;
    }

    private function parse_separators(string $format) : string
    {
        if(!empty($this->thousandsSeparator) && !empty($this->decimalsSeparator) && $this->thousandsSeparator === $this->decimalsSeparator)
        {
            $this->makeError(
                t(
                    'Cannot use %1$s as both thousands and decimals separator character.',
                    '<code>'.$this->thousandsSeparator.'</code>'
                ),
                Mailcode_Commands_Command_ShowNumber::VALIDATION_SEPARATORS_SAME_CHARACTER
            );
        }

        return $format;
    }

    private function parse_decimals(string $format) : string
    {
        if(empty($this->decimalsSeparator))
        {
            return $format;
        }

        $parts = ConvertHelper::explodeTrim($this->decimalsSeparator, $format);

        if(!isset($parts[1]))
        {
            $this->makeError(
                t('Cannot determine the amount of decimals.').' '.
                    t('Add the amount of decimals by adding the according amount of zeros.').' '.
                    t('Example:').' '.
                    t(
                        '%1$s would add two decimals.',
                        '<code>'.number_format(1000, 2, $this->decimalsSeparator, $this->thousandsSeparator).'</code>'
                    ),
                Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_DECIMALS_NO_DECIMALS
            );

            return $format;
        }

        if($this->validateDecimals($parts[1]))
        {
            $this->decimals = strlen($parts[1]);
        }

        return $format;
    }

    private function validateDecimals(string $decimals) : bool
    {
        if(preg_match('/\A[0]+\z/x', $decimals)) {
            return true;
        }

        $this->makeError(
            t(
                'The decimals may only contain zeros, other characters are not allowed (%1$s given)',
                '<code>'.htmlspecialchars($decimals).'</code>'
            ),
            Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_DECIMALS_CHARS
        );

        return false;
    }

    private function validateDecimalSeparator(string $separator) : bool
    {
        $validSeparators = array('.', ',');

        if(in_array($separator, $validSeparators)) {
            return true;
        }

        $this->makeError(
            t('Invalid decimal separator character.'),
            Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_DECIMAL_SEPARATOR
        );

        return false;
    }

    /**
     * Fallback regex check: The previous validations cannot take
     * all possibilities into account, so we validate the resulting
     * format string with a regex.
     *
     * @param string $format
     * @return string
     */
    private function parse_regex(string $format) : string
    {
        if(preg_match('/1[ ,.]?000|1[ ,.]?000[.,][0]+/x', $format))
        {
            return $format;
        }

        $this->makeError(
            t('Some invalid characters were found in the format string.'),
            Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_CHARACTERS
        );

        return $format;
    }
}