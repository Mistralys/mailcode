<?php

declare(strict_types=1);

namespace testsuites\Factory\Commands;

use Mailcode\Commands\Command\ShowURL\AutoTrackingID;
use Mailcode\Mailcode_Factory;
use Mailcode\Parser\PreParser;
use MailcodeTestCase;

class ShowURLTests extends MailcodeTestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        PreParser::reset();
        AutoTrackingID::resetLinkCounter();
    }

    public function test_createDefault() : void
    {
        $url = 'https://mistralys.eu';

        $cmd = Mailcode_Factory::show()->url($url);

        $this->assertTrue($cmd->isTrackingEnabled());
        $this->assertSame($url, $cmd->getURL());
        $this->assertFalse($cmd->hasQueryParams());
    }

    public function test_createWithTracking() : void
    {
        $url = 'https://mistralys.eu';

        $cmd = Mailcode_Factory::show()->url($url, 'trackme');

        $this->assertSame('{showurl: "trackme"}'.$url.'{showurl}', $cmd->getNormalized());
        $this->assertSame($url, $cmd->getURL());
        $this->assertTrue($cmd->isTrackingEnabled());
        $this->assertSame('trackme', $cmd->getTrackingID());
        $this->assertFalse($cmd->hasQueryParams());
    }

    public function test_createWithParams() : void
    {
        $url = 'https://mistralys.eu';

        $cmd = Mailcode_Factory::show()->url($url, 'trackme', array('foo' => 'bar'));

        $this->assertTrue($cmd->isTrackingEnabled());
        $this->assertSame($url, $cmd->getURL());
        $this->assertTrue($cmd->hasQueryParams());
        $this->assertSame('bar', $cmd->getQueryParam('foo'));
    }

    public function test_createWithCommands() : void
    {
        $url = 'https://mistralys.eu?param={showvar: $FOO}';

        $cmd = Mailcode_Factory::show()->url($url, 'trackme', array('foo' => 'bar'));

        $this->assertSame($url, $cmd->getURL());
    }

    public function test_normalizeTrackingID() : void
    {
        $url = 'https://mistralys.eu';

        $expected = <<<'EOT'
{showurl: "link-001"}https://mistralys.eu{showurl}
EOT;

        $cmd = Mailcode_Factory::show()->url($url);

        $this->assertSame($expected, $cmd->getNormalized());
    }

    public function test_setTrackingID() : void
    {
        $url = 'https://mistralys.eu';

        $expected = <<<'EOT'
{showurl: "trackmenow"}https://mistralys.eu{showurl}
EOT;

        $cmd = Mailcode_Factory::show()->url($url);

        $this->assertSame('link-001', $cmd->getTrackingID());

        $cmd->setTrackingID('trackmenow');

        $this->assertSame('trackmenow', $cmd->getTrackingID());
        $this->assertSame($expected, $cmd->getNormalized());
    }

    public function test_setTrackingIDNoTracking() : void
    {
        $cmd = Mailcode_Factory::show()
            ->url('https://mistralys.eu')
            ->setTrackingEnabled(false);

        $this->assertFalse($cmd->isTrackingEnabled());
        $this->assertEmpty($cmd->getTrackingID());

        $cmd->setTrackingID('trackme');

        $this->assertEmpty($cmd->getTrackingID());
    }

    public function test_getTrackingIDAfterDisabling() : void
    {
        $cmd = Mailcode_Factory::show()
            ->url('https://mistralys.eu', 'trackme');

        $this->assertTrue($cmd->isTrackingEnabled());
        $this->assertSame('trackme', $cmd->getTrackingID());

        $cmd->setTrackingEnabled(false);

        $this->assertSame('{showurl: no-tracking:}https://mistralys.eu{showurl}', $cmd->getNormalized());
        $this->assertFalse($cmd->isTrackingEnabled());
        $this->assertEmpty($cmd->getTrackingID());
    }

    public function test_setQueryParam() : void
    {
        $url = 'https://mistralys.eu';

        $expected = <<<'EOT'
{showurl: "link-001" "foo=bar" "quoted=\"quoted\""}https://mistralys.eu{showurl}
EOT;

        $cmd = Mailcode_Factory::show()
            ->url($url)
            ->setQueryParam('foo', 'bar')
            ->setQueryParam('quoted', '"quoted"');

        $this->assertSame($expected, $cmd->getNormalized());
    }

    public function test_setEmptyTrackingID() : void
    {
        $cmd = Mailcode_Factory::show()->url('https://mistralys.eu');

        $expected = <<<'EOT'
{showurl: "link-001"}https://mistralys.eu{showurl}
EOT;

        $cmd->setTrackingID('');

        $this->assertSame($expected, $cmd->getNormalized());
        $this->assertNotEmpty($cmd->getTrackingID()); // must be after getNormalized().
    }
}
