<?php
/**
 * File containing the class {@see \Mailcode\Mailcode_PreProcessor}.
 *
 * @package Mailcode
 * @subpackage Parser
 * @see \Mailcode\Mailcode_PreProcessor
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

/**
 * Pre-Processor: Handles commands that apply formatting
 * before the rest of the document is processed. An example
 * is the `mono` command, which adds monospace formatting
 * to text in an HTML context.
 *
 * @package Mailcode
 * @subpackage Parser
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_PreProcessor
{
    private Mailcode_Parser_Safeguard $safeguard;
    private string $safeSubject;

    public function __construct(string $subject)
    {
        $this->safeguard = Mailcode::create()->createSafeguard($subject);
        $this->safeSubject = $this->safeguard->makeSafePartial();
    }

    public function getSafeguard() : Mailcode_Parser_Safeguard
    {
        return $this->safeguard;
    }

    public function isValid() : bool
    {
        return $this->safeguard->isValid();
    }

    public function getValidationResult() : OperationResult
    {
        return $this->safeguard->getCollection()->getValidationResult();
    }

    public function render() : string
    {
        $formatting = $this->safeguard->createFormatting($this->safeSubject);
        $formatting->makePartial();
        $formatting->addFormatter($formatting->createPreProcessing());
        $formatting->applyFormatting();

        return $formatting->getSubject()->getString();
    }
}
