<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\RegionInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\RegionInterface
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see RegionTrait
 */
interface RegionInterface extends Mailcode_Interfaces_Commands_Command
{
    public const REGION_PARAMETER_NAME = 'region';

    public const VALIDATION_REGION_WRONG_TYPE = 166101;

    public function isRegionPresent(): bool;

    public function getRegionToken(): ?Mailcode_Parser_Statement_Tokenizer_Token;
}
