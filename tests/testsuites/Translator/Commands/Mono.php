<?php

use Mailcode\Mailcode;

final class Translator_Velocity_MonoTests extends VelocityTestCase
{
    public function test_translateCommand()
    {
        $string = '{mono}Monospaced{end}';
        $expected = 'Monospaced';

        $safe = Mailcode::create()->createSafeguard($string);
        $translator = Mailcode::create()->createTranslator()->createSyntax('ApacheVelocity');
        $result = $translator->translateSafeguard($safe);

        $this->assertEquals($expected, $result);
    }
}
