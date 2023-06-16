<?php

use Mailcode\Interfaces\Commands\Validation\BreakAtInterface;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Variables_Variable;

final class Mailcode_ForTests extends MailcodeTestCase
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
                'string' => '{for: $RECORD in: $LIST break-at: 13}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement with break-at (variable)',
                'string' => '{for: $RECORD in: $LIST break-at: $FOO.BAR}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'String as break-at parameter',
                'string' => '{for: $RECORD in: $LIST break-at: "13"}{end}',
                'valid' => false,
                'code' => BreakAtInterface::VALIDATION_BREAK_AT_CODE_WRONG_TYPE
            ),
            array(
                'label' => 'Unquoted text as break-at parameter',
                'string' => '{for: $RECORD in: $LIST break-at: UnquotedText}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Keyword as break-at parameter',
                'string' => '{for: $RECORD in: $LIST break-at: idnencode:}{end}',
                'valid' => false,
                'code' => BreakAtInterface::VALIDATION_BREAK_AT_CODE_WRONG_TYPE
            )
        );

        $this->runCollectionTests($tests);
    }

    public function test_getVariables(): void
    {
        $cmd = Mailcode_Factory::misc()->for('SOURCE', 'LOOP');

        $this->assertInstanceOf(Mailcode_Variables_Variable::class, $cmd->getSourceVariable());
        $this->assertInstanceOf(Mailcode_Variables_Variable::class, $cmd->getLoopVariable());

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
}
