<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Parser\Statement\Tokenizer\SpecialChars;

/**
 * Token representing a quoted string literal.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Statement_Tokenizer_Token_StringLiteral extends Mailcode_Parser_Statement_Tokenizer_Token implements Mailcode_Parser_Statement_Tokenizer_ValueInterface
{
    private string $text;

    protected function init() : void
    {
        $this->setText($this->stripQuotes($this->matchedText));
    }

    private function stripQuotes(string $text) : string
    {
        return trim($text, '"');
    }

    /**
    * Retrieves the text with the surrounding quotes,
    * and special characters escaped for Mailcode.
    *
    * @return string
    */
    public function getNormalized() : string
    {
        return '"'.SpecialChars::escape($this->text).'"';
    }

    public function hasSpacing(): bool
    {
        return true;
    }
    
   /**
    * Retrieves the text with the surrounding quotes.
    * @return string
    */
    public function getValue() : string
    {
        return $this->getNormalized();
    }
    
   /**
    * Retrieves the text without the surrounding quotes,
    * and special Mailcode characters not escaped.
    *
    * @return string
    */
    public function getText() : string
    {
        return SpecialChars::decode($this->text);
    }

    public function setText(string $text) : self
    {
        $this->text = SpecialChars::escape($text);
        $this->matchedText = '"'.$this->text.'"';

        return $this;
    }
}
