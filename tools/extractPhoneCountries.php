<?php
/**
 * Utility script to extract supported countries for the showphone
 * command, meant to be opened in a browser.
 *
 * @package Mailcode
 * @subpackage Tools
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see https://countrycode.org/countryCode/downloadCountryCodes
 */

declare(strict_types=1);

use AppUtils\CSVHelper;
use AppUtils\FileHelper;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;use Mailcode\Mailcode;use function AppLocalize\pts;

require_once 'prepend.php';

$outputFile = '../src/Mailcode/Commands/Command/ShowPhone/numbers.json';
$countries = generateList();

FileHelper::saveAsJSON($countries, $outputFile, true);

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php pts('Phone countries extractor') ?> - <?php echo Mailcode::getName(); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
        <style>
            BODY{
                padding:2em 0;
            }
            TEXTAREA{
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <p>
                <a href="./">&laquo; <?php pts('Back to overview'); ?></a>
            </p>
            <h1>Phone number countries extraction</h1>
            <p>
                This uses Google's <a href="https://github.com/google/libphonenumber">libphonenumber</a>
                library (via its PHP port
                <a href="https://github.com/giggsey/libphonenumber-for-php">giggsey/libphonenumber-for-php</a>).
                It generates a list of all supported countries for use in the <code>{showphone}</code>
                command class, to validate the source country phone format parameter.
            </p>
            <p>
                As the library does not contain human-readable country names, a separate CSV file
                is used to map the ISO codes to the country names.
                This file is based on the
                <a href="https://countrycode.org">countrycode.org</a>
                download.
            </p>
            <p>
                Target class: <code>Mailcode_Commands_Command_ShowPhone</code><br>
                Target data file: <code><?php echo str_replace('../', '/', $outputFile) ?></code>
            </p>
            <p>
                The command reads the file to access the countries.
            </p>
            <p>
                <strong>Extracted JSON data:</strong>
            </p>
            <textarea rows="20" class="form-control"><?php echo json_encode($countries, JSON_PRETTY_PRINT) ?></textarea>
        </div>
    </body>
</html>

<?php

function generateList() : array
{
    $labels = extractLabels();
    $phoneNumberUtil = PhoneNumberUtil::getInstance();
    $regions = $phoneNumberUtil->getSupportedRegions();

    $data = array();
    foreach ($regions as $code)
    {
        $meta = $phoneNumberUtil->getMetadataForRegion($code);
        if (!$meta) {
            die('No metadata for ' . $code);
        }

        $exampleNumber = $phoneNumberUtil->getExampleNumber($code);
        $local = $phoneNumberUtil->formatInOriginalFormat($exampleNumber, $code);
        $international = $phoneNumberUtil->format($exampleNumber, PhoneNumberFormat::INTERNATIONAL);

        $label = $code;
        if (isset($labels[$code])) {
            $label = $labels[$code];
        }

        $data[$code] = array(
            'label' => $label,
            'local' => $local,
            'international' => $international
        );
    }

    ksort($data);

    return $data;
}

function extractLabels() : array
{
    $lines = CSVHelper::parseFile('countrycodes.csv');

    $isoIdx = 1;
    $nameIdx = 0;

    $result = array();

    foreach ($lines as $line) {
        $result[$line[$isoIdx]] = $line[$nameIdx];
    }

    return $result;
}