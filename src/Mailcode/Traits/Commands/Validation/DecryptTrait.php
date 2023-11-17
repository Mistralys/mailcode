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
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use function AppUtils\sb;
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
        }

        $this->decryptionKeyToken = $token;

        $key = $this->getDecryptionKey();

        if($key !== DecryptInterface::DEFAULT_DECRYPTION_KEY) {
            return;
        }

        $this->validationResult->makeError(
            (string)sb()
            ->t('Cannot use the default decryption key:')
            ->t('No default key has been specified.')
            ->t('A default key must be set, or a key must be specified in the command.'),
            DecryptInterface::VALIDATION_DECRYPT_NO_DEFAULT_KEY
        );
    }

    public function isDecryptionEnabled() : bool
    {
        return $this->getDecryptionKeyToken() !== null;
    }

    public function getDecryptionKey() : string
    {
        $key = $this->getDecryptionKeyToken();
        if($key === null) {
            return '';
        }

        $key = $key->getText();

        if(empty($key) || $key === DecryptInterface::DEFAULT_DECRYPTION_KEY) {
            return DecryptSettings::getDefaultKey();
        }

        return $key;
    }

    public function enableDecryption(string $key=DecryptInterface::DEFAULT_DECRYPTION_KEY) : self
    {
        $this->decryptionKeyToken = $this
            ->requireParams()
            ->getInfo()
            ->addParamString(DecryptInterface::PARAMETER_NAME, $key);

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
     * decryption key is used as defined via {@see DecryptSettings::setDefaultKey()}.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral|NULL
     */
    public function getDecryptionKeyToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        return $this->decryptionKeyToken;
    }
}
