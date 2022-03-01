<?php
/**
 * File containing the class {@see \Mailcode\Factory\CommandSets\Set\Show\Snippet}.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Factory\CommandSets\Set\Show\Snippet
 */

declare(strict_types=1);

namespace Mailcode\Factory\CommandSets\Set\Show;

use Mailcode\Mailcode_Commands_Command_ShowSnippet;
use Mailcode\Mailcode_Factory_CommandSets_Set;

/**
 * Factory class for the `showsnippet` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Snippet extends Mailcode_Factory_CommandSets_Set
{
    public function create(string $snippetName) : Mailcode_Commands_Command_ShowSnippet
    {
        $snippetName = $this->instantiator->filterVariableName($snippetName);

        $cmd = $this->commands->createCommand(
            'ShowSnippet',
            '',
            $snippetName,
            '{showsnippet:'.$snippetName.'}'
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_ShowSnippet)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowSnippet', $cmd);
    }
}
