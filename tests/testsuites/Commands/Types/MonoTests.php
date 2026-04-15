<?php

declare(strict_types=1);


namespace MailcodeTests\Commands\Types;

use MailcodeTestCase;
use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_Code;
use Mailcode\Mailcode_Commands_Command_Mono;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Interfaces_Commands_ProtectedContent;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;

final class MonoTests extends MailcodeTestCase
{
    public function test_validation() : void
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{mono}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Empty parameters',
                'string' => '{mono: }{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With a variable',
                'string' => '{mono: $FOO.BAR}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'With multiline',
                'string' => '{mono: multiline:}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
    public function test_multilineDisabled() : void
    {
        $cmd = Mailcode_Factory::misc()->mono();
        
        $this->assertFalse($cmd->isMultiline());
    }

    public function test_multilineEnabled() : void
    {
        $cmd = Mailcode_Factory::misc()->mono(true);

        $this->assertTrue($cmd->isMultiline());
    }

    public function test_normalize() : void
    {
        foreach($this->monoVariants() as $test)
        {
            $this->assertEquals($test['normalizedExpected'], $test['command']->getNormalized(), $test['label']);
        }
    }

    public function test_highlight() : void
    {
        foreach($this->monoVariants() as $test)
        {
            if($test['hasParams'])
            {
                $this->assertStringContainsString(
                    '<span class="mailcode-hyphen">:</span>',
                    $test['command']->getHighlighted(),
                    $test['label'].': command with params must contain a colon in highlighted output.'
                );
            }
            else
            {
                $this->assertStringNotContainsString(
                    '<span class="mailcode-hyphen">:</span>',
                    $test['command']->getHighlighted(),
                    $test['label'].': parameterless command must not contain a colon in highlighted output.'
                );
            }
        }
    }

    /**
     * @return array<int, array{label: string, command: Mailcode_Commands_Command_Mono, hasParams: bool, normalizedExpected: string}>
     */
    private function monoVariants() : array
    {
        return array(
            array(
                'label' => 'Parameterless',
                'command' => Mailcode_Factory::misc()->mono(),
                'hasParams' => false,
                'normalizedExpected' => '{mono}'
            ),
            array(
                'label' => 'Multiline',
                'command' => Mailcode_Factory::misc()->mono(true),
                'hasParams' => true,
                'normalizedExpected' => '{mono: multiline:}'
            ),
            array(
                'label' => 'Multiline, with class',
                'command' => Mailcode_Factory::misc()->mono(true, array('class1', 'class2')),
                'hasParams' => true,
                'normalizedExpected' => '{mono: multiline: "class1 class2"}'
            )
        );
    }
}
