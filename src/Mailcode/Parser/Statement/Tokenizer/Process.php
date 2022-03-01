<?php

declare(strict_types=1);

namespace Mailcode;

abstract class Mailcode_Parser_Statement_Tokenizer_Process
{
    /**
     * @var Mailcode_Parser_Statement_Tokenizer
     */
    protected Mailcode_Parser_Statement_Tokenizer $tokenizer;

    /**
     * @var string
     */
    protected string $tokenized;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer_Token[]
     */
    protected array $tokensTemporary = array();

    /**
     * @var string
     */
    protected string $delimiter = '§§^§§';

    /**
     * Mailcode_Parser_Statement_Tokenizer_Process constructor.
     * @param Mailcode_Parser_Statement_Tokenizer $tokenizer
     * @param string $tokenized
     * @param Mailcode_Parser_Statement_Tokenizer_Token[] $tokens
     */
    public function __construct(Mailcode_Parser_Statement_Tokenizer $tokenizer, string $tokenized, array $tokens)
    {
        $this->tokenizer = $tokenizer;
        $this->tokenized = $tokenized;
        $this->tokensTemporary = $tokens;
    }

    public function getStatement() : string
    {
        return $this->tokenized;
    }

    /**
     * @return Mailcode_Parser_Statement_Tokenizer_Token[]
     */
    public function getTokens() : array
    {
        return $this->tokensTemporary;
    }

    public function process() : void
    {
        $this->_process();
    }

    abstract protected function _process() : void;

    /**
     * Registers a token to add in the statement string.
     *
     * @param string $type
     * @param string $matchedText
     * @param mixed $subject
     */
    protected function registerToken(string $type, string $matchedText, $subject=null) : void
    {
        $this->tokensTemporary[] = $this->createToken($type, $matchedText, $subject);
    }

    /**
     * @param string $type
     * @param string $matchedText
     * @param mixed $subject
     * @return Mailcode_Parser_Statement_Tokenizer_Token
     */
    public function createToken(string $type, string $matchedText, $subject=null) : Mailcode_Parser_Statement_Tokenizer_Token
    {
        $token = $this->tokenizer->createToken($type, $matchedText, $subject);
        $tokenID = $token->getID();

        $this->tokenized = str_replace(
            $matchedText,
            $this->delimiter.$tokenID.$this->delimiter,
            $this->tokenized
        );

        return $token;
    }

    protected function getTokenByID(string $tokenID) : ?Mailcode_Parser_Statement_Tokenizer_Token
    {
        foreach($this->tokensTemporary as $token)
        {
            if($token->getID() === $tokenID)
            {
                return $token;
            }
        }

        return null;
    }
}