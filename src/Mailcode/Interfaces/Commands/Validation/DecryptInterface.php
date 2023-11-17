<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\DecryptInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\DecryptInterface
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Decrypt\DecryptSettings;
use Mailcode\Mailcode_Commands_Command_ShowVariable;
use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;
use Mailcode\Traits\Commands\Validation\DecryptTrait;

/**
 * Interface for commands that support decryption.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see DecryptTrait
 */
interface DecryptInterface extends Mailcode_Interfaces_Commands_Command
{
    public const PARAMETER_NAME = 'decrypt';
    public const DEFAULT_DECRYPTION_KEY_NAME = 'default';
    public const VALIDATION_DECRYPT_NAME = 'check_decrypt';
    public const VALIDATION_DECRYPT_CODE_WRONG_TYPE = 142601;
    public const VALIDATION_DECRYPT_NO_DEFAULT_KEY = 142602;

    /**
     * Retrieves the token containing the decryption key.
     * Will use the default key if none has been specified
     * in the command.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral|NULL
     * @see DecryptSettings::setDefaultKeyName()
     */
    public function getDecryptionKeyToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;

    public function enableDecryption(string $keyName=DecryptInterface::DEFAULT_DECRYPTION_KEY_NAME) : self;

    public function disableDecryption() : self;
    public function isDecryptionEnabled() : bool;
}
