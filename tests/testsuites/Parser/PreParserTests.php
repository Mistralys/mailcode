<?php

declare(strict_types=1);

namespace testsuites\Parser;

use Mailcode\Mailcode_Collection;
use Mailcode\Mailcode_Commands_CommonConstants;
use Mailcode\Mailcode_Parser_PreParser;
use MailcodeTestCase;

final class PreParserTests extends MailcodeTestCase
{
    // region: _Tests

    public function test_emptyContent() : void
    {
        $subject = <<<'EOT'
{code: "ApacheVelocity"}{code}
EOT;

        $expected = <<<'EOT'
{code: 1 "ApacheVelocity"}
EOT;

        $parser = $this->parseString($subject);

        $this->assertPreParserValid($parser);
        $this->assertSame(1, $parser->countCommands());
        $this->assertSame($expected, $parser->getString());
    }

    /**
     * The pre parser must replace content commands with
     * standalone versions of the command, which the main
     * parser will load as per usual.
     */
    public function test_detectCommands() : void
    {
        $subject = <<<'EOT'
A text before the command.
{code: "ApacheVelocity"}
Some content here.
Including a command: {showvar: $FOO.BAR}.
Or even some CSS: 
<style>
.className{
    color: #454545;
}
</style>
{code}
And after the command as well.
EOT;

        $expected = <<<'EOT'
A text before the command.
{code: 1 "ApacheVelocity"}
And after the command as well.
EOT;

        $parser = $this->parseString($subject);

        $this->assertPreParserValid($parser);
        $this->assertSame(1, $parser->countCommands());
        $this->assertSame($expected, $parser->getString());
    }

    /**
     * The content commands must support whitespace and
     * newlines in their syntax, just like regular commands.
     */
    public function test_freeSpacing() : void
    {
        $subject = <<<'EOT'
{
  code
  
  : 
  
  "ApacheVelocity"
  
  }
Some content here.
{

   code
   
   }
EOT;

        $expected = <<<'EOT'
{code: 1 "ApacheVelocity"}
EOT;

        $parser = $this->parseString($subject);

        $this->assertPreParserValid($parser);
        $this->assertSame(1, $parser->countCommands());
        $this->assertSame($expected, $parser->getString());
    }

    /**
     * The content command names must be case-insensitive,
     * just like the regular commands. This is important to
     * check, because they are parsed independently of the
     * main parser.
     */
    public function test_caseInsensitive() : void
    {
        $subject = <<<'EOT'
{CODE: "ApacheVelocity"}
Some content here.
{coDE}
EOT;

        $expected = <<<'EOT'
{code: 1 "ApacheVelocity"}
EOT;

        $parser = $this->parseString($subject);

        $this->assertPreParserValid($parser);
        $this->assertNotEmpty($parser->getCommands());
        $this->assertSame(1, $parser->countCommands());
        $this->assertSame($expected, $parser->getString());
    }

    /**
     * A mismatch between opening and closing content
     * commands must add an error to the collection.
     */
    public function test_missingClosingCommand() : void
    {
        $subject = <<<'EOT'
A text before the command.
{code: "ApacheVelocity"}
Some content here.
{end}
And after the command as well.
EOT;

        $parser = $this->parseString($subject);

        $this->assertFalse($parser->isValid());
        $this->assertCollectionHasErrorCode(
            Mailcode_Commands_CommonConstants::VALIDATION_MISSING_CONTENT_CLOSING_TAG,
            $parser->getCollection()
        );
    }

    /**
     * Getting the content must be possible once a string has
     * been pre-processed.
     */
    public function test_getContent() : void
    {
        $subject = '{code: "ApacheVelocity"}Some content here.{code}';
        $expected = 'Some content here.';

        $this->parseString($subject);

        $this->assertSame($expected, Mailcode_Parser_PreParser::getContent(1));
    }

