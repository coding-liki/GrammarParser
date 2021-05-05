<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser\Calculators;

use CodingLiki\GrammarParser\Rule\Rule;
use CodingLiki\GrammarParser\Rule\RulePart;
use CodingLiki\GrammarParser\RulesHelper;

class FollowSetCalculator
{

    private array $followSets = [];

    private array $visitedRules = [];
    private FirstSetCalculator $firstSetCalculator;

    /**
     * FollowSetCalculator constructor.
     * @param Rule[] $rules
     */
    public function __construct(private array $rules)
    {
        $this->firstSetCalculator = new FirstSetCalculator($this->rules);
    }

    public function calculate(string $name, bool $root = true): array
    {
        if ($root) {
            $this->visitedRules = [];
        }

        if (!isset($this->visitedRules[$name])) {
            $this->visitedRules[$name] = [];
        }

        if (!isset($this->followSets[$name])) {
            $this->followSets[$name] = array_values(array_unique($this->calculateSet($name)));
        }

        return $this->followSets[$name];
    }

    private function calculateSet(string $name): array
    {
        return $this->checkTrivialFollow($name) ?? $this->calculateFollow($name);
    }

    private function checkTrivialFollow(string $name): ?array
    {
        if ($name === RulesHelper::ROOT_RULE_NAME) {
            return ['$'];
        }

        if (empty(RulesHelper::getRulesByName($name, $this->rules))) {
            return [];
        }

        return null;
    }

    private function calculateFollow(string $name): array
    {
        $follow = [];
        foreach ($this->rules as $rule) {
            $nextFollow = $this->calculateForRule($rule, $name);
            array_push($follow, ...$nextFollow);
        }

        return $follow;
    }

    private function calculateForRule(Rule $rule, string $name): array
    {
        if (in_array($rule, $this->visitedRules[$name], true)) {
            return $this->followSets[$name] ?? [];
        }

        $this->visitedRules[$name][] = $rule;

        $follow = [];
        $parts = $rule->findPartsByName($name);
        $lastRuleIndex = count($rule->getParts()) - 1;

        foreach ($parts as $index => $part) {
            if ($part->getType() === RulePart::TYPE_MUST_BE_ONCE_OR_MORE) {
                $nextFollow = $this->firstSetCalculator->calculate($part->getData());
                empty($nextFollow) ?: array_push($follow, ...$nextFollow);
            }

            if ($index < $lastRuleIndex) {
                $nextPart = $rule->getParts()[$index + 1]->getData();
                $nextFollow = $this->firstSetCalculator->calculate($nextPart);
            } else {
                $nextFollow = $this->calculate($rule->getName(), false);
            }

            empty($nextFollow) ?: array_push($follow, ...$nextFollow);
        }

        return $follow;
    }
}