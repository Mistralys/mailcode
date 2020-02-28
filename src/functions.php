<?php

namespace Mailcode;

/**
 * Translation function used to translate some of the internal
 * strings: if the localization is installed, it will use this
 * to do the translation.
 * 
 * @return string
 */
function t()
{
    $args = func_get_args();
    
    return call_user_func_array('\AppLocalize\t', $args);
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
