<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Tokenizer_Process_Variables extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        $vars = Mailcode::create()->findVariables($this->tokenized, $this->tokenizer->getSourceCommand())->getGroupedByHash();

        foreach($vars as $var)
        {
            $this->registerToken('Variable', $var->getMatchedText(), $var);
        }
    }
}