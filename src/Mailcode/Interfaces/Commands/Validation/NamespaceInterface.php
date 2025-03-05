<?php
/**
 * File containing the interface {@see \Mailcode\Interfaces\Commands\Validation\NamespaceInterface}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Interfaces\Commands\Validation\NamespaceInterface
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for commands that support decryption.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see NamespaceTrait
 */
interface NamespaceInterface extends Mailcode_Interfaces_Commands_Command
{
    public const PARAMETER_NAMESPACE_NAME = 'namespace';
    public const DEFAULT_NAMESPACE = 'global';
    public const VALIDATION_NAMESPACE_NAME = 'check_namespace';
    public const VALIDATION_NAMESPACE_WRONG_TYPE = 166401;

    public const ERROR_NO_NAMESPACE_TOKEN_PRESENT = 174001;


    public function isNamespacePresent(): bool;

    public function getNamespaceToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;

    /**
     * Like {@see self::getNamespaceToken()}, but with a guaranteed return
     * value. Throws an exception if the token is not present.
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
     */
    public function requireNamespaceToken() : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral;

    public function setNamespace(string $namespace = NamespaceInterface::DEFAULT_NAMESPACE): self;
}
