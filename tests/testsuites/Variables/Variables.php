<?php

declare(strict_types=1);

use Mailcode\Mailcode;
use Mailcode\Mailcode_Factory;
use Mailcode\Mailcode_Variables_Collection;
use Mailcode\Mailcode_Variables_Collection_Regular;
use Mailcode\Mailcode_Variables;
use Mailcode\Mailcode_Variables_Variable;

final class Variables_VariablesTests extends MailcodeTestCase
{
    public function test_collection_merge() : void
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
        $this->assertSame(3, count($merged->getGroupedByUniqueName()), '3 variables grouped by unique name');
    }
    
    public function test_collection_mergeInvalid() : void
    {
        $vars = Mailcode::create()->createVariables();
        
        $collection1 = $vars->parseString('$FOO.BAR.$BAR.FOO');
        
        $this->assertSame(2, $collection1->countVariables());
        
        $collection2 = $vars->parseString('$FOO.BAR,$1NOTHER.ONE');
        
        $this->assertSame(1, $collection2->countVariables());
        
        $merged = $collection1->mergeWith($collection2);
        
        $this->assertInstanceOf(Mailcode_Variables_Collection_Regular::class, $merged);
        $this->assertSame(3, $merged->countVariables());
        $this->assertCount(2, $merged->getGroupedByName());
        $this->assertSame(1, $merged->getInvalid()->countVariables());
    }
    
    public function test_collection_commandVariables() : void
    {
        $collection = Mailcode::create()->parseString('
            {showvar: $FOO.BAR} 
            {showvar: $BAR.FOO} 
            {showvar: $ANOTHER.ONE} 
            {showvar: $FOO.BAR}
            {if list-contains: $FOO.BAR "Search"}
            {for: $RECORD in: $LIST}'
        );
        
        $vars = $collection->getVariables();

        $this->assertSame(7, $vars->countVariables(), $this->debugVariablesCollection($vars));
        $this->assertSame(5, count($vars->getGroupedByName()), $this->debugVariablesCollection($vars));

        // By unique name, the $FOO.BAR appears 2x
        $this->assertSame(6, count($vars->getGroupedByUniqueName()), $this->debugVariablesCollection($vars));
    }

   /**
    * Checking the variables parsing, including verifying the
    * resulting name to ensure that what was matched corresponds
    * to what is expected.
    */
    public function test_parseVariableName() : void
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
    public function test_USNumbers() : void
    {
        $variables = new Mailcode_Variables();
        
        $result = $variables->parseString('$45.12');
        
        $this->assertFalse($result->hasInvalid());
        $this->assertSame(0, $result->countVariables());
        
        $result = $variables->parseString('$451');
        
        $this->assertFalse($result->hasInvalid());
        $this->assertSame(0, $result->countVariables());
    }
    
    public function test_getByFullName() : void
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

    public function test_sourceCommand_none() : void
    {
        $vars = Mailcode::create()->createVariables();

        $collection = $vars->parseString('
            $FOO.BAR 
            $BAR.FOO
        ');

        $all = $collection->getAll();

        foreach ($all as $var)
        {
            $this->assertNull($var->getSourceCommand());
        }
    }

    public function test_sourceCommand_exists() : void
    {
        $string = '
            {showvar: $FOO.BAR} 
            {for: $ENTRY in: $LOOP}
                {if variable: $IF == "Text"}
                {end}
            {end}
        ';

        $collection = Mailcode::create()->parseString($string);
        $vars = $collection->getVariables()->getAll();

        $this->assertNotEmpty($vars);

        foreach ($vars as $var)
        {
            $this->assertNotNull($var->getSourceCommand(), 'Should have a command set: ' . $var->getFullName());
        }
    }

    public function test_sourceCommand_viaCommand() : void
    {
        $string = '
            {showvar: $FOO.BAR} 
            {for: $ENTRY in: $LOOP}
                {if variable: $IF == "Text"}
                {end}
            {end}
        ';

        $commands = Mailcode::create()->parseString($string)->getCommands();
        $collection = new Mailcode_Variables_Collection_Regular();

        foreach ($commands as $command)
        {
            $collection = $collection->mergeWith($command->getVariables());
        }

        $vars = $collection->getGroupedByName();

        $names = array();
        foreach($vars as $var)
        {
            $names[] = $var->getFullName();
        }

        $this->assertCount(4, $vars, sprintf('Gotten: [%s]', implode(', ', $names)));

        foreach ($vars as $var)
        {
            $this->assertNotNull($var->getSourceCommand());
        }
    }

    public function test_sourceCommand_specialMethods() : void
    {
        $cmd = Mailcode_Factory::misc()->for('RECORD', 'LOOP');

        $this->assertNotNull($cmd->getLoopVariable()->getSourceCommand(), 'Loop variable must have a source command');
        $this->assertNotNull($cmd->getSourceVariable()->getSourceCommand(), 'Source variable must have a source command');
    }

    public function test_createVariable() : void
    {
        $collection = Mailcode::create()->createVariables();

        $this->assertSame('$FOO', $collection->createVariable('FOO')->getFullName());
        $this->assertSame('$FOO.BAR', $collection->createVariable('FOO', 'BAR')->getFullName());
    }

    public function test_createVariableByName() : void
    {
        $collection = Mailcode::create()->createVariables();

        $this->assertSame('$FOO', $collection->createVariableByName('FOO')->getFullName());
        $this->assertSame('$FOO.BAR', $collection->createVariableByName('FOO.BAR')->getFullName());
    }
}
