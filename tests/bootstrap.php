<?php
/**
 * Main bootstrapper used to set up the testsuites environment.
 * 
 * @package Mailcode
 * @subpackage Tests
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */

use AppUtils\FileHelper\FolderInfo;
use Mailcode\Mailcode;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * The tests root folder (this file's location)
 * @var string
 */
const TESTS_ROOT = __DIR__;

$autoloader = dirname(TESTS_ROOT) . '/vendor/autoload.php';

if(!file_exists($autoloader))
{
    die('ERROR: The autoloader is not present. Please run composer install first.');
}

/**
* The composer autoloader
*/
require_once $autoloader;

$logger = new Logger('mailcode-testsuites');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

Mailcode::setLogger($logger);
Mailcode::setCacheFolder(FolderInfo::factory(__DIR__.'/cache'));
