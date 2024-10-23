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

use Mailcode\CurrencyInterface;
use Mailcode\Mailcode_Commands_Command_ShowPrice;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Factory_CommandSets_Set;
use Mailcode\RegionInterface;

/**
 * Factory class for the `shownumber` command.
 *
 * @package Mailcode
 * @subpackage Factory
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 */
class Price extends Mailcode_Factory_CommandSets_Set
{
    public function create(string $variableName, bool $absolute = false, bool $withCurrencyName = true,
                           string $currencyString = null, string $currencyVariable = null,
                           string $regionString = null, string $regionVariable = null): Mailcode_Commands_Command_ShowPrice
    {
        $variableName = $this->instantiator->filterVariableName($variableName);

        $paramsString = $this->compilePriceParams($absolute, $withCurrencyName,
            $currencyString, $currencyVariable,
            $regionString, $regionVariable);

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

    private function compilePriceParams(bool   $absolute = false, bool $withCurrencyName = true,
                                        string $currencyString = null, string $currencyVariable = null,
                                        string $regionString = null, string $regionVariable = null): string
    {
        $params = array();

        if ($absolute) {
            $params[] = ' ' . Mailcode_Commands_Keywords::TYPE_ABSOLUTE;
        }

        if ($currencyString) {
            $params[] = sprintf(
                ' ' . CurrencyInterface::CURRENCY_PARAMETER_NAME . '=%s',
                $this->quoteString($currencyString)
            );
        } else if ($currencyVariable) {
            $params[] = sprintf(
                ' ' . CurrencyInterface::CURRENCY_PARAMETER_NAME . '=%s',
                $currencyVariable
            );
        } else if ($withCurrencyName) {
            $params[] = ' ' . Mailcode_Commands_Keywords::TYPE_CURRENCY_NAME;
        }

        if ($regionString) {
            $params[] = sprintf(
                ' ' . RegionInterface::REGION_PARAMETER_NAME . '=%s',
                $this->quoteString($regionString)
            );
        } else if ($regionVariable) {
            $params[] = sprintf(
                ' ' . RegionInterface::REGION_PARAMETER_NAME . '=%s',
                $regionVariable
            );
        }

        if (!empty($params)) {
            return ' ' . implode(' ', $params);
        }

        return '';
    }
}
