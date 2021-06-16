<?php

use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Interfaces_Commands_Validation_ListPropertyVariable;

final class Mailcode_IfListNotContainsTests extends MailcodeTestCase
{
    public function test_validation_contains()
    {
        $tests = array(
            array(
                'label' => 'No variable specified',
                'string' => '{if list-not-contains: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Nothing after variable',
                'string' => '{if list-not-contains: $FOO.PROP}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            ),
            array(
                'label' => 'Keyword, but no string',
                'string' => '{if list-not-contains: $FOO.PROP insensitive:}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            ),
            array(
                'label' => 'Not a list property variable',
                'string' => '{if list-not-contains: $FOO "Search"}{end}',
                'valid' => false,
                'code' => Mailcode_Interfaces_Commands_Validation_ListPropertyVariable::VALIDATION_NOT_A_LIST_PROPERTY
            ),
            array(
                'label' => 'Wrong keyword (ignored)',
                'string' => '{if list-not-contains: $FOO.PROP in: "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement case sensitive',
                'string' => '{if list-not-contains: $FOO.PROP "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement case insensitive',
                'string' => '{if list-not-contains: $FOO.PROP insensitive: "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid despite different order',
                'string' => '{if list-not-contains:"Search" insensitive: $FOO.PROP}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Several search terms',
                'string' => '{if list-not-contains: "Foo" "Bar" insensitive: $FOO.PROP}{end}',
                'valid' => true,
                'code' => 0
            )
            
        );
        
        $this->runCollectionTests($tests);
    }
}
