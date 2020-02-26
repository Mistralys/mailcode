<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Variables_Collection_Regular;

final class Variables_VariablesTests extends MailcodeTestCase
{
    public function test_collection_merge()
    {
        $vars = Mailcode::create()->createVariables();
        
        $collection1 = $vars->parseString('$FOO.BAR.$BAR.FOO');
        
        $this->assertSame(2, $collection1->countVariables());
        
        $collection2 = $vars->parseString('$FOO.BAR,$ANOTHER.ONE');

        $this->assertSame(2, $collection2->countVariables());
        
        $merged = $collection1->mergeWith($collection2);
        
        $this->assertSame(3, $merged->countVariables());
    }
    
    public function test_collection_mergeInvalid()
    {
        $vars = Mailcode::create()->createVariables();
        
        $collection1 = $vars->parseString('$FOO.BAR.$BAR.FOO');
        
        $this->assertSame(2, $collection1->countVariables());
        
        $collection2 = $vars->parseString('$FOO.BAR,$1NOTHER.ONE');
        
        $this->assertSame(2, $collection2->countVariables());
        
        $merged = $collection1->mergeWith($collection2);
        
        $this->assertInstanceOf(Mailcode_Variables_Collection_Regular::class, $merged);
        
        if($merged instanceof Mailcode_Variables_Collection_Regular)
        {
            $this->assertSame(2, $merged->countVariables());
            $this->assertSame(1, $merged->getInvalid()->countVariables());
        }
    }
    
    public function test_collection_commandVariables()
    {
        $collection = Mailcode::create()->parseString(
            "{showvar: \$FOO.BAR} {showvar: \$BAR.FOO} {showvar: \$ANOTHER.ONE} {showvar: \$FOO.BAR}"
        );
        
        $vars = $collection->getVariables();
        
        $this->assertSame(3, $vars->countVariables());
    }
}
