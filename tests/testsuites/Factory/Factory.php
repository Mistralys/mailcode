<?php

use Mailcode\Mailcode_Factory;

final class Factory_FactoryTests extends MailcodeTestCase
{
    public function test_filterVariableName()
    {
        $var = Mailcode_Factory::show()->showVar('     $FOO   .     BAR    ');
        
        $this->assertTrue($var->isValid());
        $this->assertSame('{showvar: $FOO.BAR}', $var->getNormalized());
    }
}
