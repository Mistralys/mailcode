<?php

declare(strict_types=1);

use Mailcode\Mailcode;
use Mailcode\Mailcode_Collection;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Parser_Safeguard;
use Mailcode\Mailcode_Variables_Collection;
use PHPUnit\Framework\TestCase;
use function AppUtils\sb;

abstract class MailcodeTestCase extends TestCase
{
    protected function setUp() : void
    {
        Mailcode::setDebugging(false);
    }

    protected function enableDebug() : void
    {
        Mailcode::setDebugging(true);
    }

    protected function runCollectionTests(array $tests) : void
    {
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $label = $test['label'].PHP_EOL;
            
            if(!$collection->isValid())
            {
                $label .= "Messages:".PHP_EOL;
                
                foreach($collection->getErrors() as $error)
                {
                    $label .= $error->getMessage().PHP_EOL;
                }
            }
            
            $label .= 'Command:'.$test['string'];
            
            $this->assertSame($test['valid'], $collection->isValid(), $label);
            
            if(!$test['valid'])
            {
                $error = $collection->getFirstError();
                $this->assertSame($test['code'], $error->getCode(), $label);
            }

            if($test['valid'] && $collection->isValid() && isset($test['normalized']))
            {
                try
                {
                    $safeguard = Mailcode::create()->createSafeguard($test['string']);
                    $safe = $safeguard->makeSafe();
                    $whole = $safeguard->makeWhole($safe);

                    $this->assertEquals($test['normalized'], $whole, $label);
                }
                catch (Mailcode_Exception $e)
                {
                    $this->fail($e->getMessage(). ' ' . $e->getDetails());
                }
            }
        }
    }

    /**
     * Dumps a list of all variables in the collection as a string
     * which includes the names in order of appearance, grouped by
     * name and grouped by unique name.
     *
     * @param Mailcode_Variables_Collection $collection
     * @return string
     * @throws Mailcode_Exception
     */
    protected function debugVariablesCollection(Mailcode_Variables_Collection $collection) : string
    {
        $list = $collection->getAll();

        $distinct = array();
        $names = array();
        $uniqueNames = array();

        foreach($list as $variable)
        {
            $distinct[] = $variable->getFullName();
            $names[] = $variable->getFullName();
            $uniqueNames[] = $variable->getUniqueName();
        }

        $names = array_unique($names);
        $uniqueNames = array_unique($uniqueNames);

        sort($names);
        sort($uniqueNames);

        return
            PHP_EOL.'----------------------------------------------------------'.PHP_EOL.
            'VARIABLES DUMP'.PHP_EOL.
            PHP_EOL.'----------------------------------------------------------'.PHP_EOL.
            'In order of appearance ('.count($distinct).'):'.PHP_EOL.
            '  -'.implode(PHP_EOL.'  -', $distinct).PHP_EOL.
            'Grouped by name ('.count($names).'):'.PHP_EOL.
            '  -'.implode(PHP_EOL.'  -', $names).PHP_EOL.
            'Unique instances by command ('.count($uniqueNames).'):'.PHP_EOL.
            '  -'.implode(PHP_EOL.'  -', $uniqueNames).PHP_EOL.PHP_EOL;
    }

    protected function assertSafeguardValid(Mailcode_Parser_Safeguard $safeguard) : void
    {
        $this->assertCollectionValid($safeguard->getCollection());
    }

    protected function assertCollectionValid(Mailcode_Collection $collection) : void
    {
        $errors = $collection->getErrors();

        $msg = array();

        foreach ($errors as $error)
        {
            $msg[] = 'Mailcode error: '.$error->getMessage();
        }

        $this->assertTrue(
            $collection->isValid(),
            implode(PHP_EOL, $msg)
        );
    }

    protected function assertCollectionHasErrorCode(int $code, Mailcode_Collection $collection) : void
    {
        if($collection->hasErrorCode($code))
        {
            $this->addToAssertionCount(1);
            return;
        }

        $message = sb()
            ->sf('Collection is invalid, but does not have error code [%s].', $code)
            ->eol()
            ->sf('Available error codes: [%s].', implode(', ', $collection->getErrorCodes()));

        $this->assertTrue(
            $collection->hasErrorCode($code),
            (string)$message
        );
    }

    protected function parseStringValid(string $subject) : Mailcode_Collection
    {
        $collection = Mailcode::create()->parseString($subject);

        $this->assertCollectionValid($collection);

        return $collection;
    }

    protected function parseCommandStringValid(string $subject) : Mailcode_Commands_Command
    {
        $command = $this->parseStringValid($subject)->getFirstCommand();

        $this->assertNotNull($command);

        return $command;
    }
}
