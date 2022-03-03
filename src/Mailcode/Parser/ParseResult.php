<?php
/**
 * File containing the class {@see \Mailcode\Parser\ParseResult}.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Parser\ParseResult
 */

declare(strict_types=1);

namespace Mailcode\Parser;

use Mailcode\Mailcode_Collection;

/**
 * Stores the result of the main parser's parsing
 * process, to access the command collection, and
 * optionally gain insight into what happened during
 * the process.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ParseResult
{
    private Mailcode_Collection $collection;
    private PreParser $preParser;

    public function __construct(Mailcode_Collection $collection, PreParser $preParser)
    {
        $this->collection = $collection;
        $this->preParser = $preParser;
    }

    /**
     * @return Mailcode_Collection
     */
    public function getCollection() : Mailcode_Collection
    {
        return $this->collection;
    }

    /**
     * @return PreParser
     */
    public function getPreParser() : PreParser
    {
        return $this->preParser;
    }
}
