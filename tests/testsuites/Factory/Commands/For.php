<?php

use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;

final class Factory_ForTests extends FactoryTestCase
{
    protected function getExpectedClass(): string
    {
        return Mailcode_Commands_Command_For::class;
    }

    public function test_for()
    {
        $this->runCommand(
            'Variable name without $',
            function () {
                return Mailcode_Factory::misc()->for('SOURCE', 'LOOPVAR');
            }
        );

        $this->runCommand(
            'Variable name with $',
            function () {
                return Mailcode_Factory::misc()->for('$SOURCE', '$LOOPVAR');
            }
        );

        $this->runCommand(
            'Variable name with $',
            function () {
                return Mailcode_Factory::misc()->for('$SOURCE', '$LOOPVAR', '13');
            }
        );

        $this->runCommand(
            'Variable name with $',
            function () {
                return Mailcode_Factory::misc()->for('$SOURCE', '$LOOPVAR', '$FOO.BAR');
            }
        );
    }

    public function test_error_same_variable()
    {
        $this->expectException(Mailcode_Factory_Exception::class);

        Mailcode_Factory::misc()->for('$SOURCE', '$SOURCE');
    }

    public function test_error_break_string()
    {
        $this->expectException(Mailcode_Factory_Exception::class);

        Mailcode_Factory::misc()->for('$SOURCE', '$SOURCE', '"13"');
    }

    public function test_error_break_unquotedtext()
    {
        $this->expectException(Mailcode_Factory_Exception::class);

        Mailcode_Factory::misc()->for('$SOURCE', '$SOURCE', 'UnquotedText');
    }
}
