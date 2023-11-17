<?php
/**
 * @package MailcodeTests
 * @subpackage Commands
 * @see \MailcodeTests\Commands\Types\ShowDateTests
 */

namespace MailcodeTests\Commands\Types;

use Mailcode\Interfaces\Commands\Validation\TimezoneInterface;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowDate;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Date_FormatInfo;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use MailcodeTestCase;

/**
 * @package MailcodeTests
 * @subpackage Commands
 * @covers \Mailcode\Mailcode_Commands_Command_ShowDate
 */
final class ShowDateTests extends MailcodeTestCase
{
    public function test_validation(): void
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
                'label' => 'With valid variable and only timezone',
                'string' => '{showdate: $foo_bar timezone="US/Eastern"}',
                'valid' => false,
                'code' => Mailcode_Date_FormatInfo::VALIDATION_INVALID_FORMAT_CHARACTER
            ),
            array(
                'label' => 'With valid variable and invalid format string',
                'string' => '{showdate: $foo_bar "Y-m-B"}',
                'valid' => false,
                'code' => Mailcode_Date_FormatInfo::VALIDATION_INVALID_FORMAT_CHARACTER
            ),
            array(
                'label' => 'With valid variable, valid format string, invalid timezone',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s" timezone=US/Eastern}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'With valid variable and valid format string',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s"}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, valid format string, valid timezone',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s" timezone="US/Eastern"}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, valid format string, valid variable timezone',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s" timezone=$FOO.TIMEZONE}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, valid format string, valid variable timezone, and additional keyword behind',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s" timezone=$FOO.TIMEZONE urlencode:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, valid format string, and additional keyword behind',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s" urlencode:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, valid format string, valid variable timezone, and additional keyword in front',
                'string' => '{showdate: urlencode: $foo_bar "Y-m-d H:i:s" timezone="Europe/Berlin"}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, valid format string, and mixup in timezone and additional keyword',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s" timezone=urlencode: "Europe/Berlin"}',
                'valid' => false,
                'code' => TimezoneInterface::VALIDATION_TIMEZONE_CODE_WRONG_TYPE
            ),
            array(
                'label' => 'With valid variable, valid format string, invalid numeric timezone',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s" timezone=13}',
                'valid' => false,
                'code' => TimezoneInterface::VALIDATION_TIMEZONE_CODE_WRONG_TYPE
            ),
            array(
                'label' => 'With valid variable, milliseconds and time zone formats',
                'string' => '{showdate: $foo_bar "Y-m-d H:i:s ve"}',
                'valid' => true,
                'code' => 0
            )
        );

        $this->runCollectionTests($tests);
    }

    public function test_timezoneDefaultPHP() : void
    {
        $cmd = Mailcode_Factory::show()->date('DATE');

        $timezone = $cmd->getTimezoneToken();
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral::class, $timezone);
        $this->assertSame(date_default_timezone_get(), $timezone->getText());
    }

    public function test_timezoneDefaultString() : void
    {
        $cmd = Mailcode_Factory::show()->date('DATE');

        Mailcode_Commands_Command_ShowDate::setDefaultTimezone('Europe/Paris');

        $this->assertNotSame('Europe/Paris', date_default_timezone_get());

        $timezone = $cmd->getTimezoneToken();
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral::class, $timezone);
        $this->assertSame('Europe/Paris', $timezone->getText());
    }

    public function test_timezoneDefaultVariable() : void
    {
        $cmd = Mailcode_Factory::show()->date('DATE');
        $variables = Mailcode::create()->createVariables();

        Mailcode_Commands_Command_ShowDate::setDefaultTimezone($variables->createVariable('TIMEZONE'));

        $timezone = $cmd->getTimezoneToken();
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Variable::class, $timezone);
        $this->assertSame('$TIMEZONE', $timezone->getVariable()->getFullName());
    }

    public function test_getVariable() : void
    {
        $cmd = Mailcode_Factory::show()->date('foobar');

        $this->assertEquals('$foobar', $cmd->getVariable()->getFullName());
        $this->assertEquals('$foobar', $cmd->getVariableName());
    }

    /**
     * The showdate and showvar commands can verify if they are nested
     * in a loop (FOR command), which enables finding the source variable.
     */
    public function test_isInLoop(): void
    {
        $string =
            '{for: $RECORD in: $FOO}
            {if not-empty: $RECORD.NAME}
                {showdate: $RECORD.NAME}
            {end}
        {end}';

        $collection = Mailcode::create()
            ->getParser()
            ->parseString($string)
            ->getCollection();

        $showCommands = $collection->getShowDateCommands();
        $forCommands = $collection->getForCommands();

        $show = array_pop($showCommands);
        $for = array_pop($forCommands);

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowDate::class, $show);
        $this->assertTrue($show->isInLoop());
        $this->assertEquals($for, $show->getLoopCommand());
    }

    public function test_urlencode(): void
    {
        $cmd = Mailcode::create()->parseString('{showdate: $FOO urlencode:}')->getFirstCommand();

        $this->assertNotNull($cmd);
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowDate::class, $cmd);
        $this->assertTrue($cmd->isURLEncoded());
    }

    public function test_timezoneString(): void
    {
        $cmd = Mailcode::create()->parseString('{showdate: $FOO "Y.m.d" timezone="Europe/Paris"}')->getFirstCommand();

        $this->assertNotNull($cmd);
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowDate::class, $cmd);

        $timezone = $cmd->getTimezoneToken();
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral::class, $timezone);
        $this->assertSame('Europe/Paris', $timezone->getText());
    }

    public function test_timezoneVariable(): void
    {
        $cmd = Mailcode::create()->parseString('{showdate: $FOO "Y.m.d" timezone=$TIMEZONE}')->getFirstCommand();

        $this->assertNotNull($cmd);
        $this->assertInstanceOf(Mailcode_Commands_Command_ShowDate::class, $cmd);

        $timezone = $cmd->getTimezoneToken();
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Variable::class, $timezone);
        $this->assertSame('$TIMEZONE', $timezone->getVariable()->getFullName());
    }

    /**
     * Setting the timezone programmatically must adjust the command
     * statement accordingly.
     */
    public function test_setTimezoneProgrammatically() : void
    {
        $cmd = Mailcode_Factory::show()->date('FOO', 'Y-m-d H:i:s');

        Mailcode_Commands_Command_ShowDate::setDefaultTimezone('Europe/Berlin');

        $timezone = $cmd->getTimezoneToken();
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral::class, $timezone);
        $this->assertSame('Europe/Berlin', $timezone->getText());

        $variable = Mailcode_Factory::var()->fullName('FOO');
        $cmd->setTimezone($variable);

        $timezone = $cmd->getTimezoneToken();
        $this->assertInstanceOf(Mailcode_Parser_Statement_Tokenizer_Token_Variable::class, $timezone, $timezone->getNormalized());
        $this->assertSame('$FOO', $timezone->getVariable()->getFullName());
        $this->assertStringContainsString('timezone=$FOO', $cmd->getNormalized());
    }
}
