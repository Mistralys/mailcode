<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Info_Keywords
{
    /**
     * @var Mailcode_Parser_Statement_Info
     */
    private $info;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer
     */
    private $tokenizer;

    public function __construct(Mailcode_Parser_Statement_Info $info, Mailcode_Parser_Statement_Tokenizer $tokenizer)
    {
        $this->info = $info;
        $this->tokenizer = $tokenizer;
    }

    /**
     * Retrieves a keyword by its position in the command's parameters.
     * Returns null if there is no parameter at the specified index, or
     * if it is of another type.
     *
     * @param int $index Zero-based index.
     * @return Mailcode_Parser_Statement_Tokenizer_Token_Keyword|NULL
     */
    public function getByIndex(int $index) : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword
    {
        $token = $this->info->getTokenByIndex($index);

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            return $token;
        }

        return null;
    }

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token_Keyword[]
     */
    public function getAll() : array
    {
        $result = array();
        $tokens = $this->info->getTokens();

        foreach($tokens as $token)
        {
            if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
            {
                $result[] = $token;
            }
        }

        return $result;
    }

    /**
     * Adds or removes a keyword depending on whether it should be enabled.
     *
     * @param string $keyword The keyword name, with or without :
     * @param bool $enabled
     * @return Mailcode_Parser_Statement_Info_Keywords
     * @throws Mailcode_Exception
     */
    public function setEnabled(string $keyword, bool $enabled) : Mailcode_Parser_Statement_Info_Keywords
    {
        if($enabled)
        {
            return $this->add($keyword);
        }

        return $this->remove($keyword);
    }

    /**
     * Adds a keyword to the command.
     *
     * @param string $keyword Keyword name, with or without :
     * @return $this
     * @throws Mailcode_Exception
     */
    public function add(string $keyword) : Mailcode_Parser_Statement_Info_Keywords
    {
        $keyword = rtrim($keyword, ':').':';

        if(!$this->hasKeyword($keyword))
        {
            $this->tokenizer->appendKeyword($keyword);
        }

        return $this;
    }

    /**
     * Removes a keyword from the command, if it has one.
     * Has no effect otherwise.
     *
     * @param string $keyword Keyword name, with or without :
     * @return Mailcode_Parser_Statement_Info_Keywords
     */
    public function remove(string $keyword) : Mailcode_Parser_Statement_Info_Keywords
    {
        $keyword = rtrim($keyword, ':').':';
        $keywords = $this->getAll();

        foreach ($keywords as $kw)
        {
            if ($kw->getKeyword() !== $keyword) {
                continue;
            }

            $this->tokenizer->removeToken($kw);
        }

        return $this;
    }

    /**
     * Whether the command has the specified keyword.
     *
     * @param string $keyword Keyword name, with or without :
     * @return bool
     */
    public function hasKeyword(string $keyword) : bool
    {
        $keyword = rtrim($keyword, ':').':';
        $keywords = $this->getAll();

        foreach ($keywords as $kw)
        {
            if($kw->getKeyword() === $keyword)
            {
                return true;
            }
        }

        return false;
    }
}
