<?php

declare(strict_types=1);

namespace Mailcode;

use AppUtils\OperationResult;

class Mailcode_PreProcessor
{
    /**
     * @var string
     */
    private $subject;

    /**
     * @var Mailcode_Parser_Safeguard
     */
    private $safeguard;

    /**
     * @var string
     */
    private $safeSubject;

    public function __construct(string $subject)
    {
        $this->subject = $subject;
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
