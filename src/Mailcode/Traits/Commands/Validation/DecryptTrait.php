<?php
/**
 * File containing the trait {@see \Mailcode\Traits\Commands\Validation\DecryptTrait}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\DecryptTrait
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Decrypt\DecryptSettings;
use Mailcode\Interfaces\Commands\Validation\DecryptInterface;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use function Mailcode\t;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see DecryptInterface
 */
trait DecryptTrait
{
    private ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $decryptionKeyToken = null;

    protected function validateSyntax_check_decrypt(): void
    {
        $token = $this
            ->requireParams()
            ->getInfo()
            ->getTokenByParamName(DecryptInterface::PARAMETER_NAME);

        if($token === null) {
            return;
        }

        if (!$token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            $this->validationResult->makeError(
                t('Invalid decryption key token:') . ' ' . t('Expected a string.'),
                DecryptInterface::VALIDATION_DECRYPT_CODE_WRONG_TYPE
            );
            return;
        }

        $this->decryptionKeyToken = $token;
    }

    public function isDecryptionEnabled() : bool
    {
        return $this->getDecryptionKeyToken() !== null;
    }

    public function getDecryptionKeyName() : string
    {
        $key = $this->getDecryptionKeyToken();
        if($key === null) {
            return '';
        }

        $keyName = $key->getText();

        if(empty($keyName)) {
            $keyName = (string)DecryptSettings::getDefaultKeyName();
        }

        return $keyName;
    }

    public function enableDecryption(string $keyName=DecryptInterface::DEFAULT_DECRYPTION_KEY_NAME) : self
    {
        $this->decryptionKeyToken = $this
            ->requireParams()
            ->getInfo()
            ->addParamString(DecryptInterface::PARAMETER_NAME, $keyName);

        return $this;
    }

    public function disableDecryption() : self
    {
        if(isset($this->decryptionKeyToken)) {
            $this
                ->requireParams()
                ->getInfo()
                ->removeToken($this->decryptionKeyToken);
        }

        return $this;
    }

    /**
     * Gets the decryption key to use for the command. If none has
     * been specified in the original command, the default
     * decryption key is used as defined via {@see DecryptSettings::setDefaultKeyName()}.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral|NULL
     */
    public function getDecryptionKeyToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        return $this->decryptionKeyToken;
    }
}
