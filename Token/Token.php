<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser\Token;

class Token
{
    public function __construct(private string $type, private string $value)
    {
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}