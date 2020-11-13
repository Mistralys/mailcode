<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_Normalized_Location} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_Normalized_Location
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * These locations never need any adjustment.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 * 
 * @property Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables $formatter
 */
class Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables_Location extends Mailcode_Parser_Safeguard_Formatter_Type_HTMLHighlighting_Location
{
    protected function init() : void
    {
        parent::init();
        
        $this->append = $this->formatter->getAppendTag();
        $this->prepend = $this->formatter->getPrependTag();
    }
    
    public function requiresAdjustment(): bool
    {
        if(!parent::requiresAdjustment())
        {
            return false;
        }
        
        $command = $this->placeholder->getCommand();
        
        return $command instanceof Mailcode_Commands_ShowBase;
    }
}
