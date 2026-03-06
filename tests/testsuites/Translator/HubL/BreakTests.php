<?php

declare(strict_types=1);

namespace MailcodeTests\Translator\HubL;

use Mailcode\Mailcode_Factory;
use MailcodeTestClasses\HubLTestCase;

/**
 * Regression guard: the Break command must translate to the canonical
 * HubL not-supported comment, since HubL has no equivalent break construct.
 *
 * @see \Mailcode\Translator\Syntax\HubL\BreakTranslation
 */
final class BreakTests extends HubLTestCase
{
    public function test_basic(): void
    {
        $this->runCommands(
            array(
                array(
                    'label' => 'Break, basic',
                    'mailcode' => Mailcode_Factory::misc()->break(),
                    'expected' => self::buildNotSupportedComment('break'),
                )
            )
        );
    }
}
