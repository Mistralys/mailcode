<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowDate;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Date_FormatInfo;
use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ShowDateTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{showdate:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{showdate: foobar}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Without variable',
                'string' => '{showdate: "Some text"}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'With valid variable, omitting format string',
                'string' => '{showdate: $foo_bar}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and empty format string',
                'string' => '{showdate: $foo_bar ""}',
                'valid' => false,
                'code' => Mailcode_Date_FormatInfo::VALIDATION_EMPTY_FORMAT_STRING
            ),
            array(
                'label' => 'With valid variable and invalid format string',
                'string' => '{showdate: $foo_bar "Y-m-B"}',
                'valid' => false,
                'code' => Mailcode_Date_FormatInfo::VALIDATION_INVALID_FORMAT_CHARACTER
            ),
            array(
                'label' => 'With valid variable and valid format string',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s"}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_getVariable()
    {
        $cmd = Mailcode_Factory::showDate('foobar');
        
        $this->assertEquals('$foobar', $cmd->getVariable()->getFullName());
        $this->assertEquals('$foobar', $cmd->getVariableName());
    }

    /**
     * The showdate and showvar commands can verify if they are nested
     * in a loop (FOR command), which enables finding the source variable.
     */
    public function test_isInLoop() : void
    {
        $string =
        '{for: $RECORD in: $FOO.BAR}
            {if not-empty: $RECORD.NAME}
                {showdate: $RECORD.NAME}
            {end}
        {end}';

        $collection = Mailcode::create()->getParser()->parseString($string);
        $showCommands = $collection->getShowDateCommands();
        $forCommands = $collection->getForCommands();

        $show = array_pop($showCommands);
        $for = array_pop($forCommands);

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowDate::class, $show);
        $this->assertTrue($show->isInLoop());
        $this->assertEquals($for, $show->getLoopCommand());
    }

    public function test_urlencode() : void
    {
        $cmd = Mailcode::create()->parseString('{showdate: $FOO urlencode:}')->getFirstCommand();

        $this->assertTrue($cmd->isURLEncoded());
    }
}
