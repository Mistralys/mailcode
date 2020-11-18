<?php

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ConvertHelper;

trait Mailcode_Traits_Commands_ProtectedContent
{
    protected static $protectedCounter = 0;
    protected $protectedContent = '';
    protected $protectedPlaceholder = '';

    public function getContent() : string
    {
        return $this->protectedContent;
    }

    public function getContentPlaceholder() : string
    {
        return $this->protectedPlaceholder;
    }

    public function protectContent(string $string, Mailcode_Parser_Safeguard_Placeholder $open, Mailcode_Parser_Safeguard_Placeholder $end) : string
    {
        if(!$end->getCommand() instanceof Mailcode_Commands_Command_End)
        {
            throw new Mailcode_Exception(
                'Invalid commands nesting',
                'The code command was not closed with an end command.',
                Mailcode_Interfaces_Commands_ProtectedContent::ERROR_INVALID_NESTING_NO_END
            );
        }

        $start = strpos($string, $open->getReplacementText()) + $open->getReplacementLength();
        $end = strpos($string, $end->getReplacementText());

        $content = substr($string, $start, ($end-$start));

        /**
        echo PHP_EOL;
        print_r(array(
            'string' => $string,
            'start' => $start,
            'end' => $end,
            'content' => $content
        ));
        echo PHP_EOL;
         */

        return $this->replaceContent($string, $content);
    }

    protected function replaceContent(string $string, string $content) : string
    {
        self::$protectedCounter++;

        $this->protectedContent = trim($content);
        $this->protectedPlaceholder = '__CT'.self::$protectedCounter.'__';

        return str_replace($content, $this->protectedPlaceholder, $string);
    }

    public function restoreContent($string) : string
    {
        return str_replace($this->protectedPlaceholder, $this->protectedContent, $string);
    }
}
