<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\Commands\Command\ShowURL\AutoTrackingID;
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

    /**
     * Tracking is enabled by default, even if no tracking
     * ID has been specified. In this case, an automatically
     * generated ID is used.
     */
    public function test_tracking_defaultEnabled() : void
    {
        $subject = <<<'EOT'
{showurl: "param=value"}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertTrue($command->isTrackingEnabled());
        $this->assertNotEmpty($command->getTrackingID());
        $this->assertSame($this->autoLinkName, $command->getTrackingID());
    }

    /**
     * The command requires parameters to be set,
     * so an empty tracking ID can be used to have
     * one generated automatically.
     */
    public function test_emptyTrackingID() : void
    {
        // An empty tracking ID must be specified
        $subject = <<<'EOT'
{showurl: ""}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertTrue($command->isTrackingEnabled());
        $this->assertNotEmpty($command->getTrackingID());
        $this->assertSame($this->autoLinkName, $command->getTrackingID());
    }

    /**
     * When tracking is disabled, no tracking ID is
     * available.
     */
    public function test_trackingDisabled() : void
    {
        $subject = <<<'EOT'
{showurl: no-tracking:}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertFalse($command->isTrackingEnabled());
        $this->assertEmpty($command->getTrackingID());
        $this->assertSame($subject, $command->getNormalized());
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

        $this->assertSame('trackme', $command->getTrackingID());
        $this->assertSame($subject, $command->getNormalized());
    }

    public function test_normalize_trackingDisabledWithQueryParam() : void
    {
        $subject = <<<'EOT'
{showurl: no-tracking: "param=value"}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertSame($subject, $command->getNormalized());
    }

    public function test_nestedMailcode() : void
    {
        $subject = <<<'EOT'
{showurl: "trackme"}
{showvar: $FOO}
{showurl}
EOT;

        $command = $this->parseCommand($subject);

        $this->assertTrue($command->isMailcodeEnabled());

        $collection = $command->getNestedMailcode();
        $commands = $collection->getCommands();

        $this->assertCount(1, $commands);
        $this->assertSame('showvar', $commands[0]->getName());
    }

    /**
     * When fetching the command's list of variables,
     * the variables used in nested commands must be
     * included in the collection.
     */
    public function test_getNestedVariables() : void
    {
        $subject = <<<'EOT'
{showurl: "trackme"}
{showvar: $FOO}
{if variable: $BAR == ""}
https://mistralys.eu
{end}
{showurl}
EOT;

        $command = $this->parseCommand($subject);
        $variables = $command->getVariables()->getAll();

        $this->assertCount(2, $variables);
        $this->assertSame('$FOO', $variables[0]->getFullName());
        $this->assertSame('$BAR', $variables[1]->getFullName());
    }

    public function test_autoTracker_resetCustomGenerator() : void
    {
        AutoTrackingID::resetGenerator();

        $this->assertFalse(AutoTrackingID::hasCustomGenerator());
    }

    // endregion

    // region: Support methods

    protected string $autoLinkName;

    protected function setUp() : void
    {
        parent::setUp();

        AutoTrackingID::resetLinkCounter();

        $this->autoLinkName = sprintf(AutoTrackingID::AUTO_ID_TEMPLATE, 1);
    }

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
