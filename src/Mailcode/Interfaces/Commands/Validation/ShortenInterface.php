<?php
/**
 * File containing the {@see \Mailcode\Interfaces\Commands\Validation\ShortenInterface} interface.
 *
 * @see \Mailcode\Interfaces\Commands\Validation\ShortenInterface
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode\Interfaces\Commands\Validation;

use Mailcode\Mailcode_Interfaces_Commands_Command;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
use Mailcode\Traits\Commands\Validation\ShortenTrait;

/**
 * Interface for commands that support the shorten: keyword.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Daniel Storch <daniel.storch@ionos.com>
 *
 * @see ShortenTrait
 */
interface ShortenInterface extends Mailcode_Interfaces_Commands_Command
{
    public const VALIDATION_NAME_SHORTEN = 'shorten';

    public function isShortenEnabled() : bool;
    public function setShortenEnabled(bool $enabled) : self;
    public function getShortenToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Keyword;
} 