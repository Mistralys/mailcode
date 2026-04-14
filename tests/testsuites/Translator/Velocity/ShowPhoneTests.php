<?php


declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class ShowPhoneTests extends VelocityTestCase
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
