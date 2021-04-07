<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser;

class Rule
{
    /**
     * Rule constructor.
     * @param string $name
     * @param string[] $parts
     */
    public function __construct(
        public string $name,
        public array $parts
    )
    {
    }

    public function __toString(): string
    {
        return sprintf("%s: %s", $this->name, implode(' ', $this->parts));
    }
}