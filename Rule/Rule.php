<?php
declare(strict_types=1);

namespace Rule;

class Rule
{
    /**
     * Rule constructor.
     * @param string $name
     * @param RulePart[] $parts
     */
    public function __construct(private string $name, private array $parts)
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
        if($position === null || $position >= count($this->parts)){
            $this->parts[] = $rulePart;
        } else if($position >= 0){
            array_splice($this->parts, $position, 0, $rulePart);
        }

        return $this;
    }
}