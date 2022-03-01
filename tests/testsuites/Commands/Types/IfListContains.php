<?php

use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Interfaces_Commands_Validation_ListPropertyVariable;

final class Mailcode_IfListContainsTests extends MailcodeTestCase
{
    public function test_validation_contains()
    {
        $tests = array(
            array(
                'label' => 'No variable specified',
                'string' => '{if list-contains: "Value"}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_VARIABLE_MISSING
            ),
            array(
                'label' => 'Nothing after variable',
                'string' => '{if list-contains: $FOO.PROP}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            ),
            array(
                'label' => 'Keyword, but no string',
                'string' => '{if list-contains: $FOO.PROP insensitive:}{end}',
                'valid' => false,
                'code' => Mailcode_Commands_CommonConstants::VALIDATION_SEARCH_TERM_MISSING
            ),
            array(
                'label' => 'Not a list property variable',
                'string' => '{if list-contains: $FOO "Search"}{end}',
                'valid' => false,
                'code' => Mailcode_Interfaces_Commands_Validation_ListPropertyVariable::VALIDATION_NOT_A_LIST_PROPERTY
            ),
            array(
                'label' => 'Wrong keyword (ignored)',
                'string' => '{if list-contains: $FOO.PROP in: "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement case sensitive',
                'string' => '{if list-contains: $FOO.PROP "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid statement case insensitive',
                'string' => '{if list-contains: $FOO.PROP insensitive: "Search"}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Valid despite different order',
                'string' => '{if list-contains:"Search" insensitive: $FOO.PROP}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Several search terms',
                'string' => '{if list-contains: "Foo" "Bar" insensitive: $FOO.PROP}{end}',
                'valid' => true,
                'code' => 0
            ),
            array(
                'label' => 'Regex with brackets',
                'string' => '{if list-contains: $LIST.PROPERTY "[0-9]{3}" regex:}{end}',
                'valid' => true,
                'code' => 0
            )
        );
        
        $this->runCollectionTests($tests);
    }

    public function test_bracketsInRegex() : void
    {
        $cmd = Mailcode_Factory::if()
            ->listContains(
                'LIST.PROP',
                array("[0-9:]{3}"),
                false,
                true
            );

        $this->assertSame(
            '{if list-contains: $LIST.PROP regex: "[0-9:]\{3\}"}',
            $cmd->getNormalized()
        );
    }

    public function test_getListVariable() : void
    {
        $cmd = Mailcode_Factory::if()->listContains('LIST.PROP', array("Foo"));

        $listVar = $cmd->getListVariable();
        $propVar = $cmd->getListProperty();

        $this->assertEquals('$LIST', $listVar->getFullName());
        $this->assertEquals('$LIST.PROP', $propVar->getFullName());
    }

    public function test_caseDisabledByDefault() : void
    {
        $cmd = Mailcode_Factory::if()->listContains('LIST.PROP', array("Foo"));

        $this->assertFalse($cmd->isCaseInsensitive());
    }

    public function test_isCaseInsensitive() : void
    {
        $cmd = Mailcode_Factory::if()->listContains('LIST.PROP', array("Foo"), true);

        $this->assertTrue($cmd->isCaseInsensitive());
    }

    public function test_regexDisabledByDefault() : void
    {
        $cmd = Mailcode_Factory::if()->listContains('LIST.PROP', array("Foo"));

        $this->assertFalse($cmd->isRegexEnabled());
    }

    public function test_isRegexEnabled() : void
    {
        $cmd = Mailcode_Factory::if()->listContains('LIST.PROP', array("Foo"), false, true);

        $this->assertTrue($cmd->isRegexEnabled());
    }
}
