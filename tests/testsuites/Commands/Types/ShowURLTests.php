<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_ShowURL;
use MailcodeTestCase;

final class ShowURLTests extends MailcodeTestCase
{
    // region: _Tests

    public function test_paramsWithEqualSigns() : void
    {
        $subject = <<<'EOT'
{showurl: "param=Value=Other=Blah"}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertTrue($command->hasQueryParam('param'));
        $this->assertSame('Value=Other=Blah', $command->getQueryParam('param'));
    }

    public function test_getURL() : void
    {
        $subject = <<<'EOT'
{showurl: "trackme"}
%1$s
{showurl}
EOT;

        $tests = array(
            array(
                'label' => 'Regular URL',
                'url' => 'https://mistralys.eu'
            ),
            array(
                'label' => 'URL with variable',
                'url' => 'https://mistralys.eu?param={showvar: $PRODUCT.ID}'
            ),
            array(
                'label' => 'URL with if command',
                'url' => 'https://mistralys.eu?{if variable: $PRODUCT.FREE == "true"}param={showvar: $PRODUCT.ID}{end}'
            )
        );

        foreach($tests as $test)
        {
            $testSubject = sprintf($subject, $test['url']);

            $command = $this->parseCommand($testSubject);

            $this->assertSame($test['url'], $command->getURL());
        }
    }

    public function test_tracking_defaultEnabled() : void
    {
        $subject = <<<'EOT'
{showurl: "param=value"}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertTrue($command->isTrackingEnabled());
    }

    public function test_tracking_disabled() : void
    {
        $subject = <<<'EOT'
{showurl: no-tracking:}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertFalse($command->isTrackingEnabled());
    }

    public function test_setTrackingEnabled() : void
    {
        $subject = <<<'EOT'
{showurl: no-tracking:}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertFalse($command->isTrackingEnabled());
        $this->assertNotNull($command->getNoTrackingToken());

        $command->setTrackingEnabled(true);

        $this->assertTrue($command->isTrackingEnabled());
        $this->assertNull($command->getNoTrackingToken());
    }

    public function test_normalize_trackingEnabled() : void
    {
        $subject = <<<'EOT'
{showurl: "trackme"}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertSame($subject, $command->getNormalized());
    }

    public function test_normalize_trackingDisabled() : void
    {
        $subject = <<<'EOT'
{showurl: no-tracking: "param=value"}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertSame($subject, $command->getNormalized());
    }

    // endregion

    // region: Support methods

    private function parseCommand(string $subject) : Mailcode_Commands_Command_ShowURL
    {
        $collection = Mailcode::create()->parseString($subject);
        $this->assertCollectionValid($collection);

        $command = $collection->getFirstCommand();
        $this->assertNotNull($command);

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowURL::class, $command);

        return $command;
    }

    // endregion
}
