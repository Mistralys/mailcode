<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\CountTrait} trait.
 *
 * @see \Mailcode\Traits\Commands\Validation\CountTrait
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Interfaces\Commands\Validation\CountInterface;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Mailcode_Parser_Statement_Tokenizer_Token_Variable;
use Mailcode\Mailcode_Variables_Variable;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use function Mailcode\t;

/**
 * Command validation drop-in: checks for the presence
 * of the `count:` keyword in the command statement.
 *
 * @package Mailcode
 * @subpackage Validation
 * @author Olaf Böcker <olaf.boecker@ionos.com>
 *
 * @see CountInterface
 */
trait CountTrait
{
    private bool $countEnabled = false;

    private ?Mailcode_Parser_Statement_Tokenizer_Token_Variable $countToken = null;

    protected function validateSyntax_check_count(): void
    {
        $token = $this->requireParams()->getInfo()->getTokenForKeyword(Mailcode_Commands_Keywords::TYPE_COUNT);
        if ($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            $this->countToken = $token;
        }

        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_COUNT);

        $this->countEnabled = $val->isValid();

        if ($this->countEnabled) {
            if (!$this->countToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
                $this->validationResult->makeError(
                    t('Invalid count type.' . ' ' . 'Expected Variable.'),
                    CountInterface::VALIDATION_COUNT_CODE_WRONG_TYPE
                );
                return;
            }
        }
    }

    public function isCountEnabled(): bool
    {
        return $this->countEnabled;
    }

    public function getCountVariable(): ?Mailcode_Variables_Variable
    {
        return $this->countToken != null ? $this->countToken->getVariable() : null;
    }
}
