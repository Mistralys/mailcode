<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode\Translator\Syntax;

use Mailcode\Interfaces\Commands\EncodableInterface;
use Mailcode\Mailcode;
use Mailcode\Mailcode_Commands_Keywords;
use Mailcode\Translator\BaseCommandTranslation;
use function Mailcode\dollarize;
use function Mailcode\undollarize;

/**
 * Abstract base class for Hubspot "HubL" command translation classes.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
abstract class HubL extends BaseCommandTranslation
{
    public const SYNTAX_NAME = 'HubL';
    
    public function getLabel(): string
    {
        return 'Hubspot HubL';
    }

    public function getSyntaxName(): string
    {
        return self::SYNTAX_NAME;
    }

    public static function areVariableNamesLowercase() : bool
    {
        return true;
    }

    protected function formatVariableName(string $name) : string
    {
        $varName = undollarize($name);

        if(self::areVariableNamesLowercase()) {
            $varName = strtolower($varName);
        }

        return $varName;
    }

    protected function formatVariablesInString(string $subject) : string
    {
        $variables = Mailcode::create()->createVariables()->parseString($subject)->getAll();

        foreach($variables as $variable) {
            $subject = str_replace($variable->getMatchedText(), $this->formatVariableName($variable->getFullName()), $subject);
        }

        return $subject;
    }

    public function renderQuotedValue(string $value): string
    {
        return sprintf(
            '"%s"',
            str_replace('"', '\"', $value)
        );
    }

    /**
     * @var array<string,string>
     */
    private array $encodingTemplates = array(
        Mailcode_Commands_Keywords::TYPE_URLENCODE => '%s|urlencode',
        Mailcode_Commands_Keywords::TYPE_URLDECODE => '%s|urldecode',
    );

    protected function renderEncodings(EncodableInterface $command, string $statement): string
    {
        $encodings = $command->getActiveEncodings();
        $result = $statement;

        foreach ($encodings as $encoding) {
            $result = $this->renderEncoding($encoding, $result, $command);
        }

        return $result;
    }

    protected function renderEncoding(string $keyword, string $result, EncodableInterface $command): string
    {
        $template = $this->encodingTemplates[$keyword] ?? '%s';

        return sprintf($template, $result);
    }

    public function renderStringToNumber(string $varName): string
    {
        return sprintf(
            '%s|float',
            $this->formatVariableName($varName)
        );
    }
}
