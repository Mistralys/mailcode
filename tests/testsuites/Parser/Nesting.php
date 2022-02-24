<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Collection_Error_Command;
use Mailcode\Mailcode_Collection_Error_Message;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Commands_Command_If;
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Commands_Command_Type_Closing;
use Mailcode\Mailcode_Commands_Command_Type_Opening;
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
        $collection = $parser->parseString($string)->getCollection();
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
        $collection = $parser->parseString($string)->getCollection();
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

    /**
     * The nesting handler must correctly set the command's opening
     * and closing command instances.
     */
    public function test_openingAndClosing() : void
    {
        $string =
        '{if empty: $LOPOS}
            {for: $RECORD in: $FOO}
                {showvar: $RECORD.NAME}
            {end}
        {end}';

        $commands = Mailcode::create()->parseString($string)->getCommands();

        $ifOpening = array_shift($commands);
        $ifClosing = array_pop($commands);
        $forOpening = array_shift($commands);
        $forClosing = array_pop($commands);

        $this->assertInstanceOf(Mailcode_Commands_Command_Type_Opening::class, $ifOpening);
        $this->assertInstanceOf(Mailcode_Commands_Command_Type_Closing::class, $ifClosing);

        $this->assertSame($ifClosing, $ifOpening->getClosingCommand());
        $this->assertSame($ifOpening, $ifClosing->getOpeningCommand());
        $this->assertSame($forClosing, $forOpening->getClosingCommand());
        $this->assertSame($forOpening, $forClosing->getOpeningCommand());
    }

    /**
     * Ensure that sibling commands correctly get assigned their
     * whole siblings tree.
     */
    public function test_siblings() : void
    {
        $string =
        '{if empty: $FIRST}
            Text here
        {elseif empty: $SECOND}
            More text
        {elseif empty: $THIRD}
            Yahoo
        {else}
            And a last one.
        {end}';

        $commands = Mailcode::create()->parseString($string)->getCommands();

        $ifFirst = array_shift($commands);
        $ifSecond = array_shift($commands);
        $ifThird = array_shift($commands);
        $else = array_shift($commands);
        $end = array_shift($commands);

        $this->assertCount(3, $ifFirst->getSiblingCommands());
        $this->assertSame($ifFirst, $ifSecond->getOpeningCommand());
        $this->assertContains($ifThird, $ifSecond->getSiblingCommands());
        $this->assertContains($else, $ifSecond->getSiblingCommands());
        $this->assertContains($ifSecond, $else->getSiblingCommands());
        $this->assertSame($ifFirst, $end->getOpeningCommand());
    }

    /**
     * Test attempting to reproduce a bug where the space
     * between two commands gets stripped out during the
     * safeguarding.
     */
    public function test_spaceBug() : void
    {
        $string = '<p>Dear %1$s %2$s,</p><p>Thank you for your patronage.</p>';

        $safeguard = Mailcode::create()->createSafeguard(sprintf(
            $string,
            '{showvar: $CUSTOMER.FIRST_NAME}',
            '{showvar: $CUSTOMER.LAST_NAME}'
        ));

        $placeholders = $safeguard->getPlaceholdersCollection()->getStrings();

        $this->assertCount(2, $placeholders);

        $this->assertSame(
            sprintf(
                $string,
                $placeholders[0],
                $placeholders[1]
            ),
            $safeguard->makeSafePartial()
        );
    }
}
