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
    public const VALIDATION_NAME_CONTENT_ID = 'content_id';

    public const VALIDATION_ERROR_CONTENT_ID_MISSING = 101501;

    public const ERROR_NO_CONTENT_ID_TOKEN = 101801;

    public function getContentID() : int;
    public function getContentIDToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Number;
    public function getContent() : string;
    public function getContentTrimmed() : string;
}
