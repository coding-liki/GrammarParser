<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser\Rule;

class Rule
{
    /**
     * Rule constructor.
     * @param string $name
     * @param RulePart[] $parts
     * @param string $partsString
     */
    public function __construct(private string $name, private array $parts, private string $partsString = '')
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Rule
     */
    public function setName(string $name): Rule
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return RulePart[]
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * @param string $name
     * @return RulePart[]
     */
    public function findPartsByName(string $name): array
    {
        $result = [];
        foreach ($this->parts as $key => $part){
            if($part->getData() === $name){
                $result[$key] = $part;
            }
        }

        return $result;
    }

    /**
     * @param RulePart[] $parts
     * @return Rule
     */
    public function setParts(array $parts): Rule
    {
        $this->parts = $parts;
        return $this;
    }

    public function addPart(RulePart $rulePart, ?int $position = null): self
    {
        if ($position === null || $position >= count($this->parts)) {
            $this->parts[] = $rulePart;
        } else {
            if ($position >= 0) {
                array_splice($this->parts, $position, 0, $rulePart);
            }
        }

        $partStrings = array_map(static function (RulePart $rulePart): string {
            return $rulePart->getData() . $rulePart->getType();
        }, $this->parts);
        $this->partsString = implode(' ', $partStrings);
        return $this;
    }

    /**
     * @return string
     */
    public function getPartsString(): string
    {
        return $this->partsString;
    }

    public function __toString(): string
    {
        return sprintf('%s: %s;', $this->name, $this->partsString);
    }
}