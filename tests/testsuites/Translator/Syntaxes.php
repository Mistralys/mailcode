<?php

use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Translator;
use Mailcode\Mailcode_Translator_Syntax;

final class Translator_SyntaxesTests extends VelocityTestCase
{
    public function test_getSyntaxes() : void
    {
        $translator = new Mailcode_Translator();
        $syntaxes = $translator->getSyntaxes();

        $this->assertNotEmpty($syntaxes);

        $syntax = array_pop($syntaxes);

        $this->assertInstanceOf(Mailcode_Translator_Syntax::class, $syntax);
    }

    public function test_getSyntaxNames() : void
    {
        $translator = new Mailcode_Translator();
        $names = $translator->getSyntaxNames();

        $this->assertNotEmpty($names);
        $this->assertContains('ApacheVelocity', $names);
    }

    public function test_syntaxExists() : void
    {
        $translator = new Mailcode_Translator();

        $this->assertTrue($translator->syntaxExists('ApacheVelocity'));
        $this->assertFalse($translator->syntaxExists('UnknownSyntax'));
    }

    public function test_createSyntax() : void
    {
        $translator = new Mailcode_Translator();

        $syntax = $translator->createSyntax('ApacheVelocity');

        $this->assertEquals('ApacheVelocity', $syntax->getTypeID());
    }

    public function test_createSyntax_notExists() : void
    {
        $translator = new Mailcode_Translator();

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
