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

use AppUtils\ConvertHelper;
use Mailcode\Parser\Statement\Tokenizer\SpecialChars;

/**
 * Prepares a string to be parsed by the parser, by rendering
 * it compatible through a number of adjustments. This includes
 * stripping out `style` tags in HTML strings, since CSS syntax
 * conflicts with Mailcode.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_StringPreProcessor
{
    /**
     * @var string
     */
    private string $subject;

    public function __construct(string $subject)
    {
        $this->subject = $subject;
    }

    public function process() : string
    {
        $this->stripStyleTags();
        $this->escapeRegexBrackets();
        $this->encodeBrackets();

        return $this->subject;
    }

    /**
     * Special case for escaped brackets: to allow regular
     * parsing of these, we replace them with placeholders.
     */
    private function encodeBrackets() : void
    {
        $this->subject = str_replace('\{', SpecialChars::PLACEHOLDER_BRACKET_OPEN, $this->subject);
        $this->subject = str_replace('\}', SpecialChars::PLACEHOLDER_BRACKET_CLOSE, $this->subject);
    }

    /**
     * Detects regex size brackets, e.g. `{1,2}` or `{5}`,
     * and escapes the brackets as expected by Mailcode commands,
     * e.g. `\{5\}`.
     *
     * @return void
     */
    private function escapeRegexBrackets() : void
    {
        preg_match_all('/{[0-9]+,[0-9]+}|{[0-9]+}/xU', $this->subject, $result, PREG_PATTERN_ORDER);

        $matches = array_unique($result[0]);

        foreach ($matches as $match)
        {
            $this->subject = $this->escapeRegexBracketMatch($this->subject, $match);
        }
    }

    /**
     * Removes all <style> tags to avoid conflicts with CSS code.
     */
    private function stripStyleTags() : void
    {
        if(!ConvertHelper::isStringHTML($this->subject))
        {
            return;
        }

        $this->subject = (string)preg_replace(
            '%<style\b[^>]*>(.*?)</style>%six',
            '',
            $this->subject
        );
    }

    private function escapeRegexBracketMatch(string $subject, string $needle) : string
    {
        $replacement =  str_replace(
            array('{', '}'),
            array('\{', '\}'),
            $needle
        );

        return str_replace($needle, $replacement, $subject);
    }
}
