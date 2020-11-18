<?php

declare(strict_types=1);

namespace Mailcode;

class Mailcode_Parser_Statement_Info_Variables
{
    /**
     * @var Mailcode_Parser_Statement_Info
     */
    private $info;

    /**
     * @var Mailcode_Parser_Statement_Tokenizer
     */
    private $tokenizer;

    public function __construct(Mailcode_Parser_Statement_Info $info, Mailcode_Parser_Statement_Tokenizer $tokenizer)
    {
        $this->info = $info;
        $this->tokenizer = $tokenizer;
    }

    /**
     * Whether the whole statement is a variable being assigned a value.
     *
     * @return bool
     */
    public function isAssignment() : bool
    {
        $variable = $this->getByIndex(0);
        $operand = $this->info->getOperandByIndex(1);
        $value = $this->info->getTokenByIndex(2);

        if($variable && $operand && $value && $operand->isAssignment())
        {
            return true;
        }

        return false;
    }

    /**
     * Whether the whole statement is a variable being compared to something.
     *
     * @return bool
     */
    public function isComparison() : bool
    {
        $variable = $this->getByIndex(0);
        $operand = $this->info->getOperandByIndex(1);
        $value = $this->info->getTokenByIndex(2);

        if($variable && $operand && $value && $operand->isComparator())
        {
            return true;
        }

        return false;
    }

    /**
     * Retrieves all variables used in the statement.
     *
     * @return Mailcode_Variables_Variable[]
     * @throws Mailcode_Exception
     */
    public function getAll() : array
    {
        $result = array();
        $tokens = $this->tokenizer->getTokens();

        foreach($tokens as $token)
        {
            if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
            {
                $result[] = $token->getVariable();
            }
        }

        return $result;
    }

    /**
     * Retrieves a variable by its position in the command's parameters.
     * Returns null if there is no parameter at the specified index, or
     * if it is of another type.
     *
     * @param int $index Zero-based index.
     * @return Mailcode_Parser_Statement_Tokenizer_Token_Variable|NULL
     */
    public function getByIndex(int $index) : ?Mailcode_Parser_Statement_Tokenizer_Token_Variable
    {
        $token = $this->info->getTokenByIndex($index);

        if($token instanceof Mailcode_Parser_Statement_Tokenizer_Token_Variable)
        {
            return $token;
        }

        return null;
    }
}
