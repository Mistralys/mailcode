<?php

use Mailcode\Mailcode;

final class Mailcode_Highlighting_WithoutFormatterTests extends MailcodeTestCase
{
    /**
     * Test for a highlighting PHP error when no formatter has
     * been chosen at all in the safeguard.
     */
    public function test_withoutFormatter() : void
    {
        $text = '{showvar: $FOOBAR}';
        
        $safeguard = Mailcode::create()->createSafeguard($text);
        $safe = $safeguard->makeSafe();
        
        $safeguard->makeHighlighted($safe);
        
        // if no error occurrs, all is well.
        
        $this->addToAssertionCount(1);
    }
}
