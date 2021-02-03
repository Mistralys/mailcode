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

    public function test_getCommands() : void
    {
        $collection = Mailcode::create()->parseString($this->tplShowVars);

        $this->assertCount(4, $collection->getShowCommands());
        $this->assertCount(1, $collection->getShowVariableCommands());
        $this->assertCount(1, $collection->getShowDateCommands());
        $this->assertCount(1, $collection->getCommandsByClass(Mailcode_Commands_Command_ShowSnippet::class));
    }

    public function test_merge() : void
    {
        $collectionA = Mailcode::create()->parseString($this->tplShowVars);
        $collectionB = Mailcode::create()->parseString('{showvar: $OTHER}');

        $merged = $collectionA->mergeWith($collectionB);

        $this->assertNotSame($collectionA, $merged);
        $this->assertCount(5, $merged->getCommands());
    }
}
