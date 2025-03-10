<?php

declare(strict_types=1);

namespace MailcodeTests\Translator;

use Mailcode\Mailcode;
use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\VelocityTestCase;

final class ApacheVelocityTests extends VelocityTestCase
{
    public function test_logicKeywords(): void
    {
        $cmd = Mailcode_Factory::if()->var('FOO.BAR', '==', '20');
        $cmd->getLogicKeywords()->appendAND('$BARFOO == "Other value"', 'variable');

        $expected = '#if($FOO.BAR == 20 && $BARFOO == "Other value")';

        $syntax = $this->translator->createApacheVelocity();

        $result = $syntax->translateCommand($cmd);

        $this->assertEquals($expected, $result);
    }

    public function test_list_equals(): void
    {
        $cmd = Mailcode_Factory::if()->listEquals("FOO.BAR", ["true"]);

        $expected = '#if($map.hasElement($FOO.list(), "BAR", "(?s)\Atrue\Z"))';

        $syntax = $this->translator->createApacheVelocity();

        $result = $syntax->translateCommand($cmd);

        $this->assertEquals($expected, $result);
    }

    public function test_show_decryption(): void
    {
        $cmd = Mailcode_Factory::show()
            ->var("FOO.BAR")
            ->enableDecryption();

        $expected = '${text.decrypt($FOO.BAR, "default")}';

        $syntax = $this->translator->createApacheVelocity();

        $result = $syntax->translateCommand($cmd);

        $this->assertEquals($expected, $result);
    }

    public function test_show_decryption_custom(): void
    {
        $cmd = Mailcode_Factory::show()->var("FOO.BAR")
            ->enableDecryption('barfoo');

        $expected = '${text.decrypt($FOO.BAR, "barfoo")}';

        $syntax = $this->translator->createApacheVelocity();

        $result = $syntax->translateCommand($cmd);

        $this->assertEquals($expected, $result);
    }

