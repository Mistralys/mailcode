<?php
/**
 * Utility script to extract supported countries for the showphone
 * command, meant to be opened in a browser.
 *
 * @package Mailcode
 * @subpackage Tools
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */

declare(strict_types=1);

use AppUtils\FileHelper;
use AppUtils\FileHelper_Exception;

require_once 'prepend.php';

$xmlURL = 'https://raw.githubusercontent.com/google/libphonenumber/master/resources/PhoneNumberMetadata.xml';

$cacheFile = 'libphone.xml';
$cacheExpiry = 60 * 60; // 1 hour

if(!file_exists($cacheFile) || (filemtime($cacheFile) + $cacheExpiry) < time())
{
    try
    {
        $xml = FileHelper::downloadFile($xmlURL);
        FileHelper::saveFile($cacheFile, $xml);
    }
    catch (FileHelper_Exception $e)
    {
        die('Unable to download the current libphonenumber XML from <a href="'.$xmlURL.'">'.$xmlURL.'</a>');
    }
}
else
{
    $xml = FileHelper::readContents($cacheFile);
}

preg_match_all('/<!-- (\w+) \(([A-Z]+)\) -->/si', $xml, $result, PREG_PATTERN_ORDER);

$lines = array();
foreach($result[1] as $idx => $country)
{
    $lines[] = sprintf(
        "    '%s' => '%s'",
        $result[2][$idx],
        $country
    );
}

$code = 'array('.PHP_EOL.implode(",".PHP_EOL, $lines).PHP_EOL.');';

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Mailcode - Phone format countries</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
        <style>
            BODY{
                padding:2em 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Phone number countries extraction</h1>
            <p>
                This downloads Google's official LibPhoneNumber countries XML,
                and extracts a list of countries from it, for use in the <code>{showphone}</code>
                command class, to validate the source country phone format parameter.
            </p>
            <p>
                Target class: <code>Mailcode_Commands_Command_ShowPhone</code>
            </p>
            <p>
                It is meant to be pasted into the class' <code>$supportedCountries</code>
                property.
            </p>
            <textarea rows="20" class="form-control"><?php echo $code ?></textarea>
        </div>
    </body>
</html>
