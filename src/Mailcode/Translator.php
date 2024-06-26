<?php
/**
 * File containing the {@see \Mailcode\Mailcode_Translator} class.
 *
 * @package Mailcode
 * @subpackage Translator
 * @see \Mailcode\Mailcode_Translator
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ClassHelper;
use AppUtils\FileHelper;
use Mailcode\Translator\Syntax;
use Mailcode\Translator\Syntax\ApacheVelocity;
use Mailcode\Translator\Syntax\HubL;

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
     * Creates an instance of the specified syntax.
     *
     * @param string $name The name of the syntax, e.g. "ApacheVelocity"
     * @return Syntax
     */
    public function createSyntax(string $name) : Syntax
    {
        if($this->syntaxExists($name))
        {
            return new Syntax($name);
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

    public function createApacheVelocity() : Syntax
    {
        return $this->createSyntax(ClassHelper::getClassTypeName(ApacheVelocity::class));
    }

    public function createHubL() : Syntax
    {
        return $this->createSyntax(ClassHelper::getClassTypeName(HubL::class));
    }

    /**
     * Retrieves an instance for each syntax available
     * in the system.
     *
     * @return Syntax[]
     */
    public function getSyntaxes() : array
    {
        $names = $this->getSyntaxNames();
        $result = array();

        foreach($names as $name)
        {
            $result[] = $this->createSyntax($name);
        }

        return $result;
    }

    /**
     * Retrieves a list of the names of all syntaxes supported
     * by the system.
     *
     * @return string[]
     */
    public function getSyntaxNames() : array
    {
        return FileHelper::createFileFinder(__DIR__.'/Translator/Syntax')
            ->getPHPClassNames();
    }

    /**
     * Checks whether the specified syntax exists.
     *
     * @param string $name The syntax name, e.g. "ApacheVelocity"
     * @return bool
     */
    public function syntaxExists(string $name) : bool
    {
        $names = $this->getSyntaxNames();

        return in_array($name, $names);
    }
}
