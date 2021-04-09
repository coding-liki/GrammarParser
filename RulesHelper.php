<?php
declare(strict_types=1);

namespace CodingLiki\GrammarParser;

use CodingLiki\GrammarParser\Rule\Rule;
use CodingLiki\GrammarParser\Rule\RulePart;

class RulesHelper
{
    public static array $rulesByName = [];

    public const ROOT_RULE_NAME = 'S\'';

    public const ROOT_SYMBOL = '^';

    /**
     * @param string $name
     * @param Rule[] $rules
     * @return array
     */
    public static function getRulesByName(string $name, array $rules): array
    {
        if (!isset(self::$rulesByName[$name])) {
            self::$rulesByName[$name] = [];
            foreach ($rules as $rule) {
                if ($rule->getName() === $name) {
                    self::$rulesByName[$name][] = $rule;
                }
            }
        }

        return self::$rulesByName[$name];
    }

    /**
     * @param Rule[] $rules
     * @return Rule
     */
    public static function buildRootRule(array $rules): Rule
    {
        $rule = new Rule(self::ROOT_RULE_NAME, []);
        $rule->addPart(new RulePart($rules[0]->getName(), RulePart::TYPE_NORMAL));
        return $rule;
    }

    public static function cleanCache(): void
    {
        self::$rulesByName = [];
    }
}