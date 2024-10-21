<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_ShowPrice;
use Mailcode\Mailcode_Translator_Command_ShowPrice;
use Mailcode\Translator\Syntax\ApacheVelocity;

/**
 * Translates the {@see Mailcode_Commands_Command_ShowPrice} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 */
class ShowPriceTranslation extends ApacheVelocity implements Mailcode_Translator_Command_ShowPrice
{
    public function translate(Mailcode_Commands_Command_ShowPrice $command): string
    {
        $localCurrency = $command->getLocalCurrency();

        if ($command->isRegionPresent()) {
            $regionToken = $command->getRegionToken();

            $localCurrency = $localCurrency->withRegion($regionToken);
        }

        if ($command->isCurrencyPresent()) {
            $currencyToken = $command->getCurrencyToken();

            $localCurrency = $localCurrency->withCurrency($currencyToken);
        }

        $statement = $this->renderPrice(
            $command->getVariableName(),
            $localCurrency,
            $command->isAbsolute(),
            $command->isCurrencyNameEnabled()
        );

        return $this->renderVariableEncodings($command, $statement);
    }
}
