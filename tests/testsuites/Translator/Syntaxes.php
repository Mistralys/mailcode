<?php

use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Translator;
use Mailcode\Translator\BaseSyntax;
use MailcodeTestClasses\VelocityTestCase;

final class Translator_SyntaxesTests extends VelocityTestCase
{
    public function test_getSyntaxes() : void
    {
        $translator = Mailcode_Translator::create();
        $syntaxes = $translator->getSyntaxes();

        $this->assertNotEmpty($syntaxes);

        $syntax = array_pop($syntaxes);

        $this->assertInstanceOf(BaseSyntax::class, $syntax);
    }

    public function test_getSyntaxNames() : void
    {
        $translator = Mailcode_Translator::create();
        $names = $translator->getSyntaxNames();

        $this->assertNotEmpty($names);
        $this->assertContains('ApacheVelocity', $names);
    }

    public function test_syntaxExists() : void
    {
        $translator = Mailcode_Translator::create();

        $this->assertTrue($translator->syntaxExists('ApacheVelocity'));
        $this->assertFalse($translator->syntaxExists('UnknownSyntax'));
    }

    public function test_createSyntax() : void
    {
        $translator = Mailcode_Translator::create();

        $syntax = $translator->createApacheVelocity();

        $this->assertEquals('ApacheVelocity', $syntax->getTypeID());
    }

    public function test_createSyntax_notExists() : void
    {
        $translator = Mailcode_Translator::create();

        try
        {
            $translator->createSyntax('UnknownSyntax');
        }
        catch(Mailcode_Exception $e)
        {
            $this->assertEquals($e->getCode(), Mailcode_Translator::ERROR_INVALID_SYNTAX_NAME);
            return;
        }

        $this->fail('No exception triggered.');
    }
}
