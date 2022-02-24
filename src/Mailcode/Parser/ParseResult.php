<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_ParseResult
{
    private Mailcode_Collection $collection;
    private Mailcode_Parser_PreParser $preParser;

    public function __construct(Mailcode_Collection $collection, Mailcode_Parser_PreParser $preParser)
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
     * @return Mailcode_Parser_PreParser
     */
    public function getPreParser() : Mailcode_Parser_PreParser
    {
        return $this->preParser;
    }
}
