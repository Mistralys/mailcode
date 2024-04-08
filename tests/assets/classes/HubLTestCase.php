<?php

declare(strict_types=1);

namespace MailcodeTestClasses;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Translator;
use Mailcode\Translator\Syntax\HubL\ShowEncodedTranslation;
use MailcodeTestCase;

abstract class HubLTestCase extends MailcodeTestCase
{
    protected Mailcode_Translator $translator;

    protected function setUp() : void
    {
        parent::setUp();

        $this->translator = Mailcode::create()->createTranslator();

        ShowEncodedTranslation::resetCounter();
    }

    protected function runCommands(array $tests) : void
    {
        $syntax = $this->translator->createHubL();

        foreach($tests as $test)
        {
            try
            {
                $result = $syntax->translateCommand($test['mailcode']);
            }
            catch(Mailcode_Exception $e)
            {
                $this->fail('Exception triggered: '.$e->getMessage().' | '.$e->getDetails());
            }

            $expected = str_replace(
                array('[SLASH]', '[DBLSLASH]', '[FOURSLASH]', '[NL]'),
                array('\\', '\\\\', '\\\\\\\\', PHP_EOL),
                $test['expected']
            );

            $this->assertEquals($expected, $result, $test['label']);
        }
    }

    protected function translateCommand(Mailcode_Commands_Command $command) : string
    {
        return $this->translator
            ->createHubL()
            ->translateCommand($command);
    }
}
