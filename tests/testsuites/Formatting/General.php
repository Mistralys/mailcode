<?php

use Mailcode\Mailcode;

final class Formatting_GeneralTests extends MailcodeTestCase
{
    /**
     * The safe string is made to be transformed, and placeholders
     * can be removed. For this reason, the safeguard has the
     * partial mode. The formatting also has this, but it has to
     * be enabled manually.
     */
    public function test_emptyString() : void
    {
        $mailcode = Mailcode::create();
        $safeguard = $mailcode->getParser()->createSafeguard('');
        
        $formatting = $safeguard->createFormatting($safeguard->makeSafe());
        $formatting->replaceWithHTMLHighlighting();
        $formatting->formatWithMarkedVariables();
        
        $this->assertEquals(
            '',
            $formatting->toString()
        );
    }
}
