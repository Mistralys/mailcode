<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowPhone} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowPhone
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\FileHelper;
use AppUtils\FileHelper_Exception;

/**
 * Mailcode command: show a phone number variable value, in E164 format.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowPhone extends Mailcode_Commands_ShowBase
{
    const VALIDATION_SOURCE_FORMAT_MISSING = 84001;
    const VALIDATION_INVALID_COUNTRY = 84002;

    /**
     * Two-letter country code, uppercase.
     * @var string
     */
    protected $sourceFormat = '';

    /**
     * List of supported countries in the libphonenumber package.
     * NOTE: This can be extracted automatically using the provided PHP script.
     *
     * @var array<string,Mailcode_Commands_Command_ShowPhone_Number>
     *
     * @see /tools/extractPhoneCountries.php
     */
    protected static $supportedCountries = array();

    /**
     * @var bool
     */
    protected static $countriesLoaded = false;

    public function getName() : string
    {
        return 'showphone';
    }

    public function getLabel() : string
    {
        return t('Show phone number variable');
    }

    protected function getValidations() : array
    {
        return array(
            'variable',
            'source_format',
            'country_code',
            'urlencode'
        );
    }

    protected function validateSyntax_source_format(): void
    {
        $val = $this->validator->createStringLiteral();

        if($val->isValid())
        {
            $this->sourceFormat = strtoupper($val->getToken()->getText());
            return;
        }

        $this->validationResult->makeError(
            t('No country code for the source phone format specified.'),
            self::VALIDATION_SOURCE_FORMAT_MISSING
        );
    }

    /**
     * Validates the specified country code to ensure it is one of the
     * supported countries.
     *
     * @throws FileHelper_Exception
     */
    protected function validateSyntax_country_code(): void
    {
        $countries = self::getSupportedCountries();

        if(isset($countries[$this->sourceFormat])) {
            return;
        }

        $this->validationResult->makeError(
            t('The country code %1$s is not supported for phone number conversion.', '<code>'.$this->sourceFormat.'</code>'),
            self::VALIDATION_INVALID_COUNTRY
        );
    }

    /**
     * Retrieves the list of countries supported for phone number conversions,
     * as an associative array with uppercase country code => number class pairs.
     *
     * @return array<string,Mailcode_Commands_Command_ShowPhone_Number>
     * @throws FileHelper_Exception
     */
    public static function getSupportedCountries() : array
    {
        if(self::$countriesLoaded) {
            return self::$supportedCountries;
        }

        self::$countriesLoaded = true;

        $data = FileHelper::parseJSONFile(__DIR__.'/ShowPhone/numbers.json');

        foreach($data as $code => $def)
        {
            $code = strval($code);

            self::$supportedCountries[$code] = new Mailcode_Commands_Command_ShowPhone_Number(
                $code,
                strval($def['label']),
                strval($def['local']),
                strval($def['international'])
            );
        }

        return self::$supportedCountries;
    }

    /**
     * @return string Two-letter country code, uppercase.
     */
    public function getSourceFormat() : string
    {
        return $this->sourceFormat;
    }
}

