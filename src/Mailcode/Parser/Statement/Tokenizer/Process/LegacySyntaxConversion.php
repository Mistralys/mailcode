<?php
/**
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Mailcode_Parser_Statement_Tokenizer_Process_LegacySyntaxConversion
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Converts older, deprecated mailcode command syntax to
 * the current format to stay backwards compatible.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Process_LegacySyntaxConversion extends Mailcode_Parser_Statement_Tokenizer_Process
{
    private array $conversions = array(
        'timezone:' => 'timezone=',
        'break-at:' => 'break-at=',
        'count:' => 'count='
    );

    protected function _process() : void
    {
        $this->tokenized = str_replace(array_keys($this->conversions), array_values($this->conversions), $this->tokenized);
    }
}
