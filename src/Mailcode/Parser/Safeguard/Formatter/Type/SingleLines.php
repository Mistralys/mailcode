<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_SingleLines} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_SingleLines
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Single line formatter: ensures that all commands in the
 * subject string are placed on a separate line. This is 
 * typically used when using a custom parser for HTML documents,
 * to make it easier to identify commands.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatter_Type_SingleLines extends Mailcode_Parser_Safeguard_Formatter_FormatType
{
   /**
    * @var string
    */
    private $eol;
    
   /**
    * @var int
    */
    private $eolLength;
    
    protected function initFormatting() : void
    {
        $this->eol = $this->resolveNewlineChar($this->subject->getString());
        $this->eolLength = strlen($this->eol);
    }
    
    public function getPriority() : int
    {
        return PHP_INT_MAX - 1;
    }
    
    public function getEOLChar() : string
    {
        return $this->eol;
    }
    
    public function getEOLLength() : int
    {
        return $this->eolLength;
    }

    public function processesContent() : bool
    {
        return false;
    }
}

