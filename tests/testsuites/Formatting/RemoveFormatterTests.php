<?php

declare(strict_types=1);

namespace testsuites\Formatting;

use Mailcode\Mailcode;
use MailcodeTestCase;

final class RemoveFormatterTests extends MailcodeTestCase
{
    /**
     * When removing commands, whitespace is preserved
     * even when using logic commands.
     */
    public function test_leftoverText() : void
    {
        $subject = <<<'EOT'
{if variable: $FOO == "value"}
Variable here: {showvar: $BAR}
{end}
End of string: {showvar: $FOOBAR}.
EOT;

        $expected = <<<'EOT'

Variable here: 

End of string: .
EOT;

        $safeguard = Mailcode::create()->createSafeguard($subject);
        
        $this->assertSafeguardValid($safeguard);
        
        $safe = $safeguard->makeSafe();
            
        $formatting = $safeguard->createFormatting($safe);
        $formatting->makePartial();
        $formatting->replaceWithRemovedCommands();

        $result = $formatting->toString();

        $this->assertSame($expected, $result);
    }

    /**
     * Code commands must be entirely replaced by an empty string,
     * regardless of their content.
     */
    public function test_codeCommands() : void
    {
        $subject = <<<'EOT'
{code: "ApacheVelocity"}
    Some content of the command.
    {showvar: $SUB.COMMAND}
{code}
EOT;

        $safeguard = Mailcode::create()->createSafeguard($subject);

        $this->assertSafeguardValid($safeguard);

        $safe = $safeguard->makeSafe();

        $formatting = $safeguard->createFormatting($safe);
        $formatting->makePartial();
        $formatting->replaceWithRemovedCommands();

        $result = $formatting->toString();

        $this->assertSame('', $result);
    }
}
