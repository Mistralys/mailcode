<?php
/**
 * Main bootstrapper used to set up the testsuites environment.
 * 
 * @package Mailcode
 * @subpackage Tests
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */

    /**
     * The tests root folder (this file's location)
     * @var string
     */
    define('TESTS_ROOT', __DIR__ );

    $autoloader = realpath(TESTS_ROOT.'/../vendor/autoload.php');
    
    if($autoloader === false) 
    {
        die('ERROR: The autoloader is not present. Run composer install first.');
    }

   /**
    * The composer autoloader
    */
    require_once $autoloader;
    
   /**
    * The test case base class for the testsuites.
    */
    require_once TESTS_ROOT.'/assets/classes/MailcodeTestCase.php';

   
   /**
    * Test case base class for the factory tests.
    */
    require_once TESTS_ROOT.'/assets/classes/FactoryTestCase.php';

    /**
     * Test case base class for the apache velocity tests.
     */
    require_once TESTS_ROOT.'/assets/classes/VelocityTestCase.php';
