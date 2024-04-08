<?php

use Mailcode\Mailcode_Commands_Command_ShowDate;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_ShowDateTests extends FactoryTestCase
{
    protected function getExpectedClass() : string
    {
        return Mailcode_Commands_Command_ShowDate::class;
    }

    public function test_showDate() : void
    {
        $this->runCommand(
            'Variable name without $',
            function() { return Mailcode_Factory::show()->date('VAR.NAME'); }
        );

        $this->runCommand(
            'Variable name with $',
            function() { return Mailcode_Factory::show()->date('$VAR.NAME'); }
        );

        $this->runCommand(
            'With format string',
            function() { return Mailcode_Factory::show()->date('$VAR.NAME', 'd.m.Y'); }
        );

        $this->runCommand(
            'With timezone string',
            function() { return Mailcode_Factory::show()->date('$VAR.NAME', 'd.m.Y', 'Europe/Berlin'); }
        );

        $this->runCommand(
            'With timezone variable',
            function() { return Mailcode_Factory::show()->date('$VAR.NAME', 'd.m.Y', null, '$TIME.ZONE'); }
        );

        $this->runCommand(
            'With timezone variable',
            function() { return Mailcode_Factory::show()->date('$VAR.NAME', 'd.m.Y', null, '$TIME.ZONE'); }
        );
    }

    public function test_showDate_variableError() : void
    {
        $this->expectException(Mailcode_Factory_Exception::class);

        Mailcode_Factory::show()->date('0INVALIDVAR');
    }

    public function test_showDate_formatError() : void
    {
        $this->expectException(Mailcode_Factory_Exception::class);

        Mailcode_Factory::show()->date('VAR.NAME', 'd.m.Z');
    }

    public function test_showDate_timezone_variableError() : void
    {
        $this->expectException(Mailcode_Factory_Exception::class);

        Mailcode_Factory::show()->date('VAR.NAME', 'd.m.Y',null, 'TIME.ZONE');
    }
}
