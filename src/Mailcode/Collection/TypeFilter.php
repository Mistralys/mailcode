<?php
/**
 * File containing the class {@see Mailcode_Collection_TypeFilter}.
 *
 * @package Mailcode
 * @subpackage Collection
 * @see Mailcode_Collection_TypeFilter
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Command filtering utility class, to fetch
 * commands by command type.
 *
 * @package Mailcode
 * @subpackage Collection
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Collection_TypeFilter
{
    /**
     * Retrieves only ShowXXX commands in the collection, if any.
     * Includes ShowVariable, ShowDate, ShowNumber, ShowSnippet.
     *
     * @param Mailcode_Commands_Command[] $commands
     * @return Mailcode_Commands_ShowBase[]
     */
    public static function getShowCommands(array $commands): array
    {
        $result = array();

        foreach($commands as $command)
        {
            if($command instanceof Mailcode_Commands_ShowBase)
            {
                $result[] = $command;
            }
        }

        return $result;
    }

    /**
     * Retrieves all commands that implement the ListVariables interface,
     * meaning that they use list variables.
     *
     * @param Mailcode_Commands_Command[] $commands
     * @return Mailcode_Interfaces_Commands_ListVariables[]
     *
     * @see Mailcode_Interfaces_Commands_ListVariables
     */
    public static function getListVariableCommands(array $commands) : array
    {
        $result = array();

        foreach($commands as $command)
        {
            if($command instanceof Mailcode_Interfaces_Commands_ListVariables)
            {
                $result[] = $command;
            }
        }

        return $result;
    }

    /**
     * Retrieves only show variable commands in the collection, if any.
     *
     * @param Mailcode_Commands_Command[] $commands
     * @return Mailcode_Commands_Command_ShowVariable[]
     */
    public static function getShowVariableCommands(array $commands): array
    {
        $result = array();

        foreach($commands as $command)
        {
            if($command instanceof Mailcode_Commands_Command_ShowVariable)
            {
                $result[] = $command;
            }
        }

        return $result;
    }

    /**
     * Retrieves only for commands in the collection, if any.
     *
     * @param Mailcode_Commands_Command[] $commands
     * @return Mailcode_Commands_Command_For[]
     */
    public static function getForCommands(array $commands): array
    {
        $result = array();

        foreach($commands as $command)
        {
            if($command instanceof Mailcode_Commands_Command_For)
            {
                $result[] = $command;
            }
        }

        return $result;
    }

    /**
     * Retrieves only show date commands in the collection, if any.
     *
     * @param Mailcode_Commands_Command[] $commands
     * @return Mailcode_Commands_Command_ShowDate[]
     */
    public static function getShowDateCommands(array $commands): array
    {
        $result = array();

        foreach($commands as $command)
        {
            if($command instanceof Mailcode_Commands_Command_ShowDate)
            {
                $result[] = $command;
            }
        }

        return $result;
    }

    /**
     * Retrieves only if commands in the collection, if any.
     *
     * @param Mailcode_Commands_Command[] $commands
     * @return Mailcode_Commands_Command_If[]
     */
    public static function getIfCommands(array $commands): array
    {
        $result = array();

        foreach($commands as $command)
        {
            if($command instanceof Mailcode_Commands_Command_If)
            {
                $result[] = $command;
            }
        }

        return $result;
    }
}
