<?php

declare(strict_types=1);

namespace testsuites\Commands\Types;

use Mailcode\Mailcode_Factory;
use MailcodeTestCase;

final class IfTests extends MailcodeTestCase
{
    public function test_validation_passthru() : void
    {
        $tests = array(
            array(
                'label' => 'No validation.',
                'string' => '{if: $FOOBAR * "String" == 6 / 14}{end}',
                'normalized' => '{if: $FOOBAR * "String" == 6 / 14}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Velocity syntax',
                'string' => '{if: $FOOBAR.urldecode().matches(".*[?].*")}{end}',
                'normalized' => '{if: $FOOBAR.urldecode().matches(".*[?].*")}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Escaping brackets',
                'string' => '{if: $\{FOOBAR\} == "Value"}{end}',
                'normalized' => '{if: $\{FOOBAR\} == "Value"}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }
    
   /**
    * Check for the issue where a zero in a command 
    * would be stripped out when normalized.
    */
    public function test_normalize_zero() : void
    {
        $cmd = Mailcode_Factory::if()->if("0 == 1");
        
        $this->assertEquals('{if: 0 == 1}', $cmd->getNormalized());
    }
}
