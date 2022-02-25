<?php

declare(strict_types=1);

namespace testsuites\Factory\Commands;

use Mailcode\Mailcode_Factory;
use Mailcode\Parser\PreParser;
use MailcodeTestCase;

class ShowURLTests extends MailcodeTestCase
{
    protected function setUp() : void
    {
        parent::setUp();

        PreParser::reset();
    }

    public function test_createDefault() : void
    {
        $url = 'https://mistralys.eu';

        $cmd = Mailcode_Factory::show()->url($url);

        $this->assertTrue($cmd->isTrackingEnabled());
        $this->assertSame($url, $cmd->getURL());
        $this->assertFalse($cmd->hasTrackingID());
        $this->assertFalse($cmd->hasQueryParams());
    }

    public function test_createWithTracking() : void
    {
        $url = 'https://mistralys.eu';

        $cmd = Mailcode_Factory::show()->url($url, 'trackme');

        $this->assertSame('{showurl: "trackme"}'.$url.'{showurl}', $cmd->getNormalized());
        $this->assertSame($url, $cmd->getURL());
        $this->assertTrue($cmd->isTrackingEnabled());
        $this->assertTrue($cmd->hasTrackingID());
        $this->assertSame('trackme', $cmd->getTrackingID());
        $this->assertFalse($cmd->hasQueryParams());
    }

    public function test_createWithParams() : void
    {
        $url = 'https://mistralys.eu';

        $cmd = Mailcode_Factory::show()->url($url, 'trackme', array('foo' => 'bar'));

        $this->assertTrue($cmd->isTrackingEnabled());
        $this->assertTrue($cmd->hasTrackingID());
        $this->assertSame($url, $cmd->getURL());
        $this->assertTrue($cmd->hasQueryParams());
        $this->assertSame('bar', $cmd->getQueryParam('foo'));
    }

    public function test_createWithCommands() : void
    {
        $url = 'https://mistralys.eu?param={showvar: $FOO}';

        $cmd = Mailcode_Factory::show()->url($url, 'trackme', array('foo', 'bar'));

        $this->assertSame($url, $cmd->getURL());
    }
}
