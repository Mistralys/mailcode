<?php


declare(strict_types=1);
namespace MailcodeTests\Isolation;


use MailcodeTestCase;
final class IsolatedTests extends MailcodeTestCase
{
    public function test_isolated() : void
    {
        $this->addToAssertionCount(1);
    }
    
    // -------------------------------------------------------
    // USED IN CASE IT IS NECESSARY TO RUN INDIVIDUAL TESTS.
    // -------------------------------------------------------
}
