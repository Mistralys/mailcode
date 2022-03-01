<?php
/**
 * File containing the class {@see \Mailcode\Factory\CommandSets\Set\Show\URL}.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Factory\CommandSets\Set\Show\URL
 */

declare(strict_types=1);

namespace Mailcode\Factory\CommandSets\Set\Show;

use Mailcode\Mailcode_Commands_Command_ShowURL;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Factory_CommandSets_Set;
use Mailcode\Mailcode_Factory_Exception;
use Mailcode\Parser\PreParser;

/**
 * Factory class for the `showurl` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class URL extends Mailcode_Factory_CommandSets_Set
{
    /**
     * @param string $url The target URL. Can contain Mailcode.
     * @param string|null $trackingID If not set, an auto-generated tracking ID will be used.
     * @param array<string,string> $queryParams
     * @return Mailcode_Commands_Command_ShowURL
     * @throws Mailcode_Factory_Exception
     */
    public function create(string $url, ?string $trackingID=null, array $queryParams=array()) : Mailcode_Commands_Command_ShowURL
    {
        $contentID = PreParser::storeContent($url);

        $params = array();
        $params[] = (string)$contentID;

        if($trackingID !== null)
        {
            $params[] = sprintf('"%s"', $trackingID);
        }

        if(!empty($queryParams))
        {
            foreach($queryParams as $name => $value)
            {
                $params[] = sprintf(
                    '"%s=%s"',
                    $name,
                    $value
                );
            }
        }

        $paramsString = implode(' ', $params);

        $cmd = $this->commands->createCommand(
            'ShowURL',
            '',
            $paramsString,
            '{showurl: '.$paramsString.'}'
        );

        $this->instantiator->checkCommand($cmd);

        if($cmd instanceof Mailcode_Commands_Command_ShowURL)
        {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowURL', $cmd);
    }
}
