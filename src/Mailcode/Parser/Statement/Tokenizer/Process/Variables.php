<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Tokenizer_Process_Variables extends Mailcode_Parser_Statement_Tokenizer_Process
{
    protected function _process() : void
    {
        $vars = Mailcode::create()->findVariables($this->tokenized, $this->tokenizer->getSourceCommand())->getGroupedByHash();

        $names = array();
        foreach($vars as $var)
        {
            $names[$var->getMatchedText()] = $var;
        }

        // Sort the variable names by longest to shortest,
        // to ensure that $FOO does not get replaced before
        // $FOO.BAR, which would break the second one.
        uksort($names, static function(string $a, string $b) : int {
            return strlen($b) - strlen($a);
        });

        foreach($names as $name => $var)
        {
            $this->registerToken('Variable', $name, $var);
        }
    }
}
