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
    protected string $content = '';
    private ?Mailcode_Collection $nestedMailcode = null;
    protected ?Mailcode_Parser_Statement_Tokenizer_Token_Number $contentIDToken = null;

    public function getContent() : string
    {
        return $this->content;
    }

    public function getContentTrimmed() : string
    {
        return trim($this->content);
    }

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

    public function getNestedMailcode() : Mailcode_Collection
    {
         if(isset($this->nestedMailcode))
         {
             return $this->nestedMailcode;
         }

         if($this->isMailcodeEnabled())
         {
             $collection = Mailcode::create()->parseString($this->getContent());
         }
         else
         {
             $collection = new Mailcode_Collection();
         }

         $this->nestedMailcode = $collection;

         return $collection;
    }

    protected function validateSyntax_nested_mailcode() : void
    {
        $collection = $this->getNestedMailcode();

        if($collection->isValid())
        {
            return;
        }

        $errors = $collection->getErrors();

        foreach($errors as $error)
        {
            $this->getValidationResult()->makeError(
                $error->getMessage(),
                $error->getCode()
            );
        }
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

    public function getVariables() : Mailcode_Variables_Collection_Regular
    {
        $variables = parent::getVariables();

        if($this->isMailcodeEnabled())
        {
            $nested = $this->getNestedMailcode()
                ->getVariables()
                ->getAll();

            foreach($nested as $variable)
            {
                $variables->add($variable);
            }
        }

        return $variables;
    }
}
