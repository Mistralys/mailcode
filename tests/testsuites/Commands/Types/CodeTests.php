<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_Code;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Interfaces_Commands_ProtectedContent;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;
use Mailcode\Parser\PreParser;
use MailcodeTestCase;

final class CodeTests extends MailcodeTestCase
{
    public function test_validation() : void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{code}{code}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_MISSING_CONTENT_OPENING_TAG
            ),
            array(
                'label' => 'Empty parameters',
                'string' => '{code: }{code}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_Code::VALIDATION_LANGUAGE_NOT_SPECIFIED
            ),
            array(
                'label' => 'With a variable',
                'string' => '{code: $FOO.BAR}{code}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_Code::VALIDATION_LANGUAGE_NOT_SPECIFIED
            ),
            array(
                'label' => 'With invalid syntax name',
                'string' => '{code: "Unknown syntax"}{code}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_Code::VALIDATION_UNKNOWN_LANGUAGE
            ),
            array(
                'label' => 'With valid syntax',
                'string' => sprintf('{code: "%s"}{code}', Mailcode_Commands_Command_Code::SYNTAX_APACHE_VELOCITY),
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_getSyntaxName() : void
    {
        $syntaxes = Mailcode_Commands_Command_Code::getSupportedSyntaxes();

        foreach($syntaxes as $syntax)
        {
            $cmd = Mailcode_Factory::misc()->code($syntax, 'dummy content');
            $this->assertEquals($syntax, $cmd->getSyntaxName());
        }
    }
    
   /**
    * Ensure that trying to get the syntax name of an erroneous
    * code command will throw an exception.
    */
    public function test_getSyntax_exception() : void
    {
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'Code',
            '',
            'params',
            '{code: params}'
        );

        if($cmd instanceof Mailcode_Commands_Command_Code)
        {
            $this->assertFalse($cmd->isValid());

            $this->expectException(Mailcode_Exception::class);

            $cmd->getSyntaxName();
            return;
        }

        $this->fail('Invalid instance: '.get_class($cmd));
    }

    /**
     * Commands that protect their content must strip it out
     * and replace it with a placeholder text during the
     * safeguarding process, so it cannot be transformed or
     * even formatted by the selected formatters.
     *
     * It is restored once the string is made whole again.
     */
    public function test_stripCode() : void
    {
        $code = <<<'EOD'
${FOO.urlencode()}
#if($ARGH == "Blablup")
   Some text
#endif
EOD;

        // The commands string we will be working with,
        // which has linebreaks after the code command,
        // and before the end command.
        $string = <<<EOT
Some text here,
{code: "ApacheVelocity"}
$code
{code}
And more here.
EOT;

        $safeguard = Mailcode::create()->createSafeguard($string);
        $this->assertSafeguardValid($safeguard);

        $safe = $safeguard->makeSafe();

        $placeholders = $safeguard->getPlaceholdersCollection()->getAll();
        $found = false;

        foreach($placeholders as $placeholder)
        {
            $command = $placeholder->getCommand();
            if($command instanceof Mailcode_Interfaces_Commands_ProtectedContent)
            {
                // The stored content of the command must
                // be the exact code we inserted between the commands.
                $this->assertEquals($command->getContentTrimmed(), $code);
                $found = true;
            }
        }

        $this->assertTrue($found);

        $this->assertEquals($string, $safeguard->makeWhole($safe));
    }

    /**
     * Nesting Mailcode commands makes no real sense
     */
    public function test_code_nestedCommands() : void
    {
        $string = <<<'EOD'
{showvar: $FOO}
{code: "Mailcode"}
Some code here and a variable {showvar: $FOO}
{code}
EOD;
        $expectedContent = <<<'EOD'

Some code here and a variable {showvar: $FOO}

EOD;

        $safeguard = Mailcode::create()->createSafeguard($string);

        $this->assertSafeguardValid($safeguard);

        $collection = $safeguard->getPlaceholdersCollection();
        $this->assertCount(2, $collection->getAll());

        $safe = $safeguard->makeSafe();

        $command = $collection
            ->getByIndex(1)
            ->getCommand();

        if ($command instanceof Mailcode_Commands_Command_Code)
        {
            $content = $command->getContent();

            $this->assertSame($expectedContent, $content);
            $this->assertSame($string, $safeguard->makeWhole($safe));
        }
        else
        {
            $this->fail('Not a code command.');
        }
    }

    /**
     * When normalizing a protected content command,
     * it must be restored to the original command
     * string instead of the inline version that the
     * pre-parser created for the main parser.
     */
    public function test_normalize() : void
    {
        $subject = <<<'EOD'
{code: "Mailcode"}
Some code here and a variable {showvar: $FOO}.
{code}
EOD;
        $expected = <<<'EOD'
{code: "Mailcode"}
Some code here and a variable {showvar: $FOO}.
{code}
EOD;

        $collection = Mailcode::create()->parseString($subject);

        $this->assertCollectionValid($collection);

        $command = $collection->getFirstCommand();

        $this->assertNotNull($command);
        $this->assertInstanceOf(Mailcode_Commands_Command_Code::class, $command);
        $this->assertSame($expected, $command->getNormalized());
    }

    public function test_getContent() : void
    {
        $subject = <<<'EOD'
{code: "Mailcode"}
Some code here and a variable {showvar: $FOO}.
{code}
EOD;
        $expected = <<<'EOD'
Some code here and a variable {showvar: $FOO}.
EOD;

        $collection = Mailcode::create()->parseString($subject);

        $this->assertCollectionValid($collection);

        $command = $collection->getFirstCommand();

        $this->assertNotNull($command);
        $this->assertInstanceOf(Mailcode_Commands_Command_Code::class, $command);
        $this->assertSame($expected, $command->getContentTrimmed());
    }

    public function test_getContentEmptyCommand() : void
    {
        $subject = <<<'EOD'
{code: "Mailcode"}{code}
EOD;

        $collection = Mailcode::create()->parseString($subject);
        $this->assertCollectionValid($collection);
        $command = $collection->getFirstCommand();

        $this->assertNotNull($command);
        $this->assertInstanceOf(Mailcode_Commands_Command_Code::class, $command);
        $this->assertSame('', $command->getContentTrimmed());
    }
}
