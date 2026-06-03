<?php

declare(strict_types=1);

namespace MailcodeTests\PreProcessor;

use Mailcode\Mailcode;
use MailcodeTestCase;

/**
 * Tests that escaped brackets in template text (\{ and \}) are correctly
 * unescaped in the output of PreProcessor::render().
 */
final class EscapedBracketsTests extends MailcodeTestCase
{
    /**
     * Escaped brackets alongside a pre-processor command must be unescaped
     * in render() output while the command is applied correctly.
     */
    public function test_renderWithEscapedBrackets(): void
    {
        $subject = '\{SAFE\} {mono}text{end}';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertStringContainsString('{SAFE}', $result);
        $this->assertStringContainsString('<code>text</code>', $result);
        $this->assertStringNotContainsString('\{', $result);
    }

    /**
     * Escaped brackets with no pre-processor commands at all.
     */
    public function test_renderNoCommandsWithEscapedBrackets(): void
    {
        $subject = 'Hello \{WORLD\}';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertEquals('Hello {WORLD}', $result);
    }

    /**
     * Multiple escaped sequences in a single template.
     */
    public function test_renderMultipleEscapedBrackets(): void
    {
        $subject = '\{A\} {mono}code{end} \{B\}';

        $processor = Mailcode::create()->createPreProcessor($subject);
        $result = $processor->render();

        $this->assertStringContainsString('{A}', $result);
        $this->assertStringContainsString('{B}', $result);
        $this->assertStringContainsString('<code>code</code>', $result);
        $this->assertStringNotContainsString('\{', $result);
    }
}
