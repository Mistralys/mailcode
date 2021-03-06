<?php

declare(strict_types=1);

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_Code;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Interfaces_Commands_ProtectedContent;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Exception;

final class Mailcode_MonoTests extends MailcodeTestCase
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
        $tests = array(
            array(
                'label' => 'Parameterless',
                'command' => Mailcode_Factory::misc()->mono(),
                'expected' => '{mono}'
            ),
            array(
                'label' => 'Multiline',
                'command' => Mailcode_Factory::misc()->mono(true),
                'expected' => '{mono: multiline:}'
            ),
            array(
                'label' => 'Multiline, with class',
                'command' => Mailcode_Factory::misc()->mono(true, array('class1', 'class2')),
                'expected' => '{mono: multiline: "class1 class2"}'
            )
        );

        foreach($tests as $test)
        {
            $this->assertEquals($test['expected'], $test['command']->getNormalized(), $test['label']);
        }
    }
}
