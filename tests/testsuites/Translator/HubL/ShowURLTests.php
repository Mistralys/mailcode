<?php

declare(strict_types=1);

namespace testsuites\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

/**
 * @see ShowURLTranslation
 */
final class ShowURLTests extends HubLTestCase
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
            'https://mistralys.eu',
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
            'https://mistralys.eu',
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

        $this->assertSame(
            'https://mistralys.eu',
            $this->translateCommand($cmd)
        );
    }

    /**
     * When using a URL which in turn contains commands,
     * these must be translated to Velocity as well.
     */
    public function test_nestedCommands() : void
    {
        $this->markTestIncomplete('IF commands not ready yet');

        $url = <<<'EOT'
{if variable: $COUNTRY == "fr"}
https://mistralys.fr
{else}
https://mistralys.eu
{end}
EOT;

        $expectedURL = <<<'EOT'
{% if country == "fr" %}https://mistralys.fr{% else %}https://mistralys.eu{% endif %}
EOT;

        $cmd = Mailcode_Factory::show()->url($url)
            ->setTrackingEnabled(false);

        $this->assertSame(
            $expectedURL,
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
        $expectedURL = 'https://mistralys.eu?domain={{ domain.name|urlencode }}';

        $this->assertSame(
            $expectedURL,
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
        $expectedURL = 'https://mistralys.eu?domain={% set literal001 = "iönöüs.com" %}{{ literal001|urlencode }}';

        $this->assertSame(
            $expectedURL,
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
        $expectedURL = '{{ product.url }}?domain={% set literal001 = "iönöüs.com" %}{{ literal001 }}';

        $this->assertSame(
            $expectedURL,
            $this->translateCommand($cmd)
        );
    }

    // endregion
}
