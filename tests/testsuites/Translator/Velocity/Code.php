<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Translator;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_CodeTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $string = <<<'EOD'
Text before
{code: "ApacheVelocity"}
(Velocity statements)
{code}
Text after
EOD;

        $expected = <<<'EOD'
Text before
#**
 Wrapper IF for the code command to insert native ApacheVelocity commands.
 This is needed for technical Mailcode reasons. 
*#
#if(true)(Velocity statements)#{end}
Text after
EOD;

        $safeguard = Mailcode::create()->createSafeguard($string);

        $translator = Mailcode_Translator::create();
        $result = $translator->createApacheVelocity()->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }
}
