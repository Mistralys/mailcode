<?php

declare(strict_types=1);

namespace MailcodeTests\Highlighting;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_ShowURL;
use MailcodeTestCase;

/**
 * Tests for highlighting of protected content commands, ensuring
 * the internal content ID parameter is not displayed and the full
 * command (opening tag + content + closing tag) is highlighted.
 *
 * @see \Mailcode\Mailcode_Commands_Highlighter_ProtectedContent
 */
final class ProtectedContentTests extends MailcodeTestCase
{
    private const BRACKET_OPEN = '<span class="mailcode-bracket">{</span>';
    private const BRACKET_CLOSE = '<span class="mailcode-bracket">}</span>';
    private const NAME_SHOWURL = '<span class="mailcode-command-name">showurl</span>';
    private const PARAMS_OPEN = '<span class="mailcode-params">';
    private const PARAMS_CLOSE = '</span>';

    /**
     * Verifies the reported bug: ShowURL content commands were highlighted
     * using their internal pre-parsed form (e.g., {showurl: 1 "trackme"})
     * instead of their normalized form.
     */
    public function test_showURL_trackingID() : void
    {
        $subject = <<<'EOT'
{showurl: "trackme"}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseShowURL($subject);

        $expected =
            self::BRACKET_OPEN .
            self::NAME_SHOWURL .
            '<span class="mailcode-hyphen">:</span>' .
            '<wbr>' .
            ' ' .
            self::PARAMS_OPEN .
            '<span class="mailcode-token-stringliteral">"trackme"</span>' .
            self::PARAMS_CLOSE .
            self::BRACKET_CLOSE .
            "\nhttps://mistralys.eu\n" .
            self::BRACKET_OPEN .
            self::NAME_SHOWURL .
            self::BRACKET_CLOSE;

        $highlighted = $command->getHighlighted();

        $this->assertStringNotContainsString(
            '1 "trackme"',
            $highlighted,
            'The content ID must not appear in the highlighted output.'
        );

        $this->assertSame($expected, $highlighted);
    }

    /**
     * Verifies that ShowURL without a tracking ID is highlighted correctly.
     */
    public function test_showURL_noTracking() : void
    {
        $subject = <<<'EOT'
{showurl: no-tracking:}
https://mistralys.eu
{showurl}
EOT;

        $command = $this->parseShowURL($subject);

        $highlighted = $command->getHighlighted();

        $this->assertStringNotContainsString(
            '>1<',
            $highlighted,
            'The content ID must not appear in the highlighted output.'
        );

        $this->assertStringContainsString(
            self::BRACKET_OPEN . self::NAME_SHOWURL . self::BRACKET_CLOSE,
            $highlighted,
            'The highlighted closing tag must be present.'
        );

        $this->assertStringContainsString(
            'https://mistralys.eu',
            $highlighted,
            'The command content (URL) must be present in the highlighted output.'
        );
    }

    /**
     * Verifies that the Code command (the other protected content command)
     * is highlighted correctly.
     */
    public function test_codeCommand() : void
    {
        $subject = <<<'EOT'
{code: "ApacheVelocity"}
#set( $foo = "bar" )
{code}
EOT;

        $collection = Mailcode::create()->parseString($subject);
        $this->assertCollectionValid($collection);

        $command = $collection->getFirstCommand();
        $this->assertNotNull($command);

        $highlighted = $command->getHighlighted();

        $this->assertStringNotContainsString(
            '>1<',
            $highlighted,
            'The content ID must not appear in the highlighted output.'
        );

        $this->assertStringContainsString(
            '#set( $foo = "bar" )',
            $highlighted,
            'The code content must be present in the highlighted output.'
        );

        $this->assertStringContainsString(
            '<span class="mailcode-bracket">{</span><span class="mailcode-command-name">code</span><span class="mailcode-bracket">}</span>',
            $highlighted,
            'The highlighted closing tag must be present.'
        );
    }

    private function parseShowURL(string $subject) : Mailcode_Commands_Command_ShowURL
    {
        $collection = Mailcode::create()->parseString($subject);
        $this->assertCollectionValid($collection);

        $command = $collection->getFirstCommand();
        $this->assertNotNull($command);
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowURL::class, $command);

        return $command;
    }
}
