<?php
/**
 * File containing the interface {@see \Mailcode\Mailcode_Interfaces_Commands_ProtectedContent}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Interfaces_Commands_ProtectedContent
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for commands that capture their content
 * and protect it from being parsed by the parser,
 * like the `code` command.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_ProtectedContent
 */
interface Mailcode_Interfaces_Commands_ProtectedContent extends Mailcode_Commands_Command_Type_Standalone
{
    /**
     * @see Mailcode_Traits_Commands_ProtectedContent::validateSyntax_content_id()
     */
    public const VALIDATION_NAME_CONTENT_ID = 'content_id';

    /**
     * @see Mailcode_Traits_Commands_ProtectedContent::validateSyntax_nested_mailcode()
     */
    public const VALIDATION_NAME_NESTED_MAILCODE = 'nested_mailcode';

    public const VALIDATION_ERROR_CONTENT_ID_MISSING = 101501;

    public const ERROR_NO_CONTENT_ID_TOKEN = 101801;

    public function getContentID() : int;
    public function getContentIDToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Number;
    public function getContent() : string;
    public function getContentTrimmed() : string;

    /**
     * Retrieves a collection of nested Mailcode commands.
     *
     * NOTE: Returns an empty collection if the nested Mailcode
     * is not enabled (see the `code` command, which does not
     * modify or look at its content at all).
     *
     * @return Mailcode_Collection
     */
    public function getNestedMailcode() : Mailcode_Collection;

    /**
     * Whether the content of the command may contain
     * Mailcode, and should this be analyzed? If yes,
     * the Mailcode will be validated (separately),
     * and any variables will also be included in the
     * command's variables collection.
     *
     * @return bool
     */
    public function isMailcodeEnabled() : bool;
}
