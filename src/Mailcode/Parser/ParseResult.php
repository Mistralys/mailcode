<?php

declare(strict_types=1);

namespace Mailcode\Parser;

use Mailcode\Mailcode_Collection;

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
