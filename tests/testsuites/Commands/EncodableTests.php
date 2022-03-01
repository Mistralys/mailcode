<?php

declare(strict_types=1);

namespace testsuites\Commands;

use Mailcode\Mailcode_Commands_Command_ShowEncoded;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Factory;
use MailcodeTestCase;

final class EncodableTests extends MailcodeTestCase
{
    // region: _Tests

    public function test_disableEncoding() : void
    {
        $command = $this->createCommand();

        $command->setEncodingEnabled(Mailcode_Commands_Keywords::TYPE_URLENCODE, false);

        $this->assertFalse($command->isEncodingEnabled(Mailcode_Commands_Keywords::TYPE_URLENCODE));
        $this->assertFalse($command->isURLEncoded());
    }

    public function test_enableEncoding() : void
    {
        $command = $this->createCommand();

        $this->assertFalse($command->isIDNEncoded());

        $command->setEncodingEnabled(Mailcode_Commands_Keywords::TYPE_IDN_ENCODE, true);

        $this->assertTrue($command->isEncodingEnabled(Mailcode_Commands_Keywords::TYPE_IDN_ENCODE));
        $this->assertTrue($command->isIDNEncoded());
    }

    /**
     * When enabling encodings programmatically, they
     * must keep the code order when normalizing the
     * command, so the order stays intact.
     */
    public function test_enableEncodingsOrder() : void
    {

    }

    // endregion

    // region: Support methods

    private function createCommand() : Mailcode_Commands_Command_ShowEncoded
    {
        $cmd = Mailcode_Factory::show()->encoded('Test', array(Mailcode_Commands_Keywords::TYPE_URLENCODE));

        $this->assertTrue($cmd->isURLEncoded());

        return $cmd;
    }

    // endregion
}
