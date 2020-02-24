<?php

use Mailcode\Mailcode;

final class Mailcode_MailcodeTests extends MailcodeTestCase
{
    public function test_createCode()
    {
        $code = Mailcode::create();
        
        $this->assertInstanceOf(Mailcode::class, $code);
    }
}
