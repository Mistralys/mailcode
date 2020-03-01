<?php

use Mailcode\Mailcode;
use Mailcode\Mailcode_Variables_Collection_Regular;
use Mailcode\Mailcode_Variables;
use Mailcode\Mailcode_Variables_Variable;

final class Variables_VariablesTests extends MailcodeTestCase
{
    public function test_collection_merge()
    {
        $vars = Mailcode::create()->createVariables();
        
        $collection1 = $vars->parseString('
            $FOO.BAR 
            $BAR.FOO
        ');
        
        $this->assertSame(2, $collection1->countVariables());
        
        $collection2 = $vars->parseString('
            $FOO.BAR 
            $ANOTHER.ONE 
            $FOO . BAR
        ');

        $this->assertSame(3, $collection2->countVariables());
        
        $merged = $collection1->mergeWith($collection2);
        
        $this->assertSame(5, $merged->countVariables(), '5 variables in total, ungrouped');
        $this->assertSame(4, count($merged->getGroupedByHash()), '4 variables grouped by hash');
        $this->assertSame(3, count($merged->getGroupedByName()), '3 variables grouped by name');
    }
    
    public function test_collection_mergeInvalid()
    {
        $vars = Mailcode::create()->createVariables();
        
        $collection1 = $vars->parseString('$FOO.BAR.$BAR.FOO');
        
        $this->assertSame(2, $collection1->countVariables());
        
        $collection2 = $vars->parseString('$FOO.BAR,$1NOTHER.ONE');
        
        $this->assertSame(1, $collection2->countVariables());
        
        $merged = $collection1->mergeWith($collection2);
        
        $this->assertInstanceOf(Mailcode_Variables_Collection_Regular::class, $merged);
        
        if($merged instanceof Mailcode_Variables_Collection_Regular)
        {
            $this->assertSame(3, $merged->countVariables());
            $this->assertSame(2, count($merged->getGroupedByName()));
            $this->assertSame(1, $merged->getInvalid()->countVariables());
        }
    }
    
    public function test_collection_commandVariables()
    {
        $collection = Mailcode::create()->parseString("
            {showvar: \$FOO.BAR} 
            {showvar: \$BAR.FOO} 
            {showvar: \$ANOTHER.ONE} 
            {showvar: \$FOO.BAR}"
        );
        
        $vars = $collection->getVariables();
        
        $this->assertSame(4, $vars->countVariables());
        $this->assertSame(3, count($vars->getGroupedByName()));
    }

   /**
    * Checking the variables parsing, incuding verifying the 
    * resulting name to ensure that what was matched corresponds
    * to what is expected.
    */
    public function test_parseVariableName()
    {
        $tests = array(
            array(
                'label' => 'Valid without underscores',
                'var' => '$FOO.BAR',
                'valid' => true,
                'name' => '$FOO.BAR'
            ),
            array(
                'label' => 'Valid with underscores',
                'var' => '$FOO_BAR.BAR_FOO',
                'valid' => true,
                'name' => '$FOO_BAR.BAR_FOO'
            ),
            array(
                'label' => 'Valid with spacing',
                'var' => '
                    $FOO_BAR
                    .
                    BAR_FOO',
                'valid' => true,
                'name' => '$FOO_BAR.BAR_FOO'
            ),
            array(
                'label' => 'Valid with underscores and numbers',
                'var' => '$FOO_1BAR.B8AR_00FOO',
                'valid' => true,
                'name' => '$FOO_1BAR.B8AR_00FOO'
            ),
            array(
                'label' => 'Invalid with starting number in path',
                'var' => '$8OO.BAR',
                'valid' => false,
                'name' => ''
            ),
            array(
                'label' => 'Invalid with starting number in name',
                'var' => '$FOO.8AR',
                'valid' => false,
                'name' => ''
            ),
            array(
                'label' => 'Invalid with starting underscore in path',
                'var' => '$_OO.BAR',
                'valid' => false,
                'name' => ''
            ),
            array(
                'label' => 'Invalid with ending underscore in path',
                'var' => '$FOO_.BAR',
                'valid' => false,
                'name' => ''
            ),
            array(
                'label' => 'Invalid with starting underscore in name',
                'var' => '$FOO._BAR',
                'valid' => false,
                'name' => ''
            ),
            array(
                'label' => 'Invalid with ending underscore in name',
                'var' => '$FOO.BAR_',
                'valid' => false,
                'name' => ''
            ),
            array(
                'label' => 'Valid single name variable',
                'var' => '$FOOBAR',
                'valid' => true,
                'name' => '$FOOBAR'
            ),
            array(
                'label' => 'Invalid single name variable',
                'var' => '$8OOBAR',
                'valid' => false,
                'name' => ''
            ),
            array(
                'label' => 'Valid mixed case variable',
                'var' => '$Foo.Bar',
                'valid' => true,
                'name' => '$Foo.Bar'
            )
        );
        
        $variables = new Mailcode_Variables();
        
        foreach($tests as $test)
        {
            $result = $variables->parseString($test['var']);
            
            if($test['valid'])
            {
                $this->assertSame(1, $result->countVariables());
            }
            
            $this->assertSame($test['valid'], !$result->hasInvalid(), $test['label']);
            
            $first = $result->getFirst();
            
            if($test['valid'])
            {
                $this->assertInstanceOf(Mailcode_Variables_Variable::class, $first);
                $this->assertSame($test['name'], $first->getFullName(), $test['label']);
            }
        }
    }
    
   /**
    * Ensure that US numbers notation is not accidentally considered
    * like variables, or even invalid variables.
    */
    public function test_USNumbers()
    {
        $variables = new Mailcode_Variables();
        
        $result = $variables->parseString('$45.12');
        
        $this->assertFalse($result->hasInvalid());
        $this->assertSame(0, $result->countVariables());
        
        $result = $variables->parseString('$451');
        
        $this->assertFalse($result->hasInvalid());
        $this->assertSame(0, $result->countVariables());
    }
    
    public function test_getByFullName()
    {
        $tests = array(
            array(
                'label' => 'Regular uppercase variable',
                'var' => '$FOO.BAR',
            ),
            array(
                'label' => 'Single uppercase variable',
                'var' => '$SINGLE',
            ),
            array(
                'label' => 'Mixed case variable',
                'var' => '$Mixed.Case',
            )
        );
        
        $variables = new Mailcode_Variables();
    
        foreach($tests as $test)
        {
            $result = $variables->parseString($test['var']);
            
            $this->assertInstanceOf(Mailcode_Variables_Variable::class, $result->getByFullName($test['var'])->getFirst());
        }
    }
}
