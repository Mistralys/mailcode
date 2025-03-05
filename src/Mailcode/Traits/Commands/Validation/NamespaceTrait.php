<?php
/**
 * File containing the trait {@see \Mailcode\Traits\Commands\Validation\NamespaceTrait}.
 *
 * @package Mailcode
 * @subpackage Validation
 * @see \Mailcode\Traits\Commands\Validation\NamespaceTrait
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;
use Mailcode\Commands\CommandException;

/**
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see NamespaceInterface
 */
trait NamespaceTrait
{
    private ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral $namespaceToken = null;

    protected function validateSyntax_check_namespace(): void
    {
        $token = $this
            ->requireParams()
            ->getInfo()
            ->getTokenByParamName(NamespaceInterface::PARAMETER_NAMESPACE_NAME);

        if ($token === null) {
            return;
        }

        if (!$token instanceof Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral) {
            $this->validationResult->makeError(
                t('Invalid namespace token:') . ' ' . t('Expected a string.'),
                NamespaceInterface::VALIDATION_NAMESPACE_WRONG_TYPE
            );
            return;
        }

        $this->namespaceToken = $token;
    }

    public function isNamespacePresent(): bool
    {
        return $this->getNamespaceToken() !== null;
    }

    public function getNamespaceToken(): ?Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        return $this->namespaceToken;
    }

    public function requireNamespaceToken() : Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
    {
        $token = $this->getNamespaceToken();

        if($token !== null) {
            return $token;
        }

        throw new CommandException(
            'The command does not have a namespace token.',
            sprintf(
                'A namespace token was required, but not present. '.PHP_EOL.
                'Use [%s] to check for its presence before calling this method.',
                ConvertHelper::callback2string(array($this, 'isNamespacePresent'))
            ),
            NamespaceInterface::ERROR_NO_NAMESPACE_TOKEN_PRESENT
        );
    }

    public function setNamespace(string $namespace = NamespaceInterface::DEFAULT_NAMESPACE): self
    {
        $this->namespaceToken = $this
            ->requireParams()
            ->getInfo()
            ->addParamString(NamespaceInterface::PARAMETER_NAMESPACE_NAME, $namespace);

        return $this;
    }
}
