<?php

use Mailcode\Mailcode_Exception;

abstract class FactoryTestCase extends MailcodeTestCase
{
    abstract protected function getExpectedClass() : string;
    
    protected function runCommand(string $label, $callback) : void
    {
        try
        {
            $cmd = call_user_func($callback);
        }
        catch(Mailcode_Exception $e)
        {
            $this->fail(sprintf(
                '%s: #%s (%s)',
                $e->getMessage(),
                $e->getCode(),
                $e->getDetails()
            ));
        }
        
        $this->assertInstanceOf($this->getExpectedClass(), $cmd);
    }
}
