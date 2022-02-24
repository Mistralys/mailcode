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
    public const LITERAL_BRACKET_RIGHT_REPLACEMENT = '﴿';
    public const LITERAL_BRACKET_LEFT_REPLACEMENT = '﴾';
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

        return $this->subject;
    }

    private function escapeRegexBrackets() : void
    {
        preg_match_all('/{[0-9]+,[0-9]+}|{[0-9]+}/xU', $this->subject, $result, PREG_PATTERN_ORDER);

        $matches = array_unique($result[0]);

        foreach ($matches as $match)
        {
            $this->subject = $this->replaceBrackets($this->subject, $match);
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

    private function replaceBrackets(string $subject, string $needle) : string
    {
        $replacement =  str_replace(
            array('{', '}'),
            array(
                self::LITERAL_BRACKET_LEFT_REPLACEMENT,
                self::LITERAL_BRACKET_RIGHT_REPLACEMENT
            ),
            $needle
        );

        return str_replace($needle, $replacement, $subject);
    }
}
