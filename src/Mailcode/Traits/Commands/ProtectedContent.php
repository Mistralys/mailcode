<?php
/**
 * File containing the trait {@see \Mailcode\Mailcode_Traits_Commands_ProtectedContent}.
 *
 * @package Mailcode
 * @subpackage Commands
 * @see \Mailcode\Mailcode_Traits_Commands_ProtectedContent
 */

declare(strict_types=1);

namespace Mailcode;

use Mailcode\Parser\PreParser;

/**
 * @package Mailcode
 * @subpackage Commands
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 *
 * @see Mailcode_Interfaces_Commands_ProtectedContent
 */
trait Mailcode_Traits_Commands_ProtectedContent
{
    /**
     * @var string
     */
    protected string $content = '';

    public function getContent() : string
    {
        return $this->content;
    }

    public function getContentTrimmed() : string
    {
        return trim($this->content);
    }

    protected ?Mailcode_Parser_Statement_Tokenizer_Token_Number $contentIDToken = null;

    public function getContentIDToken() : ?Mailcode_Parser_Statement_Tokenizer_Token_Number
    {
        return $this->contentIDToken;
    }

    protected function validateSyntax_content_id() : void
    {
        $contentIDToken = $this->requireParams()
            ->getInfo()
            ->getTokenByIndex(0);

        if($contentIDToken instanceof Mailcode_Parser_Statement_Tokenizer_Token_Number)
        {
            $this->contentIDToken = $contentIDToken;
            $this->loadContent();
            return;
        }

        $this->validationResult->makeError(
            t('The content ID parameter is missing.'),
            Mailcode_Interfaces_Commands_ProtectedContent::VALIDATION_ERROR_CONTENT_ID_MISSING
        );
    }

    public function getContentID() : int
    {
        if(isset($this->contentIDToken))
        {
            return (int)$this->contentIDToken->getValue();
        }

        throw new Mailcode_Exception(
            'No content ID set',
            '',
            Mailcode_Interfaces_Commands_ProtectedContent::ERROR_NO_CONTENT_ID_TOKEN
        );
    }

    private function loadContent() : void
    {
        $contentID = $this->getContentID();

        $this->content = PreParser::getContent($contentID);

        PreParser::clearContent($contentID);
    }

    public function getNormalized() : string
    {
        return (new Mailcode_Commands_Normalizer_ProtectedContent($this))->normalize();
    }
}
