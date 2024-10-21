<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\CurrencyInterface;
use Mailcode\Interfaces\Commands\Validation\URLEncodingInterface;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Commands_Command_ShowPrice;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\RegionInterface;
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
                'label' => 'Invalid region type',
                'string' => '{showprice: $FOO region=23}',
                'valid' => false,
                'code' => RegionInterface::VALIDATION_REGION_WRONG_TYPE
            ),
            array(
                'label' => 'Invalid currency type',
                'string' => '{showprice: $FOO currency=23}',
                'valid' => false,
                'code' => CurrencyInterface::VALIDATION_CURRENCY_WRONG_TYPE
            ),
            array(
                'label' => 'Invalid currency type',
                'string' => '{showprice: $FOO currency="de_DE" currency-name:}',
                'valid' => false,
                'code' => CurrencyInterface::VALIDATION_CURRENCY_EXCLUSIVE
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
            ),
            array(
                'label' => 'With valid variable, with region variable',
                'string' => '{showprice: $FOO region=$FOO.REGION }',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, with region string, with currency string',
                'string' => '{showprice: $FOO "banana" region="de_DE" currency="USD"}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With valid variable, with region variable, with currency variable',
                'string' => '{showprice: $FOO "banana" region=$FOO.REGION currency=$FOO.CURRENCY}',
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
        $cmd = Mailcode_Factory::show()->price('foobar', true, true);

        $this->assertTrue($cmd->isAbsolute());
        $this->assertSame('{showprice: $foobar absolute: currency-name:}', $cmd->getNormalized());
    }

    public function test_absolute_currency_string(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar', true, false, "USD");

        $this->assertTrue($cmd->isAbsolute());
        $this->assertSame('{showprice: $foobar absolute: currency="USD"}', $cmd->getNormalized());
    }

    public function test_absolute_currency_string_name(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar', true, true, "USD");

        $this->assertTrue($cmd->isAbsolute());
        $this->assertSame('{showprice: $foobar absolute: currency="USD"}', $cmd->getNormalized());
    }

    public function test_absolute_currency_variable(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar', true, true,
            null, '$FOO.CURRENCY');

        $this->assertTrue($cmd->isAbsolute());
        $this->assertSame('{showprice: $foobar absolute: currency=$FOO.CURRENCY}', $cmd->getNormalized());
    }

    public function test_absolute_region_string(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar', true, true,
            null, null,
            "de_DE");

        $this->assertTrue($cmd->isAbsolute());
        $this->assertSame('{showprice: $foobar absolute: currency-name: region="de_DE"}', $cmd->getNormalized());
    }

    public function test_absolute_region_variable(): void
    {
        $cmd = Mailcode_Factory::show()->price('foobar', true, true,
            null, null,
            null, '$FOO.REGION');

        $this->assertTrue($cmd->isAbsolute());
        $this->assertSame('{showprice: $foobar absolute: currency-name: region=$FOO.REGION}', $cmd->getNormalized());
    }
}