    /**
     * Attempting to get the content for a content ID that
     * does not exist must throw an exception.
     */
    public function test_getContentException() : void
    {
        $this->expectExceptionCode(Mailcode_Parser_PreParser::ERROR_CONTENT_ID_NOT_FOUND);

        Mailcode_Parser_PreParser::getContent(999);
    }

    /**
     * Clearing a stored content by its ID must remove the
     * content, so that it cannot be retrieved again afterwards.
     */
    public function test_clearContent() : void
    {
        $subject = '{code: "ApacheVelocity"}Some content here.{code}';

        $this->parseString($subject);

        Mailcode_Parser_PreParser::getContent(1);
        Mailcode_Parser_PreParser::clearContent(1);

        $this->expectExceptionCode(Mailcode_Parser_PreParser::ERROR_CONTENT_ID_NOT_FOUND);

        Mailcode_Parser_PreParser::getContent(1);
    }

    /**
     * Ensure that nested content commands are extracted
     * correctly when they are escaped.
     */
    public function test_nestedContentCommand() : void
    {
        $subject = <<<'EOT'
{code: "ApacheVelocity"}
\{code: "ApacheVelocity"\}
Some text here.
\{code\}
{code}
EOT;
        $expected = <<<'EOT'

\{code: "ApacheVelocity"\}
Some text here.
\{code\}

EOT;

        $parser = $this->parseString($subject);

        $this->assertPreParserValid($parser);

        $this->assertSame($expected, Mailcode_Parser_PreParser::getContent(1));
    }

    /**
     * If the user tries to nest content commands within each
     * other, the validation must catch the error.
     */
    public function test_invalidNestedContentCommands() : void
    {
        $subject = <<<'EOT'
{code: "ApacheVelocity"}
    {code: "ApacheVelocity"}
        Some text here.
    {code}
{code}
EOT;

        $parser = $this->parseString($subject);

        $this->assertCollectionHasErrorCode(
            Mailcode_Commands_CommonConstants::VALIDATION_UNESCAPED_NESTED_COMMAND,
            $parser->getCollection()
        );
    }

    /**
     * Commands must be in the same order as they were
     * found in the document, and their stored content
     * must match as well.
     */
    public function test_severalCommands() : void
    {
        $subject = <<<'EOT'
{code: "ApacheVelocity"}Content #1{code}
{showurl: "TrackingID"}Content #2{showurl}
{code: "Mailcode"}Content #3{code}
EOT;

        $parser = $this->parseString($subject, true);

        $this->assertPreParserValid($parser);

        $this->assertSame(3, $parser->countCommands());
        $this->assertSame(3, Mailcode_Parser_PreParser::getContentCounter());

        $commands = $parser->getCommands();

        $counter = 0;
        foreach ($commands as $command)
        {
            $counter++;

            echo $command->getReplacementCommand().PHP_EOL;

            $this->assertSame($counter, $command->getContentID());
            $this->assertSame('Content #'.$counter, $command->getContent());
        }
    }

    public function test_storeContent() : void
    {
        $this->assertSame(0, Mailcode_Parser_PreParser::getContentCounter());

        for($i=1; $i <= 10; $i++)
        {
            $content = 'Content #'.$i;

            Mailcode_Parser_PreParser::storeContent($content);
            $this->assertSame($content, Mailcode_Parser_PreParser::getContent($i));

            Mailcode_Parser_PreParser::clearContent($i);
        }

        $this->assertSame(10, Mailcode_Parser_PreParser::getContentCounter());
    }

    // endregion

    // region: Support methods

    protected function setUp() : void
    {
        parent::setUp();

        Mailcode_Parser_PreParser::reset();
    }

    private function parseString(string $subject, bool $debug=false) : Mailcode_Parser_PreParser
    {
        $collection = new Mailcode_Collection();

        return (new Mailcode_Parser_PreParser($subject, $collection))
            ->enableDebug($debug)
            ->parse();
    }

    private function assertPreParserValid(Mailcode_Parser_PreParser $parser) : void
    {
        $this->assertCollectionValid($parser->getCollection());
        $this->assertTrue($parser->isValid());
    }

    // endregion
}