    public function test_translateSafeguard(): void
    {
        $syntax = $this->translator->createApacheVelocity();

        $subject = '
{setvar: $CUSTOMER.CUSTOMER_ID = "42"}

{showvar: $CUSTOMER.CUSTOMER_ID}

{if variable: $FOO.BAR == "NOPE"}
    Some text here in the IF command.
{elseif: 6 + 2 == 5}
    You will never see me.
{else}
    Lucky you!
{end}
';

        $expected = '
#set($CUSTOMER.CUSTOMER_ID = "42")

${CUSTOMER.CUSTOMER_ID}

#if($FOO.BAR == "NOPE")
    Some text here in the IF command.
#elseif(6 + 2 == 5)
    You will never see me.
#{else}
    Lucky you!
#{end}
';

        $safeguard = Mailcode::create()->createSafeguard($subject);

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showvar_decrypt(): void
    {
        $syntax = $this->translator->createApacheVelocity();

        $subject = '{showvar: $CUSTOMER.CUSTOMER_ID decrypt="my-key" idnencode:}';
        $expected = '${text.idn(${text.decrypt($CUSTOMER.CUSTOMER_ID, "my-key")})}';

        $safeguard = Mailcode::create()->createSafeguard($subject);
        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showvar_decrypt_custom(): void
    {
        $syntax = $this->translator->createApacheVelocity();

        $subject = '{showvar: $CUSTOMER.CUSTOMER_ID decrypt="barfoo" idnencode:}';
        $expected = '${text.idn(${text.decrypt($CUSTOMER.CUSTOMER_ID, "barfoo")})}';

        $safeguard = Mailcode::create()->createSafeguard($subject);
        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    /**
     * Configuring the translation of showdate commands to use a
     * specific internal date format when they are translated,
     * while not translating them manually.
     */
    public function test_translateSafeguard_dates(): void
    {
        $subject = '{showdate: $FOO.BAR "d.m.Y"}';

        $internalFormat = 'yyyy-MM-dd';
        $expected = '${time.input("' . $internalFormat . '", $FOO.BAR).output("dd.MM.yyyy").zone("UTC")}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);
        $dateCommands = $safeguard->getCollection()->getShowDateCommands();

        foreach ($dateCommands as $dateCommand) {
            $dateCommand->setTranslationParam('internal_format', $internalFormat);
        }

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showDate_keyword_behind(): void
    {
        $subject = '{showdate: $FOO.BAR "Y-m-d" urlencode:}';

        $internalFormat = 'yyyy-MM-dd';
        $expected = '${esc.url($time.input("' . $internalFormat . '", $FOO.BAR).output("yyyy-MM-dd").zone("UTC"))}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);
        $dateCommands = $safeguard->getCollection()->getShowDateCommands();

        foreach ($dateCommands as $dateCommand) {
            $dateCommand->setTranslationParam('internal_format', $internalFormat);
        }

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showDate_keyword_before(): void
    {
        $subject = '{showdate: urlencode: $FOO.BAR "Y-m-d"}';

        $internalFormat = 'yyyy-MM-dd';
        $expected = '${esc.url($time.input("' . $internalFormat . '", $FOO.BAR).output("yyyy-MM-dd").zone("UTC"))}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);
        $dateCommands = $safeguard->getCollection()->getShowDateCommands();

        foreach ($dateCommands as $dateCommand) {
            $dateCommand->setTranslationParam('internal_format', $internalFormat);
        }

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showDate_keyword_behind_timezone(): void
    {
        $subject = '{showdate: $FOO.BAR "Y-m-d" timezone="Europe/Berlin" urlencode:}';

        $internalFormat = 'yyyy-MM-dd';
        $expected = '${esc.url($time.input("' . $internalFormat . '", $FOO.BAR).output("yyyy-MM-dd").zone("Europe/Berlin"))}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);
        $dateCommands = $safeguard->getCollection()->getShowDateCommands();

        foreach ($dateCommands as $dateCommand) {
            $dateCommand->setTranslationParam('internal_format', $internalFormat);
        }

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showDate_keyword_before_timezone(): void
    {
        $subject = '{showdate: urlencode: $FOO.BAR "Y-m-d" timezone="Europe/Berlin"}';

        $internalFormat = 'yyyy-MM-dd';
        $expected = '${esc.url($time.input("' . $internalFormat . '", $FOO.BAR).output("yyyy-MM-dd").zone("Europe/Berlin"))}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);
        $dateCommands = $safeguard->getCollection()->getShowDateCommands();

        foreach ($dateCommands as $dateCommand) {
            $dateCommand->setTranslationParam('internal_format', $internalFormat);
        }

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showDate_keyword_between_timezone(): void
    {
        $subject = '{showdate: $FOO.BAR "Y-m-d" urlencode: timezone="Europe/Berlin"}';

        $internalFormat = 'yyyy-MM-dd';
        $expected = '${esc.url($time.input("' . $internalFormat . '", $FOO.BAR).output("yyyy-MM-dd").zone("Europe/Berlin"))}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);
        $dateCommands = $safeguard->getCollection()->getShowDateCommands();

        foreach ($dateCommands as $dateCommand) {
            $dateCommand->setTranslationParam('internal_format', $internalFormat);
        }

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showPrice_defaults(): void
    {
        $subject = '{showprice: $FOO.BAR}';

        $expected = '${money.amount($FOO.BAR, ".").group(",").unit("$", "US").separator(" ")}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showPrice_currencyName(): void
    {
        $subject = '{showprice: $FOO.BAR currency-name:}';

        $expected = '${money.amount($FOO.BAR, ".").group(",").unit("USD", "US").separator(" ")}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }

    public function test_showPrice_region_variable(): void
    {
        $subject = '{showprice: $FOO.BAR region=$FOO.REGION}';

        $expected = '${money.amount($FOO.BAR, ".").group(",").unit("$", $FOO.REGION).separator(" ")}';

        $syntax = $this->translator->createApacheVelocity();
        $safeguard = Mailcode::create()->createSafeguard($subject);

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }
}
