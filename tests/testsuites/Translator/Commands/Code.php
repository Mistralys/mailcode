<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Translator;

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

        $translator = new Mailcode_Translator();
        $result = $translator->createSyntax('ApacheVelocity')->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }
}
