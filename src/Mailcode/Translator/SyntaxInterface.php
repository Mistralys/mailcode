<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator;

use Mailcode\Mailcode_Commands_Command;
use Mailcode\Mailcode_Parser_Safeguard;
use Mailcode\Mailcode_Translator_Exception;

/**
 * Interface for Mailcode translator syntaxes.
 *
 * @package Mailcode
 * @subpackage Translator
 */
interface SyntaxInterface
{
    /**
     * Retrieves the syntax's type ID, e.g. {@see ApacheVelocitySyntax::SYNTAX_NAME}.
     * @return string
     */
    public function getTypeID() : string;
    public function getLabel() : string;

    /**
     * Translates a single command to the target syntax.
     *
     * @param Mailcode_Commands_Command $command
     * @throws Mailcode_Translator_Exception
     * @return string
     */
    public function translateCommand(Mailcode_Commands_Command $command) : string;

    /**
     * @param Mailcode_Commands_Command $command
     * @return BaseCommandTranslation
     */
    public function createTranslator(Mailcode_Commands_Command $command) : BaseCommandTranslation;

    /**
     * Translates all safeguarded commands in the subject string to the
     * target syntax in one go.
     *
     * @param Mailcode_Parser_Safeguard $safeguard
     * @return string
     */
    public function translateSafeguard(Mailcode_Parser_Safeguard $safeguard) : string;
}
