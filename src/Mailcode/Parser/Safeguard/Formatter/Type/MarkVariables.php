<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Variable marker: Surrounds all show variable commands with markup 
 * to highlight them, independently of the command highlighting itself.
 * 
 * This is meant to be used to highlight variables in a document even
 * after it has been run through post-processing (for example once the
 * Apache Velocity template has been rendered). 
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Parser_Safeguard_Formatter_Type_MarkVariables extends Mailcode_Parser_Safeguard_Formatter_FormatType
{
    use Mailcode_Traits_Formatting_HTMLHighlighting;
    
    const ERROR_INVALID_TAG_TEMPLATE = 66101;
    
   /**
    * @var string
    */
    private $tagTemplate = '<span class="mailcode-marked-variable">%s</span>';
    
    protected function initFormatting() : void
    {
    }
    
    public function setTemplate(string $template) : void
    {
        if(substr_count($template, '%s') !== 1)
        {
            throw new Mailcode_Exception(
                'Invalid tag template',
                'The template string must contain the placeholder [%s] exactly 1 time.',
                self::ERROR_INVALID_TAG_TEMPLATE
            );
        }
        
        $this->tagTemplate = $template;
    }
    
    public function getTemplate() : string
    {
        return $this->tagTemplate;
    }
    
    public function getPrependTag() : string
    {
        $parts = explode('%s', $this->tagTemplate);
        return array_shift($parts);
    }
    
    public function getAppendTag() : string
    {
        $parts = explode('%s', $this->tagTemplate);
        return array_pop($parts);
    }
}
