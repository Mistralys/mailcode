<?php

use AppUtils\FileHelper;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory;

final class Formatting_MarkVariablesTests extends MailcodeTestCase
{
    public function test_markVariables() : void
    {
        $tests = array(
            array(
                'label' => 'Variable in attribute',
                'html' => '<strong class="%s">Text here</strong>',
                'command' => '{showvar: $FOOBAR}',
                'marked' => false
            ),
            array(
                'label' => 'Variable at beginning of the document',
                'html' => '%s <strong>Text here</strong>',
                'command' => '{showvar: $FOOBAR}',
                'marked' => true
            ),
            array(
                'label' => 'Variable in style tag',
                'html' => '<style>%s</style>',
                'command' => '{showvar: $FOOBAR}',
                'marked' => false
            ),
            array(
                'label' => 'Variable in script tag',
                'html' => '<script>%s</script>',
                'command' => '{showvar: $FOOBAR}',
                'marked' => false
            ),
            array(
                'label' => 'Variable in valid tag',
                'html' => '<p>%s</p>',
                'command' => '{showvar: $FOOBAR}',
                'marked' => true
            ),
            array(
                'label' => 'Date in valid tag',
                'html' => '<p>%s</p>',
                'command' => '{showdate: $FOOBAR}',
                'marked' => true
            ),
            array(
                'label' => 'Logic command in valid tag',
                'html' => '<p>%s</p>',
                'command' => '{setvar: $FOOBAR "Value"}',
                'marked' => false
            )
        );
        
        $parser = Mailcode::create()->getParser();
        
        foreach($tests as $test)
        {
            $safeguard = $parser->createSafeguard(sprintf($test['html'], $test['command']));
            
            try
            {
                $safe = $safeguard->makeSafe();
                
                $formatting = $safeguard->createFormatting($safe);
                $formatter = $formatting->formatWithMarkedVariables();
                $template = $formatter->getTemplate();
                
                $highlighted = $formatting->toString();

                $expected = sprintf($test['html'], $test['command']);
                if($test['marked'])
                {
                    $expected = sprintf($test['html'], sprintf($template, $test['command']));
                }
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
            
            $this->assertEquals($expected, $highlighted, $test['label']);
        }
    }
    
    public function test_setTemplate_noPlaceholder() : void
    {
        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard('');
        $formatting = $safeguard->createFormatting('');
        
        $formatter = $formatting->formatWithMarkedVariables();
        
        $this->expectException(Mailcode_Exception::class);
        
        $formatter->setTemplate('foobar');
    }
    
    public function test_setTemplate_severalPlaceholders() : void
    {
        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard('');
        $formatting = $safeguard->createFormatting('');

        $formatter = $formatting->formatWithMarkedVariables();
        
        $this->expectException(Mailcode_Exception::class);
        
        $formatter->setTemplate('%s foo %s');
    }
    
    public function test_setTemplate() : void
    {
        $command = '{showvar: $FOOBAR}';
        $template = '<test>%s</test>';
        
        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard($command);
        $formatting = $safeguard->createFormatting($safeguard->makeSafe());
        
        $formatter = $formatting->formatWithMarkedVariables();
        $formatter->setTemplate($template);
        
        $this->assertEquals(sprintf($template, $command), $formatting->toString());
    }
    
    public function test_getPrependAppend() : void
    {
        $command = '{showvar: $FOOBAR}';
        $template = '<test>%s</test>';
        
        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard($command);
        $formatting = $safeguard->createFormatting($safeguard->makeSafe());
        
        $formatter = $formatting->formatWithMarkedVariables();
        $formatter->setTemplate($template);
        
        $this->assertEquals('<test>', $formatter->getPrependTag());
        $this->assertEquals('</test>', $formatter->getAppendTag());
    }
    
    public function test_combineWithHighlighting() : void
    {
        $command = Mailcode_Factory::showVar('FOOBAR');
        $commandString = $command->getNormalized();
        $template = '<test>%s</test>';
        
        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard($commandString);
        $formatting = $safeguard->createFormatting($safeguard->makeSafe());
        $formatting->replaceWithHTMLHighlighting();
        
        $formatter = $formatting->formatWithMarkedVariables();
        $formatter->setTemplate($template);
        
        $this->assertEquals(sprintf($template, $command->getHighlighted()), $formatting->toString());
    }
    
   /**
    * Creates a file with the highlighting to check manually in a browser.
    */
    public function test_highlightExampleFile() : void
    {
        $sourceFile = __DIR__.'/../../assets/files/test-highlight.html';
        $targetFile = __DIR__.'/../../assets/files/test-highlight-marked-variables.html';
        
        $content = FileHelper::readContents($sourceFile);
        
        $parser = Mailcode::create()->getParser();
        $safeguard = $parser->createSafeguard($content);
        $formatting = $safeguard->createFormatting($safeguard->makeSafe());
        $formatting->formatWithMarkedVariables();
        
        FileHelper::saveFile($targetFile, $formatting->toString());
        
        $this->addToAssertionCount(1);
    }
}
