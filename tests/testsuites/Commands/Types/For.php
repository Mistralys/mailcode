<?php

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Variables_Variable;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;

final class Mailcode_ForTests extends MailcodeTestCase
{
    public function test_validation() : void
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
                'string' => '{for: $FOO.BAR}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_INVALID_FOR_STATEMENT
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{for: $5OOBAR in: $SOMEVAR.YO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'With missing container variable',
                'string' => '{for: $FOO.BAR in: }{end}',
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
                'label' => 'Valid statement',
                'string' => '{for: $FOO.BAR in: $BAR.FOO}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_getVariables() : void
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
    public function test_getVariables_exception() : void
    {
        $cmd = $cmd = Mailcode::create()->getCommands()->createCommand(
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
}
