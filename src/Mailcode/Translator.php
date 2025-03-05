<?php
/**
 * @package Mailcode
 * @subpackage Translator
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ClassHelper;
use Mailcode\Translator\Syntax\ApacheVelocitySyntax;
use Mailcode\Translator\Syntax\HubLSyntax;
use Mailcode\Translator\SyntaxInterface;

/**
 * Used to translate mailcode syntax to other syntaxes.
 *
 * @package Mailcode
 * @subpackage Translator
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class Mailcode_Translator
{
    public const ERROR_INVALID_SYNTAX_NAME = 73001;

    /**
     * @var array<string,SyntaxInterface>
     */
    private array $syntaxes = array();

    private static ?Mailcode_Translator $instance = null;

    public static function create() : Mailcode_Translator
    {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        foreach($this->resolveSyntaxClasses() as $class) {
            $syntax = new $class();
            $this->syntaxes[$syntax->getTypeID()] = $syntax;
        }
    }

    /**
     * Creates an instance of the specified syntax.
     *
     * @param string $name The name of the syntax, e.g. {@see ApacheVelocitySyntax::SYNTAX_NAME}.
     * @return SyntaxInterface
     */
    public function createSyntax(string $name) : SyntaxInterface
    {
        if(isset($this->syntaxes[$name])) {
            return $this->syntaxes[$name];
        }

        throw new Mailcode_Exception(
            'Invalid translation syntax',
            sprintf(
                'The syntax [%s] does not exist. Possible values are: [%s].',
                $name,
                implode(', ', $this->getSyntaxNames())
            ),
            self::ERROR_INVALID_SYNTAX_NAME
        );
    }

    public function createApacheVelocity() : ApacheVelocitySyntax
    {
        return ClassHelper::requireObjectInstanceOf(
            ApacheVelocitySyntax::class,
            $this->createSyntax(ApacheVelocitySyntax::SYNTAX_NAME)
        );
    }

    public function createHubL() : HubLSyntax
    {
        return ClassHelper::requireObjectInstanceOf(
            HubLSyntax::class,
            $this->createSyntax(HubLSyntax::SYNTAX_NAME)
        );
    }

    /**
     * Retrieves an instance for each syntax available
     * in the system.
     *
     * @return SyntaxInterface[]
     */
    public function getSyntaxes() : array
    {
        return array_values($this->syntaxes);
    }

    /**
     * Retrieves a list of names for all syntaxes supported
     * by the system.
     *
     * @return string[]
     */
    public function getSyntaxNames() : array
    {
        return array_keys($this->syntaxes);
    }

    /**
     * @return class-string<SyntaxInterface>[]
     */
    private function resolveSyntaxClasses() : array
    {
        return ClassCache::findClassesInFolder(
            __DIR__.'/Translator/Syntax',
            false,
            SyntaxInterface::class
        );
    }

    /**
     * Checks whether the specified syntax exists.
     *
     * @param string $name The syntax name, e.g. {@see ApacheVelocitySyntax::SYNTAX_NAME}.
     * @return bool
     */
    public function syntaxExists(string $name) : bool
    {
        return isset($this->syntaxes[$name]);
    }
}
