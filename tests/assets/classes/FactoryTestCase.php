<?php

declare(strict_types=1);

namespace MailcodeTestClasses;

use Mailcode\Mailcode_Exception;
use MailcodeTestCase;

abstract class FactoryTestCase extends MailcodeTestCase
{
    /**
     * @return class-string
     */
    abstract protected function getExpectedClass(): string;

    /**
     * @param string $label
     * @param callable $callback
     * @return void
     */
    protected function runCommand(string $label, callable $callback): void
    {
        try {
            $cmd = $callback();
        } catch (Mailcode_Exception $e) {
            $this->fail(sprintf(
                '%s: #%s (%s)',
                $e->getMessage(),
                $e->getCode(),
                $e->getDetails()
            ));
        }

        $this->assertInstanceOf($this->getExpectedClass(), $cmd, $label);
    }
}
