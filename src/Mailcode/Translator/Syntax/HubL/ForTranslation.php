<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\HubL;

use Mailcode\Mailcode_Commands_Command_For;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Number;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Translator_Command_For;
use Mailcode\Mailcode_Variables_Variable;
use Mailcode\Translator\Syntax\BaseHubLCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_For} command to HubL.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class ForTranslation extends BaseHubLCommandTranslation implements Mailcode_Translator_Command_For
{
    public function translate(Mailcode_Commands_Command_For $command): string
    {
        $loopVar = $this->formatVariableName($command->getLoopVariable()->getFullName());
        $sourceVar = $this->formatVariableName($command->getSourceVariable()->getFullName());

        $sourceExpr = $sourceVar;

        if($command->isBreakAtEnabled())
        {
            $breakAt = $command->getBreakAt();

            if($breakAt instanceof Mailcode_Variables_Variable)
            {
                $sourceExpr .= sprintf('[:%s]', $this->formatVariableName($breakAt->getFullName()));
            }
            elseif(is_int($breakAt))
            {
                $sourceExpr .= sprintf('[:%d]', $breakAt);
            }
        }

        return sprintf('{%% for %s in %s %%}', $loopVar, $sourceExpr);
    }
}
