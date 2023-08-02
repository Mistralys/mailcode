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

use Mailcode\Interfaces\Commands\Validation\DecryptInterface;
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Commands_Keywords;
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
        $token = $this->requireParams()
            ->getInfo()
            ->getTokenForKeyword(Mailcode_Commands_Keywords::TYPE_DECRYPT);

        if ($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            $this->decryptionKeyToken = $token;
        }

        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_DECRYPT);

        if ($this->decryptionKeyToken === null || !$val->isValid()) {
            return;
        }

        if (!$this->decryptionKeyToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            $this->validationResult->makeError(
                t('Invalid decryption key token:') . ' ' . t('Expected a string.'),
                DecryptInterface::VALIDATION_DECRYPT_CODE_WRONG_TYPE
            );
        }
    }

    /**
     * Gets the decryption key to use for the command. If none has
     * been specified in the original command, the default
     * decryption key is used as defined via {@see Mailcode_Commands_Command_ShowVariable::setDefaultDecryptionKey()}.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
     */
    public function getDecryptionKeyToken(): Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        if (!isset($this->decryptionKeyToken)) {
            $this->decryptionKeyToken = $this->createDecryptionKeyToken();
        }

        return $this->decryptionKeyToken;
    }

    /**
     * Creates the default decryption key token on demand.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
     */
    private function createDecryptionKeyToken(): Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        $default = Mailcode_Commands_Command_ShowVariable::getDefaultDecryptionKey();

        return new Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral(
            'showvar-decryption-key-token',
            $default,
            null,
            $this
        );
    }
}
