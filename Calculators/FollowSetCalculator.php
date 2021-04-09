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
    private string $currentName = '';
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
        if($root){
            $this->currentName = $name;
        }

        if(!isset($this->visitedRules[$name])){
            $this->visitedRules[$name] = [];
        }

        if (!isset($this->followSets[$name])) {
            $this->followSets[$name] = array_values(array_unique($this->calculateSet($name)));
        }

        return $this->followSets[$name];
    }

    private function calculateSet(string $name): array
    {
        if($name === RulesHelper::ROOT_RULE_NAME){
            return ['$'];
        }

        if (empty(RulesHelper::getRulesByName($name, $this->rules))) {
            return [];
        }

        $follow = [];
        foreach ($this->rules as $rule) {
            $allParts = $rule->getParts();
            $parts = $rule->findPartsByName($name);
            $indexes = array_keys($parts);
            if(!empty($indexes)){
                foreach ($indexes as $index){
                    $offset = 1;

                    $followPart = $allParts[$index + $offset] ?? null;
                    while($followPart !== null && $followPart->getType() !== RulePart::TYPE_NORMAL ){
                        $firstSet = $this->firstSetCalculator->calculate($followPart->getData());
                        array_push($follow, ...$firstSet);
                        $offset++;
                        $followPart = $allParts[$index + $offset] ?? null;
                    }
                    if($followPart === null){
                        if(!in_array($rule->__toString(), $this->visitedRules[$this->currentName], true)){
                            $this->visitedRules[$this->currentName][] = $rule->__toString();
                            $nextFollow = $this->calculate($rule->getName(), false);
                        } else {
                            $nextFollow = [];
                        }

                        array_push($follow, ...$nextFollow);
                    } else {
                        $firstSet = $this->firstSetCalculator->calculate($followPart->getData());
                        array_push($follow, ...$firstSet);
                    }

                }
            }
        }

        return $follow;
    }
}