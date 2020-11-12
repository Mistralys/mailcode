<?php

use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Number_Info;

final class Numbers_NumberInfoTests extends MailcodeTestCase
{
    public function test_numbers_basic() : void
    {
        $test = new Mailcode_Number_Info('1000');

        $this->assertEquals('', $test->getThousandsSeparator());
        $this->assertEquals('', $test->getDecimalsSeparator());

        $this->assertSame(0, $test->getDecimals());
        $this->assertSame(0, $test->getPadding());

        $this->assertFalse($test->hasThousandsSeparator());
        $this->assertFalse($test->hasDecimals());
        $this->assertFalse($test->hasPadding());
    }

    public function test_numbers_padding() : void
    {
        $test = new Mailcode_Number_Info('1000:###');

        $this->assertTrue($test->hasPadding());
        $this->assertSame(3, $test->getPadding());

        $test = new Mailcode_Number_Info('1000:#');

        $this->assertTrue($test->hasPadding());
        $this->assertSame(1, $test->getPadding());
    }

    public function test_numbers_thousands() : void
    {
        $test = new Mailcode_Number_Info('1,000');

        $this->assertTrue($test->hasThousandsSeparator());
        $this->assertSame(',', $test->getThousandsSeparator());

        $test = new Mailcode_Number_Info('1 000');

        $this->assertTrue($test->hasThousandsSeparator());
        $this->assertSame(' ', $test->getThousandsSeparator());

        $test = new Mailcode_Number_Info('1.000');

        $this->assertTrue($test->hasThousandsSeparator());
        $this->assertSame('.', $test->getThousandsSeparator());
    }

    public function test_numbers_decimals() : void
    {
        $test = new Mailcode_Number_Info('1000,00');

        $this->assertTrue($test->hasDecimals());
        $this->assertSame(',', $test->getDecimalsSeparator());
        $this->assertSame(2, $test->getDecimals());

        $test = new Mailcode_Number_Info('1000.0');

        $this->assertTrue($test->hasDecimals());
        $this->assertSame('.', $test->getDecimalsSeparator());
        $this->assertSame(1, $test->getDecimals());
    }

    public function test_numbers_combined() : void
    {
        $test = new Mailcode_Number_Info('1.000,00:##');

        $this->assertTrue($test->hasDecimals());
        $this->assertSame(',', $test->getDecimalsSeparator());
        $this->assertSame(2, $test->getDecimals());

        $this->assertTrue($test->hasThousandsSeparator());
        $this->assertSame('.', $test->getThousandsSeparator());

        $this->assertTrue($test->hasPadding());
        $this->assertSame(2, $test->getPadding());
    }

    public function test_validate_general(): void
    {
        $tests = array(
            array(
                'label' => 'Empty string',
                'format' => '',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Whitespace string',
                'format' => '   '."\t".'    ',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Characters at the wrong position',
                'format' => '100,.00',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_CHARACTERS
            ),
            array(
                'label' => 'Same thousands and decimal separator character',
                'format' => '1.000.00',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_SEPARATORS_SAME_CHARACTER
            )
        );

        $this->processValidationTests($tests);
    }

    public function test_validate_number(): void
    {
        $tests = array(
            array(
                'label' => 'With a number other than 1000',
                'format' => '2000',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_FORMAT_NUMBER
            ),
            array(
                'label' => 'With a number other than 1000, full format',
                'format' => '1 001,45',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_FORMAT_NUMBER
            )
        );

        $this->processValidationTests($tests);
    }

    public function test_validate_thousandsSeparator(): void
    {
        $tests = array(
            array(
                'label' => 'Invalid thousands separator',
                'format' => '1-000',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_FORMAT_NUMBER
            )
        );

        $this->processValidationTests($tests);
    }

    public function test_validate_decimalsSeparator(): void
    {
        $tests = array(
            array(
                'label' => 'Invalid decimals separator',
                'format' => '1000-00',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_DECIMAL_SEPARATOR
            ),
            array(
                'label' => 'Invalid decimals separator position',
                'format' => '10,00',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_DECIMAL_SEPARATOR
            ),
            array(
                'label' => 'Invalid decimal signs',
                'format' => '1000,--',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_INVALID_DECIMALS_CHARS
            ),
            array(
                'label' => 'Valid decimals separator',
                'format' => '1000,00',
                'valid' => true,
                'code' => 0
            )
        );

        $this->processValidationTests($tests);
    }

    public function test_validate_padding(): void
    {
        $tests = array(
            array(
                'label' => 'Duplicate colons',
                'format' => '1000.00:##:##',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_PADDING_SEPARATOR_OVERFLOW
            ),
            array(
                'label' => 'Invalid padding characters',
                'format' => '1000:--',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_PADDING_INVALID_CHARS
            ),
            array(
                'label' => 'Invalid padding characters',
                'format' => '1000:##1',
                'valid' => false,
                'code' => Mailcode_Commands_Command_ShowNumber::VALIDATION_PADDING_INVALID_CHARS
            ),
            array(
                'label' => 'Valid padding',
                'format' => '1000:#',
                'valid' => true,
                'code' => 0
            )
        );

        $this->processValidationTests($tests);
    }

    private function processValidationTests(array $tests) : void
    {
        foreach ($tests as $test)
        {
            $info = new Mailcode_Number_Info($test['format']);

            $label = $test['label'].' ['.$test['format'].'] '.PHP_EOL.'Message: '.$info->getErrorMessage();

            $this->assertSame($test['valid'], $info->isValid(), $label);
            $this->assertSame($test['code'], $info->getCode(), $label);
        }
    }
}
