<?php

declare(strict_types=1);

namespace testsuites\Translator\Commands;

use Mailcode\Mailcode_Factory;
use VelocityTestCase;

final class ShowURLTests extends VelocityTestCase
{
    public function test_default() : void
    {
        $cmd = Mailcode_Factory::show()->url('https://mistralys.eu', 'trackme');

        $this->assertSame(
            '$tracking'.
            '.url("https://mistralys.eu")'.
            '.lt(${tracking_host}, ${envelope_hash}, "trackme")',
            $this->translateCommand($cmd)
        );
    }

    public function test_noTracking() : void
    {
        $cmd = Mailcode_Factory::show()->url('https://mistralys.eu')
            ->setTrackingEnabled(false);

        $this->assertSame(
            '$tracking'.
            '.url("https://mistralys.eu")',
            $this->translateCommand($cmd)
        );
    }

    public function test_queryParams() : void
    {
        $cmd = Mailcode_Factory::show()->url('https://mistralys.eu')
            ->setTrackingEnabled(false)
            ->setQueryParam('foo', 'bar')
            ->setQueryParam('quotes', '"quoted"');

        $this->assertSame(
            '$tracking'.
            '.url("https://mistralys.eu")'.
            '.query("foo", "bar")'.
            '.query("quotes", "\"quoted\"")',
            $this->translateCommand($cmd)
        );
    }

    public function test_nestedCommands() : void
    {
        $url = <<<'EOT'
{if variable: $COUNTRY == "fr"}
https://mistralys.fr
{else}
https://mistralys.eu
{end}
EOT;

        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false);

        $this->assertSame(
            '$tracking'.
            '.url("#if($COUNTRY == \"fr\")https://mistralys.fr#{else}https://mistralys.eu#{end}")',
            $this->translateCommand($cmd)
        );
    }
}
