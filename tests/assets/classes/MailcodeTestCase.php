<?php

use Mailcode\Mailcode;
use PHPUnit\Framework\TestCase;

abstract class MailcodeTestCase extends TestCase
{
    protected function runCollectionTests(array $tests) : void
    {
        foreach($tests as $test)
        {
            $collection = Mailcode::create()->parseString($test['string']);
            
            $label = $test['label'].PHP_EOL;
            
            if(!$collection->isValid())
            {
                $label .= "Messages:".PHP_EOL;
                
                foreach($collection->getErrors() as $error)
                {
                    $label .= $error->getMessage().PHP_EOL;
                }
            }
            
            $label .= 'Command:'.$test['string'];
            
            $this->assertSame($test['valid'], $collection->isValid(), $label);
            
            if(!$test['valid'])
            {
                $error = $collection->getFirstError();
                $this->assertSame($test['code'], $error->getCode(), $label);
            }
        }
    }
}
