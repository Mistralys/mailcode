<?php

use Mailcode\Mailcode_Commands_Command_ShowNumber;
use Mailcode\Mailcode_Commands_Command_ShowPrice;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Factory_Exception;
use MailcodeTestClasses\FactoryTestCase;

final class Factory_ShowPriceTests extends FactoryTestCase
{
    protected function getExpectedClass(): string
    {
        return Mailcode_Commands_Command_ShowPrice::class;
    }

    public function test_showPrice(): void
    {
        $this->runCommand(
            'Variable name without $',
            function () {
                return Mailcode_Factory::show()->price('VAR.NAME');
            }
        );
        $this->runCommand(
            'Variable name with $',
            function () {
                return Mailcode_Factory::show()->price('$VAR.NAME');
            }
        );
        $this->runCommand(
            'Variable name with $, currency name',
            function () {
                return Mailcode_Factory::show()->price('$VAR.NAME');
            }
        );
    }

    public function test_showPrice_error(): void
    {
        $this->expectException(Mailcode_Factory_Exception::class);

        Mailcode_Factory::show()->price('0INVALIDVAR');
    }
}
