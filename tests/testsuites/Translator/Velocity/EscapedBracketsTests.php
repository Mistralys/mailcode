<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\Velocity;

use Mailcode\Mailcode;
use MailcodeTestClasses\VelocityTestCase;

/**
 * Tests that escaped brackets in template text (\{ and \}) are correctly
 * unescaped in the output of BaseSyntax::translateSafeguard().
 */
final class EscapedBracketsTests extends VelocityTestCase
{
    /**
     * Escaped brackets alongside a real command must be unescaped in the
     * translated output while the command itself is translated correctly.
     */
    public function test_translateWithEscapedBrackets(): void
    {
        $subject = '\{TRACKING\} {showvar: $NAME}';

        $safeguard = Mailcode::create()->createSafeguard($subject);

        $result = $this->translator
            ->createApacheVelocity()
            ->translateSafeguard($safeguard);

        $this->assertStringContainsString('{TRACKING}', $result);
        $this->assertStringContainsString('${NAME}', $result);
        $this->assertStringNotContainsString('\{', $result);
    }

    /**
     * Escaped brackets with no commands at all: the no-placeholder early-return
     * path in translateSafeguard() must also unescape.
     */
    public function test_translateNoCommandsWithEscapedBrackets(): void
    {
        $subject = 'Hello \{WORLD\}';

        $safeguard = Mailcode::create()->createSafeguard($subject);

        $result = $this->translator
            ->createApacheVelocity()
            ->translateSafeguard($safeguard);

        $this->assertEquals('Hello {WORLD}', $result);
    }

    /**
     * Multiple escaped sequences together with a real command.
     */
    public function test_translateMultipleEscapedBrackets(): void
    {
        $subject = '\{A\} {showvar: $NAME} \{B\}';

        $safeguard = Mailcode::create()->createSafeguard($subject);

        $result = $this->translator
            ->createApacheVelocity()
            ->translateSafeguard($safeguard);

        $this->assertStringContainsString('{A}', $result);
        $this->assertStringContainsString('{B}', $result);
        $this->assertStringContainsString('${NAME}', $result);
        $this->assertStringNotContainsString('\{', $result);
    }
}
