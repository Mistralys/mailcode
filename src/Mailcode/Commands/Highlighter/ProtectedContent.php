<?php
/**
 * File containing the class {@see Mailcode_Commands_Highlighter_ProtectedContent}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Highlighter_ProtectedContent
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Specialized highlighter for protected content commands.
 * Ensures that the internal content ID parameter is removed
 * before highlighting, and appends the command content with
 * the closing command tag.
 *
 * The flow mirrors the normalizer:
 *
 * 1) Original: <code>{showurl: "trackme"}https://mistralys.eu{showurl}</code>
 * 2) Pre-parser injects a content ID: <code>{showurl: 1 "trackme"}</code>
 * 3) This highlighter removes the content ID from the highlighted output.
 * 4) Result: highlighted opening tag + content + highlighted closing tag.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_ProtectedContent::getHighlighted()
 */
class Mailcode_Commands_Highlighter_ProtectedContent extends Mailcode_Commands_Highlighter
{
    public function highlight() : string
    {
        $this->parts = array();

        $this->appendBracket('{');
        $this->appendCommand();
        $this->appendParams($this->command);
        $this->appendLogicKeywords();
        $this->appendBracket('}');

        /** @var Mailcode_Commands_Command&Mailcode_Interfaces_Commands_ProtectedContent $command */
        $command = $this->command;

        $this->parts[] = $command->getContent();

        $this->appendBracket('{');
        $this->parts[] = $this->renderTag(array('command-name'), $command->getName());
        $this->appendBracket('}');

        return implode('', $this->parts);
    }

    protected function appendParams(Mailcode_Commands_Command $command) : void
    {
        $params = $command->getParams();

        if($params === null)
        {
            return;
        }

        $tokens = $params->getInfo()->getTokens();

        if($command instanceof Mailcode_Interfaces_Commands_ProtectedContent)
        {
            $contentIDToken = $command->getContentIDToken();
            if($contentIDToken !== null)
            {
                $tokens = array_values(array_filter(
                    $tokens,
                    static fn(Mailcode_Parser_Statement_Tokenizer_Token $token) : bool => $token !== $contentIDToken
                ));
            }
        }

        if(empty($tokens))
        {
            return;
        }

        $this->parts[] = $this->renderTag(array('hyphen'), ':');
        $this->parts[] = '<wbr>';
        $this->parts[] = ' ';
        $this->parts[] = '<span class="mailcode-params">';

        $prev = null;
        foreach($tokens as $token)
        {
            $this->appendParamToken($token, $prev);
            $prev = $token;
        }

        $this->parts[] = '</span>';
    }
}
