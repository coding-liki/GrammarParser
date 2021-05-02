<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser\Calculators;

use CodingLiki\GrammarParser\Rule\Rule;
use CodingLiki\GrammarParser\Rule\RulePart;

class FirstSetCalculator
{
    /**
     * @var array<string[]>
     */
    private array $firstSets = [];

    /**
     * FirstSetCalculator constructor.
     * @param Rule[] $rules
     */
    public function __construct(private array $rules)
    {

    }

    /**
     * @param string $ruleName
     * @return string[]
     */
    public function calculate(string $ruleName): array
    {
        if (!isset($this->firstSets[$ruleName])) {
            $this->calculateAndSaveSetFromRules($ruleName);
        }

        return $this->firstSets[$ruleName];
    }

    /**
     * @param array $set
     * @param string $ruleName
     */
    private function normalizeAndSaveSet(array $set, string $ruleName): void
    {
        if (empty($set)) {
            $set[] = $ruleName;
        }

        $set = array_values(array_unique($set));
        $this->firstSets[$ruleName] = $set;
    }

    /**
     * @param mixed $ruleName
     */
    private function calculateAndSaveSetFromRules(mixed $ruleName): void
    {
        $set = [];

        foreach ($this->rules as $rule) {
            if ($rule->getName() === $ruleName) {
                $parts = $rule->getParts();
                $part = array_shift($parts);
                $nextSet = $this->calculate($part->getData());
                array_push($set, ...$nextSet);
                while ($part->getType() === RulePart::TYPE_MUST_BE_ONCE_OR_MORE){
                    $part = array_shift($parts);
                    $nextSet = $this->calculate($part->getData());
                    array_push($set, ...$nextSet);
                }
            }
        }

        $this->normalizeAndSaveSet($set, $ruleName);
    }


}