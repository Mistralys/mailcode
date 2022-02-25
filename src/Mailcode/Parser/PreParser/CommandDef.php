<?php
/**
 * File containing the class {@see \Mailcode\Parser\PreParser\CommandDef}.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Parser\PreParser\CommandDef
 */

declare(strict_types=1);

namespace Mailcode\Parser\PreParser;

use Mailcode\Mailcode_Collection;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Parser\PreParser;
use function AppUtils\sb;

/**
 * Command definition for a protected content command
 * detected by the pre-parser: Used to store all necessary
 * information on the command.
 *
 * This class does the following things:
 *
 * - Extract and store the command's content
 * - Determine the ID of the content
 * - Generate the inline command for the main parser
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class CommandDef
{
    private string $name;
    private string $openingText;
    private string $params;
    private string $closingText;
    private int $contentID = 0;
    private int $length = 0;
    private int $startPos = 0;

    public function __construct(string $name, string $openingText, string $params, string $closingText)
    {
        $this->name = $name;
        $this->openingText = $openingText;
        $this->params = $params;
        $this->closingText = $closingText;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOpeningText() : string
    {
        return $this->openingText;
    }

    /**
     * @return string
     */
    public function getParams() : string
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getClosingText() : string
    {
        return $this->closingText;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setContentID(int $id) : self
    {
        $this->contentID = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getContentID() : int
    {
        return $this->contentID;
    }

    /**
     * @return int
     */
    public function getLength() : int
    {
        return $this->length;
    }

    /**
     * @return int
     */
    public function getStartPos() : int
    {
        return $this->startPos;
    }

    public function getReplacementCommand() : string
    {
        return sprintf(
            '{%s: %s %s}',
            $this->getName(),
            $this->getContentID(),
            $this->getParams()
        );
    }

    public function getContent() : string
    {
        return PreParser::getContent($this->getContentID());
    }

    public function extractContent(string $subject) : void
    {
        $this->startPos = (int)strpos($subject, $this->openingText);
        $posContent = $this->startPos + strlen($this->openingText);
        $posEnd = (int)strpos($subject, $this->closingText);
        $this->length = ($posEnd + strlen($this->closingText)) - $this->startPos;

        // Extract the content, and store it
        $content = substr($subject, $posContent, $posEnd - $posContent);
        $this->contentID = PreParser::storeContent($content);
    }

    /**
     * @return array{openingCommand:string,params:string,closingCommand:string,content:string,contentID:int,replacementCommand:string,startPosition:int,length:int}
     */
    public function toArray() : array
    {
        return array(
            'openingCommand' => $this->getOpeningText(),
            'params' => $this->getParams(),
            'closingCommand' => $this->getClosingText(),
            'content' => $this->getContent(),
            'contentID' => $this->getContentID(),
            'replacementCommand' => $this->getReplacementCommand(),
            'startPosition' => $this->getStartPos(),
            'length' => $this->getLength()
        );
    }

    public function validateContent(PreParser $parser, Mailcode_Collection $collection) : bool
    {
        $commands = $parser->getCommands();
        $content = $this->getContent();

        foreach($commands as $command)
        {
            $string = $command->getOpeningText();

            if(strpos($content, $string) !== false)
            {
                $this->addEscapeError($command, $collection);
                return false;
            }
        }

        return true;
    }

    private function addEscapeError(CommandDef $command, Mailcode_Collection $collection) : void
    {
        $collection->addErrorMessage(
            $command->getOpeningText(),
            (string)sb()
                ->t('Command nesting error:')
                ->t(
                    'The %1$s command contains a nested %2$s command.',
                    $this->getName(),
                    $command->getName()
                )
                ->t('Nesting commands that contain text within each other is allowed, but they must be escaped.')
                ->t('To solve this issue, please escape the nested command\'s brackets.')
                ->t('For example:')
                ->sf('\{%s\}', $command->getName()),
            Mailcode_Commands_CommonConstants::VALIDATION_UNESCAPED_NESTED_COMMAND
        );
    }
}
