<?php
/**
 * File containing the {@see Mailcode_Commands_Command_ShowVariable} class.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see Mailcode_Commands_Command_ShowVariable
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Interfaces\Commands\Validation\DecryptInterface;
use Mailcode\Interfaces\Commands\Validation\IDNEncodingInterface;
use Mailcode\Traits\Commands\Validation\DecryptTrait;
use Mailcode\Traits\Commands\Validation\IDNDecodeTrait;
use Mailcode\Traits\Commands\Validation\IDNEncodeTrait;

/**
 * Mailcode command: show a variable value.
 *
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Commands_Command_ShowVariable
    extends Mailcode_Commands_ShowBase
    implements
    IDNEncodingInterface, DecryptInterface
{
    public const VALIDATION_TOO_MANY_PARAMETERS = 69701;

    use IDNEncodeTrait;
    use IDNDecodeTrait;
    use DecryptTrait;

    public function getName(): string
    {
        return 'showvar';
    }

    public function getLabel(): string
    {
        return t('Show variable');
    }

    protected function getValidations(): array
    {
        return array(
            Mailcode_Interfaces_Commands_Validation_Variable::VALIDATION_NAME_VARIABLE,
            DecryptInterface::VALIDATION_DECRYPT_NAME
        );
    }

    /**
     * Gets all validated tokens that the command supports
     * (namely the variable, and keywords).
     *
     * @return Mailcode_Parser_Statement_Tokenizer_Token[]
     * @throws Mailcode_Exception
     */
    protected function resolveActiveTokens(): array
    {
        $allowed = array($this->getVariableToken());

        $encodings = $this->getSupportedEncodings();

        foreach ($encodings as $keyword) {
            $token = $this->getEncodingToken($keyword);
            if ($token) {
                $allowed[] = $token;

                $parameter = $this->requireParams()->getInfo()->getTokenForKeyWord($token->getKeyword());
                if ($parameter) {
                    $allowed[] = $parameter;
                }
            }
        }

        return $allowed;
    }
}
