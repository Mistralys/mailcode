<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax\ApacheVelocity;

use Mailcode\Mailcode_Commands_Command_Code;
use Mailcode\Mailcode_Exception;
use Mailcode\Mailcode_Translator_Command_Code;
use Mailcode\Translator\Syntax\BaseApacheVelocityCommandTranslation;

/**
 * Translates the {@see Mailcode_Commands_Command_Code} command to Apache Velocity.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class CodeTranslation extends BaseApacheVelocityCommandTranslation implements Mailcode_Translator_Command_Code
{
    private string $template = <<<'EOD'
#**
 Wrapper IF for the code command to insert native %1$s commands.
 This is needed for technical Mailcode reasons. 
*#
#if(true)%2$s#{end}
EOD;

    /**
     * @param Mailcode_Commands_Command_Code $command
     * @return string
     * @throws Mailcode_Exception
     */
    public function translate(Mailcode_Commands_Command_Code $command): string
    {
        return sprintf(
            $this->template,
            $command->getSyntaxName(),
            $command->getContentTrimmed()
        );
    }
}
