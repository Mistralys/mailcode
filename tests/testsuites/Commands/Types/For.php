<?php

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Commands_Command_For;

final class Mailcode_ForTests extends MailcodeTestCase
{
    public function test_validation()
    {
        $tests = array(
            array(
                'label' => 'Without parameters',
                'string' => '{for}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'Empty parameters',
                'string' => '{for: }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_MISSING_PARAMETERS
            ),
            array(
                'label' => 'With only a variable',
                'string' => '{for: $FOO.BAR}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_INVALID_FOR_STATEMENT
            ),
            array(
                'label' => 'With invalid variable',
                'string' => '{for: $5OOBAR in: $SOMEVAR.YO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'With missing container variable',
                'string' => '{for: $FOO.BAR in: }{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command_For::VALIDATION_INVALID_FOR_STATEMENT
            ),
            array(
                'label' => 'With  misspelled keyword',
                'string' => '{for: $FOO in $BAR.FOO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_Command::VALIDATION_INVALID_PARAMS_STATEMENT
            ),
            array(
                'label' => 'Valid statement',
                'string' => '{for: $FOO.BAR in: $BAR.FOO}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
}
