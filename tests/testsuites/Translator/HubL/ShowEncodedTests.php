<?php

declare(strict_types=1);

namespace MailCodeTests\Translator\HubL;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_ShowEncoded;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Factory;
use Mailcode\Translator\Syntax\HubL\ShowEncodedTranslation;
use MailcodeTestClasses\HubLTestCase;

/**
 * @see Mailcode_Commands_Command_ShowEncoded
 * @see ShowEncodedTranslation
 */
final class ShowEncodedTests extends HubLTestCase
{
    public function test_urlEncode() : void
    {
        $tests = array(
            array(
                'label' => 'URL encoding',
                'mailcode' => Mailcode_Factory::show()
                    ->encoded('Foo bar', array(Mailcode_Commands_Keywords::TYPE_URLENCODE)),
                'expected' => '{% set literal001 = "Foo bar" %}{{ literal001|urlencode }}'
            )
        );

        $this->runCommands($tests);
    }
}
