<?php

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_Code;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Interfaces_Commands_ProtectedContent;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;

final class Mailcode_CodeTests extends MailcodeTestCase
{
    public function test_validation() : void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{code}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'Empty parameters',
                'string' => '{code: }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With a variable',
                'string' => '{code: $FOO.BAR}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_Code::VALIDATION_LANGUAGE_NOT_SPECIFIED
            ),
            array(
                'label' => 'With invalid syntax name',
                'string' => '{code: "Unknown syntax"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_Code::VALIDATION_UNKNOWN_LANGUAGE
            ),
            array(
                'label' => 'With valid syntax',
                'string' => '{code: "ApacheVelocity"}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_getSyntaxName() : void
    {
        $cmd = Mailcode_Factory::misc()->code('ApacheVelocity');
        
        $this->assertEquals('ApacheVelocity', $cmd->getSyntaxName());
        $this->assertEquals('ApacheVelocity', $cmd->getSyntax()->getTypeID());
    }
    
   /**
    * Ensure that trying to get the syntax name of an erroneous
    * code command will throw an exception.
    */
    public function test_getSyntax_exception() : void
    {
        $cmd = $cmd = Mailcode::create()->getCommands()->createCommand(
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
{end}
And more here.
EOT;

        // The content is trimmed automatically to be safe,
        // so the resulting string must be trimmed.
        $whole = <<<EOT
Some text here,
{code: "ApacheVelocity"}$code{end}
And more here.
EOT;

        $safeguard = Mailcode::create()->createSafeguard($string);
        $safe = $safeguard->makeSafe();

        $placeholders = $safeguard->getPlaceholders();
        $found = false;

        foreach($placeholders as $placeholder)
        {
            $command = $placeholder->getCommand();
            if($command instanceof Mailcode_Interfaces_Commands_ProtectedContent)
            {
                // The stored content of the command must
                // be the exact code we inserted between the commands.
                $this->assertEquals($command->getContent(), $code);
                $found = true;
            }
        }

        $this->assertTrue($found);

        $this->assertEquals($whole, $safeguard->makeWhole($safe));
    }

    public function test_stripCode_nestingError() : void
    {
        $string = <<<'EOD'
{code: "ApacheVelocity"}
    Some code here
    {showvar: $FOO}
{end}
EOD;

        $safeguard = Mailcode::create()->createSafeguard($string);

        try
        {
            $safeguard->makeSafe();
        }
        catch (Mailcode_Exception $e)
        {
            $this->assertEquals(Mailcode_Interfaces_Commands_ProtectedContent::ERROR_INVALID_NESTING_NO_END, $e->getCode());
            return;
        }

        $this->fail('No exception triggered.');
    }
}
