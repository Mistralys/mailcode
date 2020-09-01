<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;
use AppUtils\FileHelper;
use Mailcode\Mailcode_Parser_Safeguard;

final class Parser_HTMLHighlightingFormatterTests extends MailcodeTestCase
{
    private function createExampleSafeguard() : Mailcode_Parser_Safeguard
    {
        $content = FileHelper::readContents(__DIR__.'/../../assets/files/test-highlight.html');
        
        $parser = Mailcode::create()->getParser();
        
        return $parser->createSafeguard($content);
    }
    
    public function test_parsing()
    {
        $tests = array(
            array(
                'label' => 'Commands in attribute',
                'html' => '<strong class="{setvar: $FOOBAR "Foo"}">Text here</strong>',
                'highlighted' => '<strong class="{setvar: $FOOBAR "Foo"}">Text here</strong>'
            ),
            array(
                'label' => 'Command at beginning of the document',
                'html' => '{setvar: $FOOBAR = "Foo"} <strong>Text here</strong>',
                'highlighted' => 
                    Mailcode_Factory::setVar('FOOBAR', 'Foo')->getHighlighted().
                    ' <strong>Text here</strong>'
            ),
            array(
                'label' => 'Commands in style tag',
                'html' => '<style>{setvar: $FOOBAR "Foo"}</style>',
                'highlighted' => '<style>{setvar: $FOOBAR "Foo"}</style>'
            ),
            array(
                'label' => 'Commands in script tag 1',
                'html' => '<script>{setvar: $FOOBAR "Foo"}</script>',
                'highlighted' => '<script>{setvar: $FOOBAR "Foo"}</script>'
            ),
            array(
                'label' => 'Commands in script tag with confusing brackets in JS syntax',
                'html' => '<script>var test = 1 + 4; if(test <> 45) { test = {showvar: $AMOUNT}; }</script>',
                'highlighted' => '<script>var test = 1 + 4; if(test <> 45) { test = {showvar: $AMOUNT}; }</script>'
            ),
            array(
                'label' => 'Commands in script tag, ignoring whitespace styles',
                'html' => 
'<script>

    {setvar: $FOOBAR "Foo"}

</script>',
                'highlighted' => 
'<script>

    {setvar: $FOOBAR "Foo"}

</script>'
            )
        );
        
        $parser = Mailcode::create()->getParser();
        
        foreach($tests as $test)
        {
            $safeguard = $parser->createSafeguard($test['html']);
                
            try
            {
                $safe = $safeguard->makeSafe();
                
                $formatting = $safeguard->createFormatting($safe);
                $formatting->replaceWithHTMLHighlighting();
                
                $highlighted = $formatting->toString();
            }
            catch(Mailcode_Exception $e)
            {
                $this->fail(sprintf(
                    'Exception: #%2$s %3$s %1$s Details: %4$s %1$s Safe string: %1$s %5$s',
                    PHP_EOL,
                    $e->getCode(),
                    $e->getMessage(),
                    $e->getDetails(),
                    $safe
                ));
            }

            $this->assertEquals($test['highlighted'], $highlighted, $test['label']);
        }
    }
    
   /**
    * Tests for a bug where highlighting commands in an HTML file failed.
    */
    public function test_exampleHTML() : void
    {
        $safeguard = $this->createExampleSafeguard();
        
        $this->assertTrue($safeguard->isValid());
        
        try
        {
            $safe = $safeguard->makeSafe();

            $formatting = $safeguard->createFormatting($safe);
            $formatting->replaceWithHTMLHighlighting();
            
            FileHelper::saveFile(
                __DIR__.'/../../assets/files/test-highlight-output-raw.html', 
                $formatting->toString()
            );
        }
        catch(Mailcode_Exception $e)
        {
            $this->fail(sprintf(
                'Exception: #%2$s %3$s %1$s Details: %4$s %1$s',
                PHP_EOL,
                $e->getCode(),
                $e->getMessage(),
                $e->getDetails()
            ));
        }
    }
    
    public function test_exampleHTML_highlight() : void
    {
        $safeguard = $this->createExampleSafeguard();
        
        $this->assertTrue($safeguard->isValid());
        
        try
        {
            $safe = $safeguard->makeSafe();
            
            $formatting = $safeguard->createFormatting($safe);
            $formatting->replaceWithHTMLHighlighting();
            
            $styler = Mailcode::create()->createStyler();
            
            $html = str_replace('</body>', $styler->getStyleTag().'</body>', $formatting->toString());
            
            FileHelper::saveFile(__DIR__.'/../../assets/files/test-highlight-output.html', $html);
        }
        catch(Mailcode_Exception $e)
        {
            $this->fail(sprintf(
                'Exception: #%2$s %3$s %1$s Details: %4$s %1$s',
                PHP_EOL,
                $e->getCode(),
                $e->getMessage(),
                $e->getDetails()
            ));
        }
    }
    
   /**
    * Excluding tags must also work when the command is in the tag's attributes.
    */
    public function test_addExcludedTag_parameter() : void
    {
        $html = '<customtag class="{showvar: $FOO}">Text here</customtag>';
        
        $mailcode = Mailcode::create();
        $safeguard = $mailcode->getParser()->createSafeguard($html);
        
        $formatting = $safeguard->createFormatting($safeguard->makeSafe());
        $formatting->replaceWithHTMLHighlighting()->excludeTag('customtag');
        
        $this->assertEquals(
            $html,
            $formatting->toString()
        );
    }
    
   /**
    * When excluding a tag, any child commands must be excluded, 
    * no matter how deeply nested they are.
    */
    public function test_addExcludedTag_nesting() : void
    {
        $html = 
'<customtag>
    <p>Other text</p>
    <p class="argh">Argh</p>
    <p>
        <b>{showvar: $FOO}</b>
    </p>
</customtag>';
     
        $mailcode = Mailcode::create();
        $safeguard = $mailcode->getParser()->createSafeguard($html);
        
        $formatting = $safeguard->createFormatting($safeguard->makeSafe());
        $formatting->replaceWithHTMLHighlighting()->excludeTag('customtag');
        
        $this->assertEquals(
            $html,
            $formatting->toString()
        );
    }
}
