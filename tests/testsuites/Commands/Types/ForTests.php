<?php
/**
 * @package MailcodeTests
 * @subpackage Commands
 * @see \MailcodeTests\Commands\Types\ForTests
 */

declare(strict_types=1);

namespace MailcodeTests\Commands\Types;

use Mailcode\Interfaces\Commands\Validation\BreakAtInterface;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Number;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Variables_Variable;
use MailcodeTestCase;

/**
 * @package MailcodeTests
 * @subpackage Commands
 * @covers \Mailcode\Mailcode_Commands_Command_For
 */
final class ForTests extends MailcodeTestCase
{
    public function test_validation(): void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{for}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'Empty parameters',
                'string' => '{for: }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With only a variable',
                'string' => '{for: $FOO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_INVALID_FOR_STATEMENT
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{for: $5OOBAR in: $SOMEVAR}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'With missing container variable',
                'string' => '{for: $RECORD in: }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_INVALID_FOR_STATEMENT
            ),
            array(
                'label' => 'With misspelled keyword',
                'string' => '{for: $FOO in $BAR.FOO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'With wrong keyword',
                'string' => '{for: $FOO insensitive: $BAR.FOO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_WRONG_KEYWORD
            ),
            array(
                'label' => 'With both variables having the same name',
                'string' => '{for: $FOO in: $FOO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_VARIABLE_NAME_IS_THE_SAME
            ),
            array(
                'label' => 'List variable with dot',
                'string' => '{for: $RECORD in: $LIST.PROP}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_VARIABLE_NAME_WITH_DOT
            ),
            array(
                'label' => 'List loop variable with dot',
                'string' => '{for: $RECORD.PROP in: $LIST}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_LOOP_VARIABLE_NAME_WITH_DOT
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{for: $RECORD in: $LIST}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement with break-at (number)',
                'string' => '{for: $RECORD in: $LIST break-at=13}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement with break-at (variable)',
                'string' => '{for: $RECORD in: $LIST break-at=$FOO.BAR}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'String as break-at parameter',
                'string' => '{for: $RECORD in: $LIST break-at="13"}{end}',
                'valid' => false,
                'code' => BreakAtInterface::VALIDATION_BREAK_AT_CODE_WRONG_TYPE
            ),
            array(
                'label' => 'Unquoted text as break-at parameter',
                'string' => '{for: $RECORD in: $LIST break-at=UnquotedText}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Keyword as break-at parameter',
                'string' => '{for: $RECORD in: $LIST break-at=idnencode:}{end}',
                'valid' => false,
                'code' => BreakAtInterface::VALIDATION_BREAK_AT_CODE_WRONG_TYPE
            )
        );

        $this->runCollectionTests($tests);
    }

    public function test_getVariables(): void
    {
        $cmd = Mailcode_Factory::misc()->for('SOURCE', 'LOOP');

        $this->assertNotNull($cmd->getSourceVariable());
        $this->assertNotNull($cmd->getLoopVariable());

        $this->assertEquals('$SOURCE', $cmd->getSourceVariable()->getFullName());
        $this->assertEquals('$LOOP', $cmd->getLoopVariable()->getFullName());
    }

    /**
     * Ensure that trying to get the source variable of an erroneous
     * for command will throw an exception.
     */
    public function test_getVariables_exception(): void
    {
        $cmd = Mailcode::create()->getCommands()->createCommand(
            'For',
            '',
            'params',
            '{for: params}'
        );

        $this->assertInstanceOf(Mailcode_Commands_Command_For::class, $cmd);
        $this->assertFalse($cmd->isValid());

        $this->expectException(Mailcode_Exception::class);

        $cmd->getSourceVariable();
    }

    /**
     * Fetch all list variables found in the command: this is always
     * a single variable, the source variable of the command.
     */
    public function test_getListVariables(): void
    {
        $cmd = Mailcode_Factory::misc()->for('LIST', 'RECORD');

        $listVars = $cmd->getListVariables()->getAll();

        $this->assertCount(1, $listVars);
        $this->assertEquals('$LIST', $listVars[0]->getFullName());
    }

    public function test_getBreakAtNumericToken() : void
    {
        $cmd = Mailcode_Factory::misc()->for('LIST', 'RECORD', 42);

        $break = $cmd->getBreakAtToken();

        $this->assertNotNull($break);
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Number::class, $break);
        $this->assertSame('42', $break->getValue());
    }

    public function test_getBreakAtVariableToken() : void
    {
        $cmd = Mailcode_Factory::misc()->for('LIST', 'RECORD', '$BREAK_AT');

        $break = $cmd->getBreakAtToken();

        $this->assertNotNull($break);
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Variable::class, $break);
        $this->assertSame('$BREAK_AT', $break->getVariable()->getFullName());
    }

    public function test_getBreakAtNumericValue() : void
    {
        $cmd = Mailcode_Factory::misc()->for('LIST', 'RECORD', 42);

        $break = $cmd->getBreakAt();

        $this->assertNotNull($break);
        $this->assertSame(42, $break);
    }

    public function test_getBreakAtVariable() : void
    {
        $cmd = Mailcode_Factory::misc()->for('LIST', 'RECORD', '$BREAK_AT');

        $break = $cmd->getBreakAt();

        $this->assertNotNull($break);
        $this->assertInstanceOf(Mailcode_Variables_Variable::class, $break);
    }

    public function test_getBreakAtFloatValue() : void
    {
        $cmd = Mailcode_Factory::misc()->for('LIST', 'RECORD', '42.99');

        $break = $cmd->getBreakAt();

        $this->assertNotNull($break);
        $this->assertSame(42, $break);
    }

    public function test_setBreakAtProgrammatically() : void
    {
        $cmd = Mailcode_Factory::misc()->for('LIST', 'RECORD', 42);

        $this->assertSame(42, $cmd->getBreakAt());
        $this->assertTrue($cmd->isBreakAtEnabled());

        // Set an integer value
        $cmd->setBreakAt(13);
        $this->assertSame(13, $cmd->getBreakAt());
        $this->assertTrue($cmd->isBreakAtEnabled());
        $this->assertStringContainsString('break-at=13', $cmd->getNormalized());

        // Remove the break at value entirely
        $cmd->setBreakAt(null);
        $this->assertNull($cmd->getBreakAt());
        $this->assertFalse($cmd->isBreakAtEnabled());
        $this->assertStringNotContainsString('break-at', $cmd->getNormalized());

        // Set a variable value
        $cmd->setBreakAt(Mailcode_Factory::var()->fullName('$FOO.BAR'));
        $this->assertInstanceOf(Mailcode_Variables_Variable::class, $cmd->getBreakAt());
        $this->assertTrue($cmd->isBreakAtEnabled());
        $this->assertStringContainsString('break-at=$FOO.BAR', $cmd->getNormalized());
    }
}
