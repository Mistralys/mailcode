<?php
/**
 * File containing the class {@see Mailcode_Commands_Normalizer_ProtectedContent}.
 *
 * @package Mailcode
 * @subpackage Normalizer
 * @see Mailcode_Commands_Normalizer_ProtectedContent
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Specialized normalizer for protected content commands.
 * Ensures that the content ID parameter is removed before
 * normalizing, and converts the inline command to the
 * original command including its content.
 *
 * The flow looks like this:
 *
 * 1) Original command in the source text, e.g. <code>{code: "Mailcode"}Some text here{code}</code>
 * 2) The pre-parser converts this to an inline command, e.g. <code>{code: 1 "Mailcode"}</code>
 * 3) The parser instantiates the command from the inline version.
 * 4) The normalizer reverts it back to the original command string.
 *
 * @package Mailcode
 * @subpackage Normalizer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Traits_Commands_ProtectedContent::getNormalized()
 *
 * @property Mailcode_Interfaces_Commands_ProtectedContent $command
 */
class Mailcode_Commands_Normalizer_ProtectedContent extends Mailcode_Commands_Normalizer
{
    public const ERROR_CONTENT_ID_TOKEN_MISSING = 101601;
    public const ERROR_INVALID_COMMAND_INSTANCE = 101602;

    public function __construct(Mailcode_Commands_Command $command)
    {
        if(!$command instanceof Mailcode_Interfaces_Commands_ProtectedContent)
        {
            throw new Mailcode_Exception(
                'Invalid command',
                sprintf(
                    'The specified command is not a protected content command: [%s].',
                    get_class($command)
                ),
                self::ERROR_INVALID_COMMAND_INSTANCE
            );
        }

        parent::__construct($command);
    }

    public function normalize() : string
    {
        return
            parent::normalize().
            $this->command->getContent().
            '{'.$this->command->getName().'}';
    }

    protected function getParamsStatement(Mailcode_Commands_Command $command) : ?Mailcode_Parser_Statement
    {
        $params = $command->getParams();

        if($params === null)
        {
            return null;
        }

        return $this->removeContentID($params);
    }

    /**
     * Removes the content ID parameter from the command's
     * parameters string, since this must not be included
     * in the command's normalized version.
     *
     * @param Mailcode_Parser_Statement $params
     * @return Mailcode_Parser_Statement
     * @throws Mailcode_Exception
     */
    private function removeContentID(Mailcode_Parser_Statement $params) : Mailcode_Parser_Statement
    {
        $copy = $params->copy();
        $info = $copy->getInfo();

        $contentID = $info->getTokenByIndex(0);

        if($contentID instanceof Mailcode_Parser_Statement_Tokenizer_Token_Number)
        {
            $info->removeToken($contentID);
            return $copy;
        }

        throw new Mailcode_Exception(
            'Invalid content ID token',
            sprintf(
                'The command [%s] is missing its content ID token in the statement string: [%s].',
                $this->command->getName(),
                $copy->getStatementString()
            ),
            self::ERROR_CONTENT_ID_TOKEN_MISSING
        );
    }
}
