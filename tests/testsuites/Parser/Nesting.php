<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Collection_Error_Command;
use Mailcode\Mailcode_Collection_Error_Message;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Commands_Command_If;
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Parser;

final class Parser_NestingTests extends MailcodeTestCase
{
    public function notest_simpleNesting() : void
    {
        $string =
        '{for: $RECORD in: $FOO}
            {showvar: $RECORD.NAME}
            {showvar: $BARFOO}
        {end}';

        $parser = Mailcode::create()->getParser();
        $collection = $parser->parseString($string);
        $showCommands = $collection->getShowVariableCommands();
        $forCommands = $collection->getForCommands();
        $for = array_pop($forCommands);

        $this->assertInstanceOf(Mailcode_Commands_Command_For::class, $for);
        $this->assertEquals(2, count($showCommands));

        foreach($showCommands as $showCommand)
        {
            $this->assertTrue($showCommand->hasParent());
            $this->assertEquals($for, $showCommand->getParent());
        }
    }

    public function test_multiNesting() : void
    {
        $string =
        '{if empty: $LOPOS}
            {for: $RECORD in: $FOO}
                {showvar: $RECORD.NAME}
            {end}
        {end}';

        $parser = Mailcode::create()->getParser();
        $collection = $parser->parseString($string);
        $showCommands = $collection->getShowVariableCommands();
        $ifCommands = $collection->getCommandsByClass(Mailcode_Commands_Command_If::class);
        $forCommands = $collection->getForCommands();

        $for = array_pop($forCommands);
        $if = array_pop($ifCommands);
        $show = array_pop($showCommands);

        $this->assertInstanceOf(Mailcode_Commands_Command_For::class, $for);
        $this->assertInstanceOf(Mailcode_Commands_Command_If::class, $if);
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowVariable::class, $show);

        $this->assertNull($if->getParent());
        $this->assertEquals($if, $for->getParent(), 'IF command should be the parent.');
        $this->assertEquals($for, $show->getParent(), 'FOR command should be the parent.');
    }
}
