<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser\Calculators;

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
            $indexes = array_keys($rule->parts, $name);
            if(!empty($indexes)){
                foreach ($indexes as $index){
                    if(isset($rule->parts[$index + 1])){
                        $followPart = $rule->parts[$index + 1];
                        $firstSet = $this->firstSetCalculator->calculate($followPart);
                        array_push($follow, ...$firstSet);
                    } else {
                        if(!in_array($rule->__toString(), $this->visitedRules[$this->currentName], true)){
                            $this->visitedRules[$this->currentName][] = $rule->__toString();
                            $nextFollow = $this->calculate($rule->name, false);
                        } else {
                            $nextFollow = [];
                        }

                        array_push($follow, ...$nextFollow);
                    }
                }
            }
        }

        return $follow;
    }
}