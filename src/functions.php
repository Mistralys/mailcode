<?php
/**
 * File containing global functions.
 *
 * @package Mailcode
 * @subpackage Core
 */

namespace Mailcode;

/**
 * Translation function used to translate internal strings:
 * if the localization is installed, it will use this to do
 * the translation.
 *
 * @param string $subject
 * @param mixed ...$args
 * @return string
 */
function t(string $subject, ...$args) : string
{
    return \AppLocalize\t($subject, ...$args);
}

/**
 * @param array<mixed,mixed> $array
 * @return int|string|null
 */
function array_key_last(array $array)
{
    $keys = array_keys($array);
    return array_pop($keys);
}

/**
 * Initializes the utilities: this is called automatically
 * because this file is included in the files list in the
 * composer.json, guaranteeing it is always loaded.
 */
function init() : void
{
    if(!class_exists('\AppLocalize\Localization')) {
        return;
    }
    
    $installFolder = realpath(__DIR__.'/../');

    define('MAILCODE_INSTALL_FOLDER', $installFolder);

    // Register the classes as a localization source,
    // so they can be found, and use the bundled localization
    // files.
    \AppLocalize\Localization::addSourceFolder(
        'mailcode',
        'Mailcode Syntax Parser',
        'Composer Packages',
        $installFolder.'/localization',
        $installFolder.'/src'
    );
}

init();
