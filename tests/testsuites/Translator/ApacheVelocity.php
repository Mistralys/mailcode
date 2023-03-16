<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Factory;

final class Translator_ApacheVelocityTests extends VelocityTestCase
{
    public function test_logicKeywords()
    {
        $cmd = Mailcode_Factory::if()->var('FOO.BAR', '==', 20);
        $cmd->getLogicKeywords()->appendAND('$BARFOO == "Other value"', 'variable');

        $expected = '#if($FOO.BAR == 20 && $BARFOO == "Other value")';

        $syntax = $this->translator->createSyntax('ApacheVelocity');

        $result = $syntax->translateCommand($cmd);

        $this->assertEquals($expected, $result);
    }

    public function test_translateSafeguard()
    {
        $syntax = $this->translator->createSyntax('ApacheVelocity');

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

    /**
     * Configuring the translation of showdate commands to use a
     * specific internal date format when they are translated,
     * while not translating them manually.
     */
    public function test_translateSafeguard_dates() : void
    {
        $subject = '{showdate: $FOO.BAR "d.m.Y"}';
        $internalFormat = 'yyyy-MM-dd';
        $expected = '${time.input("'.$internalFormat.'", $FOO.BAR).output("dd.MM.yyyy").zone("UTC")}';

        $syntax = $this->translator->createSyntax('ApacheVelocity');
        $safeguard = Mailcode::create()->createSafeguard($subject);
        $dateCommands = $safeguard->getCollection()->getShowDateCommands();

        foreach($dateCommands as $dateCommand)
        {
            $dateCommand->setTranslationParam('internal_format', $internalFormat);
        }

        $result = $syntax->translateSafeguard($safeguard);

        $this->assertEquals($expected, $result);
    }
}
