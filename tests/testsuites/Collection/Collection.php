<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Command_ShowSnippet;

final class Collection_CollectionTests extends MailcodeTestCase
{
    protected $tplShowVars = <<<'EOD'
Simple {showvar: $SHOW} show command.
Displaying {shownumber: $NUMBER} a number.
It all happened on {showdate: $DATE}.
Insert content of {showsnippet: $SNIPPET} here.
EOD;

    protected $tplListVars = <<<'EOD'
{for: $RECORD in: $FORLIST}
    {if list-contains: $CONTAINS.PROP "Term"}
    {end}
    {if list-not-contains: $NOTCONTAINS.PROP "Term"}
    {end}
{end}
EOD;


    public function test_getCommands() : void
    {
        $collection = Mailcode::create()->parseString($this->tplShowVars);

        $this->assertCount(4, $collection->getShowCommands());
        $this->assertCount(1, $collection->getShowVariableCommands());
        $this->assertCount(1, $collection->getShowDateCommands());
    }

    public function test_merge() : void
    {
        $collectionA = Mailcode::create()->parseString($this->tplShowVars);
        $collectionB = Mailcode::create()->parseString('{showvar: $OTHER}');

        $merged = $collectionA->mergeWith($collectionB);

        $this->assertNotSame($collectionA, $merged);
        $this->assertCount(5, $merged->getCommands());
    }

    public function test_getListVariables() : void
    {
        $collection = Mailcode::create()->parseString($this->tplListVars);
        $commands = $collection->getListVariableCommands();

        $this->assertCount(3, $commands);

        $names = array();
        foreach($commands as $command)
        {
            $names = array_merge($names, $command->getListVariables()->getNames());
        }

        sort($names);

        $expected = array(
            '$CONTAINS',
            '$FORLIST',
            '$NOTCONTAINS'
        );

        $this->assertEquals($expected, $names);
    }

    /**
     * The validation must be executed automatically when fetching
     * a collection's commands, as this is where the nesting is
     * initialized.
     */
    public function test_validateBeforeGetCommands() : void
    {
        $string = '{showvar: $RECORD.NAME}';

        $collection = Mailcode::create()->parseString($string);

        $this->assertTrue($collection->hasBeenValidated());
    }
}
