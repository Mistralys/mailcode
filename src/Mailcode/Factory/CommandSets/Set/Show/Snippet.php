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
use Mailcode\NamespaceInterface;

/**
 * Factory class for the `showsnippet` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Snippet extends Mailcode_Factory_CommandSets_Set
{
    public function create(string $snippetName, string $namespace = null): Mailcode_Commands_Command_ShowSnippet
    {
        $snippetName = $this->instantiator->filterVariableName($snippetName);
        $paramsString = $this->compileParams($namespace);

        $cmd = $this->commands->createCommand(
            'ShowSnippet',
            '',
            $snippetName . $paramsString,
            sprintf('{showsnippet: %s%s',
                $snippetName,
                $paramsString)
        );

        $this->instantiator->checkCommand($cmd);

        if ($cmd instanceof Mailcode_Commands_Command_ShowSnippet) {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowSnippet', $cmd);
    }

    private function compileParams(string $namespace = null): string
    {
        $params = array();

        if ($namespace) {
            $params[] = sprintf(
                ' ' . NamespaceInterface::PARAMETER_NAMESPACE_NAME . '=%s',
                $this->quoteString($namespace)
            );
        }

        if (!empty($params)) {
            return ' ' . implode(' ', $params);
        }

        return '';
    }
}
