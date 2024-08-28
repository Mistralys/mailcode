<?php
/**
 * File containing the class {@see \Mailcode\Factory\CommandSets\Set\Show\Price}.
 *
 * @package Mailcode
 * @subpackage Factory
 * @see \Mailcode\Factory\CommandSets\Set\Show\Price
 */

declare(strict_types=1);

namespace Mailcode\Factory\CommandSets\Set\Show;

use Mailcode\Mailcode_Commands_Command_ShowPrice;
use Mailcode\Mailcode_Factory_CommandSets_Set;

/**
 * Factory class for the `shownumber` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 */
class Price extends Mailcode_Factory_CommandSets_Set
{
    public function create(string $variableName, bool $absolute = false, bool $withCurrencyName = true): Mailcode_Commands_Command_ShowPrice
    {
        $variableName = $this->instantiator->filterVariableName($variableName);
        $paramsString = $this->compilePriceParams($absolute, $withCurrencyName);

        $cmd = $this->commands->createCommand(
            'ShowPrice',
            '',
            $variableName . $paramsString,
            sprintf(
                '{showprice: %s%s}',
                $variableName,
                $paramsString
            )
        );

        $this->instantiator->checkCommand($cmd);

        if ($cmd instanceof Mailcode_Commands_Command_ShowPrice) {
            return $cmd;
        }

        throw $this->instantiator->exceptionUnexpectedType('ShowPrice', $cmd);
    }

    private function compilePriceParams(bool $absolute = false, bool $withCurrencyName = true): string
    {
        $params = array();

        if ($absolute) {
            $params[] = ' absolute:';
        }

        if ($withCurrencyName) {
            $params[] = ' currency-name:';
        }

        if (!empty($params)) {
            return ' ' . implode(' ', $params);
        }

        return '';
    }
}
