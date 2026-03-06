<?php


declare(strict_types=1);

namespace MailcodeTests\Factory;

use MailcodeTestCase;
use Mailcode\Mailcode_Factory;

final class FactoryTests extends MailcodeTestCase
{
    public function test_filterVariableName() : void
    {
        $var = Mailcode_Factory::show()->var('     $FOO   .     BAR    ');
        
        $this->assertTrue($var->isValid());
        $this->assertSame('{showvar: $FOO.BAR}', $var->getNormalized());
    }
}
