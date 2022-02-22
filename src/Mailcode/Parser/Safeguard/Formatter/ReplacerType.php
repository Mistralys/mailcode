<?php
/**
 * File containing the {@see Mailcode_Parser_Safeguard_Formatter_Type_Highlighting} class.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see Mailcode_Parser_Safeguard_Formatter_Type_Highlighting
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Abstract base class for replacer formatters that replace the placeholders.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class Mailcode_Parser_Safeguard_Formatter_ReplacerType extends Mailcode_Parser_Safeguard_Formatter
{
    public function getPriority() : int
    {
        return PHP_INT_MAX * -1;
    }
    
    public function replace() : void
    {
        $locations = $this->resolveLocations();
        
        foreach($locations as $location)
        {
            $location->replaceWith($this->resolveReplacement($location));

            $this->log = array_merge($this->log, $location->getLog());
        }

        $this->replaceContents();
    }

    private function replaceContents() : void
    {
        if(!$this->processesContent())
        {
            return;
        }

        $locations = $this->resolveLocations();

        foreach($locations as $location)
        {
            $command = $location->getPlaceholder()->getCommand();

            if($command instanceof Mailcode_Interfaces_Commands_ProtectedContent)
            {
                $this->processContentLocation($command, $location);
            }
        }
    }

    /**
     * @param Mailcode_Interfaces_Commands_ProtectedContent $command
     * @param Mailcode_Parser_Safeguard_Formatter_Location $location
     * @return void
     */
    private function processContentLocation(Mailcode_Interfaces_Commands_ProtectedContent $command, Mailcode_Parser_Safeguard_Formatter_Location $location) : void
    {
        $replaceWith = $this->resolveContentReplacement($command, $location);

        if ($replaceWith === null)
        {
            return;
        }

        $this->subject->replaceSubstrings(
            $command->getContentPlaceholder(),
            $replaceWith
        );
    }
    
   /**
    * Resolves the string with which this location needs to be
    * replaced.
    * 
    * @param Mailcode_Parser_Safeguard_Formatter_Location $location
    * @return string
    */
    private function resolveReplacement(Mailcode_Parser_Safeguard_Formatter_Location $location) : string
    {
        if($location->requiresAdjustment())
        {
            return $this->getReplaceString($location);
        }

        return $location->getPlaceholder()->getNormalizedText();
    }

    abstract public function getReplaceString(Mailcode_Parser_Safeguard_Formatter_Location $location) : string;

    abstract public function resolveContentReplacement(Mailcode_Interfaces_Commands_ProtectedContent $command, Mailcode_Parser_Safeguard_Formatter_Location $location) : ?string;
}
    