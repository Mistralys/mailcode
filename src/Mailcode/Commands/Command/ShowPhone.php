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
     * @var array<string,string>
     *
     * @see https://github.com/google/libphonenumber/tree/master/resources/geocoding
     * @see /tools/extractPhoneCountries.php
     */
    protected static $supportedCountries = array(
        'AD' => 'Andorra',
        'AF' => 'Afghanistan',
        'AI' => 'Anguilla',
        'AL' => 'Albania',
        'AM' => 'Armenia',
        'AO' => 'Angola',
        'AR' => 'Argentina',
        'AT' => 'Austria',
        'AU' => 'Australia',
        'AW' => 'Aruba',
        'AZ' => 'Azerbaijan',
        'BB' => 'Barbados',
        'BD' => 'Bangladesh',
        'BE' => 'Belgium',
        'BG' => 'Bulgaria',
        'BH' => 'Bahrain',
        'BI' => 'Burundi',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BN' => 'Brunei',
        'BO' => 'Bolivia',
        'BR' => 'Brazil',
        'BS' => 'Bahamas',
        'BT' => 'Bhutan',
        'BW' => 'Botswana',
        'BY' => 'Belarus',
        'BZ' => 'Belize',
        'CA' => 'Canada',
        'CH' => 'Switzerland',
        'CL' => 'Chile',
        'CM' => 'Cameroon',
        'CN' => 'China',
        'CO' => 'Colombia',
        'CU' => 'Cuba',
        'CY' => 'Cyprus',
        'CZ' => 'Czechia',
        'DE' => 'Germany',
        'DJ' => 'Djibouti',
        'DK' => 'Denmark',
        'DM' => 'Dominica',
        'DZ' => 'Algeria',
        'EC' => 'Ecuador',
        'EE' => 'Estonia',
        'EG' => 'Egypt',
        'ER' => 'Eritrea',
        'ES' => 'Spain',
        'ET' => 'Ethiopia',
        'FI' => 'Finland',
        'FJ' => 'Fiji',
        'FM' => 'Micronesia',
        'FR' => 'France',
        'GA' => 'Gabon',
        'GD' => 'Grenada',
        'GE' => 'Georgia',
        'GG' => 'Guernsey',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GL' => 'Greenland',
        'GM' => 'Gambia',
        'GN' => 'Guinea',
        'GP' => 'Guadeloupe',
        'GR' => 'Greece',
        'GT' => 'Guatemala',
        'GU' => 'Guam',
        'GY' => 'Guyana',
        'HN' => 'Honduras',
        'HR' => 'Croatia',
        'HT' => 'Haiti',
        'HU' => 'Hungary',
        'ID' => 'Indonesia',
        'IE' => 'Ireland',
        'IL' => 'Israel',
        'IN' => 'India',
        'IQ' => 'Iraq',
        'IR' => 'Iran',
        'IS' => 'Iceland',
        'IT' => 'Italy',
        'JE' => 'Jersey',
        'JM' => 'Jamaica',
        'JO' => 'Jordan',
        'JP' => 'Japan',
        'KE' => 'Kenya',
        'KG' => 'Kyrgyzstan',
        'KH' => 'Cambodia',
        'KI' => 'Kiribati',
        'KM' => 'Comoros',
        'KW' => 'Kuwait',
        'KZ' => 'Kazakhstan',
        'LA' => 'Laos',
        'LB' => 'Lebanon',
        'LI' => 'Liechtenstein',
        'LR' => 'Liberia',
        'LS' => 'Lesotho',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'LV' => 'Latvia',
        'LY' => 'Libya',
        'MA' => 'Morocco',
        'MC' => 'Monaco',
        'MD' => 'Moldova',
        'ME' => 'Montenegro',
        'MG' => 'Madagascar',
        'ML' => 'Mali',
        'MN' => 'Mongolia',
        'MO' => 'Macao',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MS' => 'Montserrat',
        'MT' => 'Malta',
        'MU' => 'Mauritius',
        'MV' => 'Maldives',
        'MW' => 'Malawi',
        'MX' => 'Mexico',
        'MY' => 'Malaysia',
        'MZ' => 'Mozambique',
        'NA' => 'Namibia',
        'NE' => 'Niger',
        'NG' => 'Nigeria',
        'NI' => 'Nicaragua',
        'NL' => 'Netherlands',
        'NO' => 'Norway',
        'NP' => 'Nepal',
        'NR' => 'Nauru',
        'NU' => 'Niue',
        'OM' => 'Oman',
        'PA' => 'Panama',
        'PE' => 'Peru',
        'PH' => 'Philippines',
        'PK' => 'Pakistan',
        'PL' => 'Poland',
        'PS' => 'Palestine',
        'PT' => 'Portugal',
        'PW' => 'Palau',
        'PY' => 'Paraguay',
        'QA' => 'Qatar',
        'RO' => 'Romania',
        'RS' => 'Serbia',
        'RU' => 'Russia',
        'RW' => 'Rwanda',
        'SC' => 'Seychelles',
        'SD' => 'Sudan',
        'SE' => 'Sweden',
        'SG' => 'Singapore',
        'SI' => 'Slovenia',
        'SK' => 'Slovakia',
        'SN' => 'Senegal',
        'SO' => 'Somalia',
        'SR' => 'Suriname',
        'SY' => 'Syria',
        'SZ' => 'Eswatini',
        'TD' => 'Chad',
        'TG' => 'Togo',
        'TH' => 'Thailand',
        'TJ' => 'Tajikistan',
        'TK' => 'Tokelau',
        'TM' => 'Turkmenistan',
        'TN' => 'Tunisia',
        'TO' => 'Tonga',
        'TR' => 'Turkey',
        'TV' => 'Tuvalu',
        'TW' => 'Taiwan',
        'TZ' => 'Tanzania',
        'UA' => 'Ukraine',
        'UG' => 'Uganda',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VE' => 'Venezuela',
        'VN' => 'Vietnam',
        'VU' => 'Vanuatu',
        'WS' => 'Samoa',
        'XK' => 'Kosovo',
        'YE' => 'Yemen',
        'YT' => 'Mayotte',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe'
    );

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
     */
    protected function validateSyntax_country_code(): void
    {
        if(isset(self::$supportedCountries[$this->sourceFormat])) {
            return;
        }

        $this->validationResult->makeError(
            t('The country code %1$s is not supported for phone number conversion.', '<code>'.$this->sourceFormat.'</code>'),
            self::VALIDATION_INVALID_COUNTRY
        );
    }

    /**
     * Retrieves the list of countries supported for phone number conversions,
     * as an associative array with uppercase country code => country name pairs.
     *
     * @return array<string,string>
     */
    public static function getSupportedCountries() : array
    {
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

