<?php
/**
 * File containing the {@see Mailcode_Translator_Syntax_ApacheVelocity_ShowVariable} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see Mailcode_Translator_Syntax_ApacheVelocity_ShowVariable
 */

declare(strict_types=1);

namespace Mailcode;

/**
 * Translates the "ShowVariable" command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator_Syntax_ApacheVelocity_ShowVariable extends Mailcode_Translator_Syntax_ApacheVelocity implements Mailcode_Translator_Command_ShowVariable
{
    public function translate(Mailcode_Commands_Command_ShowVariable $command): string
    {
        if($command->isDecryptionEnabled())
        {
            $varName = $this->renderDecryptionKey($command);
        }
        else
        {
            $varName = undollarize($command->getVariableName());
        }

        return $this->renderVariableEncodings($command, $varName);
    }

    private function renderDecryptionKey(Mailcode_Commands_Command_ShowVariable $command) : string
    {
        $keyName = $command->getDecryptionKeyName();

        if(!empty($keyName))
        {
            $varName = sprintf(
                'text.decrypt(%s, "%s")',
                dollarize($command->getVariableName()),
                $keyName
            );
        }
        else
        {
            // This will make the decryption system use the system's
            // default key name.
            $varName = sprintf(
                'text.decrypt(%s)',
                dollarize($command->getVariableName())
            );
        }

        if($this->hasVariableEncodings($command)) {
            $varName = '${'.$varName.'}';
        }

        return $varName;
    }
}
