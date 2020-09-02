<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;

final class Formatting_PartialTests extends MailcodeTestCase
{
    /**
     * The safe string is made to be transformed, and placeholders
     * can be removed. For this reason, the safeguard has the 
     * partial mode. The formatting also has this, but it has to
     * be enabled manually.
     */
    public function test_placeholderError() : void
    {
        $text = '{showvar: $FOOBAR} and {showvar: $BARFOO}';
        
        $safeguard = Mailcode::create()->createSafeguard($text);
        
        $placeholders = $safeguard->getPlaceholders();
        $placeholder = $placeholders[0];
        
        $safe = $safeguard->makeSafe();
        
        $edited = str_replace($placeholder->getReplacementText(), 'REPLACED', $safe);
        
        $formatting = $safeguard->createFormatting($edited);
        $formatting->replaceWithNormalized();
        
        $this->expectException(Mailcode_Exception::class);
        
        $formatting->toString();
    }
    
    public function test_placeholderPartial() : void
    {
        $text = '{showvar: $FOOBAR} and {showvar: $BARFOO}';
        
        $safeguard = Mailcode::create()->createSafeguard($text);
        
        $placeholders = $safeguard->getPlaceholders();
        $placeholder = $placeholders[0];
        
        $safe = $safeguard->makeSafe();
        
        $edited = str_replace($placeholder->getReplacementText(), 'REPLACED', $safe);
        
        $formatting = $safeguard->createFormatting($edited);
        $formatting->replaceWithNormalized();
        
        // Turn on partial mode
        $formatting->makePartial();
        
        $this->assertEquals('REPLACED and {showvar: $BARFOO}', $formatting->toString());
    }
}
