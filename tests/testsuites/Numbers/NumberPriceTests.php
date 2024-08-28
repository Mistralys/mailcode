<?php

declare(strict_types=1);

namespace testsuites\Numbers;

use Mailcode\Mailcode_Number_LocalCurrency;
use MailcodeTestCase;

final class NumberPriceTests extends MailcodeTestCase
{
    public function test_localized_currency_basic(): void
    {
        $test = new Mailcode_Number_LocalCurrency("US", "USD", "$", " ", "1,000.00");

        self::assertEquals('$', $test->getCurrencySymbol());
        self::assertEquals('USD', $test->getCurrencyName());
        self::assertEquals(' ', $test->getUnitSeparator());

        self::assertEquals(',', $test->getFormatInfo()->getThousandsSeparator());
        self::assertEquals('.', $test->getFormatInfo()->getDecimalsSeparator());

        self::assertSame(2, $test->getFormatInfo()->getDecimals());
        self::assertSame(0, $test->getFormatInfo()->getPadding());

        self::assertTrue($test->getFormatInfo()->hasThousandsSeparator());
        self::assertTrue($test->getFormatInfo()->hasDecimals());
        self::assertFalse($test->getFormatInfo()->hasPadding());
    }
}
