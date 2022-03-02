<?php
/**
 * File containing the class {@see \Mailcode\Factory\CommandSets\Set\Show\Encoded}.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Factory\CommandSets\Set\Show\Encoded
 */

declare(strict_types=1);

namespace Mailcode\Factory\CommandSets\Set\Show;

use Mailcode\Mailcode_Commands_Command_ShowEncoded;
use Mailcode\Mailcode_Factory_CommandSets_Set;
use Mailcode\Mailcode_Factory_Exception;

/**
 * Factory class for the `showencoded` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Encoded extends Mailcode_Factory_CommandSets_Set
{
    /**
     * @param string $subject
     * @param string[] $encodings
     * @return Mailcode_Commands_Command_ShowEncoded
     * @throws Mailcode_Factory_Exception
     */
    public function create(string $subject, array $encodings) : Mailcode_Commands_Command_ShowEncoded
    {
        $paramsString = $this->renderParams($subject, $encodings);

        $cmd = $this->commands->createCommand(
            'ShowEncoded',
            '',
            $paramsString,
            '{showencoded: '.$paramsString.'}'
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_ShowEncoded)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowEncoded', $cmd);
    }

    /**
     * @param string $subject
     * @param string[] $encodings
     * @return string
     */
    private function renderParams(string $subject, array $encodings) : string
    {
        $params = array();
        $params[] = $this->instantiator->quoteString($subject);

        foreach($encodings as $keyword)
        {
            $params[] = $this->instantiator->filterKeyword($keyword);
        }

        return implode(' ', $params);
    }
}
