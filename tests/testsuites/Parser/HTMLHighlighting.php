<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Parser_Safeguard_Placeholder_Locator_Replacer;
use AppUtils\FileHelper;

final class Parser_HTMLHighlightingFormatterTests extends MailcodeTestCase
{
    public function test_parsing()
    {
        $tests = array(
            array(
                'label' => 'Commands in attribute',
                'html' => '<strong class="{setvar: $FOOBAR "Foo"}">Text here</strong>',
                'expected' => '<strong class="{setvar: $FOOBAR "Foo"}">Text here</strong>',
                'highlighted' => '<strong class="{setvar: $FOOBAR "Foo"}">Text here</strong>'
            ),
            array(
                'label' => 'Command at beginning of the document',
                'html' => '{setvar: $FOOBAR = "Foo"} <strong>Text here</strong>',
                'expected' => 
                    '<mailcode:highlight>{setvar: $FOOBAR = "Foo"}</mailcode:highlight> <strong>Text here</strong>',
                'highlighted' => 
                    Mailcode_Factory::setVar('FOOBAR', 'Foo')->getHighlighted().
                    ' <strong>Text here</strong>'
            ),
            array(
                'label' => 'Commands in style tag',
                'html' => '<style>{setvar: $FOOBAR "Foo"}</style>',
                'expected' => '<style>{setvar: $FOOBAR "Foo"}</style>',
                'highlighted' => '<style>{setvar: $FOOBAR "Foo"}</style>'
            ),
            array(
                'label' => 'Commands in script tag 1',
                'html' => '<script>{setvar: $FOOBAR "Foo"}</script>',
                'expected' => '<script>{setvar: $FOOBAR "Foo"}</script>',
                'highlighted' => '<script>{setvar: $FOOBAR "Foo"}</script>'
            ),
            array(
                'label' => 'Commands in script tag with confusing brackets in JS syntax',
                'html' => '<script>var test = 1 + 4; if(test <> 45) { test = {showvar: $AMOUNT}; }</script>',
                'expected' => '<script>var test = 1 + 4; if(test <> 45) { test = {showvar: $AMOUNT}; }</script>',
                'highlighted' => '<script>var test = 1 + 4; if(test <> 45) { test = {showvar: $AMOUNT}; }</script>'
            ),
            array(
                'label' => 'Commands in script tag, ignoring whitespace styles',
                'html' => 
'<script>

    {setvar: $FOOBAR "Foo"}

</script>',
                'expected' => 
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
            $safeguard->selectHTMLHighlightingFormatter();
                
            try
            {
                $safe = $safeguard->makeSafe();
                $result = $safeguard->makeWhole($safe);
                $high = $safeguard->makeHighlighted($safe);
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

            $this->assertEquals($test['expected'], $result, $test['label']);
            $this->assertEquals($test['highlighted'], $high, $test['label']);
        }
    }
    
   /**
    * Tests for a bug where hzighlighting commands in an HTML file failed.
    * 
    * @see Mailcode_Parser_Safeguard_Placeholder_Locator_Replacer::replaceLocation()
    */
    public function test_exampleHTML() : void
    {
        $content = FileHelper::readContents(__DIR__.'/../../assets/files/test-highlight.html');
        
        $parser = Mailcode::create()->getParser();
        
        $safeguard = $parser->createSafeguard($content);
        $safeguard->selectHTMLHighlightingFormatter();
        
        $this->assertTrue($safeguard->isValid());
        
        try
        {
            $safe = $safeguard->makeSafe();
            $whole = $safeguard->makeWhole($safe);
            
            FileHelper::saveFile(__DIR__.'/../../assets/files/test-highlight-output.html', $whole);
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
        
        $safeguard = Mailcode::create()->getParser()->createSafeguard($html);
        
        $formatter = $safeguard->selectHTMLHighlightingFormatter();
        $formatter->excludeTag('customtag');

        $safe = $safeguard->makeSafe();
        
        $this->assertEquals(
            $html,
            $safeguard->makeWhole($safe)
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
        
        $safeguard = Mailcode::create()->getParser()->createSafeguard($html);
        
        $formatter = $safeguard->selectHTMLHighlightingFormatter();
        $formatter->excludeTag('customtag');
        
        $safe = $safeguard->makeSafe();
        
        $this->assertEquals(
            $html,
            $safeguard->makeWhole($safe)
        );
    }
}
