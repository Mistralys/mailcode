<?php

declare(strict_types=1);

namespace Mailcode;

trait Mailcode_Traits_Commands_ListVariables
{
    public function getListVariables() : Mailcode_Variables_Collection_Regular
    {
        $collection = new Mailcode_Variables_Collection_Regular();
        $this->_collectListVariables($collection);
        return $collection;
    }

    abstract protected function _collectListVariables(Mailcode_Variables_Collection_Regular $collection) : void;

    abstract public function getVariables() : Mailcode_Variables_Collection_Regular;
}
