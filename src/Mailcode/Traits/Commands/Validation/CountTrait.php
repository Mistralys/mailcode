<?php
/**
 * File containing the {@see \Mailcode\Traits\Commands\Validation\NoTrackingTrait} trait.
 *
 * @see \Mailcode\Traits\Commands\Validation\NoTrackingTrait
 * @subpackage Validation
 * @package Mailcode
 */

declare(strict_types=1);

namespace Mailcode\Traits\Commands\Validation;

use Mailcode\Mailcode_Commands_CommonConstants;
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
 *
 * @see CountInterface
 */
trait CountTrait
{
    /**
     * @var boolean
     */
    private bool $countEnabled = false;

    private ?Mailcode_Parser_Statement_Tokenizer_Token_Variable $token = null;

    protected function validateSyntax_count(): void
    {
        $token = $this->requireParams()->getInfo()->getTokenForKeyword(Mailcode_Commands_Keywords::TYPE_COUNT);
        if ($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
            $this->token = $token;
        }

        $val = $this->validator->createKeyword(Mailcode_Commands_Keywords::TYPE_COUNT);
        $this->countEnabled = $val->isValid();

        if ($this->countEnabled) {
            if (!$this->token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable) {
                $this->validationResult->makeError(
                    t('Invalid count usage'),
                    Mailcode_Commands_CommonConstants::VALIDATION_INVALID_COUNT_USAGE
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
        return $this->token->getVariable();
    }

}
