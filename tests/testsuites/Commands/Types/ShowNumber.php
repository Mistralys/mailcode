<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_ShowNumberTests extends MailcodeTestCase
{
    public function test_validation() : void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{shownumber:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'Decimal separator without actual decimals',
                'string' => '{shownumber: $FOO "1000."}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_DECIMALS_NO_DECIMALS
            ),
            array(
                'label' => 'Without variable',
                'string' => '{shownumber: "Some text"}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'With valid variable, omitting format string',
                'string' => '{shownumber: $FOO}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and empty format string',
                'string' => '{shownumber: $FOO ""}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable and invalid format string',
                'string' => '{shownumber: $FOO "Balrghus"}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_FORMAT_NUMBER
            ),
            array(
                'label' => 'With valid variable and valid format string',
                'string' => '{shownumber: $FOO "1 000,00"}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_getFormat() : void
    {
        $cmd = Mailcode_Factory::show()->number('foobar', '1 000,00:##');
        
        $this->assertEquals('$foobar', $cmd->getVariable()->getFullName());
        $this->assertEquals('1 000,00:##', $cmd->getFormatString());
        $this->assertTrue($cmd->getFormatInfo()->hasPadding());
    }

    public function test_urlencode() : void
    {
        $cmd = Mailcode::create()
            ->parseString('{shownumber: $FOO urlencode:}')
            ->getFirstCommand();

        $this->assertNotNull($cmd);
        $this->assertTrue($cmd->isURLEncoded());
    }

    public function test_absolute_factory() : void
    {
        $cmd = Mailcode_Factory::show()
            ->number('foobar', '1 000,00:##', true);

        $this->assertTrue($cmd->isAbsolute());
    }

    public function test_absolute_string() : void
    {
        $cmd = Mailcode::create()
            ->parseString('{shownumber: $FOO absolute:}')
            ->getFirstCommand();

        $this->assertNotNull($cmd);
        $this->assertTrue($cmd->isAbsolute());
    }

    public function test_absolute_default() : void
    {
        $cmd = Mailcode_Factory::show()
            ->number('foobar', '1 000,00:##');

        $this->assertFalse($cmd->isAbsolute());
    }

    public function test_absolute_set() : void
    {
        $cmd = Mailcode_Factory::show()
            ->number('foobar', '1 000,00:##');

        $cmd->setAbsolute(true);
        $this->assertTrue($cmd->isAbsolute());

        $cmd->setAbsolute(false);
        $this->assertFalse($cmd->isAbsolute());
    }

    public function test_absolute_normalized() : void
    {
        $cmd = Mailcode_Factory::show()
            ->number('foobar', '1 000,00:##', true);


        $this->assertSame('{shownumber: $foobar "1 000,00:##" absolute:}', $cmd->getNormalized());
    }
}
