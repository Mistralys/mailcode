<?php

declare(strict_types=1);

namespace testsuites\Translator\Commands;

use Mailcode\Mailcode_Factory;
use Mailcode\Translator\Syntax\ApacheVelocity\ShowURLTranslation;
use MailcodeTestClasses\VelocityTestCase;

/**
 * @see ShowURLTranslation
 */
final class ShowURLTests extends VelocityTestCase
{
    // region: _Tests

    /**
     * By default, the tracking call contains the
     * `url()` and `lt()` calls to set the URL and
     * define the tracking details.
     */
    public function test_default() : void
    {
        $url = 'https://mistralys.eu';
        $trackingID = 'trackme';
        $cmd = Mailcode_Factory::show()->url($url, $trackingID);

        $this->assertSame(
            sprintf(
                $this->baseTemplate,
                $this->varName,
                $url,
                sprintf(
                    '.lt(${tracking_host}, ${envelope.hash}, "%1$s")',
                    $trackingID
                )
            ),
            $this->translateCommand($cmd)
        );
    }

    /**
     * When tracking is disabled, the `lt()` method
     * call must be omitted.
     */
    public function test_noTracking() : void
    {
        $url = 'https://mistralys.eu';
        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false);

        $this->assertSame(
            sprintf(
                $this->baseTemplate,
                $this->varName,
                $url,
                ''
            ),
            $this->translateCommand($cmd)
        );
    }

    /**
     * Additional query parameters must be appended
     * to the method call, with the correct escaping
     * of values.
     */
    public function test_queryParams() : void
    {
        $url = 'https://mistralys.eu';
        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false)
            ->setQueryParam('foo', 'bar')
            ->setQueryParam('quotes', '"quoted"');

        $expected = sprintf(
            $this->baseTemplate,
            $this->varName,
            $url,
            '.query("foo", "bar")'.
            '.query("quotes", "\"quoted\"")'
        );

        $this->assertSame(
            $expected,
            $this->translateCommand($cmd)
        );
    }

    /**
     * When using a URL which in turn contains commands,
     * these must be translated to Velocity as well.
     */
    public function test_nestedCommands() : void
    {
        $url = <<<'EOT'
{if variable: $COUNTRY == "fr"}
https://mistralys.fr
{else}
https://mistralys.eu
{end}
EOT;

        $expectedURL = <<<'EOT'
#if($COUNTRY == "fr")https://mistralys.fr#{else}https://mistralys.eu#{end}
EOT;

        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false);

        $expected = sprintf(
            $this->baseTemplate,
            $this->varName,
            $expectedURL,
            ''
        );

        $this->assertSame(
            $expected,
            $this->translateCommand($cmd)
        );
    }

    public function test_idnEncodeDomainVariable() : void
    {
        $url = 'https://mistralys.eu?domain={showvar: $DOMAIN.NAME idnencode:}';
        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false);

        // Since the showvar command is used in a URL, it automatically gets
        // the URL encoding, which is not an issue, because the IDN encoded
        // domain is already fully URL encoding compatible.
        $expectedURL = 'https://mistralys.eu?domain=${esc.url(${text.idn($DOMAIN.NAME)})}';

        $expected = sprintf(
            $this->baseTemplate,
            $this->varName,
            $expectedURL,
            ''
        );

        $this->assertSame(
            $expected,
            $this->translateCommand($cmd)
        );
    }

    public function test_idnEncodeDomainString() : void
    {
        $url = 'https://mistralys.eu?domain={showencoded: "iönöüs.com" idnencode:}';
        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false);

        // Since the showvar command is used in a URL, it automatically gets
        // the URL encoding, which is not an issue, because the IDN encoded
        // domain is already fully URL encoding compatible.
        $expectedURL = 'https://mistralys.eu?domain=${esc.url(${text.idn("iönöüs.com")})}';

        $expected = sprintf(
            $this->baseTemplate,
            $this->varName,
            $expectedURL,
            ''
        );

        $this->assertSame(
            $expected,
            $this->translateCommand($cmd)
        );
    }

    public function test_idnEncodeVariableURL() : void
    {
        $url = '{showvar: $PRODUCT.URL}?domain={showencoded: "iönöüs.com" idnencode:}';
        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false);

        // The automated URL encoding does not work here, because
        // the variable-based URL cannot be recognized as a URL.
        $expectedURL = '${PRODUCT.URL}?domain=${text.idn("iönöüs.com")}';

        $expected = sprintf(
            $this->baseTemplate,
            $this->varName,
            $expectedURL,
            ''
        );

        $this->assertSame(
            $expected,
            $this->translateCommand($cmd)
        );
    }

    public function test_shortenEnabled() : void
    {
        $url = 'https://mistralys.eu';
        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false)
            ->setShortenEnabled(true);

        $expected = sprintf(
            $this->baseTemplate,
            $this->varName,
            $url,
            '.shorten()'
        );

        $this->assertSame(
            $expected,
            $this->translateCommand($cmd)
        );
    }

    public function test_shortenWithTracking() : void
    {
        $url = 'https://mistralys.eu';
        $trackingID = 'trackme';
        $cmd = Mailcode_Factory::show()->url($url, $trackingID)
            ->setShortenEnabled(true);

        $expected = sprintf(
            $this->baseTemplate,
            $this->varName,
            $url,
            sprintf(
                '.lt(${tracking_host}, ${envelope.hash}, "%1$s").shorten()',
                $trackingID
            )
        );

        $this->assertSame(
            $expected,
            $this->translateCommand($cmd)
        );
    }

    public function test_shortenWithQueryParams() : void
    {
        $url = 'https://mistralys.eu';
        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false)
            ->setShortenEnabled(true)
            ->setQueryParam('foo', 'bar');

        $expected = sprintf(
            $this->baseTemplate,
            $this->varName,
            $url,
            '.shorten().query("foo", "bar")'
        );

        $this->assertSame(
            $expected,
            $this->translateCommand($cmd)
        );
    }

    // endregion

    // region: Support methods

    private string $baseTemplate =
        '#{define}($%1$s)%2$s#{end}'.
        '${tracking'.
        '.url(${%1$s})%3$s}';

    private string $varName = '';

    protected function setUp() : void
    {
        parent::setUp();

        ShowURLTranslation::resetURLCounter();

        $this->varName = sprintf(ShowURLTranslation::URL_VAR_TEMPLATE, 1);
    }

    // endregion
}
