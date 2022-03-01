<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowEncoded;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Commands_Keywords;
use MailcodeTestCase;

final class ShowEncodedTests extends MailcodeTestCase
{
    // region: _Tests

    public function test_validation() : void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{showencoded:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'Without encodings',
                'string' => '{showencoded: "text"}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_NO_ENCODINGS_SPECIFIED
            ),
            array(
                'label' => 'With unused keyword',
                'string' => '{showencoded: "text" multiline:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid encoding',
                'string' => '{showencoded: "text" urlencode:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With multiple encodings',
                'string' => '{showencoded: "text" urlencode: idnencode:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With random order',
                'string' => '{showencoded: idnencode: "text" urlencode:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With encodings but no text',
                'string' => '{showencoded: idnencode:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowEncoded::VALIDATION_MISSING_SUBJECT_STRING
            )
        );

        $this->runCollectionTests($tests);
    }

    /**
     * The encodings must be accessible in the exact
     * same order than they are specified in the command's
     * parameters, since they are also applied in this
     * order.
     */
    public function test_keywordOrder() : void
    {
        $subject = <<<'EOT'
{showencoded: "text" urlencode: idnencode:}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertTrue($command->isURLEncoded());
        $this->assertTrue($command->isIDNEncoded());

        $list = $command->getActiveEncodings();
        $this->assertCount(2, $list);
        $this->assertSame(Mailcode_Commands_Keywords::TYPE_URLENCODE, $list[0]);
        $this->assertSame(Mailcode_Commands_Keywords::TYPE_IDN_ENCODE, $list[1]);
    }

    public function test_getText() : void
    {
        $subject = <<<'EOT'
{showencoded: "Subject with \"quotes\" and \{brackets\}" idnencode:}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertSame('Subject with "quotes" and {brackets}', $command->getText());
    }

    public function test_setText() : void
    {
        $subject = <<<'EOT'
{showencoded: "Initial text" idnencode:}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertSame('Initial text', $command->getText());

        $command->setText('New text');
        $this->assertSame('New text', $command->getText());

        $command->setText('"Quoted"');
        $this->assertSame('"Quoted"', $command->getText());

        $command->setText('\"Escaped\"');
        $this->assertSame('"Escaped"', $command->getText());

        $command->setText('{Brackets}');
        $this->assertSame('{Brackets}', $command->getText());

        $command->setText('\{Brackets\}');
        $this->assertSame('{Brackets}', $command->getText());
    }

    // endregion

    // region: Support methods

    private function parseCommand(string $subject) : Mailcode_Commands_Command_ShowEncoded
    {
        $command = $this->parseCommandStringValid($subject);

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowEncoded::class, $command);

        return $command;
    }

    // endregion
}
