<?php
/**
 * File containing the class {\Mailcode\Mailcode_Parser_StringPreProcessor}.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Mailcode_Parser_StringPreProcessor
 */

declare(strict_types=1);

namespace Mailcode;

use function AppUtils\sb;

/**
 * Does a first parsing pass of the subject string, to
 * handle all commands that can have content (like the
 * `code` command).
 *
 * These are handled differently to avoid their contents
 * from being parsed by the main parser. To ensure that
 * no changes are made to their contents, this is the very
 * first step in the parsing process.
 *
 * This makes it possible for these commands to contain
 * Mailcode or any other form of content, without interfering
 * in any way with the rest of the Mailcode in the document.
 *
 * In oder for the parsing of these commands to work, their
 * syntax is slightly different. The closing tag must use
 * the same name as the command, for example:
 *
 * <pre>
 * {code: "ApacheVelocity}
 *     (Some velocity code here)
 * {code}
 * </pre>
 *
 * The preprocessor extracts the information, and replaces
 * the command in the string with a regular command that the
 * main parser can parse as per normal.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_PreParser
{
    public const ERROR_CONTENT_ID_NOT_FOUND = 101401;

    private string $subject;
    private Mailcode_Collection $collection;
    private static int $contentCounter = 0;
    private bool $debug = false;

    /**
     * @var Mailcode_PreParser_CommandDef[]
     */
    private array $commands = array();

    /**
     * @var array<int,string>
     */
    private static array $contents = array();

    public function __construct(string $subject, Mailcode_Collection $collection)
    {
        $this->subject = $subject;
        $this->collection = $collection;
    }

    /**
     * @return Mailcode_Collection
     */
    public function getCollection() : Mailcode_Collection
    {
        return $this->collection;
    }

    public function isValid() : bool
    {
        return $this->collection->isValid();
    }

    public function enableDebug(bool $enable) : self
    {
        $this->debug = $enable;
        return $this;
    }

    /**
     * Resets the stored contents and content counter.
     *
     * NOTE: Used primarily for the unit tests.
     */
    public static function reset() : void
    {
        self::$contentCounter = 0;
        self::$contents = array();
    }

    /**
     * Fetches the content string stored under the specified ID.
     *
     * @param int $id
     * @return string
     *
     * @throws Mailcode_Parser_Exception
     * @see Mailcode_Parser_PreParser::ERROR_CONTENT_ID_NOT_FOUND
     */
    public static function getContent(int $id) : string
    {
        if(isset(self::$contents[$id]))
        {
            return self::$contents[$id];
        }

        throw new Mailcode_Parser_Exception(
            'Command content not found',
            sprintf(
                'The content stored under ID [%s] does not exist.',
                $id
            ),
            self::ERROR_CONTENT_ID_NOT_FOUND
        );
    }

    /**
     * Removes the content string stored under the specified ID, if it exists.
     *
     * @param int $id
     * @return void
     */
    public static function clearContent(int $id) : void
    {
        if(isset(self::$contents[$id]))
        {
            unset(self::$contents[$id]);
        }
    }

    public static function getContentCounter() : int
    {
        return self::$contentCounter;
    }

    /**
     * @return $this
     * @throws Mailcode_Exception
     */
    public function parse() : self
    {
        $this->subject = self::safeguardBrackets($this->subject);

        $names = $this->getContentCommandNames();

        foreach($names as $name)
        {
            $this->collapseContentCommand($name);
        }

        $this->subject = self::restoreBrackets($this->subject);

        return $this;
    }

    /**
     * @return string[]
     * @throws Mailcode_Exception
     */
    private function getContentCommandNames() : array
    {
        $commands = Mailcode::create()->getCommands()->getContentCommands();
        $result = array();

        foreach($commands as $command)
        {
            $result[] = $command->getName();
        }

        return $result;
    }

    /**
     * @var array<string,string>
     */
    private static array $escapeChars = array(
        '\{' => '__BRACKET_OPEN__',
        '\}' => '__BRACKET_CLOSE__'
    );

    public static function safeguardBrackets(string $subject) : string
    {
        return str_replace(
            array_keys(self::$escapeChars),
            array_values(self::$escapeChars),
            $subject
        );
    }

    public static function restoreBrackets(string $subject) : string
    {
        return str_replace(
            array_values(self::$escapeChars),
            array_keys(self::$escapeChars),
            $subject
        );
    }

    public static function unescapeBrackets(string $subject) : string
    {
        return str_replace(
            array(
                '\{',
                '\}'
            ),
            array(
                '{',
                '}'
            ),
            $subject
        );
    }

    public function countCommands() : int
    {
        return count($this->commands);
    }

    public function getString() : string
    {
        return $this->subject;
    }

    /**
     * @return Mailcode_PreParser_CommandDef[]
     */
    public function getCommands() : array
    {
        return $this->commands;
    }

    private function collapseContentCommand(string $name) : void
    {
        $this->commands = $this->detectCommands($name);

        foreach($this->commands as $commandDef)
        {
            $this->processCommand($commandDef);
        }

        $this->validateCommandContents();
    }

    private function validateCommandContents() : void
    {
        foreach($this->commands as $command)
        {
            if(!$command->validateContent($this, $this->collection))
            {
                return;
            }
        }
    }

    /**
     * @return Mailcode_PreParser_CommandDef[]
     */
    private function detectCommands(string $name) : array
    {
        $openingCommands = $this->detectOpeningCommands($name);

        if(empty($openingCommands))
        {
            return array();
        }

        $closingCommands = $this->detectClosingCommands($name);

        if(count($closingCommands) !== count($openingCommands))
        {
            $this->addClosingError($name);
            return array();
        }

        $result = array();

        foreach($openingCommands as $idx => $def)
        {
            $result[] = new Mailcode_PreParser_CommandDef(
                $name,
                $def['matchedText'],
                $def['parameters'],
                $closingCommands[$idx]
            );
        }

        return $result;
    }

    /**
     * @param string $name
     * @return array<int,array{matchedText:string,parameters:string}>
     */
    private function detectOpeningCommands(string $name) : array
    {
        preg_match_all('/{\s*'.$name.'\s*:([^}]+)}/sixU', $this->subject, $matches);

        $result = array();

        foreach ($matches[0] as $idx => $matchedText)
        {
            $result[(int)$idx] = array(
                'matchedText' => (string)$matchedText,
                'parameters' => trim((string)$matches[1][$idx])
            );
        }

        $this->debugOpeningCommands($matches);

        return $result;
    }

    /**
     * @param array<int,array<int,string>> $matches
     * @return void
     */
    private function debugOpeningCommands(array $matches) : void
    {
        if($this->debug === false)
        {
            return;
        }

        echo 'Opening command matches:'.PHP_EOL;
        print_r($matches);
    }

    /**
     * @param string $name
     * @return array<int,string>
     */
    private function detectClosingCommands(string $name) : array
    {
        preg_match_all('/{\s*'.$name.'\s*}/sixU', $this->subject, $matches);

        return $matches[0];
    }

    private function addClosingError(string $name) : void
    {
        $this->collection->addErrorMessage(
            '',
            (string)sb()
                ->t(
                    'Incorrectly closed %1$s command:',
                    sb()->code($name)
                )
                ->t(
                    'Please ensure that each of the commands has a matching %1$s closing tag.',
                    sb()->code('{'.$name.'}')
                ),
            Mailcode_Commands_CommonConstants::VALIDATION_MISSING_CONTENT_CLOSING_TAG
        );
    }

    private function processCommand(Mailcode_PreParser_CommandDef $commandDef) : void
    {
        $commandDef->extractContent($this->subject);

        $this->debugCommandDef($commandDef);

        // Replace the original command and content with the replacement command
        $this->subject = substr_replace(
            $this->subject,
            $commandDef->getReplacementCommand(),
            $commandDef->getStartPos(),
            $commandDef->getLength()
        );
    }

    private function debugCommandDef(Mailcode_PreParser_CommandDef $commandDef) : void
    {
        if($this->debug === true)
        {
            echo 'Command definition:'.PHP_EOL;
            print_r($commandDef->toArray());
        }
    }

    /**
     * Stores the content of the command. The command will retrieve
     * it using {@see Mailcode_Parser_PreParser::getContent()} when
     * it is created by the main parser.
     *
     * @param string $content
     * @return int
     */
    public static function storeContent(string $content) : int
    {
        self::$contentCounter++;

        self::$contents[self::$contentCounter] = self::restoreBrackets($content);

        return self::$contentCounter;
    }
}
