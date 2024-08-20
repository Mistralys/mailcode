<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\Interfaces\Commands\Validation\URLEncodingInterface;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Commands_Command_ShowPrice;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_CommonConstants;
use MailcodeTestCase;

final class ShowPriceTests extends MailcodeTestCase
{
    public function test_validation(): void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{showprice:}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'Without variable',
                'string' => '{showprice: "Some text"}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'With valid variable',
                'string' => '{showprice: $FOO}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, expect currency name',
                'string' => '{showprice: $FOO currency-name:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, expect currency name, expect absolute',
                'string' => '{showprice: $FOO currency-name: absolute:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, expect currency name, expect absolute, alternative order',
                'string' => '{showprice: $FOO absolute: currency-name:}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, ignore string',
                'string' => '{showprice: $FOO "banana"}',
                'valid' => true,
                'code' => 0
            )
        );

        $this->runCollectionTests($tests);
    }

    public function test_getFormat(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar');

        $this->assertEquals('$foobar', $cmd->getVariable()->getFullName());
        $this->assertEquals('1,000.00', $cmd->getLocalCurrency()->getFormatString());
        $this->assertFalse($cmd->getLocalCurrency()->getFormatInfo()->hasPadding());
    }

    public function test_urlencode(): void
    {
        $cmd = Mailcode::create()
            ->parseString('{showprice: $FOO urlencode:}')
            ->getFirstCommand();

        $this->assertInstanceOf(URLEncodingInterface::class, $cmd);
        $this->assertTrue($cmd->isURLEncoded());
    }

    public function test_absolute_string(): void
    {
        $cmd = Mailcode::create()
            ->parseString('{showprice: $FOO absolute:}')
            ->getFirstCommand();

        $this->assertInstanceOf(Mailcode_Commands_Command_ShowPrice::class, $cmd);
        $this->assertTrue($cmd->isAbsolute());
    }

    public function test_absolute_default(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar');

        $this->assertFalse($cmd->isAbsolute());
    }

    public function test_absolute_set(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar');

        $cmd->setAbsolute(true);
        $this->assertTrue($cmd->isAbsolute());

        $cmd->setAbsolute(false);
        $this->assertFalse($cmd->isAbsolute());
    }

    public function test_absolute_normalized(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar', true);

        $this->assertTrue($cmd->isAbsolute());
        $this->assertSame('{showprice: $foobar absolute: currency-name:}', $cmd->getNormalized());
    }
}
