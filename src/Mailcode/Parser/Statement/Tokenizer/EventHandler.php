<?php
/**
 * File containing the class {@see \Mailcode\Parser\Statement\Tokenizer\EventHandler}.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Parser\Statement\Tokenizer\EventHandler
 */

declare(strict_types=1);

namespace Mailcode\Parser\Statement\Tokenizer;

use Mailcode\Mailcode_Parser_Statement_Tokenizer;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;

/**
 * Event handler for the command parameter tokenizer:
 * allows listening to modifications of the token
 * collection.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class EventHandler
{
    private Mailcode_Parser_Statement_Tokenizer $tokenizer;

    public function __construct(Mailcode_Parser_Statement_Tokenizer $tokenizer)
    {
        $this->tokenizer = $tokenizer;
    }

    // region: _Listeners

    /**
     * @var array<string,array<int,callable>>
     */
    private array $listeners = array();

    /**
     * Callback arguments:
     *
     * 1. Tokenizer instance
     *
     * @param callable $callback
     * @return void
     */
    public function onKeywordsChanged(callable $callback) : void
    {
        $this->addListener(self::EVENT_KEYWORDS_CHANGED, $callback);
    }

    /**
     * Callback arguments:
     *
     * 1. Tokenizer instance
     * 2. Keyword name
     *
     * @param callable $callback
     * @return void
     */
    public function onKeywordRemoved(callable $callback) : void
    {
        $this->addListener(self::EVENT_KEYWORD_REMOVED, $callback);
    }

    /**
     * Callback arguments:
     *
     * 1. Tokenizer instance
     * 2. Keyword name
     *
     * @param callable $callback
     * @return void
     */
    public function onKeywordAdded(callable $callback) : void
    {
        $this->addListener(self::EVENT_KEYWORD_ADDED, $callback);
    }

    /**
     * Callback arguments:
     *
     * 1. Tokenizer instance
     * 2. Token instance
     *
     * @param callable $callback
     * @return void
     */
    public function onTokenRemoved(callable $callback) : void
    {
        $this->addListener(self::EVENT_TOKEN_REMOVED, $callback);
    }

    /**
     * Callback arguments:
     *
     * 1. Tokenizer instance
     * 2. Token instance
     *
     * @param callable $callback
     * @return void
     */
    public function onTokenAdded(callable $callback) : void
    {
        $this->addListener(self::EVENT_TOKEN_ADDED, $callback);
    }

    /**
     * Callback arguments:
     *
     * 1. Tokenizer instance
     * 2. Token instance
     *
     * @param callable $callback
     * @return void
     */
    public function onTokenAppended(callable $callback) : void
    {
        $this->addListener(self::EVENT_TOKEN_APPENDED, $callback);
    }

    /**
     * Callback arguments:
     *
     * 1. Tokenizer instance
     * 2. Token instance
     *
     * @param callable $callback
     * @return void
     */
    public function onTokenPrepended(callable $callback) : void
    {
        $this->addListener(self::EVENT_TOKEN_PREPENDED, $callback);
    }

    /**
     * Callback arguments:
     *
     * 1. Tokenizer instance
     *
     * @param callable $callback
     * @return void
     */
    public function onTokensChanged(callable $callback) : void
    {
        $this->addListener(self::EVENT_TOKENS_CHANGED, $callback);
    }

    private function addListener(string $eventName, callable $listener) : void
    {
        if(!isset($this->listeners[$eventName]))
        {
            $this->listeners[$eventName] = array();
        }

        $this->listeners[$eventName][] = $listener;
    }

    // endregion

    // region: Event handlers

    public function handleTokenRemoved(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->triggerTokenRemoved($token);
    }

    public function handleTokenAppended(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->triggerTokenAppended($token);
    }

    public function handleTokenPrepended(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->triggerTokenPrepended($token);
    }

    // endregion

    // region: Event triggers

    public const EVENT_TOKENS_CHANGED = 'TokensChanged';
    public const EVENT_TOKEN_ADDED = 'TokenAdded';
    public const EVENT_TOKEN_APPENDED = 'TokenAppended';
    public const EVENT_TOKEN_PREPENDED = 'TokenPrepended';
    public const EVENT_TOKEN_REMOVED = 'TokenRemoved';
    public const EVENT_KEYWORD_ADDED = 'KeywordAdded';
    public const EVENT_KEYWORD_REMOVED = 'KeywordRemoved';
    public const EVENT_KEYWORDS_CHANGED = 'KeywordsChanged';

    private function triggerTokenRemoved(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->triggerEvent(self::EVENT_TOKEN_REMOVED, $token);

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            $this->triggerKeywordRemoved($token->getKeyword());
        }

        $this->triggerTokensChanged();
    }

    private function triggerTokenAppended(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->triggerEvent(self::EVENT_TOKEN_APPENDED, $token);

        $this->triggerTokenAdded($token);
    }

    private function triggerTokenPrepended(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->triggerEvent(self::EVENT_TOKEN_PREPENDED, $token);

        $this->triggerTokenAdded($token);
    }

    private function triggerTokenAdded(Mailcode_Parser_Statement_Tokenizer_Token $token) : void
    {
        $this->triggerEvent(self::EVENT_TOKEN_ADDED, $token);

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Keyword)
        {
            $this->triggerKeywordAdded($token->getKeyword());
        }

        $this->triggerTokensChanged();
    }

    private function triggerTokensChanged() : void
    {
        $this->triggerEvent(self::EVENT_TOKENS_CHANGED);
    }

    private function triggerKeywordRemoved(string $keyword) : void
    {
        $this->triggerEvent(self::EVENT_KEYWORD_REMOVED, $keyword);

        $this->triggerKeywordsChanged();
    }

    private function triggerKeywordAdded(string $keyword) : void
    {
        $this->triggerEvent(self::EVENT_KEYWORD_ADDED, $keyword);

        $this->triggerKeywordsChanged();
    }

    private function triggerKeywordsChanged() : void
    {
        $this->triggerEvent(self::EVENT_KEYWORDS_CHANGED);
    }

    /**
     * @param string $eventName
     * @param mixed ...$listenerArgs
     * @return void
     */
    private function triggerEvent(string $eventName, ...$listenerArgs) : void
    {
        if(!isset($this->listeners[$eventName]))
        {
            return;
        }

        foreach($this->listeners[$eventName] as $listener)
        {
            $listener($this->tokenizer, ...$listenerArgs);
        }
    }

    // endregion

}
