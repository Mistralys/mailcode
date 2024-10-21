<?php
/**
 * File containing the trait {@see \Mailcode\Traits\Commands\Validation\RegionTrait}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\RegionTrait
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see RegionInterface
 */
trait RegionTrait
{
    private ?Mailcode_Parser_Statement_Tokenizer_Token $region = null;

    /**
     * @throws Mailcode_Exception
     */
    protected function validateSyntax_check_region(): void
    {
        $token = $this
            ->requireParams()
            ->getInfo()
            ->getTokenByParamName(RegionInterface::REGION_PARAMETER_NAME);

        if ($token === null) {
            return;
        }

        if (!$token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable &&
            !$token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            $this->validationResult->makeError(
                t('Invalid region token:') . ' ' . t('Expected a variable or a string.'),
                RegionInterface::VALIDATION_REGION_WRONG_TYPE
            );
            return;
        }

        $this->region = $token;
    }

    public function isRegionPresent(): bool
    {
        return $this->getRegionToken() !== null;
    }

    public function getRegionToken(): ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        return $this->region;
    }
}
