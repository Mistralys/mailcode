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
    private bool $parsed = false;

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
        if($this->parsed)
        {
            return $this;
        }

        $this->parsed = true;
        $this->subject = self::safeguardBrackets($this->subject);

        $this->detectCommands();

        foreach($this->commands as $commandDef)
        {
            $this->processCommand($commandDef);
        }

        $this->validateCommandContents();

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
        $this->parse();

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
        $this->parse();

        return $this->commands;
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

    private function detectCommands() : void
    {
        $openingCommands = $this->detectOpeningCommands();

        if(empty($openingCommands))
        {
            return;
        }

        $closingCommands = $this->detectClosingCommands();

        if(!$this->validateCommandsList($openingCommands, $closingCommands))
        {
            return;
        }

        foreach($openingCommands as $idx => $def)
        {
            $this->commands[] = new Mailcode_PreParser_CommandDef(
                $def['name'],
                $def['matchedText'],
                $def['parameters'],
                $closingCommands[$idx]['matchedText']
            );
        }
    }

    /**
     * @param array<int,array{matchedText:string,name:string,parameters:string}> $openingCommands
     * @param array<int,array{name:string,matchedText:string}> $closingCommands
     * @return bool
     */
    private function validateCommandsList(array $openingCommands, array $closingCommands) : bool
    {
        $opening = count($openingCommands);
        $closing = count($closingCommands);
        $max = $opening;
        if($closing > $max) {
            $max = $closing;
        }

        for($i=0; $i < $max; $i++)
        {
            if(!isset($openingCommands[$i]))
            {
                // command closed that was never opened
                return false;
            }

            if(!isset($closingCommands[$i]))
            {
                // command opened that was never closed
                return false;
            }

            if($openingCommands[$i]['name'] !== $closingCommands[$i]['name'])
            {
                // command closed does not match opening
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int,array{matchedText:string,name:string,parameters:string}>
     */
    private function detectOpeningCommands() : array
    {
        $regex = sprintf(
            '/{\s*(%s)\s*:([^}]+)}/sixU',
            implode('|', $this->getContentCommandNames())
        );

        preg_match_all($regex, $this->subject, $matches);

        $result = array();

        foreach ($matches[0] as $idx => $matchedText)
        {
            $result[(int)$idx] = array(
                'matchedText' => (string)$matchedText,
                'name' => trim((string)$matches[1][$idx]),
                'parameters' => trim((string)$matches[2][$idx])
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
     * @return array<int,array{name:string,matchedText:string}>
     */
    private function detectClosingCommands() : array
    {
        $regex = sprintf(
            '/{\s*(%s)\s*}/sixU',
            implode('|', $this->getContentCommandNames())
        );

        preg_match_all($regex, $this->subject, $matches);

        $result = array();

        foreach($matches[0] as $idx => $matchedText)
        {
            $result[] = array(
                'name' => (string)$matches[1][$idx],
                'matchedText' => $matchedText
            );
        }

        return $result;
    }

    private function addClosingError(string $name) : void
    {
        $this->collection->addErrorMessage(
            '',
            (string)sb()
                ->t('Incorrectly closed content command:')
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
