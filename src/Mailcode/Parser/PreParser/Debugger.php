<?php
/**
 * File containing the class {@see \Mailcode\Parser\PreParser\Debugger}.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Parser\PreParser\Debugger
 */

declare(strict_types=1);

namespace Mailcode\Parser\PreParser;

use Mailcode\Mailcode;
use Mailcode\Parser\PreParser\CommandDef;

/**
 * Specialized debugger for the pre-parser, used to
 * prepare relevant information for debug logging.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Debugger
{
    /**
     * @param array<int,array<int,string>> $matches
     * @return void
     */
    public function debugOpeningCommands(array $matches) : void
    {
        if(!Mailcode::isDebugEnabled())
        {
            return;
        }

        Mailcode::debug('Opening command matches:');

        if(empty($matches))
        {
            Mailcode::debug('...None found.');
            return;
        }

        foreach($matches[0] as $idx => $matchedText)
        {
            $number = $idx+1;

            Mailcode::debug(sprintf('...#%02d matched: [%s]', $number, $matchedText));
            Mailcode::debug(sprintf('...#%02d name...: [%s]', $number, $matches[1][$idx]));
            Mailcode::debug(sprintf('...#%02d params.: [%s]', $number, $matches[2][$idx]));
        }
    }

    /**
     * @param array<int,array{name:string,matchedText:string}> $commands
     * @return void
     */
    public function debugClosingCommands(array $commands) : void
    {
        if(!Mailcode::isDebugEnabled())
        {
            return;
        }

        Mailcode::debug('Closing command matches:', $commands);

        if(empty($commands))
        {
            Mailcode::debug('...None found.');
            return;
        }

        foreach($commands as $idx => $command)
        {
            $number = $idx+1;

            Mailcode::debug(sprintf('...#%02d matched: [%s]', $number, $command['matchedText']));
            Mailcode::debug(sprintf('...#%02d name...: [%s]', $number, $command['name']));
        }
    }

    public function debugCommandDef(CommandDef $commandDef) : void
    {
        if(!Mailcode::isDebugEnabled())
        {
            return;
        }

        Mailcode::debug('Command definition:', $commandDef->toArray());

        if(defined('TESTS_ROOT'))
        {
            print_r($commandDef->toArray());
        }
    }
}
