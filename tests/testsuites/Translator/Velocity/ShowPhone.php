<?php

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_Velocity_ShowPhoneTests extends VelocityTestCase
{
    public function test_translateCommand() : void
    {
        $tests = array(
            array(
                'label' => 'Simple command',
                'mailcode' => Mailcode_Factory::show()->phone('PHONE', 'DE'),
                'expected' => <<<'EOD'
${phone.e164($PHONE, 'DE')}
EOD
            )
        );
        
        $this->runCommands($tests);
    }
}
