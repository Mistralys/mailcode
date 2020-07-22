<?php
/**
 * File containing the {@see Mailcode_Parser_Statement_Tokenizer_TypeInterface} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Statement_Tokenizer_TypeInterface
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Interface for parser tokens.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
interface Mailcode_Parser_Statement_Tokenizer_TypeInterface
{
    public function getTypeID() : string;
    public function getID() : string;
    public function getMatchedText() : string;
    public function getNormalized() : string;
    public function isValue() : bool;
}
