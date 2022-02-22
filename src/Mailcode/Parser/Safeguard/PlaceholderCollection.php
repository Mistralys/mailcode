<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Safeguard_PlaceholderCollection
{
    /**
     * @var Mailcode_Parser_Safeguard_Placeholder[]
     */
    private $placeholders;

    /**
     * @var string[]|NULL
     */
    protected $placeholderStrings;

    /**
     * @param Mailcode_Parser_Safeguard_Placeholder[] $placeholders
     */
    public function __construct(array $placeholders)
    {
        $this->placeholders = array_values($placeholders);
    }

    /**
     * @return Mailcode_Parser_Safeguard_Placeholder[]
     */
    public function getAll() : array
    {
        return $this->placeholders;
    }

    /**
     * @param int $index Zero-based index of the command
     * @return Mailcode_Parser_Safeguard_Placeholder
     * @throws Mailcode_Exception
     */
    public function getByIndex(int $index) : Mailcode_Parser_Safeguard_Placeholder
    {
        if(isset($this->placeholders[$index]))
        {
            return $this->placeholders[$index];
        }

        throw new Mailcode_Exception(
            'Cannot get first placeholder, no placeholders found.',
            '',
            Mailcode_Parser_Safeguard::ERROR_NO_FIRST_PLACEHOLDER
        );
    }

    public function getByCommand(Mailcode_Commands_Command $command) : Mailcode_Parser_Safeguard_Placeholder
    {
        foreach($this->placeholders as $placeholder)
        {
            if($placeholder->getCommand() === $command)
            {
                return $placeholder;
            }
        }

        throw new Mailcode_Exception(
            'Placeholder not found by command.',
            'None of the placeholders have the specified command.',
            Mailcode_Parser_Safeguard::ERROR_NO_PLACEHOLDER_FOR_COMMAND
        );
    }

    /**
     * Retrieves a placeholder instance by its ID.
     *
     * @param int $id
     * @throws Mailcode_Exception If the placeholder was not found.
     * @return Mailcode_Parser_Safeguard_Placeholder
     */
    public function getByID(int $id) : Mailcode_Parser_Safeguard_Placeholder
    {
        foreach($this->placeholders as $placeholder)
        {
            if($placeholder->getID() === $id)
            {
                return $placeholder;
            }
        }

        throw new Mailcode_Exception(
            'No such safeguard placeholder.',
            sprintf(
                'The placeholder ID [%s] is not present in the safeguard instance.',
                $id
            ),
            Mailcode_Parser_Safeguard::ERROR_PLACEHOLDER_NOT_FOUND
        );
    }

    /**
     * Retrieves a placeholder instance by its replacement text.
     *
     * @param string $string
     * @throws Mailcode_Exception
     * @return Mailcode_Parser_Safeguard_Placeholder
     */
    public function getByString(string $string) : Mailcode_Parser_Safeguard_Placeholder
    {
        foreach($this->placeholders as $placeholder)
        {
            if($placeholder->getReplacementText() === $string)
            {
                return $placeholder;
            }
        }

        throw new Mailcode_Exception(
            'No such safeguard placeholder.',
            sprintf(
                'The placeholder replacement string [%s] is not present in the safeguard instance.',
                $string
            ),
            Mailcode_Parser_Safeguard::ERROR_PLACEHOLDER_NOT_FOUND
        );
    }

    /**
     * Retrieves a list of all placeholder IDs used in the text.
     *
     * @return string[]
     */
    public function getStrings() : array
    {
        if(is_array($this->placeholderStrings))
        {
            return $this->placeholderStrings;
        }

        $this->placeholderStrings = array();

        foreach($this->placeholders as $placeholder)
        {
            $this->placeholderStrings[] = $placeholder->getReplacementText();
        }

        return $this->placeholderStrings;
    }

    public function hasPlaceholders() : bool
    {
        return $this->countPlaceholders() > 0;
    }

    public function isStringPlaceholder(string $subject) : bool
    {
        $ids = $this->getStrings();

        return in_array($subject, $ids, true);
    }

    public function countPlaceholders() : int
    {
        return count($this->placeholders);
    }

    public function getFirst() : Mailcode_Parser_Safeguard_Placeholder
    {
        return $this->getByIndex(0);
    }
}
