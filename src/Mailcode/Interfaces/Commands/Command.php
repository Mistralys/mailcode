<?php

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Skeleton for all commands.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Commands_Command
 */
interface Mailcode_Interfaces_Commands_Command
{
    public function hasParent(): bool;

    public function getParent(): ?Mailcode_Commands_Command;

    /**
     * @return string The ID of the command = the name of the command class file.
     */
    public function getID(): string;

    /**
     * Sets an optional comment that is not used anywhere, but
     * can be used by the application to track why a command is
     * used somewhere.
     *
     * @param string $comment
     * @return Mailcode_Commands_Command
     */
    public function setComment(string $comment): Mailcode_Commands_Command;

    /**
     * Retrieves the previously set comment, if any.
     *
     * @return string
     */
    public function getComment(): string;

    /**
     * Checks whether this is a dummy command, which is only
     * used to access information on the command type. It cannot
     * be used as an actual live command.
     *
     * @return bool
     */
    public function isDummy(): bool;

    /**
     * Retrieves a hash of the actual matched command string,
     * which is used in collections to detect duplicate commands.
     *
     * @return string
     * @throws Mailcode_Exception
     */
    public function getHash(): string;

    public function isValid(): bool;

    public function getValidationResult(): OperationResult;

    public function hasFreeformParameters(): bool;

    public function hasType(): bool;

    public function getType(): string;

    public function hasParameters(): bool;

    public function getMatchedText(): string;

    public function getHighlighted(): string;

    public function getParamsString(): string;

    public function getParams(): ?Mailcode_Parser_Statement;

    public function getName(): string;

    public function getLabel(): string;

    public function requiresParameters(): bool;

    public function supportsType(): bool;

    public function supportsURLEncoding(): bool;

    /**
     * Whether the command allows using logic keywords like "and:" or "or:"
     * in the command parameters.
     *
     * @return bool
     */
    public function supportsLogicKeywords(): bool;

    public function generatesContent(): bool;

    public function getDefaultType(): string;

    public function getCommandType(): string;

    public function getNormalized(): string;

    /**
     * Retrieves the names of all the command's supported types: the part
     * between the command name and the colon. Example: {command type: params}.
     *
     * @return string[]
     */
    public function getSupportedTypes(): array;

    /**
     * Retrieves all variable names used in the command.
     *
     * @return Mailcode_Variables_Collection_Regular
     */
    public function getVariables(): Mailcode_Variables_Collection_Regular;

    public function getLogicKeywords(): Mailcode_Commands_LogicKeywords;

    /**
     * Sets a parameter for the translation backend. The backend can use
     * these to allow command-specific configurations.
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setTranslationParam(string $name, $value);

    /**
     * Retrieves a previously set translation parameter.
     *
     * @param string $name
     * @return mixed
     */
    public function getTranslationParam(string $name);

    /**
     * @param bool $encoding
     * @return $this
     */
    public function setURLEncoding(bool $encoding = true);

    /**
     * Enables URL decoding for the command.
     *
     * @param bool $decode
     * @return $this
     * @throws Mailcode_Exception
     */
    public function setURLDecoding(bool $decode = true);

    public function isURLEncoded(): bool;

    public function isURLDecoded(): bool;
}
