<?php

use Mailcode\Mailcode;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_MonoTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $string = '{mono}Monospaced{end}';
        $expected = 'Monospaced';

        $safe = Mailcode::create()->createSafeguard($string);
        $translator = Mailcode::create()->createTranslator()->createApacheVelocity();
        $result = $translator->translateSafeguard($safe);

        $this->assertEquals($expected, $result);
    }
}
