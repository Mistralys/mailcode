<?php

use Mailcode\Mailcode_Commands_CommonConstants;

final class Mailcode_IfNotContainsTests extends MailcodeTestCase
{
    public function test_validation_contains() : void
    {
        $tests = array(
            array(
                'label' => 'No variable specified',
                'string' => '{if not-contains: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Nothing after variable',
                'string' => '{if not-contains: $FOO}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            ),
            array(
                'label' => 'Keyword, but no string',
                'string' => '{if not-contains: $FOO insensitive:}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            ),
            array(
                'label' => 'Wrong keyword (ignored)',
                'string' => '{if not-contains: $FOO in: "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement case sensitive',
                'string' => '{if not-contains: $FOO "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement case insensitive',
                'string' => '{if not-contains: $FOO insensitive: "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid despite different order',
                'string' => '{if not-contains:"Search" insensitive: $FOO}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Several search terms',
                'string' => '{if not-contains: "Foo" "Bar" insensitive: $FOO}{end}',
                'valid' => true,
                'code' => 0
            )
            
        );
        
        $this->runCollectionTests($tests);
    }
}
